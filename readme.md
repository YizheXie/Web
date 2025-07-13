# Homepage - 个人网站项目

## 项目概述

这是一个功能完整的个人网站项目，包含前端展示和后台管理系统。项目采用 HTML5、CSS3、JavaScript 和 PHP+MySQL 技术栈开发。

## 功能特点

### 前端功能
- ✅ **14+ JavaScript 交互功能**（超过要求的5个）
  - 主题切换（明/暗模式）
  - 项目卡片按压效果
  - 图片弹窗查看器
  - 博客文章展开/收起
  - 实时搜索功能
  - 分类过滤功能
  - 分页功能
  - 移动端响应式导航
  - 代码高亮显示
  - 随机名言生成
  - 联系表单验证
  - 评论系统
  - 平滑滚动效果
  - 文章动画效果

- ✅ **完整的表单验证系统**
  - 前端 JavaScript 实时验证
  - 后端 PHP 服务端验证
  - 双重验证机制确保数据安全

- ✅ **响应式设计**
  - 完美适配桌面、平板、手机
  - 现代化的 UI 设计
  - 优雅的交互体验

### 后端功能
- ✅ **完整的数据库设计**（5个表，超过要求的4个）
  - 用户表 (users)
  - 分类表 (categories)
  - 文章表 (articles)
  - 评论表 (comments)
  - 联系表 (contacts)

- ✅ **动态内容管理**
  - 文章发布和管理
  - 评论审核系统
  - 联系信息管理
  - 内容搜索功能

- ✅ **完整的后台管理系统**
  - 管理员登录认证
  - 统计信息仪表板
  - 文章 CRUD 操作
  - 评论审核和管理
  - 联系信息查看和处理

## 技术栈

- **前端**: HTML5, CSS3, JavaScript (原生)
- **后端**: PHP 7.4+
- **数据库**: MySQL 5.7+
- **架构**: MVC 模式
- **安全**: PDO 预处理语句、密码哈希、XSS 防护

## 安装指南

### 1. 环境要求
- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- Web 服务器 (Apache/Nginx)

### 2. 数据库配置
```sql
-- 1. 创建数据库
CREATE DATABASE homepage_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. 导入数据库结构
mysql -u username -p homepage_db < database.sql
```

### 3. 配置文件
修改 `config.php` 中的数据库连接信息：
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'homepage_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. 设置管理员密码
```bash
# 在浏览器中访问（仅首次使用）
http://你的域名/setup_admin.php

# 使用完成后删除此文件
rm setup_admin.php
```

## 使用说明

### 前端访问
- **主页**: `index.php`
- **关于页面**: `about.html`
- **博客页面**: `blog.html`
- **文章详情**: `article.php?id=文章ID`

### 后台管理
1. **登录**: 访问 `admin/login.php`
   - 用户名: `admin`
   - 密码: `admin123`

2. **管理功能**:
   - **仪表板**: 查看网站统计信息
   - **文章管理**: 创建、编辑、删除文章
   - **评论管理**: 审核、批准、删除评论
   - **联系信息**: 查看和管理用户提交的联系表单

## 文件结构

```
Homepage/
├── index.php                 # 首页
├── about.html                # 关于页面
├── blog.html                 # 博客页面
├── article.php               # 文章详情页
├── admin/                    # 管理员后台系统
│   ├── login.php            # 管理员登录页
│   └── admin.php            # 后台管理主页
├── config.php                # 数据库配置
├── database.php              # 数据库类
├── contact_handler.php       # 联系表单处理
├── database.sql              # 数据库结构
├── setup_admin.php           # 管理员密码设置（临时文件）
├── css/                      # 样式文件
│   ├── style.css
│   ├── about.css
│   ├── blog.css
│   └── root.css
├── js/                       # JavaScript 文件
│   └── script.js
├── img/                      # 图片资源
│   ├── background/
│   └── icon/
└── fonts/                    # 字体文件
```

## 主要页面介绍

### 1. 首页 (index.php)
- 动态展示最新文章
- 项目作品展示
- 个人简介
- 主题切换功能

### 2. 关于页面 (about.html)
- 个人详细介绍
- 技能展示
- 联系表单
- 实时表单验证

### 3. 博客页面 (blog.html)
- 文章列表展示
- 搜索功能
- 分类过滤
- 分页功能

### 4. 文章详情页 (article.php)
- 文章内容展示
- 评论系统
- 相关文章推荐

### 5. 后台管理系统 (admin.php)
- 统计信息仪表板
- 文章管理（增删改查）
- 评论审核管理
- 联系信息处理

## 安全特性

1. **SQL 注入防护**: 使用 PDO 预处理语句
2. **XSS 防护**: 所有输出都经过 `htmlspecialchars()` 处理
3. **密码安全**: 使用 PHP 的 `password_hash()` 和 `password_verify()`
4. **会话管理**: 安全的会话处理和登录验证
5. **输入验证**: 前端和后端双重验证

## 维护建议

1. **定期备份**: 定期备份数据库和文件
2. **安全更新**: 保持 PHP 和 MySQL 版本更新
3. **密码策略**: 定期更改管理员密码
4. **日志监控**: 监控访问日志和错误日志
5. **文件权限**: 确保适当的文件权限设置

## 扩展功能

未来可以考虑添加的功能：
- 用户注册和登录
- 文章点赞和分享
- 标签系统
- 文章分类管理
- 邮件通知系统
- 网站SEO优化
- 多语言支持

## 常见问题

### Q: 忘记管理员密码怎么办？
A: 重新运行 `setup_admin.php` 文件重置密码。

### Q: 如何修改网站主题？
A: 修改 `css/root.css` 中的 CSS 变量。

### Q: 如何添加新的文章分类？
A: 在后台管理系统中或直接在数据库的 categories 表中添加。

### Q: 如何备份网站？
A: 备份所有文件和数据库：
```bash
# 备份数据库
mysqldump -u username -p homepage_db > backup.sql

# 备份文件
tar -czf website_backup.tar.gz Homepage/
```

## 联系方式

如有问题或建议，请联系：
- 邮箱: xieyizhe66@gmail.com
- GitHub: [Your GitHub Profile]

## 许可证

本项目采用 MIT 许可证，详情请参阅 LICENSE 文件。

---

**注意**: 这是一个学习项目，部署到生产环境前请确保进行充分的安全检查和性能优化。
