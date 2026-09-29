<?php
/**
 * Product Box Block — Extra functionality.
 *
 * Image size selection, image resolution, and small pure helpers for the
 * product box template.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Pick the WordPress image size for a product box image.
 *
 * Always an uncropped core size: "Fill & crop" is done by CSS object-fit, so
 * the whole image stays available and no extra sub-sizes are generated.
 *
 * Earlier versions asked for 'product-box-image' (550×550) and
 * 'product-box-wide' (800×450), but registered them on after_setup_theme from
 * a file that loads on acf/init, after that hook has fired. The sizes were
 * never generated, so WordPress fell back to the full-size original.
 *
 * @param bool   $is_top_image Whether the Top Image style is active.
 * @param string $fit          'contain' or 'cover' (handled in CSS).
 * @param string $ratio        Frame ratio (handled in CSS).
 * @return string Image size name.
 */
function acf_product_box_image_size( $is_top_image, $fit = 'contain', $ratio = 'auto' ) {
    return $is_top_image ? 'large' : 'medium_large';
}

/**
 * Build rel/target attributes for a product box link.
 *
 * Adds noopener whenever the link opens in a new tab and drops duplicate tokens.
 *
 * @param string $rel     Space-separated rel tokens entered by the editor.
 * @param bool   $new_tab Whether the link opens in a new tab.
 * @return string Attribute string with a leading space, or ''.
 */
function acf_product_box_link_attrs( $rel, $new_tab ) {
    $tokens = preg_split( '/\s+/', strtolower( trim( (string) $rel ) ), -1, PREG_SPLIT_NO_EMPTY );
    if ( $new_tab ) {
        $tokens[] = 'noopener';
    }
    $tokens = array_unique( $tokens );

    $attrs = '';
    if ( $tokens ) {
        $attrs .= ' rel="' . esc_attr( implode( ' ', $tokens ) ) . '"';
    }
    if ( $new_tab ) {
        $attrs .= ' target="_blank"';
    }
    return $attrs;
}

/**
 * Resolve the best image source for a product box.
 *
 * Priority:
 * 1. Direct external URL → use as-is
 * 2. Direct URL from same domain → find attachment, serve $size if available
 * 3. ACF image array (with ID) → serve $size, fallback to medium
 *
 * @param array|false  $image     ACF image array (or false).
 * @param string       $image_url Direct image URL (or empty).
 * @param string       $alt       Fallback alt text.
 * @param string       $size      WordPress image size to use.
 * @return array{src: string, alt: string, srcset: string, sizes: string, width: int, height: int}
 */
function acf_product_box_resolve_image( $image, $image_url, $alt = 'Product image', $size = 'medium_large' ) {
    $result = [ 'src' => '', 'alt' => $alt, 'srcset' => '', 'sizes' => '', 'width' => 0, 'height' => 0 ];

    // Case 1: Direct URL provided
    if ( $image_url ) {
        $result['src'] = $image_url;

        // Check if it's a same-domain URL — try to get the attachment ID
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
        $url_host  = wp_parse_url( $image_url, PHP_URL_HOST );

        // Match domain or subdomain (e.g. cdn.example.com matches example.com)
        if ( $url_host && $site_host && ( $url_host === $site_host || acf_blocks_str_ends_with( $url_host, '.' . $site_host ) ) ) {
            $attachment_id = acf_blocks_url_to_attachment_id( $image_url );
            if ( $attachment_id ) {
                $sized = wp_get_attachment_image_src( $attachment_id, $size );
                if ( ! $sized ) {
                    $sized = wp_get_attachment_image_src( $attachment_id, 'medium' );
                }
                if ( $sized ) {
                    $result['src']    = $sized[0];
                    $result['width']  = (int) $sized[1];
                    $result['height'] = (int) $sized[2];
                }
                $img_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                if ( $img_alt ) {
                    $result['alt'] = $img_alt;
                }
                $result = acf_blocks_attach_srcset( $result, $attachment_id, $size );
            }
        }
        // External URL: use as-is (no size manipulation)
        return $result;
    }

    // Case 2: ACF image field (array with 'ID') or raw attachment ID from compat layer
    $attachment_id = 0;
    if ( $image && is_array( $image ) && ! empty( $image['ID'] ) ) {
        $attachment_id = (int) $image['ID'];
        $result['alt'] = ! empty( $image['alt'] ) ? $image['alt'] : $alt;
    } elseif ( $image && is_numeric( $image ) ) {
        // Compat layer returns raw attachment ID from $block['data']
        $attachment_id = (int) $image;
        $img_alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
        if ( $img_alt ) {
            $result['alt'] = $img_alt;
        }
    }

    if ( $attachment_id > 0 ) {
        // Try requested size first, then medium, then full URL
        $sized = wp_get_attachment_image_src( $attachment_id, $size );
        if ( ! $sized ) {
            $sized = wp_get_attachment_image_src( $attachment_id, 'medium' );
        }
        if ( $sized ) {
            $result['src']    = $sized[0];
            $result['width']  = (int) $sized[1];
            $result['height'] = (int) $sized[2];
        } elseif ( is_array( $image ) && ! empty( $image['url'] ) ) {
            $result['src'] = $image['url'];
        } else {
            $full = wp_get_attachment_url( $attachment_id );
            if ( $full ) {
                $result['src'] = $full;
            }
        }
        $result = acf_blocks_attach_srcset( $result, $attachment_id, $size );
        return $result;
    }

    return $result;
}

/**
 * Parse a free-text price such as "$1,299.99", "₹1,29,999" or "1.299,00 €".
 *
 * Returns null for anything it can't read with confidence ("Free", "From $99",
 * "$99/mo"), so callers show nothing rather than a wrong number.
 *
 * @param string $price Price as the editor typed it.
 * @return array{amount: float, prefix: string, suffix: string, decimals: int, dec: string, thou: string, indian: bool}|null
 */
function acf_product_box_parse_price( $price ) {
    $price = trim( (string) $price );
    if ( ! preg_match( '/^(\D*?)(\d(?:[\d.,\x{00A0}\x{202F} \']*\d)?)(\D*)$/u', $price, $m ) ) {
        return null;
    }

    // Only a currency symbol or code may surround the number.
    $prefix = $m[1];
    $suffix = $m[3];
    foreach ( [ $prefix, $suffix ] as $affix ) {
        if ( mb_strlen( trim( $affix ) ) > 4 || preg_match( '/[\/%]/', $affix ) ) {
            return null;
        }
    }

    $number = preg_replace( '/[\x{00A0}\x{202F} \']/u', '', $m[2] );
    $dec    = '';
    $thou   = '';
    $has_dot   = false !== strpos( $number, '.' );
    $has_comma = false !== strpos( $number, ',' );

    if ( $has_dot && $has_comma ) {
        $dec  = strrpos( $number, '.' ) > strrpos( $number, ',' ) ? '.' : ',';
        $thou = '.' === $dec ? ',' : '.';
    } elseif ( $has_dot || $has_comma ) {
        $sep   = $has_dot ? '.' : ',';
        $parts = explode( $sep, $number );
        // One separator followed by 1-2 digits is a decimal point; anything
        // else ("1,299", "1.299", "1,29,999") is digit grouping.
        if ( 2 === count( $parts ) && strlen( $parts[1] ) <= 2 ) {
            $dec = $sep;
        } else {
            $thou = $sep;
        }
    }

    $decimals = 0;
    if ( '' !== $dec ) {
        $decimals = strlen( substr( $number, strrpos( $number, $dec ) + 1 ) );
    }

    $normalized = '' !== $thou ? str_replace( $thou, '', $number ) : $number;
    if ( '' !== $dec ) {
        $normalized = str_replace( $dec, '.', $normalized );
    }
    if ( ! is_numeric( $normalized ) ) {
        return null;
    }

    return [
        'amount'   => (float) $normalized,
        'prefix'   => $prefix,
        'suffix'   => $suffix,
        'decimals' => $decimals,
        'dec'      => $dec,
        'thou'     => $thou,
        'indian'   => (bool) preg_match( '/\d,\d\d,\d{3}/', $m[2] ),
    ];
}

/**
 * Work out a "You save" amount from the original and current prices.
 *
 * Both prices must parse and use the same currency, and the current price must
 * be lower. The result copies the current price's symbol and number format.
 *
 * @param string $original Original/list price.
 * @param string $current  Current/sale price.
 * @return string Formatted saving (e.g. "$60.57"), or '' when there's none to show.
 */
function acf_product_box_savings( $original, $current ) {
    $from = acf_product_box_parse_price( $original );
    $to   = acf_product_box_parse_price( $current );
    if ( ! $from || ! $to ) {
        return '';
    }
    if ( trim( $from['prefix'] ) !== trim( $to['prefix'] ) || trim( $from['suffix'] ) !== trim( $to['suffix'] ) ) {
        return '';
    }

    $decimals = max( $from['decimals'], $to['decimals'] );
    $saving   = round( $from['amount'] - $to['amount'], $decimals );
    if ( $saving <= 0 ) {
        return '';
    }

    // Reuse whichever separators the editor typed; infer the missing one.
    $dec  = $to['dec'] ?: $from['dec'];
    $thou = $to['thou'] ?: $from['thou'];
    if ( '' === $dec ) {
        $dec = '.' === $thou ? ',' : '.';
    }
    if ( '' === $thou ) {
        $thou = '.' === $dec ? ',' : '.';
    }

    if ( ( $from['indian'] || $to['indian'] ) && ',' === $thou ) {
        $whole    = (string) (int) floor( $saving );
        $fraction = $decimals ? $dec . substr( number_format( $saving, $decimals, '.', '' ), -$decimals ) : '';
        $last3    = substr( $whole, -3 );
        $rest     = substr( $whole, 0, -3 );
        $grouped  = '' !== $rest ? preg_replace( '/\B(?=(\d{2})+(?!\d))/', ',', $rest ) . ',' . $last3 : $last3;
        $amount   = $grouped . $fraction;
    } else {
        $amount = number_format( $saving, $decimals, $dec, $thou );
    }

    return $to['prefix'] . $amount . $to['suffix'];
}

/**
 * Turn a stored date (ACF's Ymd, or anything strtotime() reads) into display parts.
 *
 * @param string $raw Stored date value.
 * @return array{iso: string, display: string}|null
 */
function acf_product_box_format_date( $raw ) {
    $raw = trim( (string) $raw );
    if ( '' === $raw ) {
        return null;
    }

    if ( preg_match( '/^\d{8}$/', $raw ) ) {
        $date = DateTime::createFromFormat( '!Ymd', $raw );
        $timestamp = $date ? $date->getTimestamp() : false;
    } else {
        $timestamp = strtotime( $raw );
    }
    if ( ! $timestamp ) {
        return null;
    }

    $format = get_option( 'date_format' ) ?: 'j M Y';
    return [
        'iso'     => gmdate( 'Y-m-d', $timestamp ),
        'display' => function_exists( 'date_i18n' ) ? date_i18n( $format, $timestamp ) : gmdate( $format, $timestamp ),
    ];
}

/**
 * Accept only #rgb or #rrggbb, so the value is safe inside a style attribute.
 *
 * @param string $color Color from the picker.
 * @return string Lowercase hex color, or ''.
 */
function acf_product_box_sanitize_hex( $color ) {
    $color = strtolower( trim( (string) $color ) );
    return preg_match( '/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/', $color ) ? $color : '';
}

/**
 * Pick black-ish or white text for a background color, whichever contrasts more.
 *
 * @param string $hex Sanitized #rgb or #rrggbb color.
 * @return string '#111827' or '#ffffff'.
 */
function acf_product_box_contrast_text( $hex ) {
    $hex = ltrim( $hex, '#' );
    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $channels = [];
    foreach ( [ 0, 2, 4 ] as $offset ) {
        $c = hexdec( substr( $hex, $offset, 2 ) ) / 255;
        $channels[] = $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
    }
    $luminance = 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];

    // Contrast against white (L=1) vs. #111827 (L≈0.0137).
    $with_white = 1.05 / ( $luminance + 0.05 );
    $with_dark  = ( $luminance + 0.05 ) / 0.0637;
    return $with_dark > $with_white ? '#111827' : '#ffffff';
}

/**
 * Inline SVG icon for perks and buttons. Uses currentColor, hidden from screen readers.
 *
 * @param string $name  Icon name.
 * @param string $class CSS class for the <svg>.
 * @return string SVG markup, or '' for an unknown name.
 */
function acf_product_box_icon( $name, $class ) {
    $paths = [
        'check'  => '<path d="M20 6 9 17l-5-5"/>',
        'truck'  => '<path d="M3 6h11v10H3z"/><path d="M14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
        'return' => '<path d="M3 12a9 9 0 1 0 2.6-6.4"/><path d="M3 4v5h5"/>',
        'shield' => '<path d="M12 3 4.5 6v6c0 4.6 3.2 7.8 7.5 9 4.3-1.2 7.5-4.4 7.5-9V6z"/><path d="m9 12 2 2 4-4"/>',
        'lock'   => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'tag'    => '<path d="M3 12V3h9l9 9-9 9z"/><circle cx="7.5" cy="7.5" r="1.3"/>',
        'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'gift'   => '<rect x="3" y="8" width="18" height="4" rx="1"/><path d="M5 12v9h14v-9"/><path d="M12 8v13"/><path d="M12 8c-1.5-3-5-4.5-5-2s3 2 5 2c2 0 5 .5 5-2s-3.5-1-5 2"/>',
        'arrow'  => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
    ];
    if ( ! isset( $paths[ $name ] ) ) {
        return '';
    }
    return '<svg class="' . esc_attr( $class ) . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}
