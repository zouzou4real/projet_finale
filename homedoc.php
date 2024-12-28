<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../css/homedoc.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>



<body>
    <?php
    session_start();

    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hospital";

    $conn = mysqli_connect($host, $username, $password, $dbname);

    if (!$conn) {
        die("Échec de la connexion: " . mysqli_connect_error());
    }


    if (isset($_SESSION['id'])) {
        $user_id = $_SESSION['id'];
        $query = "SELECT * FROM doctor WHERE id_doc = $user_id";
        $result = mysqli_query($conn, $query);
        $role='doc';
        if ($result && mysqli_num_rows($result) > 0) {
            $user_info = mysqli_fetch_assoc($result);
            $name = $user_info['name'];
            $type = $user_info['type'];
        }
    } else {
        header('Location: loginadm.php');
    }




    ?>


    <header>
        <div class="logo">
            <img src="../img/logo.png" alt="Home" style="cursor: pointer;">
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
                    <a href="Profileadm.php?id=<?php echo $_SESSION['id']; ?>&role=<?php echo $role ?> "id="move">
                        <i class="fa-solid fa-user"></i>
                        <?php echo $name; ?>
                    </a>
                </li>


                <li>
                    <a href="homedoc.php">
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
                            <th>Email</th>
                            <th>Date</th>
                            <th>Paiment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($type == "general") {
                            $rdv = "general";
                        } else {
                            $rdv = "prothese";
                        }
                        $etat = "accepted";
                        $sql = "SELECT *FROM rdv WHERE type ='$type' AND etat='$etat'";
                        $query = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($query) > 0) {
                            while ($rdv_info = mysqli_fetch_assoc($query)) {
                                $idrdv = $rdv_info['id_rdv'];
                                $idclient = $rdv_info['id_client'];
                                $rdvdate = $rdv_info['rdv_date'];
                                $client = "SELECT *FROM client WHERE id_client='$idclient'";
                                $queryclient = mysqli_query($conn, $client);
                                $client_info = mysqli_fetch_assoc($queryclient);
                                $name = $client_info['name'];
                                $email = $client_info['email'];
                                echo '
                                    <tr>
                                         <td>' . $name  . '</td>
                                         <td>' . $email . '</td>
                                         <td>' . $rdvdate . '</td>
                                         <td>
                                <div class="etat">
                                   <a href="paiment.php?idrdv=' . $idrdv . '&idclient=' . $idclient . '"> <input type="button" name="" id="edit" value="Paiment"> </a>
                                    

                                </div>
                            </td>
                                       
                                <tr>  
                            ';
                            }
                        } else {
                            echo '                    
                            </tbody>
                               </table>';
                        }

                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $rmvrdv = intval($_POST['idrdv']);
                            $sql = "UPDATE rdv SET etat = 'archived' WHERE id_rdv = $rmvrdv";
                            $result = mysqli_query($conn, $sql);
                            header("Location: " . $_SERVER["PHP_SELF"]);
                        }


                        ?>
                    </tbody>
                </table>




            </div>
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