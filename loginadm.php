<?php
ob_start();
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/loginadm.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hospital";

    $conn = mysqli_connect($host, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $error = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        // Determine the table according to the role
        $table = ($role == "dct") ? "doctor" : "secretaire";

        // Select the name and type from the secrétaire table
        $query = "SELECT * FROM $table WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            $_SESSION['id'] = $user['id_sec'];  // Store the secrétaire ID in session
            $_SESSION['name'] = $user['name'];  // Store the secrétaire name
            $_SESSION['email'] = $email;       // Store the email in session
            $_SESSION['secretaire_type'] = $user['type']; 

            // Redirect based on role
            if ($role == 'dct') {
                $_SESSION['id'] = $user['id_doc'];
                header("Location: homedoc.php");
            } else {
                $_SESSION['id']= $user['id_sec'];
                header("Location: homesec.php"); // For secrétaire
            }
            exit();
        } else {
            $error = "Incorrect email or password";
        }
    }
    ?>

    <div class="loginbox">
        <div class="logo">
            <img src="../img/logo.png" alt="">
        </div>
        <div class="text">
            <p class="name"> Your Best Health Clinic</p>
            <p class="desp"> Welcome To Our WebApp. Fill the Form to Get in </p>
        </div>
        <div class="login">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <input type="email" name="email" required placeholder="Please Enter Your email">
                <i class="fa-solid fa-envelope"></i>
                <br>
                <input id="pass" type="password" name="password" required placeholder="Please Enter Your password">
                <i class="fa-solid fa-lock"></i>
                <i id="eye" class="fa-regular fa-eye"></i>
                <br>
                <label for="">Role</label>
                <select name="role" id="">
                    <option value="dct"> Doctor</option>
                    <option value="sec"> Secretaire</option>
                </select>

                <input type="submit" value="Log in">
                <div class="error">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST") { echo $error; } ?>
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
    });

    eye.onclick = function() {
        if (passfield.type == "password") {
            passfield.type = "text";
        } else {
            passfield.type = "password";
        }
    }
</script>

</html>
