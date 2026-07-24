<?php
/**
 * DB接続設定
 * XAMPP環境（MySQL）
 */

return array(
	'default' => array(
		'type'        => 'pdo',
		'connection'  => array(
			'dsn'        => 'mysql:host=localhost;port=3307;dbname=task_app;charset=utf8mb4',
			'username'   => 'root',
			'password'   => '',
			'persistent' => false,
			'compress'   => false,
		),
		'identifier'   => '`',
		'table_prefix' => '',
		'charset'      => 'utf8mb4',
		'collation'    => 'utf8mb4_unicode_ci',
		'enable_cache' => true,
		'profiling'    => false,
		'readonly'     => false,
	),
);