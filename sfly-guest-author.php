<?php
/*Plugin Name: Guest Author Name 
Plugin URI: http://www.shooflysolutions.com/guestauthor
Description: An ideal plugin for cross posting. Guest Author Name helps you to publish posts by authors without having to add them as users. If the Guest Author field is filled in on the post, the Guest Author name will override the author.  The optional Url link allows you to link to another web site.
Version: 1.0
Author: A. R. Jones (nomadcoder)
Author URI: http://www.shooflysolutions.com
Copyright (C) 2015 Shoofly Solutions
Contact me at http://www.shooflysolutions.com.com*/

new sfly_guest_author();
class sfly_guest_author
{
    function __construct()
    {
        add_filter( 'the_author', array($this, 'guest_author_name') );
        add_filter( 'get_the_author_display_name', array($this, 'guest_author_name'));
         add_filter( 'author_link', array($this, 'guest_author_link'));
        add_filter('get_the_author_link', 'guest_author_link');
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
    }
    //Add a filter to use the custom author name instead of the author name it has been filled in.
    function guest_author_name( $name ) {
        global $post;
        $author = get_post_meta( $post->ID, 'sfly_guest_author_names', true );
        if ( $author )
            $name = $author;
        return $name;
    }
      function guest_author_link( $link ) {
        global $post;
        $author = get_post_meta( $post->ID, 'sfly_guest_author_names', true );
        if ( $author )
           $link = get_post_meta( $post->ID, 'sfly_guest_link', true );      
        return $link;
    }
	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
            $post_types = array('post', 'page');     //limit meta box to certain post types
            if ( in_array( $post_type, $post_types )) {
		add_meta_box(
			'some_meta_box_name'
			,__( 'Guest Author', 'sfly_guest_author' )
			,array( $this, 'render_meta_box_content' )
			,$post_type
			,'advanced'
			,'high'
		);
            }
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
	
	
		
		if ( ! isset( $_POST['sfly_guest_author_nonce'] ) )
			return $post_id;

		$nonce = $_POST['sfly_guest_author_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'sfly_guest_author_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
     
		// Sanitize the user input.
		$author = sanitize_text_field( $_POST['sfly_guest_author'] );
        $link = esc_url($_POST['sfly_guest_link']);
		// Update the meta field.
		update_post_meta( $post_id, 'sfly_guest_author_names', $author );
        if ($link)
            update_post_meta( $post_id, 'sfly_guest_link', $link);
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'sfly_guest_author_box', 'sfly_guest_author_nonce' );

		// Use get_post_meta to retrieve an existing values from the database.
		$value = get_post_meta( $post->ID, 'sfly_guest_author_names', true );
        $link = get_post_meta( $post->ID, 'sfly_guest_link', true );
		// Display the form, using the current values.
		echo '<label for="sfly_guest_author">';
		_e( 'Guest Author Name(s)', 'sfly_guest_author' );
		echo '</label> ';
		echo '<input type="text" id="sfly_guest_author" name="sfly_guest_author"';
                echo ' value="' . esc_attr( $value ) . '" style="max-width:100%" size="150" />';
		echo '<label for="sfly_guest_link">';
		_e( 'Guest Url', 'sfly_guest_link' );
		echo '</label> ';
		echo '<input type="text" id="sfly_guest_link" name="sfly_guest_link"';
                echo ' value="' . esc_url( $link ) . '" style="max-width:100%" size="150" />';
	}
}
?>