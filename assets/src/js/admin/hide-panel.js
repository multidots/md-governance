(function (wp, mdGovernanceSettings) {

    // Ensure the code runs only if the block editor data is available
    if (typeof mdGovernanceSettings !== 'undefined' && mdGovernanceSettings.isBlockEditor) {

        const { createHigherOrderComponent } = wp.compose;
        const { useEffect, Fragment, Children } = wp.element;
        const { addFilter } = wp.hooks;
        const { useSelect, dispatch } = wp.data;
        const { InspectorControls } = wp.blockEditor;
        const restrictedBlocks = mdGovernanceSettings?.restrictedBlocks || [];

        // HOC to handle restricted access
        const withRestrictedAccessMessage = createHigherOrderComponent((BlockEdit) => {
            return (props) => {
                const { name, clientId } = props;

                const isRestricted = restrictedBlocks.includes(name);

                const isSelected = useSelect((select) => select('core/block-editor').isBlockSelected(clientId));

                // Deselect block if it is restricted
                useEffect(() => {
                    if (isSelected && isRestricted) {
                        dispatch('core/block-editor').clearSelectedBlock();
                    }
                }, [isSelected, isRestricted]);

                // Render the block normally if not restricted
                return <BlockEdit {...props} />;
            };
        }, 'withRestrictedAccessMessage');

        // Add the HOC and control filter for BlockEdit
        addFilter(
            'editor.BlockEdit',
            'md-governance/with-restricted-access-message',
            withRestrictedAccessMessage
        );

        // HOC to handle restricted access and class addition
        const withRestrictedBlockClass = createHigherOrderComponent((BlockListBlock) => {
            return (props) => {
                const { name } = props;

                // Update restricted blocks list
                const restrictedBlocks = mdGovernanceSettings?.restrictedBlocks || [];
                const isRestricted = restrictedBlocks.includes(name);

                const blockProps = {
                    ...props,
                    className: isRestricted ? `md_restricted_block` : props.className,
                };


                return <BlockListBlock {...blockProps} />;
            };
        }, 'withRestrictedBlockClass');

        // Add the HOC and control filter for BlockListBlock
        addFilter(
            'editor.BlockListBlock',
            'md-governance/with-restricted-block-class',
            withRestrictedBlockClass
        );

    }


})(window.wp, window.mdGovernanceSettings);

