<?php
session_start();
require_once '../database.php';

// 如果已经登录，重定向到管理页面
if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit;
}

// 页面更新：获取并清除session中的错误信息
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = '请填写用户名和密码';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $db = Database::getInstance();
        $admin = $db->authenticateAdmin($username, $password);
        
        if ($admin) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            header('Location: admin.php');
            exit;
        } else {
            $_SESSION['login_error'] = '用户名或密码错误';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录</title>
    <link rel="stylesheet" href="css/root.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1 class="login-title">后台管理系统登录</h1>
            <p class="login-subtitle">请输入账户密码以进行访问~</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" id="username" name="username" required 
                       placeholder="请输入用户名">
                <span class="error-message" id="usernameError"></span>
            </div>

            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" id="password" name="password" required
                       placeholder="请输入密码">
                <span class="error-message" id="passwordError"></span>
            </div>

            <button type="submit" class="login-btn">登录</button>
        </form>

        <div class="back-link">
            <a href="../index.php">返回首页</a>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>
</html> 