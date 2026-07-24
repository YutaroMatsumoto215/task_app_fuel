<?php
/**
 * タスク一覧画面
 * - Knockout.jsで一覧を動的に制御（observableArray）
 * - fetch(Ajax)で完了状態を画面遷移なしに更新（非同期処理）
 * - 【XSS対策】json_encodeのHEXフラグでタグ混入を防止
 * - 【CSRF対策】AjaxリクエストにもCSRFトークンを付与
 */
?>

<div class="card" style="padding:14px 20px;">
	<div class="nav-bar">
		<strong>表示：</strong>
		<a href="<?php echo Uri::create('task?mode=all'); ?>"
		   class="btn btn-sm <?php echo $display_mode === 'all' ? 'btn-primary' : 'btn-secondary'; ?>">すべて</a>
		<a href="<?php echo Uri::create('task?mode=incomplete'); ?>"
		   class="btn btn-sm <?php echo $display_mode === 'incomplete' ? 'btn-primary' : 'btn-secondary'; ?>">未完了</a>
		<a href="<?php echo Uri::create('task?mode=complete'); ?>"
		   class="btn btn-sm <?php echo $display_mode === 'complete' ? 'btn-primary' : 'btn-secondary'; ?>">完了済み</a>

		<form method="get" action="<?php echo Uri::create('task'); ?>" style="display:flex;gap:8px;margin-left:auto;">
			<input type="text" name="tag_filter"
			       value="<?php echo htmlspecialchars($tag_filter, ENT_QUOTES, 'UTF-8'); ?>"
			       placeholder="タグで絞り込み" style="width:160px;">
			<button type="submit" class="btn btn-sm btn-primary">検索</button>
			<a href="<?php echo Uri::create('task?tag_filter='); ?>" class="btn btn-sm btn-secondary">解除</a>
		</form>
	</div>
</div>

<div id="task-app">

	<!-- ko if: tasks().length === 0 -->
	<div class="card" style="text-align:center; color:#999; padding:40px;">
		タスクがありません。<a href="<?php echo Uri::create('task/create'); ?>">タスクを追加</a>しましょう。
	</div>
	<!-- /ko -->

	<!-- ko foreach: tasks -->
	<div class="card" data-bind="css: { 'task-done': done() }">
		<div style="display:flex; align-items:flex-start; gap:12px;">

			<input type="checkbox"
			       data-bind="checked: doneChecked, event: { change: $root.toggleDone.bind($root, $data) }"
			       style="width:18px;height:18px;margin-top:3px;cursor:pointer;">

			<div style="flex:1;">
				<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
					<span class="task-title" data-bind="text: title, css: { 'done-text': done() }"></span>
					<!-- ko if: tag_name -->
					<span class="tag-badge" data-bind="text: '[' + tag_name() + ']'"></span>
					<!-- /ko -->
				</div>

				<div style="display:flex;gap:18px;margin-top:6px;font-size:13px;color:#888;">
					<!-- ko if: deadline -->
					<span>締切：<span data-bind="text: deadline" style="color:#e53935;font-weight:bold;"></span></span>
					<!-- /ko -->
					<!-- ko if: startdate -->
					<span>開始：<span data-bind="text: startdate"></span></span>
					<!-- /ko -->
				</div>

				<!-- ko if: memo -->
				<div style="margin-top:6px;font-size:13px;color:#555;" data-bind="text: memo"></div>
				<!-- /ko -->

				<!-- ko if: subtask_length() > 0 -->
				<div style="margin-top:8px;font-size:12px;color:#888;">
					サブタスク：<span data-bind="text: done_count"></span> / <span data-bind="text: subtask_length"></span> 完了
				</div>
				<!-- /ko -->

				<div style="margin-top:10px;">
					<button class="btn btn-sm btn-secondary"
					        data-bind="click: $root.toggleSubtask.bind($root, $data),
					                   text: showSubtask() ? 'サブタスクを閉じる' : 'サブタスクを表示'"></button>

					<div data-bind="visible: showSubtask" style="margin-top:10px;display:none;">
						<div data-bind="foreach: subtasks">
							<div style="display:flex;align-items:center;gap:8px;padding:5px 0;border-bottom:1px solid #f0f0f0;">
								<input type="checkbox"
								       data-bind="checked: doneChecked, event: { change: $root.toggleSubtaskDone.bind($root, $data) }"
								       style="cursor:pointer;">
								<span data-bind="text: title, css: { 'done-text': done() }"></span>
								<button class="btn btn-sm btn-danger" style="margin-left:auto;"
								        data-bind="click: $root.deleteSubtask.bind($root, $data)">削除</button>
							</div>
						</div>

						<div style="display:flex;gap:8px;margin-top:10px;">
							<input type="text" data-bind="value: newSubtaskTitle" placeholder="サブタスク名" style="flex:1;">
							<button class="btn btn-sm btn-primary"
							        data-bind="click: $root.addSubtask.bind($root, $data)">追加</button>
						</div>
					</div>
				</div>
			</div>

			<div style="display:flex;flex-direction:column;gap:6px;min-width:60px;">
				<a data-bind="attr: { href: '<?php echo Uri::create('task/edit'); ?>/' + id() }"
				   class="btn btn-sm btn-secondary">編集</a>
				<a data-bind="attr: { href: '<?php echo Uri::create('task/delete'); ?>/' + id() }"
				   class="btn btn-sm btn-danger"
				   onclick="return confirm('削除しますか？')">削除</a>
			</div>
		</div>
	</div>
	<!-- /ko -->

</div>

<style>
.task-done { opacity: 0.6; }
.done-text { text-decoration: line-through; color: #aaa; }
.task-title { font-size: 16px; font-weight: 600; }
.tag-badge { font-size: 11px; background: #e8f0fe; color: #1a73e8; padding: 2px 8px; border-radius: 12px; }
</style>

<script>
// 【XSS対策】HEXフラグでscriptタグの混入を防いでJSへ渡す
var initialTasks = <?php
$json_tasks = array();
foreach ($tasks as $t)
{
	$json_tasks[] = array(
		'id'             => (int) $t['id'],
		'title'          => $t['title'],
		'deadline'       => $t['deadline'],
		'startdate'      => $t['startdate'],
		'tag_name'       => $t['tag_name'],
		'memo'           => $t['memo'],
		'done'           => (int) $t['done'],
		'done_count'     => (int) $t['done_count'],
		'subtask_length' => (int) $t['subtask_length'],
	);
}
echo json_encode($json_tasks, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
?>;

// 【CSRF対策】AjaxにもCSRFトークンを付与する
var CSRF_TOKEN_NAME = '<?php echo Config::get('security.csrf_token_key', 'fuel_csrf_token'); ?>';
var CSRF_TOKEN      = '<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>';

var URL_TASK_DONE      = '<?php echo Uri::create('task/done'); ?>';
var URL_SUBTASK_INDEX  = '<?php echo Uri::create('subtask/index'); ?>';
var URL_SUBTASK_STORE  = '<?php echo Uri::create('subtask/store'); ?>';
var URL_SUBTASK_DONE   = '<?php echo Uri::create('subtask/done'); ?>';
var URL_SUBTASK_DELETE = '<?php echo Uri::create('subtask/delete'); ?>';

// ===== Knockout.js Model =====
function SubtaskModel(data) {
	this.id          = ko.observable(data.id);
	this.title       = ko.observable(data.title);
	this.done        = ko.observable(Number(data.done));
	this.doneChecked = ko.observable(Number(data.done) === 1);
}

function TaskModel(data) {
	this.id              = ko.observable(data.id);
	this.title           = ko.observable(data.title);
	this.deadline        = ko.observable(data.deadline || '');
	this.startdate       = ko.observable(data.startdate || '');
	this.tag_name        = ko.observable(data.tag_name || '');
	this.memo            = ko.observable(data.memo || '');
	this.done            = ko.observable(data.done);
	this.doneChecked     = ko.observable(data.done === 1);
	this.done_count      = ko.observable(data.done_count);
	this.subtask_length  = ko.observable(data.subtask_length);
	this.subtasks        = ko.observableArray([]);
	this.showSubtask     = ko.observable(false);
	this.newSubtaskTitle = ko.observable('');
}

// ===== ViewModel =====
function TaskAppViewModel() {
	var self = this;

	self.tasks = ko.observableArray(initialTasks.map(function(t) {
		return new TaskModel(t);
	}));

	// 完了状態のトグル（Ajax・画面遷移なし）
	self.toggleDone = function(task) {
		var newDone = task.doneChecked() ? 1 : 0;
		task.done(newDone);

		var fd = new FormData();
		fd.append(CSRF_TOKEN_NAME, CSRF_TOKEN);
		fd.append('done', newDone);

		fetch(URL_TASK_DONE + '/' + task.id(), { method: 'POST', body: fd })
			.then(function(r) { return r.json(); })
			.then(function(d) {
				if (d.status !== 'ok') {
					task.done(newDone === 1 ? 0 : 1);
					task.doneChecked(newDone !== 1);
					alert('更新に失敗しました。');
				}
			})
			.catch(function() {
				task.done(newDone === 1 ? 0 : 1);
				task.doneChecked(newDone !== 1);
			});
	};

	// サブタスクの開閉（初回だけAjaxで取得）
	self.toggleSubtask = function(task) {
		if ( ! task.showSubtask() && task.subtasks().length === 0) {
			fetch(URL_SUBTASK_INDEX + '/' + task.id())
				.then(function(r) { return r.json(); })
				.then(function(d) {
					if (d.status === 'ok') {
						task.subtasks(d.subtasks.map(function(s) {
							return new SubtaskModel(s);
						}));
					}
				});
		}
		task.showSubtask( ! task.showSubtask());
	};

	// サブタスクの追加（Ajax）
	self.addSubtask = function(task) {
		var title = task.newSubtaskTitle().trim();
		if ( ! title) return;

		var fd = new FormData();
		fd.append(CSRF_TOKEN_NAME, CSRF_TOKEN);
		fd.append('task_id', task.id());
		fd.append('title', title);

		fetch(URL_SUBTASK_STORE, { method: 'POST', body: fd })
			.then(function(r) { return r.json(); })
			.then(function(d) {
				if (d.status === 'ok') {
					task.subtasks.push(new SubtaskModel(d.subtask));
					task.subtask_length(task.subtask_length() + 1);
					task.newSubtaskTitle('');
				}
			});
	};

	// サブタスクの完了状態を更新（Ajax）
	self.toggleSubtaskDone = function(subtask) {
		var newDone = subtask.doneChecked() ? 1 : 0;
		subtask.done(newDone);

		var fd = new FormData();
		fd.append(CSRF_TOKEN_NAME, CSRF_TOKEN);
		fd.append('done', newDone);

		fetch(URL_SUBTASK_DONE + '/' + subtask.id(), { method: 'POST', body: fd })
			.catch(function() {
				subtask.done(newDone === 1 ? 0 : 1);
				subtask.doneChecked(newDone !== 1);
			});
	};

	// サブタスクの削除（Ajax）
	self.deleteSubtask = function(subtask) {
		if ( ! confirm('このサブタスクを削除しますか？')) return;

		fetch(URL_SUBTASK_DELETE + '/' + subtask.id())
			.then(function(r) { return r.json(); })
			.then(function(d) {
				if (d.status === 'ok') {
					self.tasks().forEach(function(task) {
						var removed = task.subtasks.remove(function(s) {
							return s.id() === subtask.id();
						});
						if (removed.length > 0) {
							task.subtask_length(Math.max(0, task.subtask_length() - 1));
						}
					});
				}
			});
	};
}

ko.applyBindings(new TaskAppViewModel(), document.getElementById('task-app'));
</script>