<?php

// static class
class Post
{
    /**
     * Retrieve all posts from database
     */
    public static function getAllPosts()
    {
        return DB::Connect()->select(
            'SELECT * FROM posts ORDER BY id DESC',
            [],
            true
        );
    }

    /**
     * Retrieve post data by id
     */
    public static function getPostById( $post_id )
    {
        return DB::connect()->select(
            'SELECT * FROM posts WHERE id = :id',
            [
                'id' => $post_id
            ]
        );
    }

    /**
     * Add new post
     */
    public static function add( $title, $status )
    {
        return DB::connect()->insert(
            'INSERT INTO posts (title , status) 
            VALUES (:title, :status)',
            [
                'title' => $title,
                'status' => $status
            ]
        );
    }

    /**
     * Delete post
     */
    public static function delete( $post_id )
    {
        return DB::connect()->delete(
            'DELETE FROM posts where id = :id',
            [
                'id' => $post_id
            ]
        );
    }
}