<?php
/*
 * plugin should create a file named ‘uninstall.php’ in the base plugin folder. This file will be called, if it exists,
 * during the uninstall process bypassing the uninstall hook.
 * ref: https://developer.wordpress.org/reference/functions/register_uninstall_hook/
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
	die;
}
if (get_option("mxp_complete_remove", "yes") == "yes") {
	global $wpdb;
	$wpdb->query("DROP TABLE {$wpdb->prefix}mxp_pchome_post_log");
	delete_option("mxp_pchome2wp_db_version");
	delete_option("mxp_complete_remove");
	delete_option("mxp_pchome2wp_account");
	delete_option("mxp_pchome2wp_post_status");
	delete_option("mxp_pchome2wp_post_comment_status");
	delete_option("mxp_pchome2wp_post_ping_status");
	delete_option("mxp_pchome2wp_post_type");
	delete_option("mxp_pchome2wp_post_tags");
	delete_option("mxp_pchome2wp_post_comment_admin_displayname");
	delete_option("mxp_pchome2wp_post_comment_admin_email");
	delete_option("mxp_pchome2wp_post_category");
	delete_option("mxp_pchome2wp_post_author");
	delete_option("mxp_pchome2wp_agree_terms");
	delete_option("mxp_enable_debug");
	delete_option("mxp_pchome2wp_user_id");
	delete_option("mxp_pchome2wp_auth_token");
	delete_option("mxp_pchome2wp_pay_user");
	delete_option("mxp_pchome2wp_post_quota");
	delete_option("mxp_pchome2wp_usage");
	delete_option("mxp_pchome2wp_blogname");
}