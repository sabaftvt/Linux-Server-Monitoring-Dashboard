<?php include 'config.php'; ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitored Servers List</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 1px;
            color: #343a40;
        }
        .header {
            background-color: #9c27b0;
            color: white;
            padding: 46px;
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
        .server-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            margin-top: 20px;
            width: 100%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            max-width: 76%;
        }
        .server-table th {
            background-color: #f0e6ff;
            color: rgb(0, 0, 0);
            padding: 12px;
            text-align: center;
            
        }
        .server-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .server-table tr:hover {
            background-color: #f1f1f1;
        }
        .password-display {
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            display: inline-block;
            min-width: 100px;
        }
        .btn-show {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-show:hover {
            background-color: #5a6268;
        }
        .btn-show.btn-warning {
            background-color:rgb(255, 147, 7);
            color: #212529;
        }
        .btn-action {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-edit {
            background-color:rgb(52, 179, 253);
        }
        .btn-edit:hover {
            background-color:rgb(25, 169, 247);
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .actions-cell {
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="header-txt">Monitored Servers List</h1>
    </div>

    <div class="back-btn-container">
        <a href="../main_form/index.html" class="back-btn btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Main Page
        </a>
    </div>
    
    <table class="server-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Server IP</th>
                <th>Username</th>
                <th>Password</th>
                <th></th>
                <th>Actions</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM servers";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $counter = 1;
                while($row = $result->fetch_assoc()) {
                    echo '<tr>
                        <td>'.$counter.'</td>
                        <td>'.$row['ip'].'</td>
                        <td>'.$row['username'].'</td>
                        <td>
                            <span class="password-display" id="pass-display-'.$counter.'">••••••••</span>
                            <span id="pass-real-'.$counter.'" style="display:none">'.$row['pass'].'</span>
                        </td>
                        <td>
                            <button class="btn-show" onclick="togglePassword('.$counter.')">
                                <i class="fas fa-eye"></i> Show
                            </button>
                        </td>
                        <td class="actions-cell">
                            <a href="edit.php?ip='.$row['ip'].'" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="delete.php?ip='.$row['ip'].'" class="btn-action btn-delete" onclick="return confirm(\'Are you sure?\')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>';
                    $counter++;
                }
            } else {
                echo '<tr><td colspan="6" style="text-align:center;">No servers found</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <!-- Bootstrap 5 JS + Font Awesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(id) {
            const displaySpan = document.getElementById('pass-display-'+id);
            const realSpan = document.getElementById('pass-real-'+id);
            const btn = document.querySelector(`button[onclick="togglePassword(${id})"]`);
            
            if (realSpan.style.display === 'none') {
                displaySpan.style.display = 'none';
                realSpan.style.display = 'inline';
                btn.innerHTML = '<i class="fas fa-eye-slash"></i> Hide';
                btn.classList.add('btn-warning');
            } else {
                displaySpan.style.display = 'inline';
                realSpan.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-eye"></i> Show';
                btn.classList.remove('btn-warning');
            }
        }
    </script>
</body>
</html>