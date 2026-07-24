-- タスク管理アプリ テーブル作成SQL
-- 実行前に task_app データベースを選択してください

-- tagテーブル（正規化：タグ情報を分離）
CREATE TABLE tag (
    id         INT(11)      NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    created_at DATETIME     NULL,
    updated_at DATETIME     NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- taskテーブル（親：1側）
CREATE TABLE task (
    id             INT(11)      NOT NULL AUTO_INCREMENT,
    title          VARCHAR(255) NOT NULL,
    startdate      DATE         NULL,
    deadline       DATE         NULL,
    tag_id         INT(11)      NULL,
    memo           VARCHAR(255) NULL,
    done           INT(1)       NOT NULL DEFAULT 0,
    done_count     INT(2)       NOT NULL DEFAULT 0,
    subtask_length INT(11)      NOT NULL DEFAULT 0,
    created_at     DATETIME     NULL,
    updated_at     DATETIME     NULL,
    deleted_at     DATETIME     NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- sub_taskテーブル（子：n側）task.id = sub_task.task_id で1:n関係
CREATE TABLE sub_task (
    id         INT(11)      NOT NULL AUTO_INCREMENT,
    task_id    INT(11)      NOT NULL,
    title      VARCHAR(255) NULL,
    done       INT(1)       NOT NULL DEFAULT 0,
    created_at DATETIME     NULL,
    updated_at DATETIME     NULL,
    deleted_at DATETIME     NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (task_id) REFERENCES task(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- サンプルタグ
INSERT INTO tag (name, created_at, updated_at) VALUES
('仕事', NOW(), NOW()),
('プライベート', NOW(), NOW()),
('勉強', NOW(), NOW()),
('買い物', NOW(), NOW());