( function ( wp ) {
	var el                          = wp.element.createElement;
	var registerPlugin              = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel  = wp.editor.PluginDocumentSettingPanel
	                                  || ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );
	var ToggleControl               = wp.components.ToggleControl;
	var useSelect                   = wp.data.useSelect;
	var useDispatch                 = wp.data.useDispatch;

	if ( ! PluginDocumentSettingPanel ) {
		return;
	}

	var PredictiveSearchPanel = function () {
		var meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );

		var editPost = useDispatch( 'core/editor' ).editPost;

		var isExcluded = meta._ps_exclude_item === '1';

		return el(
			PluginDocumentSettingPanel,
			{
				name:  'wc-ps-predictive-search-panel',
				title: wp.i18n.__( 'Predictive Search', 'woocommerce-predictive-search' ),
				icon:  'search',
			},
			el(
				ToggleControl,
				{
					__nextHasNoMarginBottom: true,
					label:    wp.i18n.__( 'Hide from Predictive Search results', 'woocommerce-predictive-search' ),
					checked:  isExcluded,
					onChange: function ( value ) {
						editPost( {
							meta: { _ps_exclude_item: value ? '1' : '' },
						} );
					},
				}
			)
		);
	};

	registerPlugin( 'wc-ps-predictive-search', {
		render: PredictiveSearchPanel,
		icon:   'search',
	} );

} )( window.wp );
