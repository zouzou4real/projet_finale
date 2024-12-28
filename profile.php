<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #edf4fa;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        header {
            background-color: #1c2833;
            width: 100%;
            cursor: pointer;
            height: 14%;
            display: flex;
            align-items: center;
            flex-direction: row;
        }


        .logo {
            width: 20%;
            overflow: hidden;
        }

        .logo img {
            width: 90%;
        }

        header span {
            color: #02aeae;
            cursor: pointer;
            margin-left: 20%;
            font-size: 25px;
            font-weight: 600;

        }

        /* 
margin-left: 83%;
margin-bottom: 4%; */

        .out {
            margin-left: 14%;
        }

        /* 
font-size: 20px;
padding: 7px;
width: 70%;
cursor: pointer;
outline: none;
border: none;
border-radius: 8px;
background-color: white;
transition: 0.6s; */

        .out button {
            font-size: 20px;
            border-radius: 8px;
            padding: 12px 17px;
            width: 140%;
            border: none;
            transition: 0.8s;
            outline: none;
            cursor: pointer;
            background-color: #1c2833;
            color: #02aeae;
        }

        /* 
color: white;
width: 75%;
padding-left: 20px;
background-color: red; */

        .out button:hover {
            color: white;
            background-color: red;

        }

        /* 
color: black;
position: relative; */



        .profilebox {
            padding: 13px 9px 15px 9px;
            width: 26%;
            margin-left: 35%;
            margin-top: 10%;
        }

        .username {
            padding-top: 3%;
        }

        .pic {
            margin-left: 37%;
            width: 30%;
            overflow: hidden;
            border-radius: 50%;
        }

        img {
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .username p {
            cursor: pointer;
            padding-left: 6%;
            font-size: 25px;
            width: 50%;
            overflow: hidden;
            margin-left: 30%;
            margin-top: 2%;
        }

        .buttons {

            margin-top: 4%;
            padding-left: 6%;
        }

        button {
            width: 95%;
            padding: 7px;
            border-radius: 5px;
            outline: none;
            border: none;
            margin-bottom: 5%;
            font-size: 18px;
            cursor: pointer;
            color: white;
        }

        #ed {
            background-color: rgba(40, 146, 222, 255);
        }

        #out {
            background-color: rgba(244, 90, 56, 255);
        }

        #dlt {
            background-color: red;
        }
    </style>

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


    <div class="profilebox">

        <div class="username">

            <p>
                <?php echo $name ?>
            </p>

        </div>
        <div class="buttons">
            <?php
            $user_id = $_GET['id'];
            ?>
            <a href="editcl.php?id=<?php echo $user_id; ?>"> <button id="ed"> Edit Profile </button></a>
            <a href="rdvclient.php"> <button id="out"> Back </button></a>
        </div>



    </div>



</body>

</html>