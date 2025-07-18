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
    
    // 创建文章（包含标签、封面图片、推荐状态、AI生成标记）
    public function createArticleWithExtras($title, $content, $excerpt, $categoryId, $authorId, $status = 'draft', $tags = '', $featuredImage = '', $isFeatured = 0, $isAiGenerated = 0) {
        $sql = "INSERT INTO articles (title, content, excerpt, category_id, author_id, status, tags, featured_image, is_featured, is_ai_generated) VALUES (:title, :content, :excerpt, :category_id, :author_id, :status, :tags, :featured_image, :is_featured, :is_ai_generated)";
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
        $stmt->bindParam(':is_ai_generated', $isAiGenerated, PDO::PARAM_INT);
        
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
    
    // 更新文章（包含标签、封面图片、推荐状态、AI生成标记）
    public function updateArticleWithExtras($id, $title, $content, $excerpt, $categoryId, $status, $tags = '', $featuredImage = '', $isFeatured = 0, $isAiGenerated = 0) {
        $sql = "UPDATE articles SET title = :title, content = :content, excerpt = :excerpt, category_id = :category_id, status = :status, tags = :tags, featured_image = :featured_image, is_featured = :is_featured, is_ai_generated = :is_ai_generated WHERE id = :id";
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
        $stmt->bindParam(':is_ai_generated', $isAiGenerated, PDO::PARAM_INT);
        
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
    
    // 获取推荐文章数量
    public function getFeaturedArticleCount() {
        $sql = "SELECT COUNT(*) FROM articles WHERE is_featured = 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
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
        // 优先从活动日志表获取记录的活动
        $sql = "SELECT 
                    al.type, al.action, al.item_id, al.item_title, al.details, al.created_at,
                    u.username as admin_name
                FROM activity_logs al
                LEFT JOIN users u ON al.admin_id = u.id
                ORDER BY al.created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $logActivities = $stmt->fetchAll();
        
        $activities = [];
        
        foreach ($logActivities as $log) {
            $iconMap = [
                'article' => [
                    'created' => '📝',
                    'updated' => '✏️',
                    'deleted' => '🗑️',
                    'featured_toggled' => '⭐'
                ],
                'recommendation' => [
                    'created' => '⭐',
                    'updated' => '✏️',
                    'deleted' => '🗑️',
                    'status_changed' => '🔄'
                ],
                'comment' => [
                    'created' => '💬',
                    'status_changed' => '🔄',
                    'deleted' => '🗑️'
                ],
                'contact' => [
                    'created' => '📧',
                    'status_changed' => '🔄',
                    'deleted' => '🗑️'
                ]
            ];
            
            $colorMap = [
                'article' => 'success',
                'recommendation' => 'primary', 
                'comment' => 'warning',
                'contact' => 'secondary'
            ];
            
            // 根据操作类型和详情确定状态
            $displayStatus = 'active';
            if ($log['action'] === 'deleted') {
                $displayStatus = 'deleted';
            } elseif ($log['type'] === 'recommendation' && $log['action'] === 'status_changed') {
                // 推荐内容状态：停用 -> inactive，已启用 -> active
                $displayStatus = strpos($log['details'], '停用') !== false ? 'inactive' : 'active';
            } elseif ($log['type'] === 'article') {
                if ($log['action'] === 'featured_toggled') {
                    // 文章推荐操作：取消推荐 -> inactive，设为推荐 -> active
                    $displayStatus = strpos($log['details'], '取消推荐') !== false ? 'inactive' : 'active';
                } elseif ($log['action'] === 'updated' || $log['action'] === 'created') {
                    // 文章编辑/创建操作：需要查询文章实际状态
                    $articleSql = "SELECT status FROM articles WHERE id = :id";
                    $articleStmt = $this->connection->prepare($articleSql);
                    $articleStmt->bindParam(':id', $log['item_id'], PDO::PARAM_INT);
                    $articleStmt->execute();
                    $articleStatus = $articleStmt->fetchColumn();
                    
                    $displayStatus = $articleStatus === 'published' ? 'published' : 'draft';
                } else {
                    $displayStatus = 'active';
                }
            }
            
            $activities[] = [
                'type' => $log['type'],
                'action' => $log['action'],
                'item_id' => $log['item_id'],
                'title' => $log['item_title'],
                'content' => $log['details'],
                'status' => $displayStatus,
                'author' => $log['admin_name'] ?: 'admin',
                'time' => $log['created_at'],
                'icon' => $iconMap[$log['type']][$log['action']] ?? '📄',
                'color' => $colorMap[$log['type']] ?? 'secondary'
            ];
        }
        
        // 如果日志记录不足，补充一些最近的直接活动
        if (count($activities) < $limit) {
            $remaining = $limit - count($activities);
            
            // 获取最近的评论（如果没有在日志中记录）
            $sql = "SELECT 
                        c.id, c.author_name, c.content, c.status, c.created_at,
                        a.title as article_title
                    FROM comments c 
                    LEFT JOIN articles a ON c.article_id = a.id 
                    WHERE c.created_at > COALESCE((SELECT MAX(created_at) FROM activity_logs WHERE type = 'comment'), '1970-01-01')
                    ORDER BY c.created_at DESC 
                    LIMIT :remaining";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':remaining', $remaining, PDO::PARAM_INT);
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
        }
        
        // 按时间排序
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        // 返回指定数量的活动
        return array_slice($activities, 0, $limit);
    }
    
    // 推荐内容相关方法
    // 获取所有推荐内容
    public function getAllRecommendations($limit = null, $category = null, $status = 'active') {
        $sql = "SELECT * FROM recommendations WHERE 1=1";
        $params = [];
        
        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        
        if ($category) {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }
        
        $sql .= " ORDER BY sort_order ASC, created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $limit;
        }
        
        $stmt = $this->connection->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key === 'limit') {
                $stmt->bindValue(':limit', (int)$value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':' . $key, $value);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // 获取推荐内容（按分类）
    public function getRecommendationsByCategory($category, $limit = null) {
        return $this->getAllRecommendations($limit, $category, 'active');
    }
    
    // 获取单个推荐内容
    public function getRecommendation($id) {
        $sql = "SELECT * FROM recommendations WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // 创建推荐内容
    public function createRecommendation($title, $url, $image, $tags, $description, $category, $date, $status = 'active', $sortOrder = 0) {
        $sql = "INSERT INTO recommendations (title, url, image, tags, description, category, date, status, sort_order) 
                VALUES (:title, :url, :image, :tags, :description, :category, :date, :status, :sort_order)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':image', $image);
        $stmt->bindValue(':tags', $tags);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // 更新推荐内容
    public function updateRecommendation($id, $title, $url, $image, $tags, $description, $category, $date, $status, $sortOrder) {
        $sql = "UPDATE recommendations SET title = :title, url = :url, image = :image, tags = :tags, 
                description = :description, category = :category, date = :date, status = :status, sort_order = :sort_order 
                WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':url', $url);
        $stmt->bindValue(':image', $image);
        $stmt->bindValue(':tags', $tags);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':sort_order', $sortOrder, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // 删除推荐内容
    public function deleteRecommendation($id) {
        $sql = "DELETE FROM recommendations WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // 更新推荐内容状态
    public function updateRecommendationStatus($id, $status) {
        $sql = "UPDATE recommendations SET status = :status WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status);
        return $stmt->execute();
    }
    
    // 获取推荐内容统计数据
    public function getRecommendationStats() {
        $sql = "SELECT 
                    COUNT(*) as total_recommendations,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_recommendations,
                    SUM(CASE WHEN category = '学习资源' THEN 1 ELSE 0 END) as learning_resources,
                    SUM(CASE WHEN category = '工具推荐' THEN 1 ELSE 0 END) as tool_recommendations
                FROM recommendations";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    // 活动日志相关方法
    public function logActivity($type, $action, $itemId, $itemTitle, $details = '', $adminId = null) {
        $sql = "INSERT INTO activity_logs (type, action, item_id, item_title, details, admin_id) 
                VALUES (:type, :action, :item_id, :item_title, :details, :admin_id)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':type', $type);
        $stmt->bindValue(':action', $action);
        $stmt->bindValue(':item_id', $itemId, PDO::PARAM_INT);
        $stmt->bindValue(':item_title', $itemTitle);
        $stmt->bindValue(':details', $details);
        $stmt->bindValue(':admin_id', $adminId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // 简单的Markdown解析函数
    public static function parseMarkdown($text) {
        if (empty($text)) return '';
        
        // 转义HTML特殊字符
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        // 处理代码块 ```
        $text = preg_replace_callback('/```(\w+)?\n(.*?)\n```/s', function($matches) {
            $language = !empty($matches[1]) ? htmlspecialchars($matches[1]) : '';
            $code = htmlspecialchars($matches[2]);
            return '<pre class="line-numbers"><code class="language-' . $language . '">' . $code . '</code></pre>';
        }, $text);
        
        // 处理行内代码 `code`
        $text = preg_replace('/`([^`]+)`/', '<code class="inline-code">$1</code>', $text);
        
        // 处理标题 (## 或 ### )
        $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
        $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
        $text = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);
        
        // 处理粗体 **text**
        $text = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $text);
        
        // 处理斜体 *text*
        $text = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $text);
        
        // 处理链接 [text](url)
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
        
        // 处理引用块 > text (支持多行引用)
        $text = preg_replace_callback('/(^> .+(?:\n> .+)*)/m', function($matches) {
            $lines = explode("\n", $matches[1]);
            $content = '';
            foreach ($lines as $line) {
                if (preg_match('/^> (.+)$/', $line, $lineMatch)) {
                    $content .= ($content ? '<br>' : '') . $lineMatch[1];
                }
            }
            return '<blockquote>' . $content . '</blockquote>';
        }, $text);
        
        // 处理无序列表
        $text = preg_replace_callback('/^(\s*)[-*+] (.+)$/m', function($matches) {
            $indent = strlen($matches[1]);
            $item = $matches[2];
            return str_repeat('  ', $indent) . '<li>' . $item . '</li>';
        }, $text);
        
        // 包装连续的li标签为ul
        $text = preg_replace('/(<li>.*<\/li>(\s*<li>.*<\/li>)*)/s', '<ul>$1</ul>', $text);
        
        // 处理有序列表
        $text = preg_replace_callback('/^(\s*)(\d+)\. (.+)$/m', function($matches) {
            $indent = strlen($matches[1]);
            $item = $matches[3];
            return str_repeat('  ', $indent) . '<li>' . $item . '</li>';
        }, $text);
        
        // 包装连续的li标签为ol (优先级低于ul)
        $text = preg_replace('/(?<!<\/ul>)(<li>.*<\/li>(\s*<li>.*<\/li>)*)(?!<ul>)/s', '<ol>$1</ol>', $text);
        
        // 处理换行
        $text = nl2br($text);
        
        return $text;
    }
}
?> 