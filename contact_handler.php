<?php
require_once 'database.php';

// Enable CORS for form submissions
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => '只允许POST请求']);
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate input
$errors = [];

// Name validation
if (empty($name)) {
    $errors[] = '姓名不能为空';
} elseif (strlen($name) < 2) {
    $errors[] = '姓名至少需要2个字符';
} elseif (strlen($name) > 50) {
    $errors[] = '姓名不能超过50个字符';
} elseif (!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z\s]+$/u', $name)) {
    $errors[] = '姓名只能包含中英文字符和空格';
}

// Email validation
if (empty($email)) {
    $errors[] = '邮箱不能为空';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = '请输入有效的邮箱地址';
} elseif (strlen($email) > 100) {
    $errors[] = '邮箱地址不能超过100个字符';
}

// Message validation
if (empty($message)) {
    $errors[] = '留言不能为空';
} elseif (strlen($message) < 10) {
    $errors[] = '留言至少需要10个字符';
} elseif (strlen($message) > 1000) {
    $errors[] = '留言不能超过1000个字符';
}

// If there are validation errors, return them
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => '输入验证失败',
        'errors' => $errors
    ]);
    exit;
}

// Save to database
try {
    $db = Database::getInstance();
    $result = $db->saveContact($name, $email, $message);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => '消息已成功发送！我们会尽快回复您。'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '发送失败，请稍后重试。'
        ]);
    }
} catch (Exception $e) {
    // Log the error (in production, use proper logging)
    error_log("Contact form error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => '服务器错误，请稍后重试。'
    ]);
}
?> 