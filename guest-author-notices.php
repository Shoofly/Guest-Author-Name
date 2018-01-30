<?php
add_action( 'admin_notices', array( 'guest_author_names_free_notices', 'simply_guest_author_premium_notice' ) );
add_action( 'admin_init', array ( 'guest_author_names_free_notices', 'simply_guest_author_names_premium_nag_ignore' ) );

/**
 * guest_author_names_notices class.
 */
if ( !class_exists ('guest_author_names_free_notices' ) ):
class guest_author_names_free_notices
{
		/**
		 * simply_guest_author_premium_notice function.
		 * create the premium/conversion notice
		 * @access public
		 * @static
		 * @return void
		 */
		static function simply_guest_author_premium_notice( ) {
			$hide = __( 'Hide Notice', 'guest-author-name' );
			global $current_user ;
		    $user_id = $current_user->ID;
		    /* Check that the user hasn't already clicked to ignore the message */
			$nag_id = 'nag_2';
		    $user_nag_meta = get_user_meta($user_id, 'simply_guest_author_premium_nag_ignore', true);
		    $nag_ignore = $user_nag_meta && isset( $user_nag_meta[$nag_id] ) ? $user_nag_meta[$nag_id] : false;
			$link = site_url ( '/wp-admin/options-general.php?page=guest_author_name' );
			$quesamp = strrchr($link, '?') != false ? '&' : '?';
			if ( !$nag_ignore )   {
		        echo '<div class="updated"><p>';
		        printf(__('Thanks for installing our plugin Simply Guest Author Name!! Premium is here. <a href="http://plugins.protoframework.com/guest-author-name/" target="_blank">Click here for details or to go premium!</a>    | <a href="%s">%s</a>'), $link  . $quesamp .'simply_guest_author_premium_nag_ignore=0', $hide);
		        echo "</p></div>";
			}
		}


		/**
		 * simply_guest_author_names_premium_nag_ignore function.
		 * update the nag ignore funtion if 'hide notice' has been clicked
		 * @access public
		 * @static
		 * @return void
		 */
		static function simply_guest_author_names_premium_nag_ignore( ) {
			global $current_user;
			$nag_id = 'nag_2';
	        $user_id = $current_user->ID;
		    $user_nag_meta = get_user_meta($user_id, 'simply_guest_author_premium_nag2_ignore', false);

	        /* If user clicks to ignore the notice, add that to their user meta */
	        if ( isset($_GET['simply_guest_author_premium_nag_ignore']) && '0' == $_GET['simply_guest_author_premium_nag_ignore'] ) {
		        $user_nag_meta[$nag_id] = true;
		        if (! $user_nag_meta )
	             	add_user_meta($user_id, 'simply_guest_author_premium_nag_ignore', $user_nag_meta, false);
	            else
	            	update_user_meta( $user_id, 'simply_guest_author_premium_nag_ignore', $user_nag_meta );
			}
		}
}
endif;