<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../css/rdvclient.css">
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
        $query = "SELECT * FROM client WHERE id_client = $user_id";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_info = mysqli_fetch_assoc($result);
            $name = $user_info['name'];
        }
    } else {
        header('Location: logincl.php');
    }



    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $datetime = $_POST['datetime']; 
        $type_rdv = mysqli_real_escape_string($conn, $_POST['type']);
    
        $query = "SELECT id_doc FROM doctor WHERE type = '$type_rdv'";
        $result = mysqli_query($conn, $query);
    
        if (mysqli_num_rows($result) > 0) {
            $doctor = mysqli_fetch_assoc($result);
            $doctor_id = $doctor['id_doc'];
    
            $insert_query = "INSERT INTO rdv (id_doc, id_client, rdv_date, type, etat)
                             VALUES ('$doctor_id', '$user_id', '$datetime', '$type_rdv', 'pending')";
    
            if (mysqli_query($conn, $insert_query)) {
                header("Location: rdvclient.php?id=$user_id&success=true");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "No doctor found with the specified type.";
        }
    }
    


    $yes = "";
    if (isset($_GET['success']) && $_GET['success'] == 'true') {
        $yes = "Votre rendez-vous est en attente de validation. Vous recevrez un e-mail de confirmation dès qu’il sera approuvé.";
    }

    mysqli_close($conn);


    ?>



    <header>
        <div class="logo">
            <a href="rdvclient.php">
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
                    <a href="profile.php?id=<?php echo $user_id; ?>" id="move">
                        <i class="fa-solid fa-user"></i>
                        <?php echo $name; ?>
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



            <div class="notes">

                <?php if (!empty($yes)) : ?>
                    <div class="yes show"><?php echo $yes; ?></div>
                <?php endif; ?>
                <P class="title"> Notes : </P>
                <p> 1- Remplissez le formulaire avec la date, l'heure et le type de rendez-vous souhaités (ex. : Général
                    ou
                    Prothèse).</p>
                <p> 2- Assurez-vous que toutes les informations fournies sont correctes pour éviter tout retard dans le
                    raitement de votre demande.</p>
                <p> 3- Une secrétaire examinera votre demande et vous enverra un email pour confirmer ou refuser le
                    rendez-vous en fonction des disponibilités.</p>
                <p>4- Consultez régulièrement votre email pour suivre l'état de votre demande de rendez-vous.</p>
                <button class="rdv"> Book Appointment </button>
            </div>

            <div class="form">
    <i class="fa-solid fa-xmark" id="cancell"></i>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table>
            <tr>
                <td><label for="datetime">Date and Time</label></td>
                <td><input name="datetime" type="datetime-local" required></td>
            </tr>
            <tr>
                <td><label for="type">Type</label></td>
                <td>
                    <select name="type" id="type" required>
                        <option value="general">General</option>
                        <option value="prothese">Prothese</option>
                    </select>
                </td>
            </tr>
        </table>
        <input type="submit" value="Send">
        <input type="reset">
    </form>
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

    let rdv = document.querySelector(".rdv");
    let form = document.querySelector(".form")
    let notes = document.querySelector(".notes")
    let cancell = document.getElementById("cancell")

    rdv.onclick = function() {
        form.style.display = 'block';
        notes.style.display = "none";
        setTimeout(function() {
            form.style.top = '0%'; // Slide down
        }, 10); // Small delay to allow the transition to take effect
    };

    // Cancel button hides the alert
    cancell.onclick = function() {
        form.style.top = '-100%'; // Slide up
        setTimeout(function() {
            form.style.display = 'none';
            notes.style.display = "block"

        }, 500); // Wait for the transition to complete
    };
</script>


</html>