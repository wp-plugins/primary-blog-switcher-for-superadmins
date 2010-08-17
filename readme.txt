=== Primary Blog Switcher for SuperAdmins ===
Contributors: dsader
Donate link: http://dsader.snowotherway.org
Tags: multisite, network, primary blog, primary site, profile, my blogs, primary blog switcher, edit users,
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: Trunk

WP3.0 multisite "mu-plugin" to allow SuperAdmin to set the "Primary Blog" (aka Primary Site) of a user while editing a profile.

== Description ==
WP3.0 multisite "mu-plugin" to allow SuperAdmin to set the "Primary Blog" (aka Primary Site) of a user while editing a user's profile.

Well, for whatever reasons (usually users fiddling around - I use WP3 multisite in a school with students grades 4-12), users aren't attached(or become unattached) to the correct "Primary Blog". 

This isn't a deal breaker, but annoying when they login and are redirected to a blog that is not their expected primary. It also is annoying when I use other plugins to list user primary blog for display in a member directory, member profiles, etc.

Telling users to reset their primary blog at their own Dashboard->My Blogs is a fix, but the SuperAdmin(Teacher in my case) can head off the confusion first with this plugin. There is no other way(AFAIK) for the SuperAdmin to set the "Primary Blog" of a user while editing their profile. 

Now, I can quickly scan the SuperAdmin list of users and edit profiles and set primary blogs of any user correctly.

I can also use my <a href="http://wordpress.org/extend/plugins/menus/">Menus plugin</a> to toggle the My Sites menu item so users can no longer fiddle with the Primary Site switcher at all. Problem solved.


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `ds_wp3_primary_blog_switcher.phpp` to the `/wp-content/mu-plugins/` directory
2. Edit user profiles as SuperAdmin Dashboard->SuperAdmin->Users->Edit
3. View Dashboard->SuperAdmin->Admin for notices of users who have the main blog set as their primary blog.

== Frequently Asked Questions ==

* Can I set the Main blog as a user's primary? Yes.

* Can I set the Dashboard blog (if enabled) as a user's primary? Yes.

* Can I set Donncha's Sitewide Tags blog (if enabled) as a user's primary? Yes.

* Can I add a user to some other blog "special blog" as their primary? Yes, but see "special blog" comments in the plugin code.

* Does this plugin filter the list of blogs already listed at a user's Dashboard->My Sites->Primary Site? No.


== Screenshots ==


== Notes ==

The original code for the Primary Site switcher is in wp-admin-includes/ms.php. I've basically copied that, but changed `get_current_user_id()` to `$edit_user = (int) $_GET['user_id'];` and added it to the "edit_user_profile" hook.


== Changelog ==

= 3.0.1 =

* Initial Release for WP3.x multisite

== Upgrade Notice ==

= 3.0.1 =
* WPMU2.9.2 version no longer supported.

