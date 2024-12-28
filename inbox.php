<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: logincl.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$dbname = "hospital";
$conn = mysqli_connect($host, $username, $password, $dbname);

$user_id = $_SESSION['id'];

// Mark notification as read
if (isset($_POST['mark_read'])) {
    $notification_id = $_POST['notification_id'];
    $update_query = "UPDATE notifications SET is_read = TRUE WHERE id_notification = ? AND id_client = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ii", $notification_id, $user_id);
    mysqli_stmt_execute($stmt);
}

$query = "SELECT * FROM notifications WHERE id_client = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$notifications = mysqli_stmt_get_result($stmt);

// Get user info
$query = "SELECT * FROM client WHERE id_client = $user_id";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $user_info = mysqli_fetch_assoc($result);
    $name = $user_info['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox</title>
    <link rel="stylesheet" href="../css/rdvclient.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .content1 {
            padding: 20px;
        }
        
        .notifications-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        
        .notifications-table th,
        .notifications-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .notifications-table th {
            background-color: #f5f5f5;
        }
        
        .unread {
            background-color: #f0f7ff;
            font-weight: bold;
        }
        
        .mark-read-btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .mark-read-btn:hover {
            background-color: #0056b3;
        }
        
        .no-notifications {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="rdvclient.php">
                <img src="../img/logo.png" alt="Home" style="cursor: pointer;">
            </a>
        </div>
        <span>
            Cabinet Dental Management System
        </span>
        <div class="out">
            <a href="logout.php"> <button> <i class="fa-solid fa-right-to-bracket"></i> Logout </button> </a>
        </div>
    </header>

    <div class="content">
        <nav>
            <i class="fa-solid fa-bars" id="bars"></i>
            <i class="fa-solid fa-xmark" id="cancel"></i>
            <ul>
                <li>
                    <a href="profile.php?id=<?php echo $user_id; ?>" id="move">
                        <i class="fa-solid fa-user"></i>
                        <?php echo htmlspecialchars($name); ?>
                    </a>
                </li>
                <li>
                    <a href="rdvclient.php">
                        <i class="fa-solid fa-house"></i>
                        Home
                    </a>
                </li>
                <li>
                    <a href="hstclient.php">
                        <i class="fa fa-history"></i>
                        History
                    </a>
                </li>
                <li>
                    <a href="inbox.php">
                        <i class="fa fa-inbox"></i>
                        Inbox
                    </a>
                </li>
            </ul>
        </nav>

        <div class="content1">
            <h2>Your Notifications</h2>
            
            <?php if (mysqli_num_rows($notifications) > 0): ?>
                <table class="notifications-table">
    <thead>
        <tr>
            <th>Message</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if (mysqli_num_rows($notifications) > 0): 
            while ($notification = mysqli_fetch_assoc($notifications)): 
        ?>
            <tr class="<?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                <td><?php echo htmlspecialchars($notification['message']); ?></td>
                <td><?php echo date('Y-m-d H:i', strtotime($notification['created_at'])); ?></td>
                <td><?php echo $notification['is_read'] ? 'Read' : 'Unread'; ?></td>
                <td>
                    <?php if ($notification['is_read'] == 0): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="notification_id" value="<?php echo $notification['id_notification']; ?>">
                            <button type="submit" name="mark_read" class="mark-read-btn">Mark as Read</button>
                        </form>
                    <?php else: ?>
                        Already Read
                    <?php endif; ?>
                </td>
            </tr>
        <?php 
            endwhile;
        else: 
        ?>
            <tr>
                <td colspan="4" class="no-notifications">No notifications available</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
            <?php else: ?>
                <div class="no-notifications">
                    No notifications available
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let bars = document.querySelector("#bars");
        let ul = document.querySelector("ul");
        let cancel = document.querySelector("#cancel");
        let nav = document.querySelector("nav");

        bars.onclick = function() {
            bars.style.left = "-100%";
            ul.style.left = "0%"
            cancel.style.left = "65%"
            nav.style.backgroundColor = "#0f1016"
        }
        
        cancel.onclick = function() {
            nav.style.backgroundColor = "#edf4fa"
            ul.style.left = "-100%"
            cancel.style.left = "-100%";
            bars.style.left = "20%";
        }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>