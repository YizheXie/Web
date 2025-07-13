-- 关于我信息管理数据库升级脚本
USE homepage_db;

-- 创建关于我信息表
CREATE TABLE IF NOT EXISTS about_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) NOT NULL UNIQUE COMMENT '信息区块名称',
    section_key VARCHAR(100) NOT NULL UNIQUE COMMENT '信息区块键名',
    content TEXT NOT NULL COMMENT '内容',
    content_type ENUM('text', 'html', 'json') DEFAULT 'text' COMMENT '内容类型',
    sort_order INT DEFAULT 0 COMMENT '排序',
    is_active BOOLEAN DEFAULT TRUE COMMENT '是否启用',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 插入默认的关于我信息
INSERT INTO about_info (section_name, section_key, content, content_type, sort_order) VALUES 
('个人姓名', 'name', '谢奕哲 (Yizhe Xie)', 'text', 1),
('个人职位', 'title', '摸鱼躺平学博士在读', 'text', 2),
('个人简介', 'bio', '嗨，我是谢奕哲🤗，中央民族大学与澳门城市大学数据科学与大数据技术专业的大二牲。我的研究兴趣包括大模型安全、多智能体和深度强化学习。我喜欢业余时间在知乎、CSDN和GitHub等平台分享见解或捣鼓些奇奇怪怪的项目。', 'text', 3),
('技能标签', 'skills', '深度学习,强化学习,大型语言模型,多智能体学习,Python,数据分析,机器学习,LLM安全,LLM-MA', 'text', 4),
('教育经历', 'education', '2023 - 至今|中央民族大学 & 澳门城市大学|数据科学与大数据技术专业双学位项目。研究方向包括大模型安全、大模型多智能体等。', 'text', 5),
('科研项目', 'publications', 'Dynamic Privacy Protection with Large Language Model in Social Networks|Yizhe Xie, Congcong Zhu*, Xinyue Zhang, Xiangyu Hu and Xuan Liu|ICA3PP 2024|https://link.springer.com/chapter/10.1007/978-981-96-1545-2_14;Who''s the Mole? Modeling and Detecting Intention-Hiding Malicious Agents in LLM-Based Multi-Agent Systems|Yizhe Xie, Congcong Zhu, Xinyue Zhang, Minghao Wang, Chi Liu, Minglu Zhu, Tianqing Zhu|preprint (arXiv 2025)|https://arxiv.org/abs/2507.04724', 'text', 6),
('荣誉奖项', 'awards', '2023-2024|国家奖学金|获得2023至2024学年度本科生国家奖学金。', 'text', 7),
('联系方式', 'contact', 'xieyizhe66@gmail.com', 'text', 8),
('头像路径', 'avatar', './static/img/selfie.jpg', 'text', 9);

SELECT 'About info management database upgrade completed successfully' AS message; 