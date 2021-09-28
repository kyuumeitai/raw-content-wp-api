<?php
    /*
    Plugin Name: Raw content in WP REST API
    Plugin URI: http://alex.acunaviera.com/
    Version: 0.2
    Author: Álex Acuña Viera
    Description: exposes raw content
    Text Domain: derpyderp
    License: GPLv3
    */

        add_action( 'rest_api_init', 'create_api_posts_raw_content' );

        function create_api_posts_raw_content() {

                register_rest_field( 'post', 'raw', array(
                        'get_callback'    => 'get_post_content_row',
                        'schema'          => null,
                        )
                );
                register_rest_field( 'page', 'raw', array(
                        'get_callback'    => 'get_post_content_row',
                        'schema'          => null,
                        )
                );

        };

        function get_post_content_row( $object ) {
                $post_content = $object['content']['raw'];
                return $post_content;
        };

add_action(
        'rest_api_init',
        function () {

                if ( ! function_exists( 'use_block_editor_for_post_type' ) ) {
                        require ABSPATH . 'wp-admin/includes/post.php';
                }

                // Surface all Gutenberg blocks in the WordPress REST API
                $post_types = get_post_types_by_support( [ 'editor' ] );
                foreach ( $post_types as $post_type ) {
                        if ( use_block_editor_for_post_type( $post_type ) ) {
                                register_rest_field(
                                        $post_type,
                                        'blocks',
                                        [
                                                'get_callback' => 'get_post_content_raw'
                                        ]
                                );
                        }
                }
        }
);

function get_post_content_raw( $post ) {
                $blocks = parse_blocks($post['content']['raw']);
                $parsedblocks = [];
                foreach($blocks as $block){

                        if(!empty($block['attrs']['data'])){
                                $block['parsed'] = true;
                                $block['attrs']['parsed'] = [];
                                foreach($block['attrs']['data'] as $key => $data) {
                                        if($key == 'subtitulo') {
                                                $block['attrs']['data'][$key] = wp_get_attachment_url($data);
                                        }
                                        if($key == 'imagen_previsualizacion') {
                                                $block['attrs']['data'][$key] = wp_get_attachment_url($data);
                                        }
                                }
                        }
                        $parsedblocks[] = $block;
                }
                return $parsedblocks;
        };
