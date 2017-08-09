<?php
/*
Plugin Name: GNA Cate List
Version: 0.9.8
Plugin URI: http://wordpress.org/plugins/gna-cate-list/
Author: Chris Dev
Author URI: http://webgna.com/
Description: [catelist] shortcode for any pages, posts or widgets
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: gna-cate-list
*/

if(!defined('ABSPATH'))exit; //Exit if accessed directly

include_once('gna-cate-list-core.php');

register_activation_hook(__FILE__, array('GNA_CateList', 'activate_handler'));		//activation hook
register_deactivation_hook(__FILE__, array('GNA_CateList', 'deactivate_handler'));	//deactivation hook
