( function( wp ) {
    const { __ } = wp.i18n;
    const { InnerBlocks } = wp.blockEditor;
    const { createElement: el } = wp.element;
    const { addFilter } = wp.hooks;

    // Custom edit component for the acf_section block.
    const ACFSectionEdit = ( props ) => {
        // You can add a wrapper or any other controls if needed.
        return el(
            'div',
            { className: props.className },
            el( InnerBlocks, {} )
        );
    };

    // Filter the BlockEdit component to replace it for our ACF block.
    addFilter(
        'editor.BlockEdit',
        'my-plugin/replace-acf-section-edit',
        ( BlockEdit ) => ( props ) => {
            if ( props.name !== 'acf/acf_section' ) {
                return el( BlockEdit, props );
            }
            return el( ACFSectionEdit, props );
        }
    );
} )( window.wp );