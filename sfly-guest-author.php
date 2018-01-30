<?php
/*
Author: Shoofly Solutions
Plugin Name: (Simply) Guest Author Name
Slug: guest-author-name
Plugin URI: http://plugins.protoframwork.com/guest-author-name
Description: An ideal plugin for cross posting. Guest Author Name helps you to publish posts by authors without having to add them as users. If the Guest Author field is filled in on the post, the Guest Author name will override the author.  The optional Url link allows you to link to another web site.
Version: 3.9
Author URI: http://www.shooflysolutions.com
Copyright (C) 2015, 2016 Shoofly Solutions
Contact me at http://www.shooflysolutions.com.com*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
remove_filter('pre_user_description', 'wp_filter_kses');

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

$active =  is_plugin_active( 'guest-author-name-premium/guest-authors.php' ) || is_plugin_active_for_network( 'guest-author-name-premium/guest-authors.php' ) ? true : false;
if ( !$active  ):

	$path =  (plugin_dir_path(__FILE__));
	if ( is_admin() )
		require_once ( $path . 'guest-author-notices.php' );
	/**
	 * sfly_guest_author class.
	 */
	 if ( !class_exists( 'sfly_guest_author' ) )
	 {
	class sfly_guest_author
	{
		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		function __construct()
		{

			if (!is_admin() )
			{
		        add_action( 'the_post', array($this, 'register_author'), 10);
				add_filter( 'the_author', array( $this, 'guest_author_name' ), 12 );
				add_filter( 'get_the_author_display_name', array( $this, 'guest_author_name' ), 12 );
				add_filter( 'author_link', array( $this, 'guest_author_link' ), 12 );
				add_filter( 'get_the_author_link', array( $this, 'guest_author_link' ), 12 );
				add_filter( 'get_the_author_url', array( $this, 'guest_author_link' ), 21 );
				add_filter( 'author_description', array( $this, 'guest_author_description'), 12) ;
				add_filter( 'get_the_author_description', array( $this,  'guest_author_description' ), 12 ) ;
				add_filter( 'get_the_author_id', array( $this, 'guest_author_id' ), 12 ) ;
				add_filter( 'author_id', array( $this, 'guest_author_id' ), 12 );
				add_filter( 'get_avatar', array( $this, 'guest_author_avatar' ), 40, 1 );
			}
			else
			{
					add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
					add_action( 'save_post', array( $this, 'save' ) );
					$options = get_option( 'guest_author_name_settings' );
					$quickedit = isset( $options['guest_author_name_quickedit'] ) ? true : false ;
					if ( $quickedit )
					{
						add_action( 'quick_edit_custom_box', array( $this, 'add_quick_meta_box' ), 10, 2 ) ;
						add_action( 'manage_posts_custom_column', array( $this, 'render_post_columns' ), 10, 2 ) ;
						add_filter( 'manage_edit-post_columns', array( $this, 'change_posttype_columns' ) );
						add_action( 'admin_enqueue_scripts', array( $this, 'guest_admin_scripts' ) );
					}
			}

		}

		/**
		 * register_author function.
		 * register the author data for the current post being edited
		 * @access public
		 * @return void
		 */
		function register_author() {
			$id = $this->get_post_id();
			$author = get_post_meta( $id, 'sfly_guest_author_names', true );
	        if (!$author)
	            return;

			global $authordata;
			$author = new WP_User();

			$author->user_url = $this->guest_author_link('');
			$author->user_email = $this->guest_author_email('');
			$author->user_description = $this->guest_author_description('');
			$author->display_name = $this->guest_author_name('');
			$author->ID = $this->guest_author_id('');

			// register the global
			$authordata = $author;

		}

		/**
		 * guest_admin_scripts function.
		 * enqueue scripts
		 * @access public
		 * @param string $hook
		 * @return void
		 */
		function guest_admin_scripts( $hook )
		{
			if ( $hook == 'edit.php' )
				wp_enqueue_script('guest_author_name_scripts', plugins_url('/guest-author.js', __FILE__), array('inline-edit-post'), '1.00');
		}
		/**
		 * guest_author_id function.
		 * get the author id
		 * @access public
		 * @param number $id
		 * @return the author id or null if it's a guest author
		 */
		function guest_author_id( $id ) {
			$id = $this->get_post_id();
			$author = get_post_meta( $id, 'sfly_guest_author_names', true );
			if ( $author )
				$id = NULL;
			return $id;
		}
		/**
		 * guest_author_name function.
		 * get the guest author name if one exists
		 * @access public
		 * @param string $name
		 * @return name to be displayed as the author name
		 */
		function guest_author_name( $name ) {
			$id = $this->get_post_id();
			$author = get_post_meta( $id, 'sfly_guest_author_names', true );
			if ( $author )
				$name = $author;
			return $name;
		}
		/**
		 * guest_author_link function.
		 * get the guest author url if one exists
		 * @access public
		 * @param string $link - real author link
		 * @return string
		 */
		function guest_author_link( $link ) {
			$id = $this->get_post_id();
			$author = get_post_meta( $id, 'sfly_guest_author_names', true );
			if ( $author )
			{
				$link = get_post_meta( $id, 'sfly_guest_link', true );
				if (!$link)
					$link = "";
			}
			return $link;
		}
		/**
		 * guest_author_description function.
		 * get the guest author bio if it exists
		 * @access public
		 * @param string $description - real author bio
		 * @return string
		 */
		function guest_author_description( $description ) {
			$id = $this->get_post_id();
			$author = get_post_meta( $id, 'sfly_guest_author_names', true );
			if ( $author )
			{
				$description = get_post_meta( $id, 'sfly_guest_author_description', true );
				if (!$description)
					$description = "";
			}
			return $description;
		}
		/**
		 * guest_author_email function.
		 * get the guest author email if one exists
		 * @access public
		 * @param string $email - real author email
		 * @return string
		 */
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
		/**
		 * guest_author_avatar function.
		 * get the guest author avatar image html
		 * @access public
		 * @param string $avatar - real author avatar
		 * @return avatar html
		 */
		function guest_author_avatar( $avatar )
		{
			global $comment;
			if ( isset( $comment ) )
				return $avatar;
			$id = $this->get_post_id();
			$author = get_post_meta( $id, 'sfly_guest_author_names', true );
			if ( $author )
			{
				$email = get_post_meta( $id, 'sfly_guest_author_email', true );
				if ($email)
					$avatar = "<img src='{$this->get_guest_gravatar($email)}'/>";
			}
			return $avatar;
		}
		/**
		 * get_guest_gravatar function.
		 *
		 * @access public
		 * @param mixed $email
		 * @param int $s (default: 80)
		 * @param string $d (default: 'mm')
		 * @param string $r (default: 'g')
		 * @param bool $img (default: false)
		 * @param array $atts (default: array())
		 * @return void
		 */
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
		/**
		 * get_post_id function.
		 * get the post id for the current post
		 * @access public
		 * @return void
		 */
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
		/**
		 * add_meta_box function.
		 *
		 * @access public
		 * @param mixed $post_type
		 * @return void
		 */
		public function add_meta_box( $post_type ) {
			$post_types = array('post', 'page');	 //limit meta box to certain post types
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
		 * change_posttype_columns function.
		 * captions for the post list
		 * @access public
		 * @param mixed $cols
		 * @return void
		 */
		function change_posttype_columns( $cols ) {
			$cols2 = array(
				'sfly_guest_author' => 'Guest Author',
				'sfly_guest_link' => 'Guest Link',
				'sfly_guest_author_description' => 'Guest Description',
				'sfly_guest_author_email' => 'Guest Email',

			);
			$cols = array_merge($cols, $cols2);
			return $cols;
		}

		// But remove it again on the edit screen (other screens to?)

		/**
		 * remove_dummy_column function.
		 *
		 * @access public
		 * @param mixed $cols
		 * @return void
		 */
		function remove_dummy_column($cols)
		{
			unset($cols['sfly_guest_author']);
			unset($cols['sfly_guest_link']);
			unset($cols['sfly_guest_author_description']);
			unset($cols['sfly_guest_author_email']);
			return $cols;
		}

		/**
		 * render_post_columns function.
		 * output the post column data for the list
		 * @access public
		 * @param mixed $column_name

		 * @param mixed $id
		 * @return void
		 */
		function render_post_columns($column_name, $id) {

			switch ($column_name) {
			case 'sfly_guest_author':
				echo get_post_meta( $id, 'sfly_guest_author_names', TRUE);
				break;
			case 'sfly_guest_link':
				echo get_post_meta( $id, 'sfly_guest_link', TRUE);
				break;
			case 'sfly_guest_author_description':
				echo get_post_meta( $id, 'sfly_guest_author_description', TRUE);
				break;
			case 'sfly_guest_author_email':
				echo get_post_meta( $id, 'sfly_guest_author_email', TRUE);


			}
		}
		/**
		 * save_quick_meta function.
		 * save the data from the quick edit screen
		 * @access public
		 * @param mixed $post_id
		 * @return void
		 */
		function save_quick_meta( $post_id ) {

			$post_types = array('post', 'page');	 //limit meta box to certain post types
			if ( in_array( $post_type, $post_types )) {
				if ( $printNonce ) {
					$printNonce = FALSE;
					wp_nonce_field( plugin_basename( __FILE__ ), 'guest_author_edit_nonce' );
				}
				if ( !current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
				if ( isset( $_REQUEST['sfly_guest_author'] ) ) {
					update_post_meta( $post_id, 'sfly_guest_author', $_REQUEST['sfly_guest_author'] );
				}
				if ( isset( $_REQUEST['sfly_guest_link'] ) ) {
					update_post_meta( $post_id, 'sfly_guest_link', $_REQUEST['sfly_guest_link'] );
				}
				if ( isset( $_REQUEST['sfly_guest_author_description'] ) ) {
					update_post_meta( $post_id, 'sfly_guest_author_description', $_REQUEST['sfly_guest_author_description'] );
				}
				if ( isset( $_REQUEST['sfly_guest_author_email'] ) ) {
					update_post_meta( $post_id, 'sfly_guest_author_email', $_REQUEST['sfly_guest_author_email'] );
				}
			}
		}

		/**
		 * add_quick_meta_box function.
		 * create the quick edit screen
		 * @access public
		 * @param mixed $col

		 * @param mixed $post_type
		 * @return void
		 */
		public function add_quick_meta_box( $col,  $post_type ) {
			static $printNonce = TRUE;
			$post_types = array('post', 'page');	 //limit meta box to certain post types
			if ( in_array( $post_type, $post_types )) {
				if ( $printNonce ) {
					wp_nonce_field( 'sfly_guest_author_box', 'sfly_guest_author_nonce' );
					$printNonce = FALSE;
					//	wp_nonce_field( plugin_basename( __FILE__ ), 'guest_author_edit_nonce' );
				}
	?>


			<?php
				switch ( $col ) {
				case 'sfly_guest_author':
					?><fieldset class="inline-edit-col-right inline-edit-book">
		  <div class="inline-edit-col column-<?php echo $col; ?>" style="display:block; border:1px;">
			<label class="inline-edit-group"><div style='display:block'><span class="sfly_guest_author" style="width:150px;">Guest Author Name(s)</span><input name="sfly_guest_author" class="widefat" /></div></label><?php
					break;
				case 'sfly_guest_link':
					?><label  class="inline-edit-group"><div style="display:block"><span class="sfly_guest_link" style="width:150px;">Guest URL</span><input name="sfly_guest_link" class="widefat" /></div></label><?php
					break;
				case 'sfly_guest_author_description':
					?><label class="inline-edit-group"><div style="display:block"><span class="sfly_guest_author_description" style="width:150px;">Author Bio / Description</span><input name="sfly_guest_author_description" class="widefat" /></div></label><?php
					break;
				case 'sfly_guest_author_email':
					?><label class="inline-edit-group"><div style="display:block"><span class="sfly_guest_author_email" style="width:150px;">Author Gravatar Email</span><input name="sfly_guest_author_email" class="widefat" /></div>
			</div></label>
		  </div>
		</fieldset><?php
					break;
				}
	?>
		<?php
			}
		}
		/**
		 * Save the meta when the post is saved.
		 *
		 * @param int $post_id The ID of the post being saved.
		 */
		/**
		 * save function.
		 *
		 * @access public
		 * @param mixed $post_id
		 * @return void
		 */
		public function save( $post_id ) {
			if ( ! isset( $_POST['sfly_guest_author_nonce'] ) )
				return $post_id;
			$nonce = $_POST['sfly_guest_author_nonce'];
			if ( ! wp_verify_nonce( $nonce, 'sfly_guest_author_box' ) )
				return $post_id;
			// If this is an autosave, our form has not been submitted,
			//	 so we don't want to do anything.
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

		 /* render_meta_box_content function.
		 *
		 * @access public
		 * @param mixed $post
		 * @return void
		 */
		/**
		 * render_meta_box_content function.
		 *
		 * @access public
		 * @param mixed $post
		 * @return void
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
			echo ' value="' . esc_attr( $value ) . '" style="max-width:100%" size="150" class="widefat" />';
			echo '<br/><label for="sfly_guest_link">';
			_e( 'Guest URL', 'sfly_guest_link' );
			echo '</label><br/>';
			echo '<input type="text" id="sfly_guest_link" name="sfly_guest_link"';
			echo ' value="' . esc_url( $link ) . '" style="max-width:100%" class="widefat"  />';
			echo '<br/><label for="sfly_guest_description">';
			_e( 'Guest Description', 'sfly_guest_description' );
			echo '</label><br/> ';
			echo '<textarea id="sfly_guest_author_description" name="sfly_guest_author_description" style="width:100%;height:40px;">' . esc_attr($description) . '</textarea>';
			echo '<label for="sfly_guest_author_email">';
			_e( 'Guest Gravatar Email', 'sfly_guest_author_email' );
			echo '</label> ';
			echo '<input type="text" id="sfly_guest_author_email" name="sfly_guest_author_email"';
			echo ' value="' . esc_attr( $email ) . '" style="max-width:100%" class="widefat" size="150" />';
		}
	}

	}

	// Admin Settings


	if ( !class_exists( 'guest_author_admin_menu' ) ) {
	/**
	 * guest_author_admin_menu class.
	 */
		class guest_author_admin_menu
		{
			function __construct()
			{

				add_action( 'admin_menu', array( $this, 'guest_author_name_add_admin_menu' ) );
				add_action( 'admin_init', array( $this, 'guest_author_name_settings_init' ) );
			}

			/**
			 * guest_author_name_add_admin_menu function.
			 *
			 * @access public
			 * @return void
			 */
			function guest_author_name_add_admin_menu(  ) {

				add_options_page( 'Guest Author Name', 'Guest Author Name', 'manage_options', 'guest_author_name', array( $this,  'guest_author_name_options_page' ) );

			}


			/**
			 * guest_author_name_settings_init function.
			 *
			 * @access public
			 * @return void
			 */
			function guest_author_name_settings_init(  ) {

				register_setting( 'guest_author_pluginPage', 'guest_author_name_settings' );

				add_settings_section(
					'guest_author_name_pluginPage_section',
					'',
					array( $this, 'guest_author_name_settings_section_callback' ),
					'guest_author_pluginPage' );

				add_settings_field(
					'guest_author_name_quickedit',
					__( 'Enable Quick Edit for Guest Author Name', 'guest-author-name' ),
					array( $this, 'guest_author_name_quickedit_render' ),
					'guest_author_pluginPage',
					'guest_author_name_pluginPage_section'
				);


			}


			/**
			 * guest_author_name_quickedit_render function.
			 *
			 * @access public
			 * @return void
			 */
			function guest_author_name_quickedit_render(  ) {

				$options = get_option( 'guest_author_name_settings' );
				$quickedit = isset( $options['guest_author_name_quickedit'] ) ? true : false ;
		?>
			<input type='checkbox' name='guest_author_name_settings[guest_author_name_quickedit]' <?php checked( $quickedit, 1 ); ?> value='1'>
			<?php

			}


			/**
			 * guest_author_name_settings_section_callback function.
			 *
			 * @access public
			 * @return void
			 */
			function guest_author_name_settings_section_callback(  ) {

				//echo __( 'This section description', 'guest-author-name' );

			}


			/**
			 * guest_author_name_options_page function.
			 *
			 * @access public
			 * @return void
			 */
			function guest_author_name_options_page(  ) {

		?>
			<form action='options.php' method='post'>

				<h2>Simply Guest Author Name</h2>

				<?php
				settings_fields( 'guest_author_pluginPage' );
				do_settings_sections( 'guest_author_pluginPage' );
				submit_button();
		?>

			</form>
				   <div>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<h3>Thank you for using our plugin. Donations for extended support are appreciated but never required!</h3>
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="FTBD2UDXFJDB6">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
						</form>
					</div>
					<div >
						<a target='_blank' href="https://wordpress.org/plugins/featured-image-pro/">You can also help by rating this plugin!</a>
					</div>
			<?php

			}

		}
	}
	new guest_author_admin_menu();
	new sfly_guest_author();
endif;