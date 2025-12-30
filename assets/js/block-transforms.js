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
                            // Extract code content
                            const codeContent = stripHtml( attributes.content || '' );

                            return createBlock( 'acf/code-block', {
                                data: {
                                    code: codeContent,
                                    language: '',
                                    filename: ''
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
                                    code: content,
                                    language: '',
                                    filename: ''
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

                            // Build checklist items with all unchecked
                            const checklistItems = listItems.map( function( text, index ) {
                                return {
                                    item_text: text,
                                    item_checked: false
                                };
                            } );

                            return createBlock( 'acf/checklist', {
                                data: {
                                    checklist_title: '',
                                    checklist_items: checklistItems,
                                    show_progress: false,
                                    enable_strikethrough: true
                                }
                            } );
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

                            // Build changelog items with default "added" type
                            const changelogItems = listItems.map( function( text ) {
                                return {
                                    changelog_type: 'added',
                                    changelog_text: text
                                };
                            } );

                            // Create a single changelog entry with default version and today's date
                            const today = new Date();
                            const dateStr = today.toISOString().split( 'T' )[ 0 ];

                            const changelogEntries = [ {
                                changelog_version: '1.0.0',
                                changelog_date: dateStr,
                                changelog_items: changelogItems
                            } ];

                            return createBlock( 'acf/changelog', {
                                data: {
                                    changelog_entries: changelogEntries
                                }
                            } );
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
