
registerBlockType( 'magnior/post-list', {
	title: news_blocks_php_data.post_list,
	icon: 'text',
	category: 'widgets',

	edit: function( props ) {
		
		var return_array = [
			el( ServerSideRender, {
				block: 'magnior/post-list',
				attributes: props.attributes,
			} ),
		];

		return_array.push(global_fields(props));

		return return_array;
	},

	save: function() {
		return null;
	},

});
