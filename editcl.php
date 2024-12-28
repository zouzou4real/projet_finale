<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
    <link rel="stylesheet" href="../css/editcl.css">

</head>


<body id="bd">

    <?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hospital";

    $conn = mysqli_connect($host, $username, $password, $dbname);

    if (!$conn) {
        die("Échec de la connexion: " . mysqli_connect_error());
    }

    $user_id = $_GET['id'];

    $query = "SELECT * FROM client WHERE id_client = $user_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user_info = mysqli_fetch_assoc($result);
        $name = $user_info['name'];
        $email = $user_info['email'];
        $age = $user_info['age'];
        $phone = $user_info['phone'];
        $credit = $user_info['credit'];
        $password = $user_info['password'];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $password = mysqli_real_escape_string($conn, $_POST['Password']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $age = mysqli_real_escape_string($conn, $_POST['age']);

        $query = "UPDATE client SET 
                  email='$email', 
                  name='$name', 
                  password='$password', 
                  phone='$phone', 
                  age='$age' 
                  WHERE id_client='$user_id'";

        if (mysqli_query($conn, $query)) {
            header("Location: editcl.php?id=$user_id&success=true");
        } else {
            echo "Error updating profile: " . mysqli_error($conn);
        }
    }

    $yes = "";
    if (isset($_GET['success']) && $_GET['success'] == 'true') {
        $yes = "Your Profile has been Updated ! ";
    }
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

    <div class="box">

        <?php if (!empty($yes)) : ?>
            <div class="yes show"><?php echo $yes; ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $user_id; ?>">

            <div class="info">
                <div class="one">
                    <label for=""> Your Email </label><br>
                    <input type="email" value="<?php echo $email; ?>" required name="email">
                </div>

                <div class="one">
                    <label for=""> Your FullName </label><br>
                    <input id="input" type="text" name="name" value="<?php echo $name; ?>" required>
                </div>

                <div class=" one">
                    <label for=""> Your Password </label><br>
                    <input id="input" type="text" required name="Password" value="<?php echo $password; ?>">
                </div>

                <div class="one">
                    <label for=""> Your Phone Number </label><br>
                    <input type="number" id="input" name="phone" required value="<?php echo $phone; ?>">
                </div>

                <div class="one">
                    <label for=""> Your Age </label><br>
                    <input type="number" required id="input" name="age" value="<?php echo $age; ?>">
                </div>

                <div class="one">
                    <label for=""> Loan </label><br>
                    <input type="number" value="<?php echo $credit; ?>" readonly>
                </div>
                <div class="buttons">
                    <input id="Update" type="submit" value="Update Profile">
                    <br>
                    <button id="bt"> <a href="profile.php?id=<?php echo $user_id; ?>"> Back </a> </button>
                </div>
            </div>
        </form>




    </div>


</body>



</html>