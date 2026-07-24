<?php
/**
 * ルーティング設定
 */

return array(
	'_root_'  => 'task/index',
	'_404_'   => 'welcome/404',

	// タスク
	'task'               => 'task/index',
	'task/create'        => 'task/create',
	'task/store'         => 'task/store',
	'task/edit/(:num)'   => 'task/edit/$1',
	'task/update/(:num)' => 'task/update/$1',
	'task/delete/(:num)' => 'task/delete/$1',
	'task/done/(:num)'   => 'task/done/$1',

	// サブタスク（Ajax API）
	'subtask/index/(:num)'  => 'subtask/index/$1',
	'subtask/store'         => 'subtask/store',
	'subtask/done/(:num)'   => 'subtask/done/$1',
	'subtask/delete/(:num)' => 'subtask/delete/$1',
);