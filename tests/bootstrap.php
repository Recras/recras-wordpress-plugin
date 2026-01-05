<?php

$recras_tests_dir = getenv('WP_TESTS_DIR');
if (!$recras_tests_dir) {
	$recras_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $recras_tests_dir . '/includes/functions.php';
//require_once $recras_tests_dir . '/includes/testcase.php';
require_once $recras_tests_dir . '/includes/bootstrap.php';
require_once __DIR__ . '/WordPressUnitTestCase.php';
require dirname(__DIR__) . '/recras-wordpress-plugin.php';

update_option('recras_currency', '€');
update_option('recras_domain', 'demo.recras.nl');
update_option('recras_decimal', '.');
