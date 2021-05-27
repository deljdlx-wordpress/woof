<?php

namespace Woof\Traits;

trait WordpressBinded
{
    public function getGlobals()
    {
        global
            $posts,
            $post,
            $wp_did_header,
            $wp_query,
            $wp_rewrite,
            $wpdb,
            $wp_version,
            $wp,
            $id,
            $comment,
            $user_ID
        ;

        return [
            'posts' => $posts,
            'post' => $post,
            'wp_did_header' => $wp_did_header,
            'wp_query' => $wp_query,
            'wp_rewrite' => $wp_rewrite,
            'wpdb' => $wpdb,
            'wp_version' => $wp_version,
            'wp' => $wp,
            'id' => $id,
            'comment' => $comment,
            'user_I' => $user_ID
        ];
    }
}