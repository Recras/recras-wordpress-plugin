<?php
/*
Plugin Name: Recras WordPress Plugin
Plugin URI: https://www.recras.nl/
Version: 6.2.2
Description: Easily integrate your Recras data into your own site
Requires at least: 6.4
Requires PHP: 7.4.0
Author: Recras
Author URI: https://www.recras.nl/
Text Domain: recras
Domain Path: /lang
*/

if (!function_exists('add_action')) {
    die('You cannot run this file directly.');
}

require_once(__DIR__ . '/vendor/autoload.php');
$recrasPlugin = new \Recras\Plugin();
