<?php
require_once '../database.php';

// 这是一个临时文件，用于设置管理员密码
// 使用后请删除此文件

$db = Database::getInstance();

// 设置管理员密码（这里设置密码为 "admin123"）
$password = "admin123";
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = "UPDATE users SET password = :password WHERE username = 'admin'";
    $stmt = $db->getConnection()->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->execute();
    
    echo "管理员密码已更新！<br>";
    echo "用户名: admin<br>";
    echo "密码: admin123<br>";
    echo "<br>现在您可以使用这些凭据登录后台管理系统。<br>";
    echo "<a href='login.php'>点击这里登录</a><br>";
    echo "<br><strong>重要：使用完成后请删除此文件！</strong>";
} catch (Exception $e) {
    echo "错误: " . $e->getMessage();
}
?> 