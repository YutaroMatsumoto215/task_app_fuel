<?php
/**
 * タスクモデル
 * 【DBクラス】FuelPHPのDBクラス（クエリビルダ）を使用
 * 　→ 値はプレースホルダでバインドされるためSQLインジェクション対策になる
 */

class Model_Task extends \Model
{
	/**
	 * タスク一覧の取得
	 * タグフィルタ・表示モードに応じてクエリを組み立てる
	 */
	public static function get_list($tag_filter = '', $display_mode = 'all', $per_page = 20)
	{
		$query = \DB::select('task.*', array('tag.name', 'tag_name'))
			->from('task')
			->join('tag', 'LEFT')->on('task.tag_id', '=', 'tag.id')
			->where('task.deleted_at', null)
			->order_by('task.id', 'desc')
			->limit($per_page);

		// タグ名で絞り込み
		if ($tag_filter !== '')
		{
			$query->where('tag.name', 'LIKE', '%'.$tag_filter.'%');
		}

		// 表示モードで絞り込み（独自configの値を使用）
		$done = \Config::get('task.done');

		if ($display_mode === 'incomplete')
		{
			$query->where('task.done', $done['incomplete']);
		}
		elseif ($display_mode === 'complete')
		{
			$query->where('task.done', $done['complete']);
		}

		return $query->execute()->as_array();
	}

	/**
	 * IDでタスクを1件取得
	 */
	public static function find_by_id($id)
	{
		$result = \DB::select('task.*', array('tag.name', 'tag_name'))
			->from('task')
			->join('tag', 'LEFT')->on('task.tag_id', '=', 'tag.id')
			->where('task.id', $id)
			->where('task.deleted_at', null)
			->execute()
			->as_array();

		return isset($result[0]) ? $result[0] : null;
	}

	/**
	 * タスクの追加（DB::insert）
	 */
	public static function add(array $data)
	{
		$result = \DB::insert('task')->set(array(
			'title'      => $data['title'],
			'startdate'  => $data['startdate'],
			'deadline'   => $data['deadline'],
			'tag_id'     => $data['tag_id'],
			'memo'       => $data['memo'],
			'done'       => 0,
			'done_count' => 0,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		))->execute();

		return $result[0];
	}

	/**
	 * タスクの更新（DB::update）
	 */
	public static function update_by_id($id, array $data)
	{
		\DB::update('task')->set(array(
			'title'      => $data['title'],
			'startdate'  => $data['startdate'],
			'deadline'   => $data['deadline'],
			'tag_id'     => $data['tag_id'],
			'memo'       => $data['memo'],
			'updated_at' => date('Y-m-d H:i:s'),
		))->where('id', $id)->execute();
	}

	/**
	 * 論理削除（deleted_atをセットすると一覧に表示されなくなる）
	 */
	public static function soft_delete($id)
	{
		\DB::update('task')->set(array(
			'deleted_at' => date('Y-m-d H:i:s'),
		))->where('id', $id)->execute();
	}

	/**
	 * 完了状態の更新（Ajaxから呼ばれる）
	 */
	public static function update_done($id, $done)
	{
		\DB::update('task')->set(array(
			'done'       => $done,
			'updated_at' => date('Y-m-d H:i:s'),
		))->where('id', $id)->execute();
	}

	/**
	 * サブタスクの完了数を再集計して更新
	 */
	public static function update_done_count($task_id)
	{
		$count = \DB::select(\DB::expr('COUNT(*) as cnt'))
			->from('sub_task')
			->where('task_id', $task_id)
			->where('done', 1)
			->where('deleted_at', null)
			->execute()
			->get('cnt');

		\DB::update('task')->set(array(
			'done_count' => (int) $count,
			'updated_at' => date('Y-m-d H:i:s'),
		))->where('id', $task_id)->execute();
	}

	/**
	 * サブタスクの総数を再集計して更新
	 */
	public static function update_subtask_length($task_id)
	{
		$count = \DB::select(\DB::expr('COUNT(*) as cnt'))
			->from('sub_task')
			->where('task_id', $task_id)
			->where('deleted_at', null)
			->execute()
			->get('cnt');

		\DB::update('task')->set(array(
			'subtask_length' => (int) $count,
			'updated_at'     => date('Y-m-d H:i:s'),
		))->where('id', $task_id)->execute();
	}
}