<?php
/**
 * Server-side rendering of the Magazine Column Block
 *
 * @package ThemeZee Blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ThemeZee Blocks Magazine Column Class
 */
class ThemeZee_Blocks_Magazine_Column {
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
			'themezee-blocks/magazine-column',
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
						'default' => 3,
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
		// Fetch posts from cache or database.
		$posts_query = ThemeZee_Blocks_Magazine_Cache::query_posts( $attributes );

		// Set up markup variables.
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
		$list_posts = sprintf( '<div class="tz-magazine-thumbnail-list">%s</div>', $list_posts );

		// Set Posts Markup.
		$posts_markup = $highlight_post . $list_posts;

		// Define Block Content.
		$block_content = sprintf( '<div class="tz-magazine-column">%s</div>', $posts_markup );

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
		$classes  = 'wp-block-themezee-blocks-magazine-column';
		$classes .= ' tz-magazine-block';

		if ( isset( $attributes['className'] ) ) {
			$classes .= ' ' . $attributes['className'];
		}

		return $classes;
	}
}

// Run Class.
ThemeZee_Blocks_Magazine_Column::setup();
