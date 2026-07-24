<?php
/**
 * 全コントローラの基底クラス
 */

class Controller_Base extends Controller_Template
{
	public $template = 'template';

	/**
	 * beforeメソッド：全アクションの実行前に共通処理を行う
	 */
	public function before()
	{
		parent::before();

		// 独自設定ファイル（task.php）を読み込む
		Config::load('task', true);

		// 【セキュリティ：CSRF対策】POSTリクエスト時はトークンを検証する
		if (Input::method() === 'POST')
		{
			if ( ! Security::check_token())
			{
				throw new HttpInvalidInputException('CSRFトークンが不正です。');
			}
		}

		// 【Cookie】表示モードを読み込む
		$display_mode = Cookie::get(
			Config::get('task.cookie_key.display_mode'),
			Config::get('task.display_mode.all')
		);

		// 【Session】選択中のタグフィルタを読み込む
		$tag_filter = Session::get('tag_filter', '');

		// テンプレートへ共通変数をセット
		$this->template->display_mode = $display_mode;
		$this->template->tag_filter   = $tag_filter;
		$this->template->csrf_token   = Security::fetch_token();
		$this->template->title        = 'タスク管理';
		$this->template->content      = '';
	}
}