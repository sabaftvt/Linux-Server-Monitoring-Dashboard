<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Stat</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
            width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0px;
            background-color: #f5f5f5;
            text-align: left; /* متن‌ها چپ‌چین شوند */
        }

        .header {
            background-color: #ff9800;
            color: white;
            padding: 41px;
            border-radius: 5px;
            margin: 0 auto;
            margin-bottom: 5px;
            margin-top: 20px;
            text-align: center;
            font-size:150%;
            position: relative;
            max-width: 76%;
        }  
        .header-txt{
            font-weight: bold;
        }
        
        .back-btn-container {
            text-align: left; /* چپ‌چین کردن دکمه */
            margin: 5px auto;
            max-width: 76%; /* هماهنگ با عرض containerهای دیگر */
            padding: 0 0; /* padding برای فاصله از لبه */
        }

        .back-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 14px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }
        .server-container {
            background-color: white;
            border-radius: 8px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            max-width: 76%; /* کاهش عرض کانتینر */
            transition: transform 0.3s ease;
            
        }
        .server-container:hover{
            transform: translateY(-5px);
        }
        .server-header {
            display: flex;
            justify-content: space-between;
            align-items: left;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .server-title {
            font-size: 18px;
            font-weight: bold;
            color: #666;
        }
        .server-ip {
            color: #666;
            font-size: 18px;
            font-weight: bold;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            margin:auto;
            width: 80%;
            gap: 65px;      
            overflow: hidden;      
        }
        
        .stat-box {
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-label {
            font-size: 14px;
            color: #000;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #555;
            
        }
        .low-usage {
            border: 3px solid rgba(40, 96, 42, 0.16);
            border-radius: 10px;
            background-color:rgb(187, 241, 194);
            
        }
        .high-usage {
            border: 3px solid rgba(244, 54, 54, 0.17);
            border-radius: 8px;
            background-color:rgb(255, 213, 213);
            
        }
        .last-update {
            font-size: 12px;
            color: #999;
            text-align: left;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="header-txt">System Stat</h1>
    </div>

    <div class="back-btn-container">
        <a href="../main_form/index.html" class="back-btn btn-secondary">
            Back to Main Page
        </a>
    </div>
    
    <?php
    // اتصال به دیتابیس

    // تنظیمات اتصال به دیتابیس برای محیط توسعه (بدون یوزرنیم و پسورد)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root'); // کاربر پیش‌فرض در XAMPP
    define('DB_PASS', ''); // پسورد خالی در محیط توسعه
    define('DB_NAME', 'linux_monitoring');

    // تابع برای اتصال ایمن به دیتابیس
    function connectToDatabase() {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            // بررسی خطاهای اتصال
            if ($conn->connect_error) {
                // اگر با کاربر root هم نشد، بدون یوزر و پسورد امتحان کن
                $conn = new mysqli(DB_HOST, '', '', DB_NAME);
                if ($conn->connect_error) {
                    throw new Exception("خطا در اتصال به دیتابیس: " . $conn->connect_error);
                }
            }
            

            
            return $conn;
        } catch (Exception $e) {
            // نمایش پیغام خطای مناسب به کاربر
            die("<div style='direction:rtl;text-align:center;margin:50px;padding:20px;background:#ffebee;border:1px solid #f44336;border-radius:5px;'>
                    <h2 style='color:#d32f2f;'>خطا در اتصال به سیستم مانیتورینگ</h2>
                    <p>{$e->getMessage()}</p>
                    <p>مشکلات احتمالی:</p>
                    <ul style='text-align:right;'>
                        <li>دیتابیس 'linux_monitoring' وجود ندارد</li>
                        <li>سرور MySQL در حال اجرا نیست</li>
                        <li>جدول 'system_stats' وجود ندارد</li>
                    </ul>
                    <p>لطفا با مدیر سیستم تماس بگیرید.</p>
                </div>");
        }
    }

    // اتصال به دیتابیس
    $conn = connectToDatabase();
    
    // بررسی اتصال
    if ($conn->connect_error) {
        die("اتصال به دیتابیس ناموفق بود: " . $conn->connect_error);
    }
    
    // تنظیم charset به utf8 برای پشتیبانی از فارسی
    $conn->set_charset("utf8");
    
    // دریافت داده‌ها از جدول system_stats
    // روش جایگزین برای سرورهای قدیمی MySQL
    $sql = "SELECT t1.* 
            FROM system_stats t1
            WHERE t1.created_at = (
                SELECT MAX(t2.created_at)
                FROM system_stats t2
                WHERE t2.host_ip = t1.host_ip
            )
            ORDER BY t1.host_ip";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // نمایش داده‌های هر سرور
        while($row = $result->fetch_assoc()) {
            echo '<div class="server-container">';
            echo '<div class="server-header">';
            echo '<div class="server-title"> Hostname: ' . htmlspecialchars($row['hostname']) . '</div>';
            echo '<div class="server-title"> Username: ' . htmlspecialchars($row['username']) . '</div>';
            echo '<div class="server-ip"> IP: ' . htmlspecialchars($row['host_ip']) . '</div>';
            echo '</div>';
            
            echo '<div class="stats-container">';
             
            // نمایش Disk Usage
            $disk_usage = $row['disk_usage'];
            $disk_class = (intval(str_replace('%', '', $disk_usage)) > 50) ? 'high-usage' : 'low-usage';
            echo '<div class="stat-box ' . $disk_class . '">';
            echo '<div class="stat-label">Disk Usage</div>';
            echo '<div class="stat-value">' . htmlspecialchars($disk_usage) . '</div>';
            echo '</div>';

            // نمایش Memory Usage
            $memory_usage = $row['memory_usage'];
            $memory_class = (intval(str_replace('%', '', $memory_usage)) > 50) ? 'high-usage' : 'low-usage';
            echo '<div class="stat-box ' . $memory_class . '">';
            echo '<div class="stat-label">RAM Usage</div>';
            echo '<div class="stat-value">' . htmlspecialchars($memory_usage) . '</div>';
            echo '</div>';

            // نمایش CPU Usage
            $cpu_usage = $row['cpu_usage'];
            $cpu_class = (intval(str_replace('%', '', $cpu_usage)) > 50) ? 'high-usage' : 'low-usage';
            echo '<div class="stat-box ' . $cpu_class . '">';
            echo '<div class="stat-label">CPU Usage</div>';
            echo '<div class="stat-value">' . htmlspecialchars($cpu_usage) . '</div>';
            echo '</div>';        
            
            echo '</div>'; // پایان stats-container
            
            echo '<div class="last-update">Last Update: ' . htmlspecialchars($row['created_at']) . '</div>';
            echo '</div>'; // پایان server-container
        }
    } else {
        echo '<p>هیچ داده‌ای یافت نشد.</p>';
    }
    
    $conn->close();
    ?>
</body>
</html>