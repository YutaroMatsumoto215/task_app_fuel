<?php
/**
 * タグモデル
 * タグ情報をtaskテーブルから分離することで正規化している
 */

class Model_Tag extends \Model
{
	/**
	 * タグ一覧を取得
	 */
	public static function get_all()
	{
		return \DB::select()
			->from('tag')
			->order_by('id', 'asc')
			->execute()
			->as_array();
	}
}