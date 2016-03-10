<?php

/**
 * Plugin Name: Editor Columns
 * Plugin URI: https://github.com/artcomventure/wordpress-plugin-columns
 * Description: Extends HTML Editor with WYSIWYG columns.
 * Version: 1.4.1
 * Text Domain: columns
 * Author: artcom venture GmbH
 * Author URI: http://www.artcom-venture.de/
 */

if ( ! defined( 'COLUMNS_PLUGIN_URL' ) ) {
	define( 'COLUMNS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'COLUMNS_PLUGIN_DIR' ) ) {
	define( 'COLUMNS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Enqueue admin and editor styles and scripts.
 */
add_action( 'admin_head', 'columns__admin_head' );
function columns__admin_head() {
	// check if user has permission to edit posts
	if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
	     // ... and is allowed to use wysiwyg
	     || ! get_user_option( 'rich_editing' ) == 'true'
	) {
		return;
	}

	// add styles to editor
	add_editor_style( plugins_url( '/css/columns.min.css?', __FILE__ ) );
	add_editor_style( plugins_url( '/css/editor.min.css?', __FILE__ ) );

	// enqueue button style
	wp_enqueue_style( 'columns-admin', COLUMNS_PLUGIN_URL . 'css/editor.min.css', array(), '20160304' );

	// add plugin js
	add_filter( 'mce_external_plugins', 'columns__mce_external_plugins' );
	// add columns button to editor
	add_filter( 'mce_buttons', 'columns__mce_buttons' );
}

/**
 * Enqueue default columns styles.
 */
add_action( 'wp_enqueue_scripts', 'columns_enqueue_scripts' );
function columns_enqueue_scripts() {
	wp_enqueue_style( 'columns', COLUMNS_PLUGIN_URL . 'css/columns.min.css', array(), '20160304' );
}

/**
 * Add plugin js.
 *
 * @param array $plugin_array
 *
 * @return array
 */
function columns__mce_external_plugins( $plugin_array ) {
	$plugin_array['columns'] = COLUMNS_PLUGIN_URL . 'js/editor.min.js';

	return $plugin_array;
}

/**
 * Add columns button to editor's first button row.
 *
 * @param array $buttons
 *
 * @return array
 */
function columns__mce_buttons( $buttons ) {
	// add to the very left
	array_unshift( $buttons, 'columns' );

	return $buttons;
}

/**
 * t9n.
 */
add_action( 'after_setup_theme', 'columns__after_setup_theme' );
function columns__after_setup_theme() {
	load_theme_textdomain( 'columns', COLUMNS_PLUGIN_DIR . 'languages' );
}

/**
 * MCE translation.
 *
 * @param array $locales
 *
 * @return array
 */
add_filter( 'mce_external_languages', 'columns__mce_external_languages' );
function columns__mce_external_languages( $locales ) {
	$locales['columns'] = COLUMNS_PLUGIN_DIR . 'languages/mce.php';

	return $locales;
}

/**
 * Remove update notification (since this plugin isn't listed on https://wordpress.org/plugins/).
 */
add_filter( 'site_transient_update_plugins', 'columns__site_transient_update_plugins' );
function columns__site_transient_update_plugins( $value ) {
  $plugin_file = plugin_basename( __FILE__ );

  if ( isset( $value->response[ $plugin_file ] ) ) {
    unset( $value->response[ $plugin_file ] );
  }

  return $value;
}