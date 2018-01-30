=== Plugin Name ===
Contributors: nomadcoder, seancjones
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FTBD2UDXFJDB6
Tags: Guest Author Name, Override Author Name, Author
Stable Tag: 3.9
Requires at least: 4.1
Tested up to: 4.9.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Create a post and override the author name with one or more names or text. You can also create a link for the author name field

== Description ==
Version 3.9

Premium

Additional Features:
1) Re-use & save guest authors
2) Upload guest author image
3) List posts written by guest authors as though they were authors
4) Bulk edit guest authors in posts
5) Add guest authors to custom posts and pages
6) Easy Conversion process from our free version
7) Still supports gravatar and custom urls
8) Author list & Grid shortcodes

Create a post and override the author name with one or more names or text. You can also create a link for the author name field, add a description and an email address for the gravatar.

You may now enable quickedit for the Guest Author Name plugin. Go to the settings menu and select ‘Guest Author Name’ to enable or disable quick edit.

For quicker support, please visit the <a href="http://www.shooflysolutions.com/software/guest-author-name/">plugin page on the Shoofly Solutions web page</a>.

http://www.shooflysolutions.com/software/guest-author-name/
Donations for extended support are appreciated but are never required!

Please be kind and rate this plugin. Everything helps!
== Installation ==

Download the Guest Author Name plugin

How to install this plugin

###Method 1:
Install this plugin using the built-in plugin installer:
Go to Plugins > Add New.
Under Search, type "Guest Author Name"
Click Install Now.
A popup window will ask you to confirm your wish to install the Plugin.
Click Proceed to continue with the installation. The resulting installation screen will list the installation as successful or note any problems during the install.
If successful, click Activate Plugin to activate it, or Return to Plugin Installer for further actions.

###Method 2:
Download the plugin. In the WordPress admin panel, click on Plugins. Select Add New to get to the "Install Plugins" page. Click on browse and choose the downloaded file.

Click on the "Install Now" button to install the plugin. Once the plugin is installed, select "Activate Plugin".

###Method 3: (Advanced Users):

    Unzip the file. Using FTP, upload the guest-author folder to the contents/plugins folder in your wordpress directory.



    == Frequently Asked Questions ==

= How can I stop Guest Author posts from showing up on my posts feed?

Our premium version handles this seamlessly.

While the author link ideally should go to the authors web page, your guest author posts will show up as one of your posts. You can get around the list problem by creating a new user called Guest Author. Create your guest author posts under this user or use an author switch plugin to change the registered author. That way, guest author posts will show up under the user "Guest Authors" instead of your account.

Please visit the FAQ page on our site at <a href="http://www.shooflysolutions.com/faq/">http://www.shooflysolutions.com/faq/</a>


= This plugin used to substitute Title Name with Author Name when using Jetpack Publicize. How do I get it back?

This was a bug, but if you were using it as a feature, then you can use this code below.

    add_filter('jetpack_open_graph_tags', function( $og_tags ) {
        $id = $this->get_post_id();
        $author = get_post_meta( $id, 'sfly_guest_author_names', true );
        if ( $author && is_singular() )  {

            $og_tags['og:title']           =   $author;
            // any other tags you want to add here
            // for more examples see https://gist.github.com/natebot/6323846
        }
        return $og_tags;
    });


== Screenshots ==

1. Override the post Author
2. Post with Guest Author
3. Settings Page
== Changelog ==
= 3.9 Fix links in nags.
      Remove author name replacement from admin (Under post list, original base author should be displayed not the guest author).
      Display avatars on archive pages
      Fix avatar for commenters (was picking up guest author avatar)
= 3.8 Add notice
= 3.7 Update tested date
= 3.6 Guest authors now populate the global author scope for better interoperability with expanded biographies.
= 3.5 Add option to enable Guest Author Name fields for quick edit.
= 3.4 Removed jetpack filter. For details on how to make author name the title, check "Frequently Asked Questions"
= 3.3 Fix undefined variable error on line 96 that would have caused the description not to be loaded.
= 3.2 Version upgrade
= 3.1 change priority to work with Pretty author box
= 3.0
* Fix code to work with Jetpack Subscriptions & Open Graph Tags. Known issue: The author link for posts sent to subscribers will link to the actual post author, not the guest author. This appears to be a possible limitation of Jetpack.
* 2.0
* Add code to manage author description/bio and gravatar for guest authors
* Add new fields for author bio and gravatar email address
* 1.1
* Fix for blank author link
* 1.0
* Initial Release

== Need More? ==

Need more? Customization is available. Contact sales@shooflysolutions.com for more information.
