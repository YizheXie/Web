-- 评论系统升级脚本
-- 1. 将 author_website 字段重命名为 author_address
ALTER TABLE comments CHANGE COLUMN author_website author_address VARCHAR(255) DEFAULT NULL;

-- 2. 添加回复功能支持
ALTER TABLE comments ADD COLUMN parent_id INT DEFAULT NULL;
ALTER TABLE comments ADD COLUMN reply_count INT DEFAULT 0;
ALTER TABLE comments ADD CONSTRAINT fk_comments_parent FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE;

-- 3. 添加索引优化查询性能
ALTER TABLE comments ADD INDEX idx_article_id (article_id);
ALTER TABLE comments ADD INDEX idx_parent_id (parent_id);
ALTER TABLE comments ADD INDEX idx_status (status);

-- 4. 更新现有数据（如果有）
UPDATE comments SET reply_count = 0 WHERE reply_count IS NULL;

-- 完成升级
SELECT 'Comments system upgrade completed!' as message; 