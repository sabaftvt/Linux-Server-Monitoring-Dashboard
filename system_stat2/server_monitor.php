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
    <title>مانیتورینگ سرورها</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js" defer></script>
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
    <div class="container">
        
        <div class="servers-grid">
            <?php foreach ($servers as $server): ?>
                <div class="server-card">
                    <h2><?php echo htmlspecialchars($server['hostname']); ?></h2>
                    <p class="ip"><?php echo htmlspecialchars($server['host_ip']); ?></p>
                    
                    <div class="metric">
                        <label>CPU Usage:</label>
                        <div class="progress-bar">
                            <div class="progress-fill cpu" 
                                 data-value="<?php echo htmlspecialchars($server['cpu_usage']); ?>"
                                 style="width: 0%; background-color: #4CAF50;"></div>
                        </div>
                        <span class="percentage">0%</span>
                    </div>
                    
                    <div class="metric">
                        <label>RAM Usage:</label>
                        <div class="progress-bar">
                            <div class="progress-fill memory" 
                                 data-value="<?php echo htmlspecialchars($server['memory_usage']); ?>"
                                 style="width: 0%; background-color: #4CAF50;"></div>
                        </div>
                        <span class="percentage">0%</span>
                    </div>
                    
                    <div class="metric">
                        <label>Disk Usage:</label>
                        <div class="progress-bar">
                            <div class="progress-fill disk" 
                                 data-value="<?php echo htmlspecialchars($server['disk_usage']); ?>"
                                 style="width: 0%; background-color: #4CAF50;"></div>
                        </div>
                        <span class="percentage">0%</span>
                    </div>
                    <p class="update-time">Last Update: <?php echo htmlspecialchars($server['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        

    </div>
    
    <script>
        const serversData = <?php echo json_encode($servers); ?>;
    </script>
</body>
</html>