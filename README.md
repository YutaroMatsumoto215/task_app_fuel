# タスク管理アプリ（FuelPHP）

FuelPHP 1.8.2 で作成した CRUD 機能を持つタスク管理アプリケーション。

## 主な機能
- タスクの追加・一覧・編集・削除（CRUD）
- サブタスク管理（task と 1:n 関係）
- タグによる絞り込み（Session 使用）
- 表示モード切替：全件／未完了／完了（Cookie 使用）
- 完了チェック・サブタスク操作を Ajax で非同期更新（Knockout.js）

## 技術要素
- サーバサイド：PHP / FuelPHP 1.8.2
- フロントエンド：Knockout.js（非同期 UI）
- データベース：MySQL（MariaDB）
- セキュリティ対策：XSS（htmlspecialchars）、CSRF（Form::csrf）、SQLインジェクション（DBクラスのプレースホルダ）

## セットアップ
1. `sql/create_tables.sql` を実行してテーブルを作成
2. `fuel/app/config/development/db.php` の接続情報を環境に合わせる
3. `php oil server` または `php -S localhost:8000 -t public` で起動
4. ブラウザで `http://localhost:8000` にアクセス
