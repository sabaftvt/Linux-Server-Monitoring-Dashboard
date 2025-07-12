<?php
// تنظیمات هدر
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

// نمایش خطاها
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تنظیمات دیتابیس
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname' => 'linux_monitoring'
];

$response = ['success' => false, 'message' => ''];

try {
    // دریافت داده‌های ورودی
    $json_input = file_get_contents('php://input');
    if (empty($json_input)) {
        throw new Exception("No data received");
    }

    $data = json_decode($json_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error processing JSON");
    }

    // اعتبارسنجی فیلدهای ضروری
    if (empty($data['ip']) || empty($data['username']) || empty($data['pass'])) {
        throw new Exception("All fields are required");
    }

    // اعتبارسنجی IP
    if (!filter_var($data['ip'], FILTER_VALIDATE_IP)) {
        throw new Exception("IP format is invalid");
    }

    // اتصال به دیتابیس
    $conn = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']}",
        $db_config['username'],
        $db_config['password']
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8mb4'");

    // درج یا به‌روزرسانی داده‌ها
    try {
        $stmt = $conn->prepare("INSERT INTO servers (ip, username, pass) VALUES (:ip, :username, :pass)");
        $stmt->execute([
            ':ip' => $data['ip'],
            ':username' => $data['username'],
            ':pass' => $data['pass']
        ]);
        
        $response['success'] = true;
        $response['message'] = "Record added successfully";
        
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            // به روزرسانی رکورد موجود اگر IP تکراری است
            $stmt = $conn->prepare("UPDATE servers SET username = :username, pass = :pass WHERE ip = :ip");
            $stmt->execute([
                ':ip' => $data['ip'],
                ':username' => $data['username'],
                ':pass' => $data['pass']
            ]);
            
            $response['success'] = true;
            $response['message'] = ".A record with this IP existed and was updated";
        } else {
            throw $e;
        }
    }

} catch(PDOException $e) {
    $response['message'] = "Database error: " . $e->getMessage();
} catch(Exception $e) {
    $response['message'] = $e->getMessage();
}

// ارسال پاسخ
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>