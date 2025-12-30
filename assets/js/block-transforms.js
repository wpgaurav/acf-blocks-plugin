/**
 * ACF Blocks - Block Transforms
 *
 * Enables converting core WordPress blocks to ACF Blocks.
 * - core/code → acf/code-block
 * - core/list → acf/checklist OR acf/changelog
 */

( function( wp ) {
    const { addFilter } = wp.hooks;
    const { createBlock } = wp.blocks;

    /**
     * Extract text content from HTML string
     */
    function stripHtml( html ) {
        const tmp = document.createElement( 'div' );
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }

    /**
     * Extract list items from core/list block
     */
    function extractListItems( block ) {
        const items = [];

        // core/list uses innerBlocks with core/list-item blocks
        if ( block.innerBlocks && block.innerBlocks.length > 0 ) {
            block.innerBlocks.forEach( function( listItem ) {
                if ( listItem.name === 'core/list-item' ) {
                    const text = stripHtml( listItem.attributes.content || '' );
                    if ( text.trim() ) {
                        items.push( text.trim() );
                    }
                }
            } );
        }

        // Fallback: try values attribute (older format)
        if ( items.length === 0 && block.attributes.values ) {
            const tmp = document.createElement( 'div' );
            tmp.innerHTML = block.attributes.values;
            const lis = tmp.querySelectorAll( 'li' );
            lis.forEach( function( li ) {
                const text = li.textContent.trim();
                if ( text ) {
                    items.push( text );
                }
            } );
        }

        return items;
    }

    /**
     * Build ACF repeater data in the flat format ACF expects
     * ACF stores repeaters as: field_0_subfield, field_1_subfield, etc.
     */
    function buildRepeaterData( fieldName, items, subFieldMap ) {
        const data = {};
        data[ fieldName ] = items.length;

        items.forEach( function( item, index ) {
            Object.keys( subFieldMap ).forEach( function( subFieldName ) {
                const value = typeof subFieldMap[ subFieldName ] === 'function'
                    ? subFieldMap[ subFieldName ]( item, index )
                    : subFieldMap[ subFieldName ];
                data[ fieldName + '_' + index + '_' + subFieldName ] = value;
            } );
        } );

        return data;
    }

    /**
     * Add transforms to ACF Code Block
     */
    function addCodeBlockTransforms( settings, name ) {
        if ( name !== 'acf/code-block' ) {
            return settings;
        }

        return Object.assign( {}, settings, {
            transforms: Object.assign( {}, settings.transforms, {
                from: ( settings.transforms && settings.transforms.from || [] ).concat( [
                    {
                        type: 'block',
                        blocks: [ 'core/code' ],
                        transform: function( attributes ) {
                            // Extract code content - core/code stores as HTML
                            const codeContent = stripHtml( attributes.content || '' );

                            return createBlock( 'acf/code-block', {
                                data: {
                                    code_content: codeContent,
                                    code_language: 'plaintext',
                                    code_filename: '',
                                    code_theme: 'dark',
                                    font_size: 'normal'
                                }
                            } );
                        }
                    },
                    {
                        type: 'block',
                        blocks: [ 'core/preformatted' ],
                        transform: function( attributes ) {
                            const content = stripHtml( attributes.content || '' );

                            return createBlock( 'acf/code-block', {
                                data: {
                                    code_content: content,
                                    code_language: 'plaintext',
                                    code_filename: '',
                                    code_theme: 'dark',
                                    font_size: 'normal'
                                }
                            } );
                        }
                    }
                ] )
            } )
        } );
    }

    /**
     * Add transforms to ACF Checklist Block
     */
    function addChecklistBlockTransforms( settings, name ) {
        if ( name !== 'acf/checklist' ) {
            return settings;
        }

        return Object.assign( {}, settings, {
            transforms: Object.assign( {}, settings.transforms, {
                from: ( settings.transforms && settings.transforms.from || [] ).concat( [
                    {
                        type: 'block',
                        blocks: [ 'core/list' ],
                        transform: function( attributes, innerBlocks ) {
                            // Create a mock block object to extract items
                            const mockBlock = {
                                attributes: attributes,
                                innerBlocks: innerBlocks || []
                            };

                            const listItems = extractListItems( mockBlock );

                            // Build ACF repeater data format
                            const repeaterData = buildRepeaterData(
                                'checklist_items',
                                listItems,
                                {
                                    checklist_item_text: function( text ) { return text; },
                                    checklist_item_checked: 0
                                }
                            );

                            // Merge with other field defaults
                            const data = Object.assign( {
                                checklist_title: '',
                                checklist_interactive: 0,
                                checklist_show_progress: 0,
                                checklist_strikethrough: 1,
                                checklist_accent_color: '#16a34a',
                                checklist_bg_color: '#f9fafb'
                            }, repeaterData );

                            return createBlock( 'acf/checklist', { data: data } );
                        }
                    }
                ] )
            } )
        } );
    }

    /**
     * Add transforms to ACF Changelog Block
     */
    function addChangelogBlockTransforms( settings, name ) {
        if ( name !== 'acf/changelog' ) {
            return settings;
        }

        return Object.assign( {}, settings, {
            transforms: Object.assign( {}, settings.transforms, {
                from: ( settings.transforms && settings.transforms.from || [] ).concat( [
                    {
                        type: 'block',
                        blocks: [ 'core/list' ],
                        transform: function( attributes, innerBlocks ) {
                            // Create a mock block object to extract items
                            const mockBlock = {
                                attributes: attributes,
                                innerBlocks: innerBlocks || []
                            };

                            const listItems = extractListItems( mockBlock );

                            // Get today's date
                            const today = new Date();
                            const dateStr = today.toLocaleDateString( 'en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            } );

                            // Build nested repeater data for changelog
                            // Structure: changelog_entries[0] has changelog_items[0..n]
                            const data = {
                                changelog_entries: 1,
                                changelog_entries_0_changelog_version: '1.0.0',
                                changelog_entries_0_changelog_date: dateStr,
                                changelog_entries_0_changelog_items: listItems.length
                            };

                            // Add each changelog item
                            listItems.forEach( function( text, index ) {
                                data[ 'changelog_entries_0_changelog_items_' + index + '_changelog_type' ] = 'added';
                                data[ 'changelog_entries_0_changelog_items_' + index + '_changelog_text' ] = text;
                            } );

                            return createBlock( 'acf/changelog', { data: data } );
                        }
                    }
                ] )
            } )
        } );
    }

    // Register the transform filters
    addFilter(
        'blocks.registerBlockType',
        'acf-blocks/code-block-transforms',
        addCodeBlockTransforms
    );

    addFilter(
        'blocks.registerBlockType',
        'acf-blocks/checklist-block-transforms',
        addChecklistBlockTransforms
    );

    addFilter(
        'blocks.registerBlockType',
        'acf-blocks/changelog-block-transforms',
        addChangelogBlockTransforms
    );

} )( window.wp );
