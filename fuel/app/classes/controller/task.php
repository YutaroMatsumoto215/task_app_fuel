<?php
/**
 * タスクのCRUDコントローラ
 * - DBとのやり取りはすべてModelに委譲する（Controllerには書かない）
 * - post_XXX と書くことでPOSTリクエストの処理を切り分けている
 */

class Controller_Task extends Controller_Base
{
	/**
	 * 【Read】タスク一覧
	 */
	public function action_index()
	{
		// 【Cookie】表示モードの切替をCookieに保存（30日間保持）
		if (Input::get('mode'))
		{
			$mode = Security::xss_clean(Input::get('mode'));
			Cookie::set(
				Config::get('task.cookie_key.display_mode'),
				$mode,
				Config::get('task.cookie_expiration')
			);
			$this->template->display_mode = $mode;
		}

		// 【Session】タグフィルタをSessionに保存
		if (Input::get('tag_filter') !== null)
		{
			$tag_filter = Security::xss_clean(Input::get('tag_filter'));
			Session::set('tag_filter', $tag_filter);
			$this->template->tag_filter = $tag_filter;
		}

		$tag_filter   = Session::get('tag_filter', '');
		$display_mode = Cookie::get(
			Config::get('task.cookie_key.display_mode'),
			Config::get('task.display_mode.all')
		);
		$per_page = Config::get('task.per_page');

		$tasks = Model_Task::get_list($tag_filter, $display_mode, $per_page);
		$tags  = Model_Tag::get_all();

		$this->template->title   = 'タスク一覧';
		$this->template->content = View::forge('task/index', array(
			'tasks'        => $tasks,
			'tags'         => $tags,
			'display_mode' => $display_mode,
			'tag_filter'   => $tag_filter,
			'csrf_token'   => Security::fetch_token(),
		), false);
	}

	/**
	 * 【Create】タスク追加フォームの表示
	 */
	public function action_create()
	{
		$this->template->title   = 'タスク追加';
		$this->template->content = View::forge('task/create', array(
			'tags' => Model_Tag::get_all(),
		), false);
	}

	/**
	 * 【Create】タスクの登録処理（POST）
	 */
	public function post_store()
	{
		// バリデーション（問題があれば早期リターン）
		$val = Validation::forge();
		$val->add('title', 'タスク名')
			->add_rule('required')
			->add_rule('max_length', Config::get('task.title_max_length'));

		if ( ! $val->run())
		{
			Session::set_flash('error', 'タスク名は必須です（255文字以内）。');
			Response::redirect('task/create');
		}

		// 【XSS対策】入力値をクリーンにしてからModelへ渡す
		Model_Task::add(array(
			'title'     => Security::xss_clean(Input::post('title', '')),
			'startdate' => Input::post('startdate') ?: null,
			'deadline'  => Input::post('deadline') ?: null,
			'tag_id'    => Input::post('tag_id') ?: null,
			'memo'      => Security::xss_clean(Input::post('memo', '')),
		));

		Session::set_flash('success', 'タスクを追加しました。');
		Response::redirect('task');
	}

	/**
	 * 【Update】タスク編集フォームの表示
	 */
	public function action_edit($id = null)
	{
		$task = Model_Task::find_by_id((int) $id);

		if ( ! $task)
		{
			throw new HttpNotFoundException();
		}

		$this->template->title   = 'タスク編集';
		$this->template->content = View::forge('task/edit', array(
			'task' => $task,
			'tags' => Model_Tag::get_all(),
		), false);
	}

	/**
	 * 【Update】タスクの更新処理（POST）
	 */
	public function post_update($id = null)
	{
		$val = Validation::forge();
		$val->add('title', 'タスク名')
			->add_rule('required')
			->add_rule('max_length', Config::get('task.title_max_length'));

		if ( ! $val->run())
		{
			Session::set_flash('error', 'タスク名は必須です（255文字以内）。');
			Response::redirect('task/edit/'.(int) $id);
		}

		Model_Task::update_by_id((int) $id, array(
			'title'     => Security::xss_clean(Input::post('title', '')),
			'startdate' => Input::post('startdate') ?: null,
			'deadline'  => Input::post('deadline') ?: null,
			'tag_id'    => Input::post('tag_id') ?: null,
			'memo'      => Security::xss_clean(Input::post('memo', '')),
		));

		Session::set_flash('success', 'タスクを更新しました。');
		Response::redirect('task');
	}

	/**
	 * 【Delete】タスクの削除（論理削除）
	 */
	public function action_delete($id = null)
	{
		Model_Task::soft_delete((int) $id);

		Session::set_flash('success', 'タスクを削除しました。');
		Response::redirect('task');
	}

	/**
	 * 完了状態の更新（Knockout.jsからAjaxで呼ばれる／非同期処理）
	 */
	public function post_done($id = null)
	{
		$done = (int) Input::post('done', 0);

		Model_Task::update_done((int) $id, $done);

		// JSONを返すのでテンプレートは使わない
		$this->template = null;

		return Response::forge(
			json_encode(array('status' => 'ok', 'done' => $done)),
			200,
			array('Content-Type' => 'application/json')
		);
	}
}