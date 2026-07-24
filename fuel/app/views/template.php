<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'タスク管理'; ?></title>

<!-- Knockout.js（ローカルに配置） -->
<script src="/assets/js/knockout.min.js"></script>

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
	font-family: 'Helvetica Neue', Arial, sans-serif;
	background: #f0f2f5;
	color: #333;
	line-height: 1.6;
}

a { color: #2d6cdf; text-decoration: none; }
a:hover { text-decoration: underline; }

.site-header {
	background: #2d6cdf;
	color: #fff;
	padding: 14px 24px;
	display: flex;
	align-items: center;
	box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.site-header h1 { font-size: 20px; }
.site-header a { color: #fff; }

.container { max-width: 900px; margin: 24px auto; padding: 0 16px; }

.card {
	background: #fff;
	border-radius: 10px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.08);
	padding: 20px 24px;
	margin-bottom: 18px;
}

.form-group { margin-bottom: 12px; }
.form-group label { display: block; font-size: 13px; color: #666; margin-bottom: 4px; }

input[type="text"], input[type="date"], textarea, select {
	width: 100%;
	padding: 8px 12px;
	border: 1px solid #ddd;
	border-radius: 6px;
	font-size: 14px;
}
input:focus, textarea:focus, select:focus {
	outline: none;
	border-color: #2d6cdf;
	box-shadow: 0 0 0 2px rgba(45,108,223,0.15);
}
textarea { resize: vertical; min-height: 70px; }

.btn {
	display: inline-block;
	padding: 8px 18px;
	border-radius: 6px;
	border: none;
	font-size: 14px;
	cursor: pointer;
	font-weight: 600;
	text-align: center;
}
.btn-primary { background: #2d6cdf; color: #fff; }
.btn-primary:hover { background: #1e4fbf; text-decoration: none; }
.btn-danger { background: #e53935; color: #fff; }
.btn-danger:hover { background: #b71c1c; text-decoration: none; }
.btn-secondary { background: #eee; color: #333; }
.btn-secondary:hover { background: #ddd; text-decoration: none; }
.btn-sm { padding: 4px 10px; font-size: 12px; }

.flash-success {
	background: #e8f5e9; color: #2e7d32;
	border: 1px solid #a5d6a7; border-radius: 6px;
	padding: 10px 16px; margin-bottom: 16px;
}
.flash-error {
	background: #ffebee; color: #c62828;
	border: 1px solid #ef9a9a; border-radius: 6px;
	padding: 10px 16px; margin-bottom: 16px;
}

.nav-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
</style>
</head>
<body>

<header class="site-header">
	<h1><a href="<?php echo Uri::create('task'); ?>">タスク管理</a></h1>
	<nav style="margin-left:auto; display:flex; gap:16px;">
		<a href="<?php echo Uri::create('task'); ?>">一覧</a>
		<a href="<?php echo Uri::create('task/create'); ?>">＋ タスク追加</a>
	</nav>
</header>

<div class="container">

	<?php if ($msg = Session::get_flash('success')): ?>
		<div class="flash-success"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></div>
	<?php endif; ?>

	<?php if ($err = Session::get_flash('error')): ?>
		<div class="flash-error"><?php echo htmlspecialchars(is_array($err) ? implode(' / ', $err) : $err, ENT_QUOTES, 'UTF-8'); ?></div>
	<?php endif; ?>

	<?php echo $content; ?>

</div>

</body>
</html>