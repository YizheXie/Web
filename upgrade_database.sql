-- 数据库升级脚本
-- 添加评论表的网站字段

USE homepage_db;

-- 检查字段是否存在，如果不存在则添加
ALTER TABLE comments ADD COLUMN IF NOT EXISTS author_website VARCHAR(255) DEFAULT NULL;

-- 或者使用这种方式（如果上面的语法不支持）
-- ALTER TABLE comments ADD COLUMN author_website VARCHAR(255) DEFAULT NULL;

SELECT 'Database upgrade completed successfully' AS message; 