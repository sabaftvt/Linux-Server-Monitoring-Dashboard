<?php
// اطلاعات اتصال به پایگاه داده
$host = 'localhost';
$dbname = 'linux_monitoring';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ایجاد جدول admins با فیلدهای مورد نظر
    $sql = "CREATE TABLE IF NOT EXISTS `admins` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `pass` VARCHAR(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $conn->exec($sql);
    
    // اضافه کردن کاربر پیش‌فرض
    $stmt = $conn->prepare("INSERT INTO admins (username, pass) VALUES (:username, :pass)");
    $stmt->execute([
        ':username' => 'admin',
        ':pass' => 'pass123' // رمز عبور پیش‌فرض
    ]);
    
    echo "جدول admins با موفقیت ایجاد شد و کاربر admin اضافه شد.<br>";
    echo "نام کاربری: admin<br>رمز عبور: password123";
    
} catch(PDOException $e) {
    echo "خطا: " . $e->getMessage();
}
?>