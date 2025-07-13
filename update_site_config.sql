-- 更新网站配置数据库
-- 填充用户原有的个人信息

USE homepage_db;

-- 清空现有的site_config表数据
DELETE FROM site_config;

-- 插入用户原有的个人信息配置
INSERT INTO site_config (config_key, config_value, config_type, description, sort_order) VALUES
-- 联系方式配置
('email', 'xieyizhe66@gmail.com', 'text', '邮箱地址', 1),
('github_url', 'https://github.com/YizheXie', 'url', 'GitHub个人主页链接', 2),
('wechat_qr', './static/img/wechat.jpg', 'text', '微信二维码图片路径', 3),
('google_scholar', 'https://scholar.google.com/', 'url', 'Google Scholar链接', 4),
('orcid', 'https://orcid.org/0009-0009-8321-5982', 'url', 'ORCID链接', 5),

-- 首页左侧栏配置
('location', 'China-Hainan', 'text', '地理位置', 10),
('education_info', 'MUC & CityU MAC', 'text', '教育信息', 11),
('personal_tags', '大二牲,AI,LLM,麦门,爱睡觉', 'text', '个人标签（逗号分隔）', 12),

-- 首页时间线配置
('timeline_events', '转世成为异世界美男|2004.10;发现异界没有五险一金|2010.01;被精灵公主倒追导致王国通货膨胀|2018.11;教兽人用PPT汇报工作|2025.11;异界黑马学院开业啦！|2030.06;35岁被学院优化|2039.04;和史莱姆一起投简历|- now', 'text', '时间线事件（格式：事件|日期;事件|日期）', 20),

-- 首页欢迎语配置
('welcome_name', 'Yizhe Xie', 'text', '首页欢迎语中的姓名', 30),
('welcome_description1', '😊 Jack of all trades, <span class="purpleText">Master of None</span>', 'html', '首页欢迎描述1', 31),
('welcome_description2', '🤗 Life was like a box of <span class="purpleText">Chocolate</span> , you <span class="purpleText">Never</span> know what you''re gonna get.', 'html', '首页欢迎描述2', 32),

-- 每日名言配置
('daily_quotes', '生活就像骑自行车，要保持平衡就得不断前进。;我不是一个特别有天赋的人，只是对问题特别好奇而已。;学习是一种态度，不是能力。;未来完全取决于你现在的努力。;人生如同写代码，看似结束的地方，其实是新的起点。;科技的目的是为人类提供更好的生活方式。;AI不是为了取代人类，而是为了增强人类的能力。;编程不仅是编写代码，更是在设计思想。;不要害怕犯错，害怕的是犯了同样的错。;每一个成功者都有一个开始。勇于开始，才能找到成功的路。;世界上只有一种真正的英雄主义，那就是在认清生活真相后依然热爱生活。;没有伞的孩子，必须努力奔跑。;当你感到悲伤时，最好是去学些什么东西。学习会使你永远立于不败之地。;如果你能梦想到，你就能实现它。;我们的征途是星辰大海。', 'text', '每日名言（分号分隔）', 40),

-- 其他配置
('site_title', 'Yizhe Xie - Homepage', 'text', '网站标题', 50),
('footer_text', 'Yizhe Xie &copy; 2025 | ID: 23160151', 'text', '页脚文本', 51);

-- 显示更新结果
SELECT 'Site configuration updated successfully with user''s original information' AS message;
SELECT COUNT(*) AS total_configs FROM site_config; 