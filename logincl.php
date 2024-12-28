<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/logincl.css">
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

    $error = "";


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $query = "SELECT * FROM client WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {

            $user = mysqli_fetch_assoc($result);
            $_SESSION['id'] = $user['id_client'];
            $_SESSION['name'] = $user['name'];
            header("Location: rdvclient.php");
        } else {
            $error = "Mot de passe ou email incorrect";
        }
    }

    mysqli_close($conn);
    ?>

    <div class="loginbox">

        <div class="logo">
            <img src="../img/logo.png" alt="">
        </div>
        <div class="text">
            <p class="name"> Your Best Health Clinic</p>
            <p class="desp"> Welcome To Our WebApp Fill The Form to Get in </p>
        </div>
        <div class="login">
            <form method="post">

                <input type="email" name="email" required placeholder="Please Enter Your email">
                <i class="fa-solid fa-envelope"></i>
                <br>
                <input id="pass" type="password" name="password" required placeholder="Please Enter Your password">
                <i class="fa-solid fa-lock"></i>
                <i id="eye" class="fa-regular fa-eye"></i>
                <br>

                <input type="submit" value="Sign in">
                <div class="error">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        echo $error;
                    }
                    ?>

                </div>


            </form>

        </div>

    </div>



</body>
<script>
    let eye = document.getElementById("eye");
    let passfield = document.getElementById("pass");


    passfield.addEventListener('input', function() {

        if (passfield.value == "") {
            eye.style.display = "none";
        } else {
            eye.style.display = "inline-block";
        }


    })

    eye.onclick = function() {

        if (passfield.type == "password") {
            passfield.type = "text";
        } else {
            passfield.type = "password";
        }
    }
</script>

</html>