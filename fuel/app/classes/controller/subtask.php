<?php
/**
 * サブタスクのCRUD（Ajax JSON API）
 * Knockout.jsからfetchで呼び出される
 */

class Controller_Subtask extends Controller
{
	public function before()
	{
		parent::before();

		Config::load('task', true);

		// 【CSRF対策】POST時はトークンを検証
		if (Input::method() === 'POST' and ! Security::check_token())
		{
			throw new HttpInvalidInputException('CSRFトークンが不正です。');
		}
	}

	/**
	 * 【Read】task_idに紐づくサブタスク一覧を返す（GET）
	 */
	public function action_index($task_id = null)
	{
		$subtasks = Model_Subtask::get_by_task_id((int) $task_id);

		return $this->json_response(array(
			'status'   => 'ok',
			'subtasks' => $subtasks,
		));
	}

	/**
	 * 【Create】サブタスクの追加（POST）
	 */
	public function post_store()
	{
		$task_id = (int) Input::post('task_id', 0);
		$title   = Security::xss_clean(Input::post('title', ''));

		// 早期リターン
		if ($title === '' or $task_id === 0)
		{
			return $this->json_response(array(
				'status'  => 'error',
				'message' => '入力値が不正です。',
			), 400);
		}

		$id = Model_Subtask::add($task_id, $title);
		Model_Task::update_subtask_length($task_id);

		return $this->json_response(array(
			'status'  => 'ok',
			'subtask' => array(
				'id'      => $id,
				'task_id' => $task_id,
				'title'   => $title,
				'done'    => 0,
			),
		));
	}

	/**
	 * 【Update】サブタスクの完了状態を更新（POST）
	 */
	public function post_done($id = null)
	{
		$done = (int) Input::post('done', 0);

		Model_Subtask::update_done((int) $id, $done);

		// 親タスクの完了数も再集計（1:n関係の集計）
		$subtask = Model_Subtask::find_by_id((int) $id);
		if ($subtask)
		{
			Model_Task::update_done_count((int) $subtask['task_id']);
		}

		return $this->json_response(array('status' => 'ok', 'done' => $done));
	}

	/**
	 * 【Delete】サブタスクの削除（論理削除）
	 */
	public function action_delete($id = null)
	{
		$subtask = Model_Subtask::find_by_id((int) $id);

		if ($subtask)
		{
			Model_Subtask::soft_delete((int) $id);
			Model_Task::update_subtask_length((int) $subtask['task_id']);
		}

		return $this->json_response(array('status' => 'ok'));
	}

	/**
	 * JSONレスポンスを返す共通処理
	 */
	private function json_response(array $data, $status = 200)
	{
		return Response::forge(
			json_encode($data, JSON_UNESCAPED_UNICODE),
			$status,
			array('Content-Type' => 'application/json')
		);
	}
}