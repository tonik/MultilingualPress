<?php # -*- coding: utf-8 -*-
/**
 * Plugin Name: MultilingualPress (Modified)
 * Plugin URI:  https://wordpress.org/plugins/multilingual-press/
 * Description: IMPORTNAT! A modified fork that resolves https://github.com/inpsyde/MultilingualPress/issues/304 bug. Replace with orginal plugin after this gets fixed.
 * Author:      Inpsyde GmbH
 * Author URI:  https://inpsyde.com
 * Version:     2.11.1
 * Text Domain: multilingual-press
 * Domain Path: /src/languages
 * License:     GPLv3
 * Network:     true
 */

defined( 'ABSPATH' ) || die();

define( 'MLP_PLUGIN_FILE', __FILE__ );

require dirname( __FILE__ ) . '/src/multilingual-press.php';
