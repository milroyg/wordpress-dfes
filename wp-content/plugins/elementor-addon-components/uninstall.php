<?php

/** Si uninstall.php n'est pas appelé par WordPress, crève charogne */
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;

$options        = $wpdb->get_results( $wpdb->prepare( "SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE %s", '%eac_option%' ) );
$transients     = $wpdb->get_results( $wpdb->prepare( "SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE %s", '%transient_eac_%' ) );
$nominatims     = $wpdb->get_results( $wpdb->prepare( "SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE %s", '%eac_nominatim_%' ) );
$menu_item_ids  = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE %s", '_eac_custom_nav_%' ) );
$attachment_ids = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE %s", 'eac_media_%' ) );

/** Nettoie les options */
if ( $options && ! empty( $options ) ) {
	foreach ( $options as $option ) {
		delete_option( $option->option_name );
	}
}

/** Nettoie les options de mise à jour et des caches par les transients */
if ( $transients && ! empty( $transients ) ) {
	foreach ( $transients as $transient ) {
		delete_option( $transient->option_name );
	}
}

/** Nettoie les options instagram nominatim du plugin et des transients */
if ( $nominatims && ! empty( $nominatims ) ) {
	foreach ( $nominatims as $nominatim ) {
		delete_option( $nominatim->option_name );
	}
}

/** Nettoie les metas données des items de menu */
if ( $menu_item_ids && ! empty( $menu_item_ids ) ) {
	foreach ( $menu_item_ids as $menu_item_id ) {
		delete_post_meta( $menu_item_id->post_id, '_eac_custom_nav_menu_item' );
	}
}

/** Nettoie les metas données des deux champs supplémentaires des images du widget advanced gallery */
if ( $attachment_ids && ! empty( $attachment_ids ) ) {
	foreach ( $attachment_ids as $attachment_id ) {
		delete_post_meta( $attachment_id->post_id, 'eac_media_url' );
		delete_post_meta( $attachment_id->post_id, 'eac_media_cat' );
	}
}

$args_cap = array(
	'edit_post'              => 'edit_page_option',
	'read_post'              => 'read_page_option',
	'delete_post'            => 'delete_page_option',
	'edit_posts'             => 'edit_page_options',
	'edit_others_posts'      => 'edit_others_page_options',
	'delete_posts'           => 'delete_page_options',
	'publish_posts'          => 'publish_page_options',
	'delete_published_posts' => 'delete_published_page_options',
	'delete_others_posts'    => 'delete_others_page_options',
	'edit_published_posts'   => 'edit_published_page_options',
	'create_posts'           => 'create_page_options',
	'read_private_posts'     => 'read_private_page_options',
	'delete_private_posts'   => 'delete_private_page_options',
	'edit_private_posts'     => 'edit_private_page_options',
);

foreach ( array( 'administrator', 'editor', 'author', 'shop_manager' ) as $each_role ) {
	$current_role = get_role( $each_role );
	if ( ! is_null( $current_role ) ) {
		foreach ( $args_cap as $cap ) {
			$current_role->remove_cap( $cap );
		}
		$current_role->remove_cap( 'eac_manage_options' );
	}
}
delete_option( 'eac_options_page_capability' );
