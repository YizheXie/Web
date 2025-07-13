<?php
require_once '../database.php';
require_once '../config.php';

echo "<h2>数据库调试工具</h2>";
echo "<hr>";

// 测试数据库连接
echo "<h3>1. 数据库连接测试</h3>";
try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "✅ 数据库连接成功<br>";
    echo "数据库配置：<br>";
    echo "- 主机：" . DB_HOST . "<br>";
    echo "- 数据库：" . DB_NAME . "<br>";
    echo "- 用户：" . DB_USER . "<br>";
    echo "<br>";
} catch (Exception $e) {
    echo "❌ 数据库连接失败：" . $e->getMessage() . "<br>";
    exit;
}

// 检查users表是否存在
echo "<h3>2. 检查users表</h3>";
try {
    $sql = "SHOW TABLES LIKE 'users'";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ users表存在<br>";
    } else {
        echo "❌ users表不存在<br>";
        echo "<br><strong>解决方案：</strong>需要运行database.sql文件创建表结构<br>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ 检查表时出错：" . $e->getMessage() . "<br>";
}

// 检查admin用户
echo "<h3>3. 检查admin用户</h3>";
try {
    $sql = "SELECT * FROM users WHERE username = 'admin'";
    $stmt = $connection->prepare($sql);
    $stmt->execute();
    $adminUser = $stmt->fetch();
    
    if ($adminUser) {
        echo "✅ 找到admin用户<br>";
        echo "- ID: " . $adminUser['id'] . "<br>";
        echo "- 用户名: " . $adminUser['username'] . "<br>";
        echo "- 邮箱: " . $adminUser['email'] . "<br>";
        echo "- 角色: " . $adminUser['role'] . "<br>";
        echo "- 密码哈希: " . substr($adminUser['password'], 0, 20) . "...<br>";
        echo "- 创建时间: " . $adminUser['created_at'] . "<br>";
    } else {
        echo "❌ 未找到admin用户<br>";
        echo "<br><strong>需要创建admin用户</strong><br>";
        
        // 创建admin用户
        $password = "admin123";
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $insertSql = "INSERT INTO users (username, email, password, role) VALUES ('admin', 'xieyizhe66@gmail.com', :password, 'admin')";
        $insertStmt = $connection->prepare($insertSql);
        $insertStmt->bindParam(':password', $hashedPassword);
        
        if ($insertStmt->execute()) {
            echo "✅ admin用户创建成功<br>";
            echo "- 用户名: admin<br>";
            echo "- 密码: admin123<br>";
        } else {
            echo "❌ admin用户创建失败<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ 检查admin用户时出错：" . $e->getMessage() . "<br>";
}

// 测试密码验证
echo "<h3>4. 测试密码验证</h3>";
$testPassword = "admin123";
if (isset($adminUser) && $adminUser) {
    if (password_verify($testPassword, $adminUser['password'])) {
        echo "✅ 密码验证成功 (admin123)<br>";
    } else {
        echo "❌ 密码验证失败<br>";
        echo "当前密码哈希: " . $adminUser['password'] . "<br>";
        echo "测试密码: " . $testPassword . "<br>";
        
        // 更新密码
        echo "<br><strong>正在更新密码...</strong><br>";
        $newHashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
        
        $updateSql = "UPDATE users SET password = :password WHERE username = 'admin'";
        $updateStmt = $connection->prepare($updateSql);
        $updateStmt->bindParam(':password', $newHashedPassword);
        
        if ($updateStmt->execute()) {
            echo "✅ 密码更新成功<br>";
            echo "新密码哈希: " . $newHashedPassword . "<br>";
        } else {
            echo "❌ 密码更新失败<br>";
        }
    }
}

// 测试认证方法
echo "<h3>5. 测试认证方法</h3>";
try {
    $authResult = $db->authenticateAdmin('admin', 'admin123');
    if ($authResult) {
        echo "✅ 认证方法测试成功<br>";
        echo "返回的用户信息：<br>";
        echo "- ID: " . $authResult['id'] . "<br>";
        echo "- 用户名: " . $authResult['username'] . "<br>";
        echo "- 角色: " . $authResult['role'] . "<br>";
    } else {
        echo "❌ 认证方法测试失败<br>";
        echo "可能的原因：<br>";
        echo "1. 用户名或密码错误<br>";
        echo "2. 用户角色不是admin<br>";
        echo "3. authenticateAdmin方法有问题<br>";
    }
} catch (Exception $e) {
    echo "❌ 认证测试时出错：" . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>快速操作</h3>";
echo "<a href='login.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>测试登录</a> ";
echo "<a href='../index.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>返回首页</a>";

echo "<br><br><strong style='color: red;'>重要：调试完成后请删除此文件！</strong>";
?> 