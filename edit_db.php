<?php
global $smcFunc;

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
   die('Admin priveleges required.');
db_extend('packages');

// Build the followers table:
$columns = array(
	array(
		'name' => 'id_member',
		'type' => 'int',
		'size' => 8,
		'unsigned' => true,
	),
	array(
		'name' => 'follows',
		'type' => 'int',
		'size' => 8,
		'unsigned' => true,
	),
);
$indexes = array(
	array(
		'columns' => array('id_member')
	),
	array(
		'columns' => array('follows')
	),
);
$smcFunc['db_create_table']('{db_prefix}followers', $columns, $indexes, array(), 'update_remove');

?>