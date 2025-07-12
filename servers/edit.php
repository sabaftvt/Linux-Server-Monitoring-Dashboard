<?php
include 'config.php';

if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];
    $sql = "SELECT * FROM servers WHERE ip='$ip'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}

if (isset($_POST['submit'])) {
    $ip = $_POST['ip'];
    $username = $_POST['username'];
    $pass = $_POST['pass'];
    
    $sql = "UPDATE servers SET username='$username', pass='$pass' WHERE ip='$ip'";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "خطا: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Edit</title></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px;background-color: #f5f5f5; }
        .header {
            background-color: #34b3fd;
            color: white;
            padding: 20px;
            max-width: 540px;
            margin: 0 auto;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
            position: relative;
        }  
        .header-txt{
            font-weight: bold;
        }
        .container {
            max-width: 550px; 
            margin: 0 auto;
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }
        .page-header { 
            max-width: 575px; 
            margin: 0 auto 15px auto; 
            display: flex; 
            justify-content: flex-end;}
        .back-btn { 
            background-color: #9c27b0; 
            color: white; 
            padding: 10px 12px; 
            border: none; 
            border-radius: 3px; 
            cursor: pointer; 
            text-decoration: none; 
            font-size: 14px; 
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #7b1fa2;
        }
        form { max-width: 500px; margin: auto; }
        label {
            display: block;
            margin-bottom: 5px;
            margin-top: 25px;
            font-weight: bold;
            text-align: left;
            font-size: 15px;
        }
        input {
            width: 100%;
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
            text-align: left;
            font-size: 15px;
            color: #858585
        }
        button { 
            margin-top: 25px; 
            padding: 12px 15px; 
            background: #7b1fa2; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background:rgb(104, 24, 139); }
    </style>
</head>
<body>
    
    <div class="page-header">
        <a href="../servers/index.php" class="back-btn">Back to Main Page</a>
    </div>
    <div class="header">
        <h1 class="header-txt">Edit server</h1>
    </div>
    <div class="container">
        <form method="POST">
            <input type="hidden" name="ip" value="<?php echo $row['ip']; ?>">
            
            <label>:IP</label>
            <input type="text" value="<?php echo $row['ip']; ?>" disabled>
            
            <label>:Username</label></label>
            <input type="text" name="username" value="<?php echo $row['username']; ?>">
            
            <label>:Password</label></label>
            <input type="text" name="pass" value="<?php echo $row['pass']; ?>">
            
            <button type="submit" name="submit">Save</button></button>
        </form>
    </div>
</body>
</html>