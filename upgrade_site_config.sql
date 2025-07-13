-- 网站配置管理数据库升级脚本
USE homepage_db;

-- 创建网站配置表
CREATE TABLE IF NOT EXISTS site_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) NOT NULL UNIQUE COMMENT '配置键名',
    config_value TEXT NOT NULL COMMENT '配置值',
    config_type ENUM('text', 'url', 'json', 'html') DEFAULT 'text' COMMENT '配置类型',
    description VARCHAR(255) COMMENT '配置描述',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否启用',
    sort_order INT DEFAULT 0 COMMENT '排序',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 插入默认的网站配置
INSERT INTO site_config (config_key, config_value, config_type, description, sort_order) VALUES 
-- 联系方式配置
('email', 'your.email@example.com', 'text', '邮箱地址', 1),
('github_url', 'https://github.com/your-username', 'url', 'GitHub个人主页链接', 2),
('wechat_qr', './static/img/wechat.jpg', 'text', '微信二维码图片路径', 3),
('google_scholar', 'https://scholar.google.com/', 'url', 'Google Scholar链接', 4),
('orcid', 'https://orcid.org/0000-0000-0000-0000', 'url', 'ORCID链接', 5),

-- 首页左侧栏配置
('location', 'China-Hainan', 'text', '地理位置', 10),
('education_info', 'MUC & CityU MAC', 'text', '教育信息', 11),
('personal_tags', '大二牲,AI,LLM,麦门,爱睡觉', 'text', '个人标签（逗号分隔）', 12),

-- 首页时间线配置
('timeline_events', '转世成为异世界美男|2004.10;发现异界没有五险一金|2010.01;被精灵公主倒追导致王国通货膨胀|2018.11;教兽人用PPT汇报工作|2025.11;异界黑马学院开业啦！|2030.06;35岁被学院优化|2039.04;和史莱姆一起投简历|- now', 'text', '时间线事件（格式：事件|日期;事件|日期）', 20),

-- 首页欢迎语配置
('welcome_name', 'Your Name', 'text', '首页欢迎语中的姓名', 30),
('welcome_description1', '😊 Jack of all trades, Master of None', 'text', '首页欢迎描述1', 31),
('welcome_description2', '🤗 Life was like a box of Chocolate, you Never know what you''re gonna get.', 'text', '首页欢迎描述2', 32),

-- 每日名言配置
('daily_quotes', '生活就像一盒巧克力，你永远不知道下一颗是什么味道。|阿甘正传;知识就是力量。|培根;学而不思则罔，思而不学则殆。|孔子;人生苦短，我用Python。|Python社区;代码改变世界。|程序员', 'text', '每日名言（格式：名言|作者;名言|作者）', 40),

-- 其他配置
('site_title', 'Your Site Title', 'text', '网站标题', 50),
('footer_text', 'Your Name &copy; 2025 | ID: 12345678', 'text', '页脚文本', 51);

SELECT 'Site configuration management database upgrade completed successfully' AS message; 