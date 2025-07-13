-- Homepage Website Database Structure
-- 创建数据库
CREATE DATABASE IF NOT EXISTS homepage_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE homepage_db;

-- 1. 用户表 (users)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. 分类表 (categories)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. 文章表 (articles)
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    excerpt VARCHAR(500),
    category_id INT,
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 4. 评论表 (comments)
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    author_name VARCHAR(50) NOT NULL,
    author_email VARCHAR(100) NOT NULL,
    author_website VARCHAR(255) DEFAULT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- 5. 联系表 (contacts)
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 插入初始数据
-- 插入默认管理员用户
INSERT INTO users (username, email, password, role) VALUES 
('admin', 'xieyizhe66@gmail.com', '$2y$10$WcSBgInqQ8Tkuc1un0T/K.c5xAhNweOTEP0K0g7Y5rCvN9uW6CVvO', 'admin');

-- 插入分类数据
INSERT INTO categories (name, slug, description) VALUES 
('技术分享', 'tech', '技术相关的文章和分享'),
('生活随笔', 'life', '生活感悟和日常记录'),
('读书笔记', 'reading', '读书心得和知识总结'),
('项目展示', 'project', '个人项目和作品展示');

-- 插入示例文章
INSERT INTO articles (title, content, excerpt, category_id, author_id, status) VALUES 
('人工智能如何改变我们的生活', 
'人工智能正以前所未有的速度发展，从语音助手到自动驾驶汽车，AI技术正在逐步融入我们的日常生活。本文将探讨AI如何改变我们的工作、生活和社交方式，以及未来可能的发展趋势。

**AI改变工作方式**
人工智能正在改变各行各业的工作方式。自动化流程、智能分析和预测性维护等AI应用已经在制造业、金融服务、医疗保健和零售业等领域广泛应用。', 
'探讨人工智能如何改变我们的工作、生活和社交方式', 
1, 1, 'published'),

('深度学习入门指南', 
'深度学习是机器学习的一个分支，它使用多层神经网络来学习数据中的复杂模式。本文将为初学者介绍深度学习的基本概念、常用框架和实践方法。

**什么是深度学习？**
深度学习是一种基于人工神经网络的机器学习技术，通过多个处理层来学习数据表示。', 
'深度学习基础概念和入门指南', 
1, 1, 'published'),

('我的2024年度总结', 
'2024年即将结束，回顾这一年的学习和成长历程，有收获也有遗憾。在这篇文章中，我想分享一些个人的感悟和对未来的展望。

**学术成长**
今年在学术研究方面取得了一些进展，发表了几篇论文，参加了重要的学术会议。', 
'2024年的学习成长和人生感悟', 
2, 1, 'published'),

('《深度学习》读书笔记', 
'Ian Goodfellow等人著作的《深度学习》是深度学习领域的经典教材。本文整理了我在阅读过程中的一些重要笔记和心得体会。

**第一章：引言**
深度学习是机器学习的一个特定分支，其灵感来自于大脑的结构和功能。', 
'深度学习经典教材的读书笔记', 
3, 1, 'published'); 

-- 添加推荐文章和标签功能的字段
ALTER TABLE articles ADD COLUMN is_featured BOOLEAN DEFAULT FALSE;
ALTER TABLE articles ADD COLUMN tags VARCHAR(255) DEFAULT NULL;
ALTER TABLE articles ADD COLUMN featured_image VARCHAR(255) DEFAULT NULL;
ALTER TABLE articles ADD COLUMN view_count INT DEFAULT 0;

-- 更新一些文章为推荐文章，并添加标签
UPDATE articles SET is_featured = TRUE, tags = 'AI,技术', featured_image = './static/img/background/background1.png' WHERE id = 1;
UPDATE articles SET is_featured = TRUE, tags = '编程,Web', featured_image = './static/img/background/background2.png' WHERE id = 2;
UPDATE articles SET tags = '生活,感悟', featured_image = './static/img/background/background3.png' WHERE id = 3;
UPDATE articles SET tags = '读书,学习', featured_image = './static/img/background/background4.png' WHERE id = 4;

-- 添加网站字段到评论表（如果表已经存在）
ALTER TABLE comments ADD COLUMN author_website VARCHAR(255) DEFAULT NULL; 