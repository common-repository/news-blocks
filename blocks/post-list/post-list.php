<?php

wp_register_script(
	'post-list',
	plugins_url( 'post-list.js', __FILE__ ),
	array( 'news-blocks' )
);

$attributes = $global_attributes;

register_block_type( 'magnior/post-list', array(
	'attributes'      => $attributes,
	'editor_script'   => 'post-list',
	'render_callback' => 'news_block_render',
) );

add_shortcode( 'news_block', 'news_block_render' );


function news_block_render( $attributes ) {
	$return = '';

	$args = news_block_query_args($attributes);
	
	$posts = new WP_Query( apply_filters( 'news_block_query_args', $args, $attributes ) );

	if ( $posts->have_posts() ){
		$return .= '<ul class="news_block_post_list '.(isset($attributes['className']) ? $attributes['className'] : '').'">';
		while ( $posts->have_posts() ){
			$posts->the_post();	
			$return .= '<li><a href="'.get_the_permalink().'">' . get_the_title() . '</a></li>';
		} 
		$return .= '</ul>';
	}
	else{
		$return = '<p>'.__('No posts found for selected filters.', 'news-blocks').'</p>';
	}

	// $return = '<p><hr>' . print_r( $attributes, true ) . '<hr>' . print_r( $args, true ) . '<hr></p>';

	return $return;
}
