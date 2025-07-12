<?php
// اتصال به دیتابیس
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "linux_monitoring";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // دریافت آخرین وضعیت هر سرور
    $stmt = $conn->prepare("
        SELECT s1.* 
        FROM system_stats s1
        INNER JOIN (
            SELECT host_ip, MAX(created_at) AS max_created_at
            FROM system_stats
            GROUP BY host_ip
        ) s2 ON s1.host_ip = s2.host_ip AND s1.created_at = s2.max_created_at
        ORDER BY s1.hostname
    ");
    $stmt->execute();
    $servers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fa" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>charts</title></title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/progressbar.js@1.1.0/dist/progressbar.min.js"></script>
    <script src="script.js" defer></script>
</head>
<body>
    <div class="container">

        <div class="header">
            <h1 class="header-txt">Comparing server resource usage</h1>
        </div>

        <div class="back-btn-container">
            <a href="../main_form/index.html" class="back-btn btn-secondary">
                Back to Main Page
            </a>
        </div>
    
        
        <!-- بخش مقایسه مصرف RAM -->
        <div class="resource-section">
            <h2>RAM Usage</h2>
            <div class="servers-comparison">
                <?php foreach ($servers as $server): 
                    $memory_usage = floatval($server['memory_usage']);
                    $memory_color = getProgressColor($memory_usage);
                ?>
                <div class="server-item">
                    <div class="server-info">
                        <h3><?php echo htmlspecialchars($server['hostname']); ?></h3>
                        <p class='ip'><?php echo htmlspecialchars($server['host_ip']); ?></p>
                    </div>
                    <div class="circle-progress" id="memory-<?php echo $server['id']; ?>" 
                         data-value="<?php echo $memory_usage; ?>"
                         data-color="<?php echo $memory_color; ?>">
                    </div>
                    <div class="usage-info">
                        <small> last Update:  <?php echo htmlspecialchars($server['created_at']); ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- بخش مقایسه مصرف CPU -->
        <div class="resource-section">
            <h2>CPU Usage</h2>
            <div class="servers-comparison">
                <?php foreach ($servers as $server): 
                    $cpu_usage = floatval($server['cpu_usage']);
                    $cpu_color = getProgressColor($cpu_usage);
                ?>
                <div class="server-item">
                    <div class="server-info">
                        <h3><?php echo htmlspecialchars($server['hostname']); ?></h3>
                        <p><?php echo htmlspecialchars($server['host_ip']); ?></p>
                    </div>
                    <div class="circle-progress" id="cpu-<?php echo $server['id']; ?>" 
                         data-value="<?php echo $cpu_usage; ?>"
                         data-color="<?php echo $cpu_color; ?>">
                    </div>
                    <div class="usage-info">
                        <small> last Update:  <?php echo htmlspecialchars($server['created_at']); ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- بخش مقایسه مصرف دیسک -->
        <div class="resource-section">
            <h2>Disk Usage</h2>
            <div class="servers-comparison">
                <?php foreach ($servers as $server): 
                    $disk_usage = floatval($server['disk_usage']);
                    $disk_color = getProgressColor($disk_usage);
                ?>
                <div class="server-item">
                    <div class="server-info">
                        <h3><?php echo htmlspecialchars($server['hostname']); ?></h3>
                        <p><?php echo htmlspecialchars($server['host_ip']); ?></p>
                    </div>
                    <div class="circle-progress" id="disk-<?php echo $server['id']; ?>" 
                         data-value="<?php echo $disk_usage; ?>"
                         data-color="<?php echo $disk_color; ?>">
                    </div>
                    <div class="usage-info">
                        <small> last Update:  <?php echo htmlspecialchars($server['created_at']); ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script>
        const serversData = <?php echo json_encode($servers); ?>;
    </script>
    
    <?php
    // تابع برای تعیین رنگ بر اساس مقدار
    function getProgressColor($value) {
        if ($value > 60) return '#e74c3c'; // قرمز
        if ($value > 30) return '#f39c12'; // نارنجی
        return '#2ecc71'; // سبز
    }
    ?>
</body>
</html>