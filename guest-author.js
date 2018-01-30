(function($) {
	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {
		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );

		// now we take care of our business

		// get the post ID
		var $post_id = 0;

		$post_id = parseInt( this.getId( id ) );


		if ( $post_id > 0 ) {
			// define the edit row
			var $edit_row = $( '#edit-' + $post_id );
			var $post_row = $( '#post-' + $post_id );

			// get the data
			var $sfly_guest_author = $( '.sfly_guest_author', $post_row ).text();
			var $sfly_guest_link = $( '.sfly_guest_link', $post_row ).text();
			var $sfly_guest_author_email = $( '.sfly_guest_author_email', $post_row ).text();
			var $sfly_guest_author_description = $( '.sfly_guest_author_description', $post_row ).text();
console.log($sfly_guest_author);
			// populate the data
			$( ':input[name="sfly_guest_author"]', $edit_row ).val( $sfly_guest_author );
			$( ':input[name="sfly_guest_link"]', $edit_row ).val( $sfly_guest_link );
			$( ':input[name="sfly_guest_author_email"]', $edit_row ).val( $sfly_guest_author_email );
			$( ':input[name="sfly_guest_author_description"]', $edit_row ).val( $sfly_guest_author_description );

		}
	};


})(jQuery);

