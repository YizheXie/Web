<?php
require_once 'config.php';

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('Database connection failed');
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Get all articles with category info
    public function getArticles($limit = null, $category = null, $offset = 0) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.status = 'published'";
        
        if ($category) {
            $sql .= " AND c.slug = :category";
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->connection->prepare($sql);
        
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Get single article by ID
    public function getArticle($id) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.id = :id AND a.status = 'published'";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // Get all categories with article counts
    public function getCategories() {
        $sql = "SELECT c.*, COUNT(a.id) as article_count 
                FROM categories c 
                LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published' 
                GROUP BY c.id 
                ORDER BY c.name";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Save contact form submission
    public function saveContact($name, $email, $message) {
        $sql = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);
        
        return $stmt->execute();
    }
    
    // Get comments for an article with reply support
    public function getComments($articleId) {
        // 先获取主评论（parent_id为NULL的评论）
        $sql = "SELECT * FROM comments WHERE article_id = :article_id AND status = 'approved' AND parent_id IS NULL ORDER BY created_at DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();
        
        $comments = $stmt->fetchAll();
        
        // 为每个主评论获取回复
        foreach ($comments as &$comment) {
            $comment['replies'] = $this->getCommentReplies($comment['id']);
        }
        
        return $comments;
    }
    
    // Get replies for a specific comment
    public function getCommentReplies($commentId) {
        $sql = "SELECT * FROM comments WHERE parent_id = :parent_id AND status = 'approved' ORDER BY created_at ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':parent_id', $commentId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Save comment with reply support
    public function saveComment($articleId, $name, $email, $content, $address = '', $parentId = 0) {
        $sql = "INSERT INTO comments (article_id, author_name, author_email, content, author_address, parent_id) VALUES (:article_id, :name, :email, :content, :address, :parent_id)";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':address', $address);
        
        // 如果parent_id为0，插入NULL而不是0
        if ($parentId == 0) {
            $stmt->bindValue(':parent_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':parent_id', $parentId, PDO::PARAM_INT);
        }
        
        $success = $stmt->execute();
        
        // 如果是回复，更新父评论的回复计数
        if ($success && $parentId > 0) {
            $this->updateCommentReplyCount($parentId);
        }
        
        return $success;
    }
    
    // Update reply count for a comment
    private function updateCommentReplyCount($commentId) {
        $sql = "UPDATE comments SET reply_count = (SELECT COUNT(*) FROM comments c WHERE c.parent_id = :comment_id AND c.status = 'approved') WHERE id = :comment_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Search articles
    public function searchArticles($query, $limit = null, $offset = 0) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.status = 'published' AND (a.title LIKE :query1 OR a.content LIKE :query2 OR a.excerpt LIKE :query3) 
                ORDER BY a.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->connection->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->bindParam(':query1', $searchTerm);
        $stmt->bindParam(':query2', $searchTerm);
        $stmt->bindParam(':query3', $searchTerm);
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // ===== 管理员功能 =====
    
    // 管理员认证
    public function authenticateAdmin($username, $password) {
        $sql = "SELECT * FROM users WHERE username = :username AND role = 'admin'";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    // 获取所有文章（管理员用）
    public function getAllArticles($limit = null, $status = null) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE 1=1";
        
        if ($status) {
            $sql .= " AND a.status = :status";
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->connection->prepare($sql);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // 获取单个文章（管理员用，不限制状态）
    public function getArticleForAdmin($id) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug, u.username as author_name 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE a.id = :id";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // 创建文章
    public function createArticle($title, $content, $excerpt, $categoryId, $authorId, $status = 'draft') {
        $sql = "INSERT INTO articles (title, content, excerpt, category_id, author_id, status) VALUES (:title, :content, :excerpt, :category_id, :author_id, :status)";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':excerpt', $excerpt);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    // 创建文章（包含标签、封面图片、推荐状态）
    public function createArticleWithExtras($title, $content, $excerpt, $categoryId, $authorId, $status = 'draft', $tags = '', $featuredImage = '', $isFeatured = 0) {
        $sql = "INSERT INTO articles (title, content, excerpt, category_id, author_id, status, tags, featured_image, is_featured) VALUES (:title, :content, :excerpt, :category_id, :author_id, :status, :tags, :featured_image, :is_featured)";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':excerpt', $excerpt);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':author_id', $authorId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':featured_image', $featuredImage);
        $stmt->bindParam(':is_featured', $isFeatured, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // 更新文章
    public function updateArticle($id, $title, $content, $excerpt, $categoryId, $status) {
        $sql = "UPDATE articles SET title = :title, content = :content, excerpt = :excerpt, category_id = :category_id, status = :status WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':excerpt', $excerpt);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    // 更新文章（包含标签、封面图片、推荐状态）
    public function updateArticleWithExtras($id, $title, $content, $excerpt, $categoryId, $status, $tags = '', $featuredImage = '', $isFeatured = 0) {
        $sql = "UPDATE articles SET title = :title, content = :content, excerpt = :excerpt, category_id = :category_id, status = :status, tags = :tags, featured_image = :featured_image, is_featured = :is_featured WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':excerpt', $excerpt);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':tags', $tags);
        $stmt->bindParam(':featured_image', $featuredImage);
        $stmt->bindParam(':is_featured', $isFeatured, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // 删除文章
    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // 获取所有评论（管理员用）
    public function getAllComments($limit = null, $status = null) {
        $sql = "SELECT c.*, a.title as article_title 
                FROM comments c 
                LEFT JOIN articles a ON c.article_id = a.id 
                WHERE 1=1";
        
        if ($status) {
            $sql .= " AND c.status = :status";
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->connection->prepare($sql);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // 更新评论状态
    public function updateCommentStatus($id, $status) {
        $sql = "UPDATE comments SET status = :status WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    // 删除评论
    public function deleteComment($id) {
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // 获取所有联系信息（管理员用）
    public function getAllContacts($limit = null, $status = null) {
        $sql = "SELECT * FROM contacts WHERE 1=1";
        
        if ($status) {
            $sql .= " AND status = :status";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->connection->prepare($sql);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // 更新联系信息状态
    public function updateContactStatus($id, $status) {
        $sql = "UPDATE contacts SET status = :status WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    // 删除联系信息
    public function deleteContact($id) {
        $sql = "DELETE FROM contacts WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // 获取统计信息
    public function getStats() {
        $stats = [];
        
        // 文章统计
        $sql = "SELECT COUNT(*) as total_articles FROM articles";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $stats['total_articles'] = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as published_articles FROM articles WHERE status = 'published'";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $stats['published_articles'] = $stmt->fetchColumn();
        
        // 评论统计
        $sql = "SELECT COUNT(*) as total_comments FROM comments";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $stats['total_comments'] = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as pending_comments FROM comments WHERE status = 'pending'";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $stats['pending_comments'] = $stmt->fetchColumn();
        
        // 联系信息统计
        $sql = "SELECT COUNT(*) as total_contacts FROM contacts";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $stats['total_contacts'] = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) as new_contacts FROM contacts WHERE status = 'new'";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $stats['new_contacts'] = $stmt->fetchColumn();
        
        return $stats;
    }
    
    // ===== 博客功能增强 =====
    
    // 获取文章总数
    public function getArticleCount($category = null) {
        $sql = "SELECT COUNT(*) FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.status = 'published'";
        
        if ($category) {
            $sql .= " AND c.slug = :category";
        }
        
        $stmt = $this->connection->prepare($sql);
        
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    // 获取搜索结果总数
    public function getSearchCount($query) {
        $sql = "SELECT COUNT(*) FROM articles a 
                WHERE a.status = 'published' 
                AND (a.title LIKE :query1 OR a.content LIKE :query2 OR a.excerpt LIKE :query3)";
        
        $stmt = $this->connection->prepare($sql);
        $searchTerm = "%{$query}%";
        $stmt->bindParam(':query1', $searchTerm);
        $stmt->bindParam(':query2', $searchTerm);
        $stmt->bindParam(':query3', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    // 获取推荐文章
    public function getFeaturedArticles($limit = 3) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.status = 'published' AND a.is_featured = 1 
                ORDER BY a.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // 设置/取消推荐文章
    public function toggleFeaturedArticle($id, $featured = true) {
        $sql = "UPDATE articles SET is_featured = :featured WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':featured', $featured, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }
    
    // 获取单个文章（支持ID查询）
    public function getArticleById($id) {
        $sql = "SELECT a.*, c.name as category_name, c.slug as category_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE a.id = :id AND a.status = 'published'";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    // 增加文章浏览次数
    public function incrementViewCount($id) {
        $sql = "UPDATE articles SET view_count = view_count + 1 WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // ===== 最近活动功能 =====
    
    // 获取最近活动数据
    public function getRecentActivities($limit = 10) {
        $activities = [];
        
        // 获取最近的文章活动
        $sql = "SELECT 
                    a.id, a.title, a.status, a.created_at, a.updated_at,
                    u.username as author_name,
                    'article' as type
                FROM articles a 
                LEFT JOIN users u ON a.author_id = u.id 
                ORDER BY a.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll();
        
        foreach ($articles as $article) {
            $activities[] = [
                'type' => 'article',
                'action' => 'created',
                'item_id' => $article['id'],
                'title' => $article['title'],
                'status' => $article['status'],
                'author' => $article['author_name'],
                'time' => $article['created_at'],
                'icon' => '📝',
                'color' => $article['status'] === 'published' ? 'success' : 'warning'
            ];
        }
        
        // 获取最近的评论活动
        $sql = "SELECT 
                    c.id, c.author_name, c.content, c.status, c.created_at,
                    a.title as article_title,
                    'comment' as type
                FROM comments c 
                LEFT JOIN articles a ON c.article_id = a.id 
                ORDER BY c.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $comments = $stmt->fetchAll();
        
        foreach ($comments as $comment) {
            $activities[] = [
                'type' => 'comment',
                'action' => 'created',
                'item_id' => $comment['id'],
                'title' => $comment['article_title'],
                'content' => mb_substr($comment['content'], 0, 50) . '...',
                'status' => $comment['status'],
                'author' => $comment['author_name'],
                'time' => $comment['created_at'],
                'icon' => '💬',
                'color' => $comment['status'] === 'approved' ? 'success' : 
                          ($comment['status'] === 'pending' ? 'warning' : 'danger')
            ];
        }
        
        // 获取最近的联系信息活动
        $sql = "SELECT 
                    id, name, email, message, status, created_at,
                    'contact' as type
                FROM contacts 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $contacts = $stmt->fetchAll();
        
        foreach ($contacts as $contact) {
            $activities[] = [
                'type' => 'contact',
                'action' => 'created',
                'item_id' => $contact['id'],
                'title' => '来自 ' . $contact['name'] . ' 的联系',
                'content' => mb_substr($contact['message'], 0, 50) . '...',
                'status' => $contact['status'],
                'author' => $contact['name'],
                'email' => $contact['email'],
                'time' => $contact['created_at'],
                'icon' => '📧',
                'color' => $contact['status'] === 'new' ? 'primary' : 
                          ($contact['status'] === 'read' ? 'secondary' : 'success')
            ];
        }
        
        // 按时间排序
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        // 返回指定数量的活动
        return array_slice($activities, 0, $limit);
    }
}
?> 