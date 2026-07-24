<?php
/**
 * サブタスクモデル
 * 【1:n関係】task(1) ←→ sub_task(n)、sub_task.task_id が外部キー
 * 【正規化】サブタスクをtaskテーブルに繰り返し持たせず別テーブルに分離している
 */

class Model_Subtask extends \Model
{
	/**
	 * task_idに紐づくサブタスク一覧を取得
	 */
	public static function get_by_task_id($task_id)
	{
		return \DB::select()
			->from('sub_task')
			->where('task_id', $task_id)
			->where('deleted_at', null)
			->order_by('id', 'asc')
			->execute()
			->as_array();
	}

	/**
	 * IDでサブタスクを1件取得
	 */
	public static function find_by_id($id)
	{
		$result = \DB::select()
			->from('sub_task')
			->where('id', $id)
			->where('deleted_at', null)
			->execute()
			->as_array();

		return isset($result[0]) ? $result[0] : null;
	}

	/**
	 * サブタスクの追加
	 */
	public static function add($task_id, $title)
	{
		$result = \DB::insert('sub_task')->set(array(
			'task_id'    => $task_id,
			'title'      => $title,
			'done'       => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		))->execute();

		return $result[0];
	}

	/**
	 * 完了状態の更新
	 */
	public static function update_done($id, $done)
	{
		\DB::update('sub_task')->set(array(
			'done'       => $done,
			'updated_at' => date('Y-m-d H:i:s'),
		))->where('id', $id)->execute();
	}

	/**
	 * 論理削除
	 */
	public static function soft_delete($id)
	{
		\DB::update('sub_task')->set(array(
			'deleted_at' => date('Y-m-d H:i:s'),
		))->where('id', $id)->execute();
	}
}