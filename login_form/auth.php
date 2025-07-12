<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'linux_monitoring';
$db_username = 'root';
$db_password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // دریافت داده‌های POST
    $input_username = $_POST['username'] ?? '';
    $input_password = $_POST['password'] ?? '';
    
    // اعتبارسنجی
    if (empty($input_username) || empty($input_password)) {
        throw new Exception('Username and password are required');
    }
    
    // جستجوی کاربر
    $stmt = $conn->prepare("SELECT id, username, pass FROM admins WHERE username = ?");
    $stmt->execute([$input_username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('No user with this username found');
    }
    
    // بررسی رمز عبور (متن ساده)
    if ($input_password === $user['pass']) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Password is incorrect');
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>