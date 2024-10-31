<?php
/*
Plugin Name: News Blocks
Description: Post list block for new (Gutenberg) editor.
Author: magnior
Version: 1.0.0
Author URI: https://magnior.com/
License: GPLv2 or later
Text Domain: news-blocks
*/

function news_block_init() {
	
	$global_attributes = array(
		'author_include' => array(
			'type' => 'array',
			'default' => [],
			'items'   => [
				'type' => 'string',
			],
		),
		'author_exclude' => array(
			'type' => 'bool',
		),
		'posts_per_page' => array(
			'type' => 'string',
		),
		'offset' => array(
			'type' => 'string',
		),
		'post_type' => array(
			'type' => 'string',
		),
		'taxonomy' => array(
			'type' => 'string',
		),
		'term' => array(
			'type' => 'array',
			'default' => [],
			'items'   => [
				'type' => 'string',
			],
		),
		'term_exclude' => array(
			'type' => 'bool',
		),
		'className' => array(
			'type' => 'string',
		),
	);

	include( plugin_dir_path( __FILE__ ) . 'blocks/post-list/post-list.php');

}
add_action( 'init', 'news_block_init' );


function news_block_global_scripts() {
	wp_enqueue_script(
		'news-blocks',
		plugins_url( 'news-blocks.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' )
	);

	$news_blocks_php_data = array(
		'post_type_taxonomy' => __( 'Post Type & Taxonomy', 'news-blocks' ),
		'post_type' => __( 'Post Type', 'news-blocks' ),
		'taxonomy' => __( 'Taxonomy', 'news-blocks' ),
		'taxonomy_equals' => __( 'Taxonomy equals', 'news-blocks' ),
		'exclude_selected_terms' => __( 'Exclude Selected Terms', 'news-blocks' ),
		'tax_exclude_info' => __( 'If checked, posts with terms selected above will not be listed.', 'news-blocks' ),
		'range_control' => __( 'Range Control', 'news-blocks' ),
		'number_of_posts' => __( 'Number of posts', 'news-blocks' ),
		'offset' => __( 'Offset', 'news-blocks' ),
		'offset_help' => __( 'How many posts should be skipped from the begining', 'news-blocks' ),
		'filter_by_author' => __( 'Filter by Author', 'news-blocks' ),
		'include_authors' => __( 'Include Authors', 'news-blocks' ),
		'multiple_info' => __( 'Hold Ctrl to select multiple', 'news-blocks' ),
		'exclude_authors' => __( 'Exclude Selected Authors', 'news-blocks' ),
		'exclude_authors_help' => __( 'If checked, posts from authors selected on list above will not be listed.', 'news-blocks' ),
		'post_list' => __( 'Post List', 'news-blocks' ),
	);

	//add authors to localize script
	foreach ( get_users( array( 'orderby' => 'display_name' ) ) as $author ) {
		$news_blocks_php_data['authors_array'][$author->display_name] = $author->ID ;
	}

	//add post types to localize script
	foreach ( get_post_types( array('public' => true), 'names' ) as $post_type ) {
		if($post_type!='attachment'){
			$news_blocks_php_data['post_types'][] = $post_type;
	   }
	}

	$taxonomies_array = get_taxonomies(array('public' => true), 'objects');
	foreach ($taxonomies_array as $key => $value) {
		// $news_blocks_php_data['taxonomies_array'][$key]['slug'] =  $key;
		$news_blocks_php_data['taxonomies_array'][$key]['name'] =  $value->labels->singular_name;
		$news_blocks_php_data['taxonomies_array'][$key]['post_types'] =  $value->object_type;
		$terms = get_terms( array(
		    'taxonomy' => $key,
		    'hide_empty' => false,
		) );
		$terms_out = array();
		foreach ($terms as $term) {
			$terms_out[$term->slug] = $term->name;
		}
		$news_blocks_php_data['taxonomies_array'][$key]['terms'] = $terms_out;
	}

	wp_localize_script( 'news-blocks', 'news_blocks_php_data', $news_blocks_php_data );

}
add_action( 'admin_enqueue_scripts', 'news_block_global_scripts' );



function news_block_query_args( $attributes ){

	$args = array(
		'post_type' => 'post',
		'posts_per_page' => '10',
	);

	if(isset($attributes['post_type']) && $attributes['post_type']!=''){
		$args['post_type'] = $attributes['post_type'];
	}

	if(!empty($attributes['author_include']) && !empty($attributes['author_include'][0]) ){
		$author_include = $attributes['author_include'];
		if( isset($attributes['author_exclude'])  && $attributes['author_exclude'] == 'true' ){
			$args['author__not_in'] = $author_include;
		}
		else{
			$args['author__in'] = $author_include;
		}
	}

	if(isset($attributes['posts_per_page']) && $attributes['posts_per_page']!=''){
		$args['posts_per_page'] = $attributes['posts_per_page'];
	}

	if(isset($attributes['offset']) && $attributes['offset']!=''){
		$args['offset'] = $attributes['offset'];
	}

	if(isset($attributes['taxonomy']) && $attributes['taxonomy']!='' && !empty($attributes['term']) && !empty($attributes['term'][0])){
		$args['tax_query'] = array(
			array(
				'taxonomy' => $attributes['taxonomy'],
				'field'    => 'slug',
				'terms'    => $attributes['term'],
			),
		);
		if( isset($attributes['term_exclude'])  && $attributes['term_exclude'] == 'true' ){
			$args['tax_query'][0]['operator'] = 'NOT IN';
		}
	}

	return $args;
}