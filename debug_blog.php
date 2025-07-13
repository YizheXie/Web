<?php
require_once 'database.php';

echo "<h1>博客数据调试</h1>";

try {
    $db = Database::getInstance();
    echo "<p>✓ 数据库连接成功</p>";
    
    // 测试获取所有文章
    $articles = $db->getArticles();
    echo "<h2>文章数据：</h2>";
    echo "<p>找到 " . count($articles) . " 篇文章</p>";
    
    if (count($articles) > 0) {
        echo "<h3>文章列表：</h3>";
        foreach ($articles as $article) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
            echo "<h4>ID: " . $article['id'] . " - " . htmlspecialchars($article['title']) . "</h4>";
            echo "<p>状态: " . $article['status'] . "</p>";
            echo "<p>分类: " . htmlspecialchars($article['category_name'] ?? 'N/A') . "</p>";
            echo "<p>内容预览: " . htmlspecialchars(substr($article['content'], 0, 100)) . "...</p>";
            echo "<p>创建时间: " . $article['created_at'] . "</p>";
            echo "</div>";
        }
    }
    
    // 测试获取分类
    $categories = $db->getCategories();
    echo "<h2>分类数据：</h2>";
    echo "<p>找到 " . count($categories) . " 个分类</p>";
    
    if (count($categories) > 0) {
        echo "<h3>分类列表：</h3>";
        foreach ($categories as $category) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
            echo "<h4>ID: " . $category['id'] . " - " . htmlspecialchars($category['name']) . "</h4>";
            echo "<p>Slug: " . $category['slug'] . "</p>";
            echo "<p>文章数量: " . $category['article_count'] . "</p>";
            echo "</div>";
        }
    }
    
    // 测试获取推荐文章
    $featuredArticles = $db->getFeaturedArticles(3);
    echo "<h2>推荐文章：</h2>";
    echo "<p>找到 " . count($featuredArticles) . " 篇推荐文章</p>";
    
    if (count($featuredArticles) > 0) {
        echo "<h3>推荐文章列表：</h3>";
        foreach ($featuredArticles as $article) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
            echo "<h4>ID: " . $article['id'] . " - " . htmlspecialchars($article['title']) . "</h4>";
            echo "<p>推荐状态: " . ($article['is_featured'] ? '是' : '否') . "</p>";
            echo "<p>封面图片: " . htmlspecialchars($article['featured_image'] ?? 'N/A') . "</p>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ 错误: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='blog.php'>返回博客页面</a></p>";
?> 