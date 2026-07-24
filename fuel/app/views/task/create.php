<?php
/**
 * タスク追加フォーム
 * 【CSRF対策】Form::csrf() でhiddenトークンを埋め込む
 */
?>

<div class="card">
	<h2 style="margin-bottom:18px;font-size:18px;color:#2d6cdf;">タスクを追加</h2>

	<form action="<?php echo Uri::create('task/store'); ?>" method="post">

		<?php echo Form::csrf(); ?>

		<div class="form-group">
			<label for="title">タスク名 <span style="color:red;">*</span></label>
			<input type="text" id="title" name="title" required maxlength="255" placeholder="タスク名を入力">
		</div>

		<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
			<div class="form-group">
				<label for="startdate">開始日</label>
				<input type="date" id="startdate" name="startdate">
			</div>
			<div class="form-group">
				<label for="deadline">締切日</label>
				<input type="date" id="deadline" name="deadline">
			</div>
		</div>

		<div class="form-group">
			<label for="tag_id">タグ</label>
			<select id="tag_id" name="tag_id">
				<option value="">-- タグなし --</option>
				<?php foreach ($tags as $tag): ?>
				<option value="<?php echo (int) $tag['id']; ?>">
					<?php echo htmlspecialchars($tag['name'], ENT_QUOTES, 'UTF-8'); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="form-group">
			<label for="memo">メモ</label>
			<textarea id="memo" name="memo" maxlength="255" placeholder="メモ（任意）"></textarea>
		</div>

		<div style="display:flex;gap:10px;justify-content:flex-end;">
			<a href="<?php echo Uri::create('task'); ?>" class="btn btn-secondary">キャンセル</a>
			<button type="submit" class="btn btn-primary">追加する</button>
		</div>

	</form>
</div>