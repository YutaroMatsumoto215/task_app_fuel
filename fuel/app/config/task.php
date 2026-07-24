<?php
/**
 * タスクアプリ独自設定ファイル（configのカスタマイズ）
 * FuelPHPの標準設定・ルーティング以外の独自値をここで管理する
 * 使い方： \Config::get('task.per_page')
 */

return array(
	// 一覧の表示件数
	'per_page' => 20,

	// 完了フラグの定義
	'done' => array(
		'incomplete' => 0,
		'complete'   => 1,
	),

	// 表示モード（Cookieに保存する値）
	'display_mode' => array(
		'all'        => 'all',
		'incomplete' => 'incomplete',
		'complete'   => 'complete',
	),

	// Cookieのキー名
	'cookie_key' => array(
		'display_mode' => 'task_display_mode',
	),

	// Cookieの有効期限（30日）
	'cookie_expiration' => 2592000,

	// 入力値の最大文字数
	'title_max_length' => 255,
	'memo_max_length'  => 255,
);