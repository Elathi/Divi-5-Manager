<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class D5TM_Layout_Manager {

    /**
     * Fetch Divi layouts
     */
    public function get_layouts( $args = array() ) {
        $default_args = array(
            'post_type'      => 'et_pb_layout',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        $query_args = wp_parse_args( $args, $default_args );
        $query = new WP_Query( $query_args );

        return $query->posts;
    }

    /**
     * Delete a layout
     */
    public function delete_layout( $post_id ) {
        if ( get_post_type( $post_id ) === 'et_pb_layout' ) {
            return wp_delete_post( $post_id, true );
        }
        return false;
    }
}
