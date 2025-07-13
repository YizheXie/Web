<?php
require_once 'database.php';
require_once 'site_config_helper.php';

$db = Database::getInstance();

// 获取网站配置
$location = SiteConfigHelper::getLocation();
$educationInfo = SiteConfigHelper::getEducationInfo();
$email = SiteConfigHelper::getEmail();
$githubUrl = SiteConfigHelper::getGithubUrl();
$wechatQr = SiteConfigHelper::getWechatQr();
$googleScholar = SiteConfigHelper::getGoogleScholar();
$orcid = SiteConfigHelper::getOrcid();

// 处理 AJAX 评论提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment']) && isset($_POST['ajax_request'])) {
    header('Content-Type: application/json');
    
    $articleId = intval($_POST['article_id']);
    $name = trim($_POST['comment_name']);
    $email = trim($_POST['comment_email']);
    $address = trim($_POST['comment_address'] ?? '');
    $content = trim($_POST['comment_content']);
    $parentId = intval($_POST['parent_id'] ?? 0);
    
    if ($articleId && $name && $email && $content) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $success = $db->saveComment($articleId, $name, $email, $content, $address, $parentId);
            if ($success) {
                echo json_encode(['success' => true, 'message' => '评论提交成功，等待管理员审核后显示']);
            } else {
                echo json_encode(['success' => false, 'message' => '评论提交失败，请重试']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '请输入有效的邮箱地址']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '请填写完整的评论信息']);
    }
    exit;
}

// 处理评论提交
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id']) && isset($_POST['comment_content'])) {
    $articleId = intval($_POST['article_id']);
    $name = trim($_POST['comment_name']);
    $email = trim($_POST['comment_email']);
    $address = trim($_POST['comment_address'] ?? '');
    $content = trim($_POST['comment_content']);
    $parentId = intval($_POST['parent_id'] ?? 0);
    
    if ($articleId && $name && $email && $content) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $success = $db->saveComment($articleId, $name, $email, $content, $address, $parentId);
            
            if ($success) {
                $commentMessage = "评论提交成功，等待管理员审核后显示。";
                $commentMessageType = "success";
            } else {
                $commentMessage = "评论提交失败，请重试。";
                $commentMessageType = "error";
            }
        } else {
            $commentMessage = "请输入有效的邮箱地址。";
            $commentMessageType = "error";
        }
    } else {
        $commentMessage = "请填写完整的评论信息。";
        $commentMessageType = "error";
    }
}

// 处理评论成功的提示信息
if (isset($_GET['comment_success']) && $_GET['comment_success'] == '1') {
    $commentMessage = "评论提交成功，等待管理员审核后显示。";
    $commentMessageType = "success";
}

// 获取请求参数
$articleId = $_GET['id'] ?? null;
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = intval($_GET['page'] ?? 1);
$limit = 6; // 每页显示6篇文章

// 单个文章查看模式
$singleArticleMode = !empty($articleId);
$singleArticle = null;
$articles = [];
$totalArticles = 0;
$totalPages = 1;
$comments = [];

if ($singleArticleMode) {
    $singleArticle = $db->getArticleById($articleId);
    if (!$singleArticle) {
        header('HTTP/1.0 404 Not Found');
        exit('文章不存在');
    }
    // 更新浏览次数
    $db->incrementViewCount($articleId);
    // 获取文章评论
    $comments = $db->getComments($articleId);
} else {
    // 计算偏移量
    $offset = ($page - 1) * $limit;

    // 获取文章列表
    if ($search) {
        $articles = $db->searchArticles($search, $limit, $offset);
        $totalArticles = $db->getSearchCount($search);
    } else {
        $articles = $db->getArticles($limit, $category, $offset);
        $totalArticles = $db->getArticleCount($category);
    }

    // 计算总页数
    $totalPages = ceil($totalArticles / $limit);
}

// 获取分类列表和统计
$categories = $db->getCategories();

// 获取推荐文章
$featuredArticles = $db->getFeaturedArticles(3);

// 当前选中的分类
$currentCategory = $category ?: 'all';

// 调试信息（已移除）
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo htmlspecialchars(SiteConfigHelper::getSiteTitle()); ?> - 博客</title>
    <link rel="icon" href="./static/img/icon/icon-nav.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="./static/css/style.css">
    <link rel="stylesheet" href="./static/css/root.css">
    <link rel="stylesheet" href="./static/css/blog.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/toolbar/prism-toolbar.min.css">
</head>

<body>

    
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    <div class="filter"></div>

    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <img src="./static/img/icon/icon-nav.png" alt="Logo">
                    <span><?php echo htmlspecialchars(SiteConfigHelper::getWelcomeName()); ?></span>
                </a>
            </div>
            <div class="nav-links">
                <a href="index.php">首页</a>
                <a href="blog.php" class="active">博客</a>
                <a href="articles.php">推荐阅读</a>
                <a href="about.php">关于我</a>
            </div>
            <div class="nav-toggle">
                <div class="toggle-line"></div>
                <div class="toggle-line"></div>
                <div class="toggle-line"></div>
            </div>
        </div>
    </nav>

    <div class="main">
        <div class="left">
            <div class="logo">
            </div>
            <div class="left-div left-des">
                <div class="left-des-item">
                    <svg t="1705773709627" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="1478">
                        <path
                            d="M512 249.976471c-99.388235 0-180.705882 81.317647-180.705882 180.705882s81.317647 180.705882 180.705882 180.705882 180.705882-81.317647 180.705882-180.705882-81.317647-180.705882-180.705882-180.705882z m0 301.17647c-66.258824 0-120.470588-54.211765-120.470588-120.470588s54.211765-120.470588 120.470588-120.470588 120.470588 54.211765 120.470588 120.470588-54.211765 120.470588-120.470588 120.470588z"
                            p-id="1479"></path>
                        <path
                            d="M512 39.152941c-216.847059 0-391.529412 174.682353-391.529412 391.529412 0 349.364706 391.529412 572.235294 391.529412 572.235294s391.529412-222.870588 391.529412-572.235294c0-216.847059-174.682353-391.529412-391.529412-391.529412z m0 891.482353C424.658824 873.411765 180.705882 686.682353 180.705882 430.682353c0-183.717647 147.576471-331.294118 331.294118-331.294118s331.294118 147.576471 331.294118 331.294118c0 256-243.952941 442.729412-331.294118 499.952941z"
                            p-id="1480"></path>
                    </svg>
                    <?php echo htmlspecialchars($location); ?>
                </div>
                <div class="left-des-item">
                    <svg t="1705773906032" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="2474">
                        <path
                            d="M729.6 234.666667H294.4V157.866667a51.2 51.2 0 0 1 51.2-51.2h332.8a51.2 51.2 0 0 1 51.2 51.2v76.8z m179.2 51.2a51.2 51.2 0 0 1 51.2 51.2v512a51.2 51.2 0 0 1-51.2 51.2H115.2a51.2 51.2 0 0 1-51.2-51.2v-512a51.2 51.2 0 0 1 51.2-51.2h793.557333z m-768 172.032c0 16.384 13.312 29.696 29.696 29.696h683.008a29.696 29.696 0 1 0 0-59.392H170.410667a29.696 29.696 0 0 0-29.696 29.696z m252.416 118.784c0 16.384 13.312 29.696 29.696 29.696h178.176a29.696 29.696 0 1 0 0-59.392H422.912a29.738667 29.738667 0 0 0-29.696 29.696z"
                            p-id="2475"></path>
                    </svg>
                    <?php echo htmlspecialchars($educationInfo); ?>
                </div>
            </div>
            <div class="left-div">
                <div class="category-title">博客分类</div>
                <div class="category-list">
                    <div><a href="blog.php" class="category-link <?php echo $currentCategory === 'all' ? 'active' : ''; ?>">全部文章<span
                                class="category-count">(<?php echo $singleArticleMode ? $db->getArticleCount() : $totalArticles; ?>)</span></a></div>
                    <?php foreach ($categories as $cat): ?>
                    <div><a href="blog.php?category=<?php echo $cat['slug']; ?>" class="category-link <?php echo $currentCategory === $cat['slug'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat['name']); ?><span
                                class="category-count">(<?php echo $cat['article_count']; ?>)</span></a></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="right">
            <header>
                <div class="index-logo">
                </div>
                <div class="welcome">
                    My Blog
                </div>
                <div class="description">✨ 记录学习与生活 <span class="purpleText">Share & Learn</span>
                </div>

                <div class="iconContainer">
                    <a class="iconItem" href="index.php">
                        <svg t="1742999202713" class="icon" viewBox="0 0 1029 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="1490" width="200" height="200">
                            <path
                                d="M1001.423238 494.592q21.504 20.48 22.528 45.056t-16.384 40.96q-19.456 17.408-45.056 16.384t-40.96-14.336q-5.12-4.096-31.232-28.672t-62.464-58.88-77.824-73.728-78.336-74.24-63.488-60.416-33.792-31.744q-32.768-29.696-64.512-28.672t-62.464 28.672q-10.24 9.216-38.4 35.328t-65.024 60.928-77.824 72.704-75.776 70.656-59.904 55.808-30.208 27.136q-15.36 12.288-40.96 13.312t-44.032-15.36q-20.48-18.432-19.456-44.544t17.408-41.472q6.144-6.144 37.888-35.84t75.776-70.656 94.72-88.064 94.208-88.064 74.752-70.144 36.352-34.304q38.912-37.888 83.968-38.4t76.8 30.208q6.144 5.12 25.6 24.064t47.616 46.08 62.976 60.928 70.656 68.096 70.144 68.096 62.976 60.928 48.128 46.592zM447.439238 346.112q25.6-23.552 61.44-25.088t64.512 25.088q3.072 3.072 18.432 17.408l38.912 35.84q22.528 21.504 50.688 48.128t57.856 53.248q68.608 63.488 153.6 142.336l0 194.56q0 22.528-16.896 39.936t-45.568 18.432l-193.536 0 0-158.72q0-33.792-31.744-33.792l-195.584 0q-17.408 0-24.064 10.24t-6.656 23.552q0 6.144-0.512 31.232t-0.512 53.76l0 73.728-187.392 0q-29.696 0-47.104-13.312t-17.408-37.888l0-203.776q83.968-76.8 152.576-139.264 28.672-26.624 57.344-52.736t52.224-47.616 39.424-36.352 19.968-18.944z"
                                p-id="1491"></path>
                        </svg>
                        <div class="iconTip">Home</div>
                    </a><a class="iconItem" href="#">
                        <svg t="1704870335945" class="icon" viewBox="0 0 1024 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="2487">
                            <path
                                d="M511.6 76.3C264.3 76.2 64 276.4 64 523.5 64 718.9 189.3 885 363.8 946c23.5 5.9 19.9-10.8 19.9-22.2v-77.5c-135.7 15.9-141.2-73.9-150.3-88.9C215 726 171.5 718 184.5 703c30.9-15.9 62.4 4 98.9 57.9 26.4 39.1 77.9 32.5 104 26 5.7-23.5 17.9-44.5 34.7-60.8-140.6-25.2-199.2-111-199.2-213 0-49.5 16.3-95 48.3-131.7-20.4-60.5 1.9-112.3 4.9-120 58.1-5.2 118.5 41.6 123.2 45.3 33-8.9 70.7-13.6 112.9-13.6 42.4 0 80.2 4.9 113.5 13.9 11.3-8.6 67.3-48.8 121.3-43.9 2.9 7.7 24.7 58.3 5.5 118 32.4 36.8 48.9 82.7 48.9 132.3 0 102.2-59 188.1-200 212.9 23.5 23.2 38.1 55.4 38.1 91v112.5c0.8 9 0 17.9 15 17.9 177.1-59.7 304.6-227 304.6-424.1 0-247.2-200.4-447.3-447.5-447.3z"
                                p-id="2488"></path>
                        </svg>
                        <div class="iconTip">Github</div>
                    </a><a class="iconItem" href="mailto:xieyizhe66@gmail.com">
                        <svg t="1704870588438" class="icon" viewBox="0 0 1024 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="3174">
                            <path
                                d="M926.47619 355.644952V780.190476a73.142857 73.142857 0 0 1-73.142857 73.142857H170.666667a73.142857 73.142857 0 0 1-73.142857-73.142857V355.644952l304.103619 257.828572a170.666667 170.666667 0 0 0 220.745142 0L926.47619 355.644952zM853.333333 170.666667a74.044952 74.044952 0 0 1 26.087619 4.778666 72.704 72.704 0 0 1 30.622477 22.186667 73.508571 73.508571 0 0 1 10.678857 17.67619c3.169524 7.509333 5.12 15.652571 5.607619 24.210286L926.47619 243.809524v24.380952L559.469714 581.241905a73.142857 73.142857 0 0 1-91.306666 2.901333l-3.632762-2.925714L97.52381 268.190476v-24.380952a72.899048 72.899048 0 0 1 40.155428-65.292191A72.97219 72.97219 0 0 1 170.666667 170.666667h682.666666z"
                                p-id="3175"></path>
                        </svg>
                        <div class="iconTip">Mail</div>
                    </a><a class="switch" href="javascript:void(0)">
                        <div class="onoffswitch">
                            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch"
                                checked>
                            <label class="onoffswitch-label" for="myonoffswitch">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </a>
                </div>
            </header>

            <main>

                <?php if ($singleArticleMode): ?>
                <!-- 单个文章查看模式 -->
                <div class="title">
                    <svg t="1742999449737" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="5674" width="200" height="200">
                        <path
                            d="M51.8 896.1c0 22.6 18.3 41 41 41h738.3c22.6 0 41-18.3 41-41 0-22.6-18.3-41-41-41H92.8c-22.7 0-41 18.4-41 41zM51.8 155.2c0 22.6 18.3 41 41 41h668.1c22.6 0 41-18.3 41-41 0-22.6-18.3-41-41-41H92.8c-22.7 0-41 18.4-41 41zM388.9 737.2c19.5 19.5 51.2 19.5 70.7 0l509.3-509.3c19.5-19.5 19.5-51.2 0-70.7s-51.2-19.5-70.7 0L388.9 666.4c-19.6 19.6-19.6 51.2 0 70.8z"
                            p-id="5675"></path>
                        <path
                            d="M832.4 935.7c22.6 0 41-18.3 41-41V531c0-22.6-18.3-41-41-41-22.6 0-41 18.3-41 41v363.8c0 22.6 18.3 40.9 41 40.9zM91.5 935.7c22.6 0 41-18.3 41-41V156.5c0-22.6-18.3-41-41-41-22.6 0-41 18.3-41 41v738.3c0 22.6 18.3 40.9 41 40.9z"
                            p-id="5676"></path>
                    </svg>
                    文章详情
                </div>
                <?php else: ?>
                <!-- 文章列表模式 -->
                <div class="title">
                    <svg t="1742999449737" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="5674" width="200" height="200">
                        <path
                            d="M51.8 896.1c0 22.6 18.3 41 41 41h738.3c22.6 0 41-18.3 41-41 0-22.6-18.3-41-41-41H92.8c-22.7 0-41 18.4-41 41zM51.8 155.2c0 22.6 18.3 41 41 41h668.1c22.6 0 41-18.3 41-41 0-22.6-18.3-41-41-41H92.8c-22.7 0-41 18.4-41 41zM388.9 737.2c19.5 19.5 51.2 19.5 70.7 0l509.3-509.3c19.5-19.5 19.5-51.2 0-70.7s-51.2-19.5-70.7 0L388.9 666.4c-19.6 19.6-19.6 51.2 0 70.8z"
                            p-id="5675"></path>
                        <path
                            d="M832.4 935.7c22.6 0 41-18.3 41-41V531c0-22.6-18.3-41-41-41-22.6 0-41 18.3-41 41v363.8c0 22.6 18.3 40.9 41 40.9zM91.5 935.7c22.6 0 41-18.3 41-41V156.5c0-22.6-18.3-41-41-41-22.6 0-41 18.3-41 41v738.3c0 22.6 18.3 40.9 41 40.9z"
                            p-id="5676"></path>
                    </svg>
                    <?php echo $search ? "搜索结果" : "最新文章"; ?>
                </div>
                <?php endif; ?>

                <?php if (!$singleArticleMode): ?>
                <div class="blog-search">
                    <form method="GET" action="blog.php">
                        <svg class="search-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>
                        <input type="text" name="search" placeholder="搜索文章..." value="<?php echo htmlspecialchars($search); ?>" />
                        <?php if ($category): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>" />
                        <?php endif; ?>
                    </form>
                </div>

                <!-- 移动端分类菜单 -->
                <div class="mobile-category-menu">
                    <div class="mobile-category-btn">
                        <svg t="1743066703060" class="icon" viewBox="0 0 1024 1024" version="1.1"
                            xmlns="http://www.w3.org/2000/svg" p-id="1523" width="200" height="200">
                            <path
                                d="M575.68 736a160.32 160.32 0 1 0 160.32-160.32H599.893333c-13.461333 0-24.192 10.752-24.192 24.192V736zM736 533.333333A202.666667 202.666667 0 1 1 533.333333 736V599.893333A66.432 66.432 0 0 1 599.872 533.333333H736zM490.666667 736a202.666667 202.666667 0 1 1-202.666667-202.666667h136.128A66.432 66.432 0 0 1 490.666667 599.872V736z m-202.666667-160.32a160.32 160.32 0 1 0 160.32 160.32V599.893333c0-13.44-10.730667-24.192-24.192-24.192H288zM533.333333 287.978667A202.666667 202.666667 0 1 1 736 490.666667H599.893333A66.432 66.432 0 0 1 533.333333 424.128V288z m202.666667 160.341333a160.32 160.32 0 1 0-160.32-160.32v136.128c0 13.44 10.730667 24.192 24.192 24.192H736zM448.32 288a160.32 160.32 0 1 0-160.32 160.32h136.128c13.461333 0 24.192-10.752 24.192-24.192V288zM288 490.666667A202.666667 202.666667 0 1 1 490.666667 288v136.128A66.432 66.432 0 0 1 424.128 490.666667H288z"
                                p-id="1524"></path>
                        </svg>
                        博客分类
                    </div>
                    <div class="mobile-category-dropdown">
                        <div><a href="blog.php" class="category-link <?php echo $currentCategory === 'all' ? 'active' : ''; ?>">全部文章<span
                                    class="category-count">(<?php echo $singleArticleMode ? $db->getArticleCount() : $totalArticles; ?>)</span></a></div>
                        <?php foreach ($categories as $cat): ?>
                        <div><a href="blog.php?category=<?php echo $cat['slug']; ?>" class="category-link <?php echo $currentCategory === $cat['slug'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat['name']); ?><span
                                    class="category-count">(<?php echo $cat['article_count']; ?>)</span></a></div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($singleArticleMode): ?>
                <!-- 单个文章显示 -->
                <div class="blog-page active">
                    <article class="blog-post blog-expanded" data-id="<?php echo $singleArticle['id']; ?>" data-category="<?php echo htmlspecialchars($singleArticle['category_slug']); ?>">
                        <div class="blog-header">
                            <div class="blog-title"><?php echo htmlspecialchars($singleArticle['title']); ?></div>
                            <div class="blog-category-corner">
                                <span class="blog-category-tag"><?php echo htmlspecialchars($singleArticle['category_name']); ?></span>
                            </div>
                        </div>
                        <div class="blog-meta">
                            <span class="blog-date"><?php echo date('Y-m-d', strtotime($singleArticle['created_at'])); ?></span>
                            <?php if ($singleArticle['tags']): ?>
                            <?php foreach (explode(',', $singleArticle['tags']) as $tag): ?>
                            <span class="blog-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            <span class="blog-views">👁 浏览 <?php echo $singleArticle['view_count'] ?? 0; ?> 次</span>
                        </div>
                        <div class="blog-content">
                            <?php echo nl2br(htmlspecialchars($singleArticle['content'])); ?>
                        </div>
                        <div class="blog-actions-inline">
                            <a href="blog.php" class="btn-inline btn-primary">返回博客列表</a>
                            <a href="blog.php?category=<?php echo $singleArticle['category_slug']; ?>" class="btn-inline btn-secondary">查看同类文章</a>
                        </div>
                    </article>

                    <!-- 评论区域 -->
                    <div class="comments-section">
                        <h3 class="comments-title">
                            <svg t="1704870588438" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <path d="M853.333333 170.666667a74.044952 74.044952 0 0 1 26.087619 4.778666 72.704 72.704 0 0 1 30.622477 22.186667 73.508571 73.508571 0 0 1 10.678857 17.67619c3.169524 7.509333 5.12 15.652571 5.607619 24.210286L926.47619 243.809524v24.380952L559.469714 581.241905a73.142857 73.142857 0 0 1-91.306666 2.901333l-3.632762-2.925714L97.52381 268.190476v-24.380952a72.899048 72.899048 0 0 1 40.155428-65.292191A72.97219 72.97219 0 0 1 170.666667 170.666667h682.666666z"></path>
                            </svg>
                            评论
                        </h3>
                        
                        <div class="comments-stats">
                            <span>已有 <strong><?php echo count($comments); ?></strong> 条评论</span>
                        </div>

                        <!-- 评论列表 -->
                        <?php if (!empty($comments)): ?>
                        <div class="comments-list">
                            <?php foreach ($comments as $comment): ?>
                            <div class="comment-item" data-comment-id="<?php echo $comment['id']; ?>">
                                <div class="comment-avatar">
                                    <div class="avatar-circle">
                                        <?php echo strtoupper(substr($comment['author_name'], 0, 1)); ?>
                                    </div>
                                </div>
                                <div class="comment-main">
                                    <div class="comment-header">
                                        <span class="comment-author"><?php echo htmlspecialchars($comment['author_name']); ?></span>
                                        <?php if ($comment['author_address']): ?>
                                        <span class="comment-address">IP: <?php echo htmlspecialchars($comment['author_address']); ?></span>
                                        <?php endif; ?>
                                        <!-- <span class="comment-badge">🎖️</span> -->
                                        <span class="comment-date"><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                    <div class="comment-content">
                                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                    </div>
                                    <div class="comment-actions">
                                        <a href="#" class="comment-reply-btn" data-comment-id="<?php echo $comment['id']; ?>" data-author="<?php echo htmlspecialchars($comment['author_name']); ?>">回复</a>
                                        <?php if (!empty($comment['replies'])): ?>
                                        <span class="comment-replies-count"><?php echo count($comment['replies']); ?> 条回复</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 显示回复 -->
                            <?php if (!empty($comment['replies'])): ?>
                            <div class="comment-replies">
                                <?php foreach ($comment['replies'] as $reply): ?>
                                <div class="comment-item comment-reply" data-comment-id="<?php echo $reply['id']; ?>">
                                    <div class="comment-avatar">
                                        <div class="avatar-circle reply-avatar">
                                            <?php echo strtoupper(substr($reply['author_name'], 0, 1)); ?>
                                        </div>
                                    </div>
                                    <div class="comment-main">
                                        <div class="comment-header">
                                            <span class="comment-author"><?php echo htmlspecialchars($reply['author_name']); ?></span>
                                            <?php if ($reply['author_address']): ?>
                                            <span class="comment-address">IP: <?php echo htmlspecialchars($reply['author_address']); ?></span>
                                            <?php endif; ?>
                                            <!-- <span class="comment-badge">💬</span> -->
                                            <span class="comment-date"><?php echo date('Y-m-d H:i', strtotime($reply['created_at'])); ?></span>
                                        </div>
                                        <div class="comment-content">
                                            <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="no-comments">
                            <p>暂无评论，快来抢沙发吧！</p>
                        </div>
                        <?php endif; ?>

                        <!-- 评论表单 -->
                        <div class="comment-form-section">
                            <h4>
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                    <path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                                </svg>
                                发表评论
                            </h4>
                            
                            <?php if (isset($commentMessage)): ?>
                            <div class="comment-message <?php echo $commentMessageType; ?>">
                                <?php echo htmlspecialchars($commentMessage); ?>
                            </div>
                            <?php endif; ?>

                            <form method="POST" class="comment-form" id="commentForm">
                                <input type="hidden" name="article_id" value="<?php echo $singleArticle['id']; ?>">
                                <input type="hidden" name="parent_id" value="0" id="parentId">
                                
                                <div class="reply-indicator" id="replyIndicator">
                                    <span class="reply-text">回复给 <strong id="replyTo"></strong></span>
                                    <a href="#" class="cancel-reply" id="cancelReply">取消</a>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <input type="text" id="comment_name" name="comment_name" required maxlength="50"
                                               placeholder="昵称" value="">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" id="comment_email" name="comment_email" required maxlength="100"
                                               placeholder="邮箱" value="">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" id="comment_address" name="comment_address" maxlength="200"
                                               placeholder="地址" value="">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <textarea id="comment_content" name="comment_content" rows="4" required maxlength="1000"
                                              placeholder="说点什么..."></textarea>
                                </div>
                                
                                <div class="comment-form-bottom">
                                    <div class="comment-emoji">
                                        <span class="emoji-item" data-emoji="😊">😊</span>
                                        <span class="emoji-item" data-emoji="😎">😎</span>
                                        <span class="emoji-item" data-emoji="😍">😍</span>
                                        <span class="emoji-item" data-emoji="🤔">🤔</span>
                                        <span class="emoji-item" data-emoji="👍">👍</span>
                                        <span class="emoji-item" data-emoji="❤️">❤️</span>
                                    </div>
                                    <button type="submit" name="submit_comment" class="btn btn-primary">
                                        <span id="submitButtonText">发表评论</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- 博客文章列表 -->
                <div class="blog-page active">
                    <?php if (!empty($articles)): ?>
                    <?php foreach ($articles as $article): ?>
                    <article class="blog-post blog-collapsed" data-id="<?php echo $article['id']; ?>" data-category="<?php echo htmlspecialchars($article['category_slug']); ?>">
                        <div class="blog-header">
                            <div class="blog-title" onclick="toggleBlogExpand(this.parentNode)"><?php echo htmlspecialchars($article['title']); ?></div>
                            <div class="blog-category-corner">
                                <span class="blog-category-tag"><?php echo htmlspecialchars($article['category_name']); ?></span>
                            </div>
                        </div>
                        <div class="blog-meta">
                            <span class="blog-date"><?php echo date('Y-m-d', strtotime($article['created_at'])); ?></span>
                            <?php if ($article['tags']): ?>
                            <?php foreach (explode(',', $article['tags']) as $tag): ?>
                            <span class="blog-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="blog-content">
                            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                        </div>
                        <a href="blog.php?id=<?php echo $article['id']; ?>" class="blog-more">阅读全文</a>
                    </article>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <!-- 无搜索结果提示 -->
                    <div class="no-search-results">
                        <div class="no-results-icon">
                            <svg t="1704870588438" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <path d="M448 384c-8.832 0-16-7.168-16-16s7.168-16 16-16 16 7.168 16 16-7.168 16-16 16m0-64c-26.496 0-48 21.504-48 48s21.504 48 48 48 48-21.504 48-48-21.504-48-48-48m128 64c-8.832 0-16-7.168-16-16s7.168-16 16-16 16 7.168 16 16-7.168 16-16 16m0-64c-26.496 0-48 21.504-48 48s21.504 48 48 48 48-21.504 48-48-21.504-48-48-48M304 824c4.736 0 9.408-2.016 12.576-5.856l124.64-148.544c2.048-2.432 3.136-5.504 3.136-8.704 0-6.592-4.864-12.224-11.392-13.632l-28.64-6.176c-10.144-2.24-20.64-3.392-31.2-3.392-70.688 0-128 57.312-128 128 0 5.12 0.32 10.176 0.832 15.2l2.112 30.688c0.64 9.248 8.352 16.416 17.6 16.416h38.336zM720 824c4.736 0 9.408-2.016 12.576-5.856l124.64-148.544c2.048-2.432 3.136-5.504 3.136-8.704 0-6.592-4.864-12.224-11.392-13.632l-28.64-6.176c-10.144-2.24-20.64-3.392-31.2-3.392-70.688 0-128 57.312-128 128 0 5.12 0.32 10.176 0.832 15.2l2.112 30.688c0.64 9.248 8.352 16.416 17.6 16.416h38.336z" p-id="7834"></path>
                            </svg>
                        </div>
                        <h3>暂无搜索结果</h3>
                        <p>
                            <?php if ($search): ?>
                            没有找到与 "<?php echo htmlspecialchars($search); ?>" 相关的文章
                            <?php else: ?>
                            当前分类下暂无文章
                            <?php endif; ?>
                        </p>
                        <div class="no-results-suggestions">
                            <p>建议您：</p>
                            <ul>
                                <li>检查输入的关键词是否正确</li>
                                <li>尝试更换关键词重新搜索</li>
                                <li>使用更简单的关键词</li>
                            </ul>
                        </div>
                        <div class="no-results-actions">
                            <a href="blog.php" class="btn btn-primary">查看所有文章</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- 分页 -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="blog.php?page=<?php echo $i; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                       class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>

                <!-- 文章推荐 -->
                <?php if (!empty($featuredArticles)): ?>
                <div class="title">
                    <svg t="1705257823317" class="icon" viewBox="0 0 1024 1024" version="1.1"
                        xmlns="http://www.w3.org/2000/svg" p-id="7833">
                        <path
                            d="M395.765333 586.570667h-171.733333c-22.421333 0-37.888-22.442667-29.909333-43.381334L364.768 95.274667A32 32 0 0 1 394.666667 74.666667h287.957333c22.72 0 38.208 23.018667 29.632 44.064l-99.36 243.882666h187.050667c27.509333 0 42.186667 32.426667 24.042666 53.098667l-458.602666 522.56c-22.293333 25.408-63.626667 3.392-54.976-29.28l85.354666-322.421333zM416.714667 138.666667L270.453333 522.581333h166.869334a32 32 0 0 1 30.933333 40.181334l-61.130667 230.954666 322.176-367.114666H565.312c-22.72 0-38.208-23.018667-29.632-44.064l99.36-243.882667H416.714667z"
                            p-id="7834"></path>
                    </svg>
                    文章推荐
                </div>

                <div class="projectList">
                    <?php foreach ($featuredArticles as $featured): ?>
                    <a class="recommendCard" href="blog.php?id=<?php echo $featured['id']; ?>">
                        <div class="recommendCard-img">
                            <img src="<?php echo htmlspecialchars($featured['featured_image'] ?: './static/img/background/background1.png'); ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>">
                            <div class="recommendCard-overlay">
                                <div class="recommendCard-overlay-title"><?php echo htmlspecialchars($featured['title']); ?></div>
                            </div>
                        </div>
                        <div class="recommendCard-content">
                            <div class="recommendCard-meta">
                                <div>
                                    <?php if ($featured['tags']): ?>
                                    <?php foreach (explode(',', $featured['tags']) as $tag): ?>
                                    <span class="recommendCard-tag"># <?php echo htmlspecialchars(trim($tag)); ?></span>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="recommendCard-date"><?php echo date('Y-m-d', strtotime($featured['created_at'])); ?></div>
                            </div>
                            <div class="recommendCard-desc"><?php echo htmlspecialchars($featured['excerpt']); ?></div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- 尾注 -->
    <footer>
        <?php echo htmlspecialchars(SiteConfigHelper::getFooterText()); ?>
    </footer>

    <script src="./static/js/script.js"></script>
    <script src="./static/js/comments.js"></script>
    <script src="./static/js/quotes.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-css.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-markup.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/toolbar/prism-toolbar.min.js"></script>
    <script
        src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js"></script>
</body>

</html> 