<?php
/**
 * Thread Builder Block Template.
 *
 * @param array  $block      The block settings and attributes.
 * @param string $content    The block inner HTML (empty).
 * @param bool   $is_preview True during AJAX preview.
 */

// Load block settings
$theme = get_field('thread_theme') ?: 'light';
$width = get_field('thread_width') ?: 'medium';
$show_connector = get_field('thread_show_connector');
$connector_color = get_field('thread_connector_color') ?: '#1DA1F2';
$show_engagement = get_field('thread_show_engagement');
$thread_posts = get_field('thread_posts');

// Width class map
$width_class_map = [
    'narrow' => 'thread-narrow',
    'medium' => 'thread-medium',
    'wide' => 'thread-wide',
    'full' => 'thread-full'
];

// Set classes
$classes = [
    'thread-builder',
    'theme-' . $theme,
    $width_class_map[$width] ?? 'thread-medium'
];

// Block ID
$block_id = 'thread-builder-' . $block['id'];
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>">
    <?php if (!empty($thread_posts) && is_array($thread_posts)) : ?>
        <div class="thread-container">
            <?php foreach ($thread_posts as $index => $post) : 
                $author_name = $post['author_name'] ?? '';
                $author_handle = $post['author_handle'] ?? '';
                $author_avatar = $post['author_avatar'] ?? '';
                $verified = $post['verified'] ?? false;
                $content = $post['content'] ?? '';
                $media = $post['media'] ?? '';
                $timestamp = $post['timestamp'] ?? '';
                $replies = $post['replies'] ?? 0;
                $reposts = $post['reposts'] ?? 0;
                $likes = $post['likes'] ?? 0;
                
                // Determine if this is the last post
                $is_last_post = $index === count($thread_posts) - 1;
            ?>
                <div class="thread-post<?php echo $is_last_post ? ' last-post' : ''; ?>">
                    <?php if ($show_connector && !$is_last_post) : ?>
                        <div class="thread-connector" style="background-color: <?php echo esc_attr($connector_color); ?>"></div>
                    <?php endif; ?>
                    
                    <div class="post-avatar">
                        <?php if ($author_avatar) : ?>
                            <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" />
                        <?php else : ?>
                            <div class="default-avatar"></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-content">
                        <div class="post-header">
                            <?php if ($author_name) : ?>
                                <span class="author-name"><?php echo esc_html($author_name); ?></span>
                            <?php endif; ?>
                            
                            <?php if ($verified) : ?>
                                <span class="verified-badge">
                                    <svg viewBox="0 0 24 24" aria-label="Verified account" role="img">
                                        <g fill="currentColor">
                                            <path d="M22.5 12.5c0-1.58-.875-2.95-2.148-3.6.154-.435.238-.905.238-1.4 0-2.21-1.71-3.998-3.818-3.998-.47 0-.92.084-1.336.25C14.818 2.415 13.51 1.5 12 1.5s-2.816.917-3.437 2.25c-.415-.165-.866-.25-1.336-.25-2.11 0-3.818 1.79-3.818 4 0 .494.083.964.237 1.4-1.272.65-2.147 2.018-2.147 3.6 0 1.495.782 2.798 1.942 3.486-.02.17-.032.34-.032.514 0 2.21 1.708 4 3.818 4 .47 0 .92-.086 1.335-.25.62 1.334 1.926 2.25 3.437 2.25 1.512 0 2.818-.916 3.437-2.25.415.163.865.248 1.336.248 2.11 0 3.818-1.79 3.818-4 0-.174-.012-.344-.033-.513 1.158-.687 1.943-1.99 1.943-3.484zm-6.616-3.334l-4.334 6.5c-.145.217-.382.334-.625.334-.143 0-.288-.04-.416-.126l-.115-.094-2.415-2.415c-.293-.293-.293-.768 0-1.06s.768-.294 1.06 0l1.77 1.767 3.825-5.74c.23-.345.696-.436 1.04-.207.346.23.44.696.21 1.04z"></path>
                                        </g>
                                    </svg>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($author_handle) : ?>
                                <span class="author-handle"><?php echo esc_html($author_handle); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($content) : ?>
                            <div class="post-text">
                                <?php 
                                // Process content for @ mentions, hashtags, and URLs
                                $processed_content = preg_replace(
                                    [
                                        '/\B@([a-zA-Z0-9_]+)/', // Mentions
                                        '/\B#([a-zA-Z0-9_]+)/', // Hashtags
                                        '/\bhttps?:\/\/[^\s<>"]+/' // URLs
                                    ],
                                    [
                                        '<a href="#" class="mention">@$1</a>',
                                        '<a href="#" class="hashtag">#$1</a>',
                                        '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>'
                                    ],
                                    esc_html($content)
                                );
                                
                                echo wpautop($processed_content); 
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($media) : ?>
                            <div class="post-media">
                                <img src="<?php echo esc_url($media); ?>" alt="" />
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($timestamp) : ?>
                            <div class="post-time"><?php echo esc_html($timestamp); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($show_engagement) : ?>
                            <div class="post-engagement">
                                <div class="engagement-item replies">
                                    <svg viewBox="0 0 24 24"><path d="M14.046 2.242l-4.148-.01h-.002c-4.374 0-7.8 3.427-7.8 7.802 0 4.098 3.186 7.206 7.465 7.37v3.828c0 .108.044.286.12.403.142.225.384.347.632.347.138 0 .277-.038.402-.118.264-.168 6.473-4.14 8.088-5.506 1.902-1.61 3.04-3.97 3.043-6.312v-.017c-.006-4.367-3.43-7.787-7.8-7.788zm3.787 12.972c-1.134.96-4.862 3.405-6.772 4.643V16.67c0-.414-.335-.75-.75-.75h-.396c-3.66 0-6.318-2.476-6.318-5.886 0-3.534 2.768-6.302 6.3-6.302l4.147.01h.002c3.532 0 6.3 2.766 6.302 6.296-.003 1.91-.942 3.844-2.514 5.176z"></path></svg>
                                    <span><?php echo esc_html($replies); ?></span>
                                </div>
                                <div class="engagement-item reposts">
                                    <svg viewBox="0 0 24 24"><path d="M23.77 15.67a.749.749 0 0 0-1.06 0l-2.22 2.22V7.65a3.755 3.755 0 0 0-3.75-3.75h-5.85a.75.75 0 0 0 0 1.5h5.85c1.24 0 2.25 1.01 2.25 2.25v10.24l-2.22-2.22a.749.749 0 1 0-1.06 1.06l3.5 3.5c.145.146.338.22.53.22s.385-.073.53-.22l3.5-3.5a.747.747 0 0 0 0-1.06z"></path><path d="M13.25 18.25h-5.85c-1.24 0-2.25-1.01-2.25-2.25V5.76l2.22 2.22a.749.749 0 1 0 1.06-1.06l-3.5-3.5a.747.747 0 0 0-1.06 0l-3.5 3.5a.749.749 0 1 0 1.06 1.06l2.22-2.22v10.24c0 2.07 1.68 3.75 3.75 3.75h5.85a.75.75 0 0 0 0-1.5z"></path></svg>
                                    <span><?php echo esc_html($reposts); ?></span>
                                </div>
                                <div class="engagement-item likes">
                                    <svg viewBox="0 0 24 24"><path d="M12 21.638h-.014C9.403 21.59 1.95 14.856 1.95 8.478c0-3.064 2.525-5.754 5.403-5.754 2.29 0 3.83 1.58 4.646 2.73.814-1.148 2.354-2.73 4.645-2.73 2.88 0 5.404 2.69 5.404 5.755 0 6.376-7.454 13.11-10.037 13.157H12z"></path></svg>
                                    <span><?php echo esc_html($likes); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="thread-placeholder">Add posts to create your thread.</p>
    <?php endif; ?>
</div>
