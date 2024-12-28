<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: loginadm.php"); 
    exit();
}

// Function to create notification
function createNotification($conn, $client_id, $appointment_date, $type, $status) {
    $appointment_date = date('Y-m-d H:i', strtotime($appointment_date));
    $message = $status === 'accepted' 
        ? "Your {$type} appointment scheduled for {$appointment_date} has been confirmed."
        : "Your {$type} appointment scheduled for {$appointment_date} has been refused.";
    
    $sql = "INSERT INTO notifications (id_client, message) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $client_id, $message);
    return mysqli_stmt_execute($stmt);
}

// Connect to the database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "hospital";
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $role = 'sec';
    $query = "SELECT * FROM secretaire WHERE id_sec = $user_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user_info = mysqli_fetch_assoc($result);
        $name = $user_info['name'];
    }
} else {
    header('Location: loginadm.php');
}

$secretaire_type = $_SESSION['secretaire_type'];

$query = "SELECT rdv.id_rdv, rdv.id_client, client.name, client.email, rdv.rdv_date, rdv.type, rdv.etat
          FROM rdv
          JOIN client ON rdv.id_client = client.id_client
          WHERE rdv.etat = 'pending'";

// Filter appointments based on the secrétaire's type
if ($secretaire_type == 'general') {
    $query .= " AND rdv.type = 'general'";
} else if ($secretaire_type == 'prothese') {
    $query .= " AND rdv.type = 'prothese'";
}

$result = mysqli_query($conn, $query);

// Handle accepting and refusing appointments
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rdv_id = $_POST['id_rdv'];
    $action = $_POST['action'];

    // Get appointment details first
    $rdv_query = "SELECT id_client, rdv_date, type FROM rdv WHERE id_rdv = ?";
    $stmt = mysqli_prepare($conn, $rdv_query);
    mysqli_stmt_bind_param($stmt, "i", $rdv_id);
    mysqli_stmt_execute($stmt);
    $rdv_result = mysqli_stmt_get_result($stmt);
    $rdv_details = mysqli_fetch_assoc($rdv_result);

    if ($action == 'accept') {
        $update_query = "UPDATE rdv SET etat = 'accepted' WHERE id_rdv = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $rdv_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Create notification for accepted appointment
            createNotification(
                $conn, 
                $rdv_details['id_client'], 
                $rdv_details['rdv_date'],
                $rdv_details['type'],
                'accepted'
            );
            header("Location: homesec.php");
            exit();
        }
    } else if ($action == 'refuse') {
        $update_query = "UPDATE rdv SET etat = 'refused' WHERE id_rdv = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $rdv_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Create notification for refused appointment
            createNotification(
                $conn, 
                $rdv_details['id_client'], 
                $rdv_details['rdv_date'],
                $rdv_details['type'],
                'refused'
            );
            header("Location: homesec.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrétaire Dashboard</title>
    <link rel="stylesheet" href="../css/homesec.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="HomeAdmin.php">
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
                    <a href="Profileadm.php?id=<?php echo $_SESSION['id']; ?>&role=<?php echo $role ?>" id="move">
                        <i class="fa-solid fa-user"></i>
                        <?php echo htmlspecialchars($name); ?>
                    </a>
                </li>
                <li>
                    <a href="homesec.php">
                        <i class="fa-solid fa-house"></i>
                        Home
                    </a>
                </li>
            </ul>
        </nav>

        <div class="content1">
            <div class="contenu">
                <h1>Rendez-vous Table</h1>

                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Email</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $rdv_date = new DateTime($row['rdv_date']);
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . $rdv_date->format('Y-m-d') . "</td>";
                                echo "<td>" . $rdv_date->format('H:i') . "</td>";
                                echo "<td>" . htmlspecialchars($row['etat']) . "</td>";
                                echo "<td>
                                        <form action='' method='POST' style='display:inline;'>
                                            <input type='hidden' name='id_rdv' value='" . $row['id_rdv'] . "'>
                                            <input type='hidden' name='action' value='accept'>
                                            <input type='submit' value='Accept' class='btn'>
                                        </form>
                                        <form action='' method='POST' style='display:inline;'>
                                            <input type='hidden' name='id_rdv' value='" . $row['id_rdv'] . "'>
                                            <input type='hidden' name='action' value='refuse'>
                                            <input type='submit' value='Refuse' class='btn'>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No pending appointments</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let bars = document.querySelector("#bars");
        let ul = document.querySelector("ul");
        let cancel = document.querySelector("#cancel")
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

<?php
// Close the connection
mysqli_close($conn);
?>

