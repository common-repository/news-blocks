var el = wp.element.createElement;
var registerBlockType = wp.blocks.registerBlockType;
var ServerSideRender = wp.components.ServerSideRender;
var TextControl = wp.components.TextControl;
var CheckboxControl = wp.components.CheckboxControl;
var SelectControl = wp.components.SelectControl;
var ColorPalette = wp.components.ColorPalette;
var RichText = wp.components.RichText;
var InspectorControls = wp.editor.InspectorControls;
var PanelBody = wp.components.PanelBody;


var authors_object = [{ value: '', label: ' -- ' }];
for(var key in news_blocks_php_data.authors_array){
	authors_object.push({ value: news_blocks_php_data.authors_array[key], label: key });
}

var post_type_object = [];
for(var key in news_blocks_php_data.post_types){
	post_type_object.push({ value: news_blocks_php_data.post_types[key], label: news_blocks_php_data.post_types[key] });
}

function get_post_type_taxonomies( post_type = 'post' ){
	var taxonomy_object = [{label: ' -- ', value: ''}];
	for(var key in news_blocks_php_data.taxonomies_array){
		if( news_blocks_php_data.taxonomies_array[key].post_types.indexOf(post_type) > -1 ){
			taxonomy_object.push({ value: key, label: news_blocks_php_data.taxonomies_array[key].name });
		}
	}
	return taxonomy_object;
}

function get_taxonomy_terms( taxonomy = 'category', post_type = 'post' ){
	var term_object = [{label: ' -- ', value: ''}];
	if(typeof(news_blocks_php_data.taxonomies_array[taxonomy])!='undefined'){
		for(var key in news_blocks_php_data.taxonomies_array[taxonomy].terms){
			term_object.push({ value: key, label: news_blocks_php_data.taxonomies_array[taxonomy].terms[key] });
		}
	}
	return term_object;
}


function global_fields( props ) {
	return [

		el( InspectorControls, {},

			
			el(PanelBody, {
	            	title: news_blocks_php_data.post_type_taxonomy,
	            	initialOpen: true
	            },
            	// el('p', {}, 'instructions-p1'),
				el( SelectControl, {
					label: news_blocks_php_data.post_type,
					value: props.attributes.post_type,
					options: post_type_object,
					onChange: ( value ) => { 
						props.setAttributes( { post_type: value, taxonomy: '', term: '' } );
					},
				} ),
				el( SelectControl, {
					id:'taxonomy_select',
					label: news_blocks_php_data.taxonomy,
					value: props.attributes.taxonomy,
					options: get_post_type_taxonomies( props.attributes.post_type ),
					onChange: ( value ) => { props.setAttributes( { taxonomy: value, term: '' } ); },
				} ),
				el( SelectControl, {
					id:'term_select',
					label: news_blocks_php_data.taxonomy_equals,
					multiple: true,
					value: props.attributes.term,
					options: get_taxonomy_terms( props.attributes.taxonomy, props.attributes.post_type ),
					onChange: ( value ) => { props.setAttributes( { term: value } ); },
				} ),
				el( CheckboxControl, {
					label: news_blocks_php_data.exclude_selected_terms,
					help: news_blocks_php_data.tax_exclude_info,
					checked: props.attributes.term_exclude,
					onChange: ( value ) => { props.setAttributes( { term_exclude: value } ); },
				} ),
			),

			el(PanelBody, {
	            	title: news_blocks_php_data.range_control,
	            	initialOpen: false
	            },
				el( TextControl, {
					label: news_blocks_php_data.number_of_posts,
					value: props.attributes.posts_per_page,
					onChange: ( value ) => { props.setAttributes( { posts_per_page: value } ); },
				} ),
				el( TextControl, {
					label: news_blocks_php_data.offset,
					help: news_blocks_php_data.offset_help,
					value: props.attributes.offset,
					onChange: ( value ) => { props.setAttributes( { offset: value } ); },
				} ),
			),

		    el(PanelBody, {
	            	title: news_blocks_php_data.filter_by_author,
	            	initialOpen: false
	            },
				el( SelectControl, {
					label: news_blocks_php_data.include_authors,
					help: news_blocks_php_data.multiple_info,
					value: props.attributes.author_include,
					multiple: true,
					options: authors_object,
					onChange: ( value ) => { props.setAttributes( { author_include: value } ); },
				} ),
				el( CheckboxControl, {
					label: news_blocks_php_data.exclude_authors,
					help: news_blocks_php_data.exclude_authors_help,
					checked: props.attributes.author_exclude,
					onChange: ( value ) => { props.setAttributes( { author_exclude: value } ); },
				} ),
	        )

		)

	]
}