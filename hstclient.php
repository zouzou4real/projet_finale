<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: logincl.php"); 
    exit();
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
    $query = "SELECT name FROM client WHERE id_client = $user_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user_info = mysqli_fetch_assoc($result);
        $name = $user_info['name'];
    }
} else {
    header('Location: loginadm.php');
}
$query = "SELECT 
              client.name AS client_name, 
              doctor.name AS doctor_name, 
              rdv.rdv_date, 
              paiment.montant
          FROM rdv
          JOIN client ON rdv.id_client = client.id_client
          JOIN doctor ON rdv.id_doc = doctor.id_doc
          LEFT JOIN paiment ON rdv.id_rdv = paiment.id_rdv
          WHERE rdv.etat = 'archived'";

$result = mysqli_query($conn, $query);
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="../css/hstclient.css">
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
            Cabinet Denatal Manegment System
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
                    <a href="profile.php" id="move">
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
                    <a href="">
                        <i class="fa fa-inbox"></i>
                        Inbox
                    </a>
                </li>

            </ul>
        </nav>
        <div class="content1">
            <div class="history">
                <p class="hst"> History </p>

                <table>
                    <thead>
                        <tr>
                            <th>Nom du patient</th>
                            <th>Nom du docteur</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Paiement</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $rdv_date = new DateTime($row['rdv_date']);
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['doctor_name']) . "</td>";
                                    echo "<td>" . $rdv_date->format('Y-m-d') . "</td>";
                                    echo "<td>" . $rdv_date->format('H:i') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['montant'] ?? 'N/A') . "</td>"; // Handle NULL montant
                                    echo "</tr>";
                        }
                            } else {
                                echo "<tr><td colspan='7'>Tu n'as pas encore pris aucun rendez-vous</td></tr>";
                            }
                    ?>

                    </tbody>
                </table>

            </div>
        </div>


</body>
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

</html>