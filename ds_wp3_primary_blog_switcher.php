<?php
/*
Plugin Name: Primary Blog Switcher for SuperAdmins
Plugin URI: http://wordpress.org/extend/plugins/primary-blog-switcher-for-superadmins/
Description: Adds a dropdown primary blog switcher to a user's profile at SuperAdmin->Users->Edit. Users with blog 1 as their primary, or have no blog, appear in an admin notice on the SuperAdmin->Admin page.
Author: D Sader
Version: 3.0.1.5
Author URI: http://dsader.snowotherway.org

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
  
*/
class ds_primary_blog_switcher {

	function ds_primary_blog_switcher() {
		
		}

	function admin_notices() {
		if( !is_super_admin() ) return;
		global $wpdb;
			$query = "SELECT * FROM {$wpdb->users} WHERE ID !='1'"; // exclude "admin" user
			$user_ids = $wpdb->get_results( $query, ARRAY_A );
			if( $user_ids )	{
				$list1	= array();
				$list2	= array();
				foreach($user_ids as $user_id) {
					$primary_blog = get_user_meta($user_id['ID'],'primary_blog', true);
					if ($primary_blog == '1') { // or "Dashboard blog" or no blog at all
						$url = esc_url( "user-edit.php?user_id=".$user_id['ID'] );
					$list1[]	= '<a class="delete" href="' . $url . '">' . $user_id['user_login'] . '</a>';
					} elseif(!$primary_blog) {
						$url = esc_url( "user-edit.php?user_id=".$user_id['ID'] );
					$list2[]	= '<a class="delete" href="' . $url . '">' . $user_id['user_login'] . '</a>';
					}
				}
				//list users whose primary blog is main blog
				if($list1)	{
				$count = count($list1);
				echo '<div id="update-nag">The following '.$count.' users list blog 1 as their primary blog: ';
				if ($count)  
				echo implode(' | ', $list1); 
				echo '</div>';
				}
				
				// ... no active blog
				if($list2)	{
				$count = count($list2);
				echo '<div id="update-nag">The following '.$count.' users belong to no active blog: ';
				if ($count)  
				echo implode(' | ', $list2); 
				echo '</div>';
				}
			}	
	}
	
	function choose_primary_blog() {
		$edit_user = (int) $_GET['user_id'];	
		?>
		<table class="form-table">
		<tr>
			<th scope="row"><?php _e('Primary Blog'); ?></th>
			<td>
			<?php
			$all_blogs = get_blogs_of_user( $edit_user );
			$primary_blog = get_user_meta( $edit_user, 'primary_blog', true );
			if( count( $all_blogs ) > 1 ) {
				$found = false;
			?>
			<select name="primary_blog">
				<?php if( $primary_blog ) { ?>
				<optgroup label="Primary Blog">
			<?php
				foreach( (array) $all_blogs as $blog ) { 
					if( $primary_blog == $blog->userblog_id ) {
						$found = true; 
			?>
					<option value='<?php echo $blog->userblog_id ?>'<?php if( $primary_blog == $blog->userblog_id ) echo ' selected="selected"' ?>><?php echo esc_url( get_home_url( $blog->userblog_id ) ) ?></option>
			<?php 
					}
				} 
			?>
				</optgroup>
				<?php } ?>
				<optgroup label="User Blogs">
			<?php
			if($primary_blog) unset($all_blogs[$primary_blog]);

				foreach( (array) $all_blogs as $blog ) {
					if($blog) {
			?>
				<option value='<?php echo $blog->userblog_id ?>'><?php echo esc_url( get_home_url( $blog->userblog_id ) ) ?></option>
				<?php } else { 
				echo "User has no active blog";
				}
				}
				?>
				</optgroup>
			<?php

				if( function_exists(get_sitewide_tags_option) && get_sitewide_tags_option( 'tags_blog_enabled' ) == '1' ) {
				 if( $tags_blog_id = get_sitewide_tags_option( 'tags_blog_id' ) ) { 
					$tags_blog = get_blog_details($tags_blog_id); ?>
				<optgroup label="Tags Blog">
				<option value='<?php echo $tags_blog_id ?>'><?php echo esc_url( get_home_url( $tags_blog_id ) ) ?></option>
				</optgroup>
			<?php } 
				}
	
			if ( $dashboard_blog_id = get_site_option( 'dashboard_blog' ) ) {
					$dashboard_blog = get_blog_details( $dashboard_blog_id ); 
?>
				<optgroup label="Dashboard Blog">
				
				<option value='<?php echo $dashboard_blog_id ?>'><?php echo esc_url( get_home_url( $dashboard_blog_id ) ) ?></option>
				</optgroup>
<?php } 

/*
?>
			// "special blog" add $special_blog_id to add user to some, well, special blog.

				<optgroup label="Other Blogs"></optgroup>
				<optgroup label="Special Blog">
				<?php $special_blog_id = '63'; //
				$special_blog = get_blog_details( $special_blog_id ); ?>
				<option value='<?php echo $special_blog_id ?>'>http://<?php echo $special_blog->domain.$special_blog->path ?></option>
				</optgroup>
<?php
*/

?>						
			</select>
			<?php
			if( !$found ) {
				$blog = array_shift( $all_blogs );
				update_user_meta( $edit_user, 'primary_blog', $blog->userblog_id );
				}
			} elseif( count( $all_blogs ) == 1 ) {
				$blog = array_shift( $all_blogs );
				echo $blog->domain;
				if( $primary_blog != $blog->userblog_id ) // Set the primary blog again if it's out of sync with blog list.
				update_user_meta( $edit_user, 'primary_blog', $blog->userblog_id );
			} else {
				echo "User has no active blog";
		}
		?>
		</td>
	</tr>
	</table>
	<?php	
	}
	
	function profile_update_primary_blog() {
		global $user_id;
		$blog_id = $_POST['primary_blog'];
		if ( isset( $blog_id ) ) {	
			update_user_meta( $user_id, 'primary_blog', $blog_id );
			$is_member = is_user_member_of_blog( $user_id, $blog_id );
			if(!$is_member) 
				add_user_to_blog($blog_id, $user_id, 'subscriber');
		}
	}
}

if (class_exists("ds_primary_blog_switcher")) {
	$ds_primary_blog_switcher = new ds_primary_blog_switcher();	
}

if (isset($ds_primary_blog_switcher) && is_multisite()) {
	add_action( 'edit_user_profile', array(&$ds_primary_blog_switcher, 'choose_primary_blog') );
	add_action( 'edit_user_profile_update',  array(&$ds_primary_blog_switcher, 'profile_update_primary_blog') );
	add_action( 'mu_rightnow_end', array(&$ds_primary_blog_switcher, 'admin_notices'));

}
?>