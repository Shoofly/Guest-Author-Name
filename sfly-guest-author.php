<?php
/*
Author: A. R. Jones
Plugin Name: Simply Guest Author Name 
Slug: guest-author-name
Plugin URI: http://www.shooflysolutions.com/guestauthor
Description: An ideal plugin for cross posting. Guest Author Name helps you to publish posts by authors without having to add them as users. If the Guest Author field is filled in on the post, the Guest Author name will override the author.  The optional Url link allows you to link to another web site.
Version: 3.1
Author URI: http://www.shooflysolutions.com
Copyright (C) 2015, 2016 Shoofly Solutions
Contact me at http://www.shooflysolutions.com.com*/

remove_filter('pre_user_description', 'wp_filter_kses');

new sfly_guest_author();
class sfly_guest_author
{
    function __construct()
    {
  
        add_filter( 'the_author', array($this, 'guest_author_name'), 12 );
        add_filter( 'get_the_author_display_name', array($this, 'guest_author_name'), 12);
        add_filter( 'author_link', array($this, 'guest_author_link'), 12);
        add_filter('get_the_author_link', array($this, 'guest_author_link'), 12);
        add_filter('get_the_author_url', array($this, 'guest_author_link'), 21);
        add_filter('author_description', array($this, 'guest_author_description'), 12);
        add_filter('get_the_author_description', array($this,  'guest_author_description'), 12);

  
        add_filter( 'jetpack_open_graph_tags', array($this, 'sfly_custom_og_author'), 12 );
        add_filter('get_the_author_id', array($this, 'guest_author_id'), 12);
        add_filter('author_id', array($this, 'guest_author_id'), 12);

        add_filter('get_avatar', array($this, 'guest_author_avatar'), 12, 1);
   

        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		add_action( 'save_post', array( $this, 'save' ) );
    }

    function sfly_custom_og_author( $og_tags ) {
       $id = $this->get_post_id();      
       $author = get_post_meta( $id, 'sfly_guest_author_names', true );
            if ( $author && is_singular() )
            {
			
			    $og_tags['og:title']           =   $author;
			    $og_tags['og:url']             =   get_post_meta( $id, 'sfly_guest_link', true );
			    $og_tags['og:description']     =  get_post_meta( $id, 'sfly_guest_author_description', true ); 
			    $og_tags['profile:first_name'] = $author;
			    $og_tags['profile:last_name']  = '';
			    if ( isset( $og_tags['article:author'] ) ) {
				    $og_tags['article:author'] = get_post_meta( $id, 'sfly_guest_link', true );
			    }
            }
	    return $og_tags;
    }


    function guest_author_id( $id ) {
   
        $id = $this->get_post_id();      
        $author = get_post_meta( $id, 'sfly_guest_author_names', true );
        if ( $author )
            $id = NULL;
        return $id;
    }
    function guest_author_name( $name ) {
        $id = $this->get_post_id();      
        
        $author = get_post_meta( $id, 'sfly_guest_author_names', true );
        if ( $author )
            $name = $author;
        return $name;
    }
      function guest_author_link( $link ) {
        $id = $this->get_post_id();      
 
        $author = get_post_meta( $id, 'sfly_guest_author_names', true );
        if ( $author )
        {
           $link = get_post_meta( $id, 'sfly_guest_link', true );     
            error_log($link);
           if (!$link)
            $link = "";
          
        }
        return $link;
    }
     function guest_author_description( $description ) {
          $id = $this->get_post_id();      
        $author = get_post_meta( $id, 'sfly_guest_author_names', true );
        if ( $author )
        {
           $description = get_post_meta( $$id, 'sfly_guest_author_description', true );      
           if (!$description)
            $description = "";
        }
        return $description;
    }
    function guest_author_email( $email ) {
        $id = $this->get_post_id();       
        $author = get_post_meta( $id, 'sfly_guest_author_names', true );
        if ( $author )
        {
           $email = get_post_meta( $id, 'sfly_guest_author_email', true );      
           if (!$email)
            $email = "";
        }
        return $email;
    }
    function guest_author_avatar($avatar)
    {
         if (is_single() or is_page())
         {
            $id = $this->get_post_id();      
             $author = get_post_meta( $id, 'sfly_guest_author_names', true );
             if ( $author )
             {
                $email = get_post_meta( $id, 'sfly_guest_author_email', true ); 
                if ($email)
                    $avatar = "<img src='{$this->get_guest_gravatar($email)}'/>";
             }
         }
         return $avatar;
    }
    function get_guest_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
    function get_post_id()
    {
        global $post;
        global $post_id;
        if (isset($post))
                $id = $post->ID;
        elseif (isset($post_id))
            $id = $post_id;   
        else
            $id = NULL;  
        return $id;   
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
        $description = sanitize_text_field( $_POST['sfly_guest_author_description']);
		$email = sanitize_email( $_POST['sfly_guest_author_email'] );
  
		// Update the meta field.
		update_post_meta( $post_id, 'sfly_guest_author_names', $author );
        update_post_meta( $post_id, 'sfly_guest_link', $link);
        update_post_meta( $post_id, 'sfly_guest_author_description', $description);
        update_post_meta( $post_id, 'sfly_guest_author_email', $email);
   
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
        $description = get_post_meta($post->ID, 'sfly_guest_author_description', true);
        $email = get_post_meta($post->ID, 'sfly_guest_author_email', true);
   
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
                echo ' value="' . esc_url( $link ) . '" style="max-width:100%"   />';

	
         echo '<br/><label for="sfly_guest_description">';
		_e( 'Guest Description', 'sfly_guest_description' );
		echo '</label><br/> ';
        echo '<textarea id="sfly_guest_author_description" name="sfly_guest_author_description" style="width:100%;height:40px;">' . esc_attr($description) . '</textarea>';

		echo '<label for="sfly_guest_author_email">';
		_e( 'Guest Gravatar Email', 'sfly_guest_author_email' );
		echo '</label> ';
		echo '<input type="text" id="sfly_guest_author_email" name="sfly_guest_author_email"';
                echo ' value="' . esc_attr( $email ) . '" style="max-width:100%" size="150" />';
	}
}


?>