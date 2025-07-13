<?php
session_start();
require_once '../database.php';

// 检查登录状态
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();

// 处理 AJAX 请求获取文章详情
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_article') {
    $articleId = $_GET['id'] ?? 0;
    
    if ($articleId > 0) {
        $article = $db->getArticleForAdmin($articleId);
        if ($article) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'article' => $article
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => '文章不存在'
            ]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => '无效的文章ID'
        ]);
    }
    exit;
}

// 处理 AJAX 请求获取推荐内容详情
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_recommendation') {
    $recommendationId = $_GET['id'] ?? 0;
    
    if ($recommendationId > 0) {
        $recommendation = $db->getRecommendation($recommendationId);
        if ($recommendation) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'recommendation' => $recommendation
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => '推荐内容不存在'
            ]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => '无效的推荐内容ID'
        ]);
    }
    exit;
}

// 处理 AJAX 请求获取关于我信息详情
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_about_info') {
    $aboutInfoId = $_GET['id'] ?? 0;
    
    if ($aboutInfoId > 0) {
        $aboutInfo = $db->getAboutInfo($aboutInfoId);
        if ($aboutInfo) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'about_info' => $aboutInfo
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => '关于我信息不存在'
            ]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => '无效的关于我信息ID'
        ]);
    }
    exit;
}

// 处理 AJAX 请求获取网站配置详情
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_site_config') {
    $configId = $_GET['id'] ?? 0;
    
    if ($configId > 0) {
        $siteConfig = $db->getSiteConfig($configId);
        if ($siteConfig) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'site_config' => $siteConfig
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => '网站配置不存在'
            ]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => '无效的网站配置ID'
        ]);
    }
    exit;
}

// 处理各种操作
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'logout':
            session_destroy();
            header('Location: login.php');
            exit;
            
        case 'update_comment_status':
            $id = $_POST['id'];
            $status = $_POST['status'];
            $db->updateCommentStatus($id, $status);
            break;
            
        case 'delete_comment':
            $id = $_POST['id'];
            $db->deleteComment($id);
            break;
            
        case 'update_contact_status':
            $id = $_POST['id'];
            $status = $_POST['status'];
            $db->updateContactStatus($id, $status);
            break;
            
        case 'delete_contact':
            $id = $_POST['id'];
            $db->deleteContact($id);
            break;
            
        case 'create_article':
            $title = $_POST['title'];
            $content = $_POST['content'];
            $excerpt = $_POST['excerpt'];
            $categoryId = $_POST['category_id'];
            $status = $_POST['status'];
            $tags = $_POST['tags'] ?? '';
            $featuredImage = $_POST['featured_image'] ?? '';
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $db->createArticleWithExtras($title, $content, $excerpt, $categoryId, $_SESSION['admin_id'], $status, $tags, $featuredImage, $isFeatured);
            break;
            
        case 'update_article':
            $id = $_POST['id'];
            $title = $_POST['title'];
            $content = $_POST['content'];
            $excerpt = $_POST['excerpt'];
            $categoryId = $_POST['category_id'];
            $status = $_POST['status'];
            $tags = $_POST['tags'] ?? '';
            $featuredImage = $_POST['featured_image'] ?? '';
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $db->updateArticleWithExtras($id, $title, $content, $excerpt, $categoryId, $status, $tags, $featuredImage, $isFeatured);
            break;
            
        case 'delete_article':
            $id = $_POST['id'];
            $db->deleteArticle($id);
            break;
            
        case 'toggle_featured':
            $id = $_POST['id'];
            $featured = $_POST['featured'] === '1';
            $db->toggleFeaturedArticle($id, $featured);
            break;
            
        case 'create_recommendation':
            $title = $_POST['title'];
            $url = $_POST['url'];
            $image = $_POST['image'] ?? '';
            $tags = $_POST['tags'] ?? '';
            $description = $_POST['description'];
            $category = $_POST['category'];
            $date = $_POST['date'];
            $status = $_POST['status'];
            $sortOrder = $_POST['sort_order'] ?? 0;
            $db->createRecommendation($title, $url, $image, $tags, $description, $category, $date, $status, $sortOrder);
            break;
            
        case 'update_recommendation':
            $id = $_POST['id'];
            $title = $_POST['title'];
            $url = $_POST['url'];
            $image = $_POST['image'] ?? '';
            $tags = $_POST['tags'] ?? '';
            $description = $_POST['description'];
            $category = $_POST['category'];
            $date = $_POST['date'];
            $status = $_POST['status'];
            $sortOrder = $_POST['sort_order'] ?? 0;
            $db->updateRecommendation($id, $title, $url, $image, $tags, $description, $category, $date, $status, $sortOrder);
            break;
            
        case 'delete_recommendation':
            $id = $_POST['id'];
            $db->deleteRecommendation($id);
            break;
            
        case 'update_recommendation_status':
            $id = $_POST['id'];
            $status = $_POST['status'];
            $db->updateRecommendationStatus($id, $status);
            break;
            

            
        case 'update_about_info':
            $id = $_POST['id'];
            $sectionName = $_POST['section_name'];
            $sectionKey = $_POST['section_key'];
            $content = $_POST['content'];
            $contentType = $_POST['content_type'] ?? 'text';
            $sortOrder = $_POST['sort_order'] ?? 0;
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $db->updateAboutInfo($id, $sectionName, $sectionKey, $content, $contentType, $sortOrder, $isActive);
            break;
            
        case 'delete_about_info':
            $id = $_POST['id'];
            $db->deleteAboutInfo($id);
            break;
            
        case 'toggle_about_info_status':
            $id = $_POST['id'];
            $isActive = $_POST['is_active'] === '1';
            $db->toggleAboutInfoStatus($id, $isActive);
            break;
            
        case 'update_site_config':
            $id = $_POST['id'];
            $configKey = $_POST['config_key'];
            $configValue = $_POST['config_value'];
            $configType = $_POST['config_type'] ?? 'text';
            $description = $_POST['description'] ?? '';
            $sortOrder = $_POST['sort_order'] ?? 0;
            $db->updateSiteConfig($id, $configKey, $configValue, $configType, $description, $sortOrder);
            break;
            

    }
    
    header('Location: admin.php');
    exit;
}

// 获取数据
$stats = $db->getStats();
$articles = $db->getAllArticles(10);
$comments = $db->getAllComments(10);
$contacts = $db->getAllContacts(10);
$categories = $db->getCategories();
$recentActivities = $db->getRecentActivities(8);
$recommendations = $db->getAllRecommendations(20, null, null); // 获取所有推荐内容（包含活跃和非活跃）
$recommendationStats = $db->getRecommendationStats();
$aboutInfoList = $db->getAllAboutInfo(); // 获取所有关于我信息
$siteConfigList = $db->getAllSiteConfig(); // 获取所有网站配置

// 当前选中的标签页
$currentTab = $_GET['tab'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - Homepage</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">🔓 后台管理系统</div>
            <div class="user-info">
                <span>欢迎, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="logout-btn">退出</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="tabs">
            <a href="?tab=dashboard" class="tab <?php echo $currentTab === 'dashboard' ? 'active' : ''; ?>">📊 仪表板</a>
            <a href="?tab=articles" class="tab <?php echo $currentTab === 'articles' ? 'active' : ''; ?>">📝 文章管理</a>
            <a href="?tab=recommendations" class="tab <?php echo $currentTab === 'recommendations' ? 'active' : ''; ?>">⭐ 推荐管理</a>
            <a href="?tab=about" class="tab <?php echo $currentTab === 'about' ? 'active' : ''; ?>">👤 关于我管理</a>
            <a href="?tab=site_config" class="tab <?php echo $currentTab === 'site_config' ? 'active' : ''; ?>">⚙️ 网站配置</a>
            <a href="?tab=comments" class="tab <?php echo $currentTab === 'comments' ? 'active' : ''; ?>">💬 评论管理</a>
            <a href="?tab=contacts" class="tab <?php echo $currentTab === 'contacts' ? 'active' : ''; ?>">📧 联系信息</a>
        </div>

        <!-- 仪表板 -->
        <div class="tab-content <?php echo $currentTab === 'dashboard' ? 'active' : ''; ?>">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_articles']; ?></div>
                    <div class="stat-label">总文章数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['published_articles']; ?></div>
                    <div class="stat-label">已发布文章</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $recommendationStats['total_recommendations']; ?></div>
                    <div class="stat-label">推荐内容</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $recommendationStats['active_recommendations']; ?></div>
                    <div class="stat-label">活跃推荐</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_comments']; ?></div>
                    <div class="stat-label">总评论数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['pending_comments']; ?></div>
                    <div class="stat-label">待审核评论</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_contacts']; ?></div>
                    <div class="stat-label">总联系信息</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['new_contacts']; ?></div>
                    <div class="stat-label">新联系信息</div>
                </div>
            </div>

            <div class="content-card">
                <div class="card-header">
                    最近活动
                    <div class="activity-refresh">
                        <small>实时更新</small>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($recentActivities)): ?>
                        <div class="no-activities">
                            <div class="no-activities-icon">📊</div>
                            <div class="no-activities-text">暂无最近活动</div>
                            <div class="no-activities-desc">当有新文章、评论或联系信息时，会在这里显示</div>
                        </div>
                    <?php else: ?>
                        <div class="activity-list">
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon activity-<?php echo $activity['color']; ?>">
                                        <?php echo $activity['icon']; ?>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-header">
                                            <span class="activity-title"><?php echo htmlspecialchars($activity['title']); ?></span>
                                            <span class="activity-time"><?php echo date('m-d H:i', strtotime($activity['time'])); ?></span>
                                        </div>
                                        <div class="activity-details">
                                            <div class="activity-meta">
                                                <span class="activity-type">
                                                    <?php
                                                    $typeNames = [
                                                        'article' => '文章',
                                                        'comment' => '评论',
                                                        'contact' => '联系'
                                                    ];
                                                    echo $typeNames[$activity['type']];
                                                    ?>
                                                </span>
                                                <?php if ($activity['author']): ?>
                                                    <span class="activity-author">由 <?php echo htmlspecialchars($activity['author']); ?></span>
                                                <?php endif; ?>
                                                <span class="activity-status status-badge status-<?php echo $activity['status']; ?>">
                                                    <?php
                                                    $statusNames = [
                                                        'published' => '已发布',
                                                        'draft' => '草稿',
                                                        'pending' => '待审核',
                                                        'approved' => '已批准',
                                                        'rejected' => '已拒绝',
                                                        'new' => '新消息',
                                                        'read' => '已读',
                                                        'replied' => '已回复'
                                                    ];
                                                    echo $statusNames[$activity['status']] ?? $activity['status'];
                                                    ?>
                                                </span>
                                            </div>
                                            <?php if (isset($activity['content'])): ?>
                                                <div class="activity-content-preview">
                                                    <?php echo htmlspecialchars($activity['content']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="activity-actions">
                                        <?php if ($activity['type'] === 'article'): ?>
                                            <a href="?tab=articles" class="activity-action" title="管理文章">
                                                <span>📝</span>
                                            </a>
                                        <?php elseif ($activity['type'] === 'comment'): ?>
                                            <a href="?tab=comments" class="activity-action" title="管理评论">
                                                <span>💬</span>
                                            </a>
                                        <?php elseif ($activity['type'] === 'contact'): ?>
                                            <a href="?tab=contacts" class="activity-action" title="管理联系信息">
                                                <span>📧</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="activity-footer">
                        <div class="btn-group">
                            <a href="?tab=articles" class="btn btn-primary">管理文章</a>
                            <a href="?tab=comments" class="btn btn-success">审核评论</a>
                            <a href="?tab=contacts" class="btn btn-warning">查看联系信息</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 文章管理 -->
        <div class="tab-content <?php echo $currentTab === 'articles' ? 'active' : ''; ?>">
            <div class="content-card">
                <div class="card-header">
                    文章管理
                    <button onclick="openArticleModal()" class="btn btn-primary">新增文章</button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>标题</th>
                                <th>分类</th>
                                <th>状态</th>
                                <th>推荐</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo $article['id']; ?></td>
                                <td class="text-truncate text-left" title="<?php echo htmlspecialchars($article['title']); ?>"><?php echo htmlspecialchars($article['title']); ?></td>
                                <td><?php echo htmlspecialchars($article['category_name'] ?? '无分类'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $article['status']; ?>">
                                        <?php echo $article['status'] === 'published' ? '已发布' : '草稿'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $article['is_featured'] ? 'published' : 'draft'; ?>">
                                        <?php echo $article['is_featured'] ? '★ 推荐' : '☆ 普通'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($article['created_at'])); ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <button onclick="editArticle(<?php echo $article['id']; ?>)" class="btn btn-warning">编辑</button>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="toggle_featured">
                                            <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                                            <input type="hidden" name="featured" value="<?php echo $article['is_featured'] ? '0' : '1'; ?>">
                                            <button type="submit" class="btn <?php echo $article['is_featured'] ? 'btn-secondary' : 'btn-success'; ?>">
                                                <?php echo $article['is_featured'] ? '取消推荐' : '设为推荐'; ?>
                                            </button>
                                        </form>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('确定删除这篇文章吗？')">
                                            <input type="hidden" name="action" value="delete_article">
                                            <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                                            <button type="submit" class="btn btn-danger">删除</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 推荐管理 -->
        <div class="tab-content <?php echo $currentTab === 'recommendations' ? 'active' : ''; ?>">
            <div class="content-card">
                <div class="card-header">
                    推荐内容管理
                    <button onclick="openRecommendationModal()" class="btn btn-primary">新增推荐</button>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>标题</th>
                                <th>分类</th>
                                <th>状态</th>
                                <th>排序</th>
                                <th>日期</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recommendations as $recommendation): ?>
                            <tr>
                                <td><?php echo $recommendation['id']; ?></td>
                                <td class="text-truncate text-left" title="<?php echo htmlspecialchars($recommendation['title']); ?>">
                                    <a href="<?php echo htmlspecialchars($recommendation['url']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($recommendation['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($recommendation['category']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $recommendation['status'] === 'active' ? 'published' : 'draft'; ?>">
                                        <?php echo $recommendation['status'] === 'active' ? '活跃' : '停用'; ?>
                                    </span>
                                </td>
                                <td><?php echo $recommendation['sort_order']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($recommendation['date'])); ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <button onclick="editRecommendation(<?php echo $recommendation['id']; ?>)" class="btn btn-warning">编辑</button>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="update_recommendation_status">
                                            <input type="hidden" name="id" value="<?php echo $recommendation['id']; ?>">
                                            <input type="hidden" name="status" value="<?php echo $recommendation['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                            <button type="submit" class="btn <?php echo $recommendation['status'] === 'active' ? 'btn-secondary' : 'btn-success'; ?>">
                                                <?php echo $recommendation['status'] === 'active' ? '停用' : '启用'; ?>
                                            </button>
                                        </form>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('确定删除这条推荐内容吗？')">
                                            <input type="hidden" name="action" value="delete_recommendation">
                                            <input type="hidden" name="id" value="<?php echo $recommendation['id']; ?>">
                                            <button type="submit" class="btn btn-danger">删除</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 关于我管理 -->
        <div class="tab-content <?php echo $currentTab === 'about' ? 'active' : ''; ?>">
            <div class="content-card">
                <div class="card-header">
                    关于我信息管理
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>区块名称</th>
                                <th>键名</th>
                                <th>内容预览</th>
                                <th>状态</th>
                                <th>排序</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aboutInfoList as $aboutInfo): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($aboutInfo['section_name']); ?></td>
                                <td><code><?php echo htmlspecialchars($aboutInfo['section_key']); ?></code></td>
                                <td class="text-truncate text-left" title="<?php echo htmlspecialchars($aboutInfo['content']); ?>">
                                    <?php echo htmlspecialchars(mb_substr($aboutInfo['content'], 0, 50)) . (mb_strlen($aboutInfo['content']) > 50 ? '...' : ''); ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $aboutInfo['is_active'] ? 'published' : 'draft'; ?>">
                                        <?php echo $aboutInfo['is_active'] ? '启用' : '停用'; ?>
                                    </span>
                                </td>
                                <td><?php echo $aboutInfo['sort_order']; ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <button onclick="editAboutInfo(<?php echo $aboutInfo['id']; ?>)" class="btn btn-warning">编辑</button>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="toggle_about_info_status">
                                            <input type="hidden" name="id" value="<?php echo $aboutInfo['id']; ?>">
                                            <input type="hidden" name="is_active" value="<?php echo $aboutInfo['is_active'] ? '0' : '1'; ?>">
                                            <button type="submit" class="btn <?php echo $aboutInfo['is_active'] ? 'btn-secondary' : 'btn-success'; ?>" 
                                                    <?php echo $aboutInfo['is_active'] ? 'title="停用此项内容后，前端页面将显示默认模板内容"' : ''; ?>>
                                                <?php echo $aboutInfo['is_active'] ? '停用' : '启用'; ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 网站配置管理 -->
        <div class="tab-content <?php echo $currentTab === 'site_config' ? 'active' : ''; ?>">
            <div class="content-card">
                <div class="card-header">
                    网站配置管理
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>配置名称</th>
                                <th>键名</th>
                                <th>配置值预览</th>
                                <th>类型</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siteConfigList as $config): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($config['description'] ?: $config['config_key']); ?></td>
                                <td><code><?php echo htmlspecialchars($config['config_key']); ?></code></td>
                                <td class="text-truncate text-left" title="<?php echo htmlspecialchars($config['config_value']); ?>">
                                    <?php echo htmlspecialchars(mb_substr($config['config_value'], 0, 50)) . (mb_strlen($config['config_value']) > 50 ? '...' : ''); ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $config['config_type'] === 'url' ? 'published' : 'draft'; ?>">
                                        <?php echo $config['config_type']; ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <button onclick="editSiteConfig(<?php echo $config['id']; ?>)" class="btn btn-warning">编辑</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 评论管理 -->
        <div class="tab-content <?php echo $currentTab === 'comments' ? 'active' : ''; ?>">
            <div class="content-card">
                <div class="card-header">评论管理</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>文章</th>
                                <th>评论者</th>
                                <th>内容</th>
                                <th>状态</th>
                                <th>时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?php echo $comment['id']; ?></td>
                                <td class="text-truncate text-left" title="<?php echo htmlspecialchars($comment['article_title']); ?>"><?php echo htmlspecialchars($comment['article_title']); ?></td>
                                <td><?php echo htmlspecialchars($comment['author_name']); ?></td>
                                <td class="text-truncate text-left" title="<?php echo htmlspecialchars($comment['content']); ?>"><?php echo htmlspecialchars($comment['content']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $comment['status']; ?>">
                                        <?php
                                        switch($comment['status']) {
                                            case 'pending': echo '待审核'; break;
                                            case 'approved': echo '已批准'; break;
                                            case 'rejected': echo '已拒绝'; break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?php if ($comment['status'] === 'pending'): ?>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="update_comment_status">
                                            <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-success">批准</button>
                                        </form>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="update_comment_status">
                                            <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-warning">拒绝</button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('确定删除这条评论吗？')">
                                            <input type="hidden" name="action" value="delete_comment">
                                            <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                            <button type="submit" class="btn btn-danger">删除</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 联系信息 -->
        <div class="tab-content <?php echo $currentTab === 'contacts' ? 'active' : ''; ?>">
            <div class="content-card">
                <div class="card-header">联系信息管理</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>姓名</th>
                                <th>邮箱</th>
                                <th>消息</th>
                                <th>状态</th>
                                <th>时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?php echo $contact['id']; ?></td>
                                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                <td class="text-truncate text-left" title="<?php echo htmlspecialchars($contact['message']); ?>"><?php echo htmlspecialchars($contact['message']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $contact['status']; ?>">
                                        <?php
                                        switch($contact['status']) {
                                            case 'new': echo '新消息'; break;
                                            case 'read': echo '已读'; break;
                                            case 'replied': echo '已回复'; break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($contact['created_at'])); ?></td>
                                <td class="actions">
                                    <div class="btn-group">
                                        <?php if ($contact['status'] === 'new'): ?>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="update_contact_status">
                                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                            <input type="hidden" name="status" value="read">
                                            <button type="submit" class="btn btn-success">标记已读</button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($contact['status'] !== 'replied'): ?>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="action" value="update_contact_status">
                                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                            <input type="hidden" name="status" value="replied">
                                            <button type="submit" class="btn btn-warning">标记已回复</button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('确定删除这条联系信息吗？')">
                                            <input type="hidden" name="action" value="delete_contact">
                                            <input type="hidden" name="id" value="<?php echo $contact['id']; ?>">
                                            <button type="submit" class="btn btn-danger">删除</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 文章编辑模态框 -->
    <div id="articleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">新增文章</h2>
                <button class="close" onclick="closeArticleModal(event)">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="articleForm" onsubmit="return validateFormSubmit(event)">
                    <input type="hidden" name="action" value="create_article" id="formAction">
                    <input type="hidden" name="id" id="articleId">
                    
                    <div class="form-group">
                        <label for="title">标题</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="excerpt">摘要</label>
                        <textarea id="excerpt" name="excerpt" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">内容</label>
                        <textarea id="content" name="content" class="form-control" rows="10" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">分类</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">选择分类</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="tags">标签</label>
                        <input type="text" id="tags" name="tags" class="form-control" placeholder="多个标签用逗号分隔，如：技术,编程,Web">
                    </div>
                    
                    <div class="form-group">
                        <label for="featured_image">封面图片</label>
                        <input type="text" id="featured_image" name="featured_image" class="form-control" placeholder="封面图片URL（推荐文章显示）">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">状态</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="draft">草稿</option>
                            <option value="published">发布</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="is_featured" name="is_featured" value="1">
                            设为推荐文章
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">保存</button>
                    <button type="button" class="btn btn-secondary" onclick="closeArticleModal(event)">取消</button>
                </form>
            </div>
        </div>
    </div>

    <!-- 推荐内容编辑模态框 -->
    <div id="recommendationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="recommendationModalTitle">新增推荐内容</h2>
                <button class="close" onclick="closeRecommendationModal(event)">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="recommendationForm" onsubmit="return validateRecommendationSubmit(event)">
                    <input type="hidden" name="action" value="create_recommendation" id="recommendationFormAction">
                    <input type="hidden" name="id" id="recommendationId">
                    
                    <div class="form-group">
                        <label for="rec_title">标题</label>
                        <input type="text" id="rec_title" name="title" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="rec_url">链接</label>
                        <input type="url" id="rec_url" name="url" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="rec_category">分类</label>
                        <select id="rec_category" name="category" class="form-control" required>
                            <option value="">选择分类</option>
                            <option value="学习资源">学习资源</option>
                            <option value="工具推荐">工具推荐</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="rec_description">描述</label>
                        <textarea id="rec_description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="rec_tags">标签</label>
                        <input type="text" id="rec_tags" name="tags" class="form-control" placeholder="多个标签用逗号分隔，如：AI,工具">
                    </div>
                    
                    <div class="form-group">
                        <label for="rec_image">图片路径</label>
                        <input type="text" id="rec_image" name="image" class="form-control" placeholder="如：./static/img/background/background1.png">
                    </div>
                    
                    <div class="form-group">
                        <label for="rec_date">日期</label>
                        <input type="date" id="rec_date" name="date" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="rec_sort_order">排序</label>
                        <input type="number" id="rec_sort_order" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="rec_status">状态</label>
                        <select id="rec_status" name="status" class="form-control" required>
                            <option value="active">活跃</option>
                            <option value="inactive">停用</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">保存</button>
                    <button type="button" class="btn btn-secondary" onclick="closeRecommendationModal(event)">取消</button>
                </form>
            </div>
        </div>
    </div>

    <!-- 关于我信息编辑模态框 -->
    <div id="aboutInfoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="aboutInfoModalTitle">编辑关于我信息</h2>
                <button class="close" onclick="closeAboutInfoModal(event)">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="aboutInfoForm" onsubmit="return validateAboutInfoSubmit(event)">
                    <input type="hidden" name="action" value="update_about_info" id="aboutInfoFormAction">
                    <input type="hidden" name="id" id="aboutInfoId">
                    
                    <div class="form-group">
                        <label for="about_section_name">区块名称</label>
                        <input type="text" id="about_section_name" name="section_name" class="form-control" required placeholder="如：个人姓名、个人简介等">
                    </div>
                    
                    <div class="form-group">
                        <label for="about_section_key">键名</label>
                        <input type="text" id="about_section_key" name="section_key" class="form-control" required placeholder="如：name、bio等，用于前端调用">
                    </div>
                    
                    <div class="form-group">
                        <label for="about_content">内容</label>
                        <textarea id="about_content" name="content" class="form-control" rows="6" required placeholder="输入具体内容"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="about_content_type">内容类型</label>
                        <select id="about_content_type" name="content_type" class="form-control" required>
                            <option value="text">文本</option>
                            <option value="html">HTML</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="about_sort_order">排序</label>
                        <input type="number" id="about_sort_order" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="about_is_active" name="is_active" value="1" checked>
                            启用此信息
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">保存</button>
                    <button type="button" class="btn btn-secondary" onclick="closeAboutInfoModal(event)">取消</button>
                </form>
            </div>
        </div>
    </div>

    <!-- 网站配置编辑模态框 -->
    <div id="siteConfigModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="siteConfigModalTitle">编辑网站配置</h2>
                <button class="close" onclick="closeSiteConfigModal(event)">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="siteConfigForm" onsubmit="return validateSiteConfigSubmit(event)">
                    <input type="hidden" name="action" value="update_site_config" id="siteConfigFormAction">
                    <input type="hidden" name="id" id="siteConfigId">
                    
                    <div class="form-group">
                        <label for="site_config_key">配置键名</label>
                        <input type="text" id="site_config_key" name="config_key" class="form-control" required readonly>
                        <small class="form-text text-muted">配置键名不可修改</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_config_value">配置值</label>
                        <textarea id="site_config_value" name="config_value" class="form-control" rows="6" required placeholder="输入配置值"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_config_type">配置类型</label>
                        <select id="site_config_type" name="config_type" class="form-control" required>
                            <option value="text">文本</option>
                            <option value="url">URL链接</option>
                            <option value="html">HTML</option>
                            <option value="json">JSON</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_config_description">配置描述</label>
                        <input type="text" id="site_config_description" name="description" class="form-control" placeholder="配置的用途描述">
                    </div>
                    
                    <div class="form-group">
                        <label for="site_config_sort_order">排序</label>
                        <input type="number" id="site_config_sort_order" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">保存</button>
                    <button type="button" class="btn btn-secondary" onclick="closeSiteConfigModal(event)">取消</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openArticleModal() {
            document.getElementById('articleModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = '新增文章';
            document.getElementById('formAction').value = 'create_article';
            document.getElementById('articleForm').reset();
        }

        function validateFormSubmit(event) {
            const modalBody = document.querySelector('.modal-body');
            
            // 检查是否正在加载中
            if (modalBody && modalBody.classList.contains('loading')) {
                event.preventDefault();
                return false; // 加载中不允许提交
            }
            
            return true; // 允许提交
        }

        function closeArticleModal(event) {
            const modal = document.getElementById('articleModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.querySelector('.modal-body');
            
            // 检查是否正在加载中
            if (modalBody && modalBody.classList.contains('loading')) {
                if (event) {
                    event.preventDefault();
                }
                return; // 加载中不允许关闭
            }
            
            // 清除加载状态
            modalTitle.removeAttribute('data-loading');
            modalBody.classList.remove('loading');
            
            // 启用所有表单字段
            const formFields = document.querySelectorAll('#articleForm input, #articleForm textarea, #articleForm select');
            formFields.forEach(field => {
                field.disabled = false;
            });
            
            modal.style.display = 'none';
        }

        function editArticle(id) {
            // 显示加载状态
            document.getElementById('articleModal').style.display = 'block';
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.querySelector('.modal-body');
            
            modalTitle.textContent = '编辑文章 - 加载中...';
            modalTitle.setAttribute('data-loading', 'true');
            modalBody.classList.add('loading');
            
            document.getElementById('formAction').value = 'update_article';
            document.getElementById('articleId').value = id;
            
            // 重置表单
            document.getElementById('articleForm').reset();
            
            // 禁用表单字段
            const formFields = document.querySelectorAll('#articleForm input, #articleForm textarea, #articleForm select');
            formFields.forEach(field => {
                field.disabled = true;
            });
            
            // 通过AJAX获取文章详情
            fetch(`admin.php?action=get_article&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 填充表单数据
                        const article = data.article;
                        document.getElementById('title').value = article.title || '';
                        document.getElementById('excerpt').value = article.excerpt || '';
                        document.getElementById('content').value = article.content || '';
                        document.getElementById('category_id').value = article.category_id || '';
                        document.getElementById('tags').value = article.tags || '';
                        document.getElementById('featured_image').value = article.featured_image || '';
                        document.getElementById('status').value = article.status || 'draft';
                        document.getElementById('is_featured').checked = article.is_featured == 1;
                        
                        // 移除加载状态
                        modalTitle.textContent = '编辑文章';
                        modalTitle.removeAttribute('data-loading');
                        modalBody.classList.remove('loading');
                        
                        // 启用表单字段
                        formFields.forEach(field => {
                            field.disabled = false;
                        });
                        
                        // 更新滚动状态
                        setTimeout(() => {
                            updateScrollState();
                        }, 100);
                    } else {
                        alert('获取文章详情失败：' + (data.message || '未知错误'));
                        closeArticleModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('获取文章详情时发生错误');
                    closeArticleModal();
                });
        }

        // 全局滚动状态检测函数
        function updateScrollState() {
            const modal = document.getElementById('articleModal');
            const modalBody = modal.querySelector('.modal-body');
            
            if (modalBody) {
                const hasScroll = modalBody.scrollHeight > modalBody.clientHeight;
                const isScrolledToBottom = modalBody.scrollTop + modalBody.clientHeight >= modalBody.scrollHeight - 5;
                
                modalBody.classList.toggle('has-scroll', hasScroll);
                modalBody.classList.toggle('scroll-bottom', isScrolledToBottom);
            }
        }

        // 页面加载完成后初始化
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('articleModal');
            const modalBody = modal.querySelector('.modal-body');

            // 监听滚动事件
            modalBody.addEventListener('scroll', updateScrollState);

            // 监听窗口大小变化和模态框打开事件
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                        if (modal.style.display === 'block') {
                            setTimeout(updateScrollState, 100);
                        }
                    }
                });
            });
            observer.observe(modal, { attributes: true });

            // 点击模态框外部关闭
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    // 检查是否正在加载中
                    const modalBody = modal.querySelector('.modal-body');
                    if (modalBody && modalBody.classList.contains('loading')) {
                        return; // 加载中不允许关闭
                    }
                    closeArticleModal();
                }
            });

            // 键盘快捷键
            document.addEventListener('keydown', function(e) {
                const modalVisible = modal.style.display === 'block';
                
                // Ctrl+S 快速保存
                if (e.ctrlKey && e.key === 's' && modalVisible) {
                    e.preventDefault();
                    const form = document.getElementById('articleForm');
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.click();
                    }
                }
                
                // Esc 关闭模态框
                if (e.key === 'Escape' && modalVisible) {
                    // 检查是否正在加载中
                    const modalBody = modal.querySelector('.modal-body');
                    if (modalBody && modalBody.classList.contains('loading')) {
                        return; // 加载中不允许关闭
                    }
                    closeArticleModal();
                }
            });
        });
        
        // 推荐内容相关功能
        function openRecommendationModal() {
            document.getElementById('recommendationModal').style.display = 'block';
            document.getElementById('recommendationModalTitle').textContent = '新增推荐内容';
            document.getElementById('recommendationFormAction').value = 'create_recommendation';
            document.getElementById('recommendationForm').reset();
            document.getElementById('rec_date').value = new Date().toISOString().split('T')[0];
        }

        function validateRecommendationSubmit(event) {
            const modalBody = document.querySelector('#recommendationModal .modal-body');
            
            if (modalBody && modalBody.classList.contains('loading')) {
                event.preventDefault();
                return false;
            }
            
            return true;
        }

        function closeRecommendationModal(event) {
            const modal = document.getElementById('recommendationModal');
            const modalTitle = document.getElementById('recommendationModalTitle');
            const modalBody = document.querySelector('#recommendationModal .modal-body');
            
            if (modalBody && modalBody.classList.contains('loading')) {
                if (event) {
                    event.preventDefault();
                }
                return;
            }
            
            modalTitle.removeAttribute('data-loading');
            modalBody.classList.remove('loading');
            
            const formFields = document.querySelectorAll('#recommendationForm input, #recommendationForm textarea, #recommendationForm select');
            formFields.forEach(field => {
                field.disabled = false;
            });
            
            modal.style.display = 'none';
        }

        function editRecommendation(id) {
            document.getElementById('recommendationModal').style.display = 'block';
            const modalTitle = document.getElementById('recommendationModalTitle');
            const modalBody = document.querySelector('#recommendationModal .modal-body');
            
            modalTitle.textContent = '编辑推荐内容 - 加载中...';
            modalTitle.setAttribute('data-loading', 'true');
            modalBody.classList.add('loading');
            
            document.getElementById('recommendationFormAction').value = 'update_recommendation';
            document.getElementById('recommendationId').value = id;
            
            document.getElementById('recommendationForm').reset();
            
            const formFields = document.querySelectorAll('#recommendationForm input, #recommendationForm textarea, #recommendationForm select');
            formFields.forEach(field => {
                field.disabled = true;
            });
            
            fetch(`admin.php?action=get_recommendation&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const rec = data.recommendation;
                        document.getElementById('rec_title').value = rec.title || '';
                        document.getElementById('rec_url').value = rec.url || '';
                        document.getElementById('rec_category').value = rec.category || '';
                        document.getElementById('rec_description').value = rec.description || '';
                        document.getElementById('rec_tags').value = rec.tags || '';
                        document.getElementById('rec_image').value = rec.image || '';
                        document.getElementById('rec_date').value = rec.date || '';
                        document.getElementById('rec_sort_order').value = rec.sort_order || 0;
                        document.getElementById('rec_status').value = rec.status || 'active';
                        
                        modalTitle.textContent = '编辑推荐内容';
                        modalTitle.removeAttribute('data-loading');
                        modalBody.classList.remove('loading');
                        
                        formFields.forEach(field => {
                            field.disabled = false;
                        });
                    } else {
                        alert('获取推荐内容详情失败：' + (data.message || '未知错误'));
                        closeRecommendationModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('获取推荐内容详情时发生错误');
                    closeRecommendationModal();
                });
        }

        // 为推荐内容模态框添加事件监听器
        document.addEventListener('DOMContentLoaded', function() {
            const recommendationModal = document.getElementById('recommendationModal');
            if (recommendationModal) {
                // 点击模态框外部关闭
                recommendationModal.addEventListener('click', function(e) {
                    if (e.target === recommendationModal) {
                        const modalBody = recommendationModal.querySelector('.modal-body');
                        if (modalBody && modalBody.classList.contains('loading')) {
                            return;
                        }
                        closeRecommendationModal();
                    }
                });
            }
        });
        
        // 关于我信息相关功能
        function validateAboutInfoSubmit(event) {
            const modalBody = document.querySelector('#aboutInfoModal .modal-body');
            
            if (modalBody && modalBody.classList.contains('loading')) {
                event.preventDefault();
                return false;
            }
            
            return true;
        }

        function closeAboutInfoModal(event) {
            const modal = document.getElementById('aboutInfoModal');
            const modalTitle = document.getElementById('aboutInfoModalTitle');
            const modalBody = document.querySelector('#aboutInfoModal .modal-body');
            
            if (modalBody && modalBody.classList.contains('loading')) {
                if (event) {
                    event.preventDefault();
                }
                return;
            }
            
            modalTitle.removeAttribute('data-loading');
            modalBody.classList.remove('loading');
            
            const formFields = document.querySelectorAll('#aboutInfoForm input, #aboutInfoForm textarea, #aboutInfoForm select');
            formFields.forEach(field => {
                field.disabled = false;
            });
            
            modal.style.display = 'none';
        }

        function editAboutInfo(id) {
            document.getElementById('aboutInfoModal').style.display = 'block';
            const modalTitle = document.getElementById('aboutInfoModalTitle');
            const modalBody = document.querySelector('#aboutInfoModal .modal-body');
            
            modalTitle.textContent = '编辑关于我信息 - 加载中...';
            modalTitle.setAttribute('data-loading', 'true');
            modalBody.classList.add('loading');
            
            document.getElementById('aboutInfoFormAction').value = 'update_about_info';
            document.getElementById('aboutInfoId').value = id;
            
            document.getElementById('aboutInfoForm').reset();
            
            const formFields = document.querySelectorAll('#aboutInfoForm input, #aboutInfoForm textarea, #aboutInfoForm select');
            formFields.forEach(field => {
                field.disabled = true;
            });
            
            fetch(`admin.php?action=get_about_info&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const aboutInfo = data.about_info;
                        document.getElementById('about_section_name').value = aboutInfo.section_name || '';
                        document.getElementById('about_section_key').value = aboutInfo.section_key || '';
                        document.getElementById('about_content').value = aboutInfo.content || '';
                        document.getElementById('about_content_type').value = aboutInfo.content_type || 'text';
                        document.getElementById('about_sort_order').value = aboutInfo.sort_order || 0;
                        document.getElementById('about_is_active').checked = aboutInfo.is_active == 1;
                        
                        modalTitle.textContent = '编辑关于我信息';
                        modalTitle.removeAttribute('data-loading');
                        modalBody.classList.remove('loading');
                        
                        formFields.forEach(field => {
                            field.disabled = false;
                        });
                    } else {
                        alert('获取关于我信息详情失败：' + (data.message || '未知错误'));
                        closeAboutInfoModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('获取关于我信息详情时发生错误');
                    closeAboutInfoModal();
                });
        }

        // 为关于我信息模态框添加事件监听器
        document.addEventListener('DOMContentLoaded', function() {
            const aboutInfoModal = document.getElementById('aboutInfoModal');
            if (aboutInfoModal) {
                // 点击模态框外部关闭
                aboutInfoModal.addEventListener('click', function(e) {
                    if (e.target === aboutInfoModal) {
                        const modalBody = aboutInfoModal.querySelector('.modal-body');
                        if (modalBody && modalBody.classList.contains('loading')) {
                            return;
                        }
                        closeAboutInfoModal();
                    }
                });
            }
        });
        
        // 网站配置相关功能
        function validateSiteConfigSubmit(event) {
            const modalBody = document.querySelector('#siteConfigModal .modal-body');
            
            if (modalBody && modalBody.classList.contains('loading')) {
                event.preventDefault();
                return false;
            }
            
            return true;
        }

        function closeSiteConfigModal(event) {
            const modal = document.getElementById('siteConfigModal');
            const modalTitle = document.getElementById('siteConfigModalTitle');
            const modalBody = document.querySelector('#siteConfigModal .modal-body');
            
            if (modalBody && modalBody.classList.contains('loading')) {
                if (event) {
                    event.preventDefault();
                }
                return;
            }
            
            modalTitle.removeAttribute('data-loading');
            modalBody.classList.remove('loading');
            
            const formFields = document.querySelectorAll('#siteConfigForm input, #siteConfigForm textarea, #siteConfigForm select');
            formFields.forEach(field => {
                field.disabled = false;
            });
            
            modal.style.display = 'none';
        }

        function editSiteConfig(id) {
            document.getElementById('siteConfigModal').style.display = 'block';
            const modalTitle = document.getElementById('siteConfigModalTitle');
            const modalBody = document.querySelector('#siteConfigModal .modal-body');
            
            modalTitle.textContent = '编辑网站配置 - 加载中...';
            modalTitle.setAttribute('data-loading', 'true');
            modalBody.classList.add('loading');
            
            document.getElementById('siteConfigFormAction').value = 'update_site_config';
            document.getElementById('siteConfigId').value = id;
            
            document.getElementById('siteConfigForm').reset();
            
            const formFields = document.querySelectorAll('#siteConfigForm input, #siteConfigForm textarea, #siteConfigForm select');
            formFields.forEach(field => {
                field.disabled = true;
            });
            
            fetch(`admin.php?action=get_site_config&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const config = data.site_config;
                        document.getElementById('site_config_key').value = config.config_key || '';
                        document.getElementById('site_config_value').value = config.config_value || '';
                        document.getElementById('site_config_type').value = config.config_type || 'text';
                        document.getElementById('site_config_description').value = config.description || '';
                        document.getElementById('site_config_sort_order').value = config.sort_order || 0;
                        
                        modalTitle.textContent = '编辑网站配置';
                        modalTitle.removeAttribute('data-loading');
                        modalBody.classList.remove('loading');
                        
                        formFields.forEach(field => {
                            field.disabled = false;
                        });
                    } else {
                        alert('获取网站配置详情失败：' + (data.message || '未知错误'));
                        closeSiteConfigModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('获取网站配置详情时发生错误');
                    closeSiteConfigModal();
                });
        }

        // 为网站配置模态框添加事件监听器
        document.addEventListener('DOMContentLoaded', function() {
            const siteConfigModal = document.getElementById('siteConfigModal');
            if (siteConfigModal) {
                // 点击模态框外部关闭
                siteConfigModal.addEventListener('click', function(e) {
                    if (e.target === siteConfigModal) {
                        const modalBody = siteConfigModal.querySelector('.modal-body');
                        if (modalBody && modalBody.classList.contains('loading')) {
                            return;
                        }
                        closeSiteConfigModal();
                    }
                });
            }
        });
    </script>
</body>
</html> 