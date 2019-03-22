<?php

$_tests_dir = getenv('WP_TESTS_DIR');
if (!$_tests_dir) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname(__DIR__) . '/recras-wordpress-plugin.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

require $_tests_dir . '/includes/bootstrap.php';

update_option('recras_currency', '€');
update_option('recras_subdomain', 'demo');
update_option('recras_decimal', '.');
