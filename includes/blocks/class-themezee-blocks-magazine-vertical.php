<?php
/**
 * Server-side rendering of the Magazine Vertical Block
 *
 * @package ThemeZee Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeZee Blocks Magazine Vertical Class
 */
class ThemeZee_Blocks_Magazine_Vertical {
	/**
	 * Setup the class
	 *
	 * @return void
	 */
	static function setup() {

		// Register Block.
		add_action( 'init', array( __CLASS__, 'register_block' ) );
	}

	/**
	 * Register block on server.
	 *
	 * @return void
	 */
	static function register_block() {
		register_block_type(
			'themezee-blocks/magazine-vertical',
			array(
				'attributes' => array(
					'className' => array(
						'type' => 'string',
					),
					'categories' => array(
						'type' => 'string',
					),
					'tags' => array(
						'type' => 'string',
					),
					'author' => array(
						'type' => 'string',
					),
					'numberOfPosts' => array(
						'type'    => 'number',
						'default' => 5,
					),
					'offset' => array(
						'type'    => 'number',
						'default' => 0,
					),
					'order' => array(
						'type'    => 'string',
						'default' => 'desc',
					),
					'orderBy' => array(
						'type'    => 'string',
						'default' => 'date',
					),
					'imageSize' => array(
						'type'    => 'string',
						'default' => 'full',
					),
					'thumbnailSize' => array(
						'type'    => 'string',
						'default' => 'full',
					),
					'metaPosition' => array(
						'type'    => 'string',
						'default' => 'below-title',
					),
					'showDate' => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showAuthor' => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'showCategories' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'showComments' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'excerptLength' => array(
						'type'    => 'number',
						'default' => 25,
					),
					'moreText' => array(
						'type'    => 'string',
						'default' => esc_html__( 'Continue Reading', 'themezee-blocks' ),
					),
				),
				'render_callback' => array( __CLASS__, 'render_block' ),
			)
		);
	}

	/**
	 * Render block.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string Returns the block content.
	 */
	static function render_block( $attributes ) {
		$query_arguments = array(
			'posts_per_page'      => intval( $attributes['numberOfPosts'] ),
			'post_status'         => 'publish',
			'order'               => esc_attr( $attributes['order'] ),
			'orderby'             => esc_attr( $attributes['orderBy'] ),
			'suppress_filters'    => false,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( isset( $attributes['categories'] ) ) {
			$query_arguments['cat'] = esc_attr( $attributes['categories'] );
		}

		if ( isset( $attributes['tags'] ) ) {
			$query_arguments['tag'] = esc_attr( $attributes['tags'] );
		}

		if ( isset( $attributes['author'] ) ) {
			$query_arguments['author'] = esc_attr( $attributes['author'] );
		}

		if ( isset( $attributes['offset'] ) && $attributes['offset'] > 0 ) {
			$query_arguments['offset'] = intval( $attributes['offset'] );
		}

		// Fetch posts from database.
		$posts_query = new WP_Query( $query_arguments );

		$highlight_post = '';
		$list_posts     = '';

		// Check if there are posts.
		if ( $posts_query->have_posts() ) :

			// Get Posts Markup.
			while ( $posts_query->have_posts() ) :
				$posts_query->the_post();

				// Display first post differently.
				if ( 0 === $posts_query->current_post ) :

					$highlight_post .= ThemeZee_Blocks_Magazine_Template::get_grid_post( $attributes, $attributes['imageSize'] );

				else :

					$list_posts .= ThemeZee_Blocks_Magazine_Template::get_list_post( $attributes, $attributes['thumbnailSize'], false );

				endif;

			endwhile;

		endif;

		// Reset Postdata.
		wp_reset_postdata();

		// Wrap Highlight Posts.
		$highlight_post = sprintf( '<div class="tz-magazine-highlight-post">%s</div>', $highlight_post );

		// Wrap Thumbnail Posts.
		$list_posts = sprintf( '<div class="tz-magazine-list tz-magazine-thumbnail-list">%s</div>', $list_posts );

		// Set Posts Markup.
		$posts_markup = $highlight_post . $list_posts;

		// Define Block Content.
		$block_content = sprintf( '<div class="tz-magazine-vertical">%s</div>', $posts_markup );

		// Get Block Classes.
		$block_classes = self::get_block_classes( $attributes );

		// Wrap Block Content.
		$block = sprintf(
			'<div class="%1$s">%2$s</div>',
			esc_attr( $block_classes ),
			$block_content
		);

		return $block;
	}

	/**
	 * Get Block Classes.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string Returns the block classes.
	 */
	static function get_block_classes( $attributes ) {
		$classes  = 'wp-block-themezee-blocks-magazine-vertical';
		$classes .= ' tz-magazine-block';

		if ( isset( $attributes['className'] ) ) {
			$classes .= ' ' . $attributes['className'];
		}

		return $classes;
	}
}

// Run Class.
ThemeZee_Blocks_Magazine_Vertical::setup();
