<?php
// تنظیم منطقه زمانی به تهران
date_default_timezone_set('Asia/Tehran');
// 1. تنظیمات پایه
$db = new mysqli("localhost", "root", "", "linux_monitoring");
if ($db->connect_error) die("خطای دیتابیس: " . $db->connect_error);

// مسیر فایل JSON
$jsonFile = 'server_monitoring.json';

// ایجاد یک آرایه خالی برای داده‌های جدید
$jsonData = [];

// 2. خواندن سرورها
$servers = $db->query("SELECT ip, username, pass FROM servers")->fetch_all(MYSQLI_ASSOC);
if (empty($servers)) die("هیچ سروری یافت نشد");

foreach ($servers as $server) {
    try {
        // 3. اجرای دستورات با PuTTY
        $cmd = '"C:\Program Files\PuTTY\plink.exe" -batch -ssh '.
               escapeshellarg($server['username'].'@'.$server['ip']).' -pw '.
               escapeshellarg($server['pass']).' '.
               '"hostname && top -bn1 | grep \'%Cpu\' && free -m && df -h --output=source,pcent,used,size"';
        
        $output = shell_exec($cmd);
        
        if (empty($output)) {
            throw new Exception("دستور هیچ خروجی نداشت");
        }

        // 4. پردازش خروجی
        $lines = array_map('trim', explode("\n", $output));
        $hostname = $lines[0] ?? 'N/A';
        
        // پردازش CPU
        $cpu = "0%";
        if (isset($lines[1]) && preg_match('/%Cpu\(s\):\s+([\d.]+)\s+us,\s+([\d.]+)\s+sy/', $lines[1], $cpu_match)) {
            $cpu = round($cpu_match[1] + $cpu_match[2]) . "%";
        }
        
        // پردازش RAM (روش قطعی)
        $ram = "0%";
        $used = 0;
        $total = 0;

        if (isset($lines[1])) {
            foreach ($lines as $line) {
                if (strpos($line, 'Mem:') === 0) {
                    $mem_line = preg_replace('/\s+/', ' ', trim($line));
                    $mem_parts = explode(' ', $mem_line);
                    
                    if (count($mem_parts) >= 6) {
                        $total = (int)$mem_parts[1];
                        $used = (int)$mem_parts[2];
                        $ram = ($total > 0) ? round(($used/$total)*100) . "%" : "0%";
                    }
                    break;
                }
            }
        }
        
        // پردازش دیسک
        $disk_percent = "0%";
        $disk_used = "0";
        $disk_total = "0";

        foreach ($lines as $line) {
            if (preg_match('/\/dev\/(sd|nvme|xvd)[a-z]\d*\s+(\d+)%\s+(\d+\.?\d*[GMK])\s+(\d+\.?\d*[GMK])/', $line, $disk_match)) {
                $disk_percent = $disk_match[2] . "%";
                $disk_used = $disk_match[3];
                $disk_total = $disk_match[4];
                break;
            }
        }

        // 5. ذخیره در دیتابیس
        $stmt = $db->prepare("INSERT INTO system_stats (host_ip, hostname, username, pass, cpu_usage, memory_usage, disk_usage) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $server['ip'], $hostname, $server['username'], $server['pass'], $cpu, $ram, $disk_percent); 
        $stmt->execute();
        $stmt->close();
        
        // 6. اضافه کردن داده‌های جدید به آرایه JSON
        $jsonData[] = [
            'host_ip' => $server['ip'],
            'hostname' => $hostname,
            'username' => $server['username'],
            'password' => $server['pass'],
            'cpu_usage' => $cpu,
            'memory_usage' => $ram,
            'disk_usage' => $disk_percent,
            'disk_used' => $disk_used,
            'disk_total' => $disk_total,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // 7. نمایش نتیجه
        echo "<div style='border:1px solid green; padding:10px; margin:10px;'>";
        echo "<h3>سرور {$server['ip']} - {$hostname}</h3>";
        echo "<p><strong>CPU:</strong> {$cpu}</p>";
        echo "<p><strong>RAM:</strong> {$ram}</p>";
        echo "<p><strong>دیسک:</strong> {$disk_percent} ({$disk_used} از {$disk_total})</p>";
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div style='border:1px solid red; padding:10px; margin:10px;'>";
        echo "<h3>خطا در سرور {$server['ip']}</h3>";
        echo "<p>{$e->getMessage()}</p>";
        echo "<p>دستور اجرا شده: " . htmlspecialchars($cmd ?? '') . "</p>";
        echo "</div>";
    }
}

// ذخیره داده‌های جدید در فایل JSON (بازنویسی کامل)
file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$db->close();
?>