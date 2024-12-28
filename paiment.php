<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Formulaire de Paiement</title>
    <style>
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin-left: 30%;
            margin-top: 10%;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            height: 50px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back-button {
            background-color: #6c757d;
        }

        .back-button:hover {
            background-color: #495057;
        }
    </style>

</head>

<body>
    <?php


    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hospital";

    $conn = mysqli_connect($host, $username, $password, $dbname);

    if (!$conn) {
        die("Échec de la connexion: " . mysqli_connect_error());
    }
    if (!isset($_GET['idrdv'])) {
        header("location:homedoc.php");
    }
    $idrdv = $_GET['idrdv'];
    $idclient = $_GET['idclient'];


    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $prixpaie = $_POST['prix_paie'];
        $prixrestant = $_POST['prix_restant'];

        $sql = "UPDATE rdv SET etat = 'archived' WHERE id_rdv = $idrdv";
        $result = mysqli_query($conn, $sql);

        $insert_query = "INSERT INTO paiment (id_rdv, id_client, montant) 
                 VALUES ('$idrdv', '$idclient', '$prixpaie')";
        $insert = mysqli_query($conn, $insert_query);

        $update_query = "UPDATE client SET credit = '$prixrestant' WHERE id_client = '$idclient'";
        $update = mysqli_query($conn, $update_query);

        header("Location: homedoc.php ");
    }

    ?>
    <form action="paiment.php?idrdv=<?= $idrdv ?>&idclient=<?= $idclient ?>" method="post">

        <label for="prix-paie">Prix payé :</label>
        <input type="number" id="prix-paie" name="prix_paie" step="100" placeholder="Saisir le prix payé" required>

        <label for="prix-restant">Prix restant :</label>
        <input type="number" id="prix-restant" name="prix_restant" step="100" placeholder="Saisir le prix restant" required>

        <button type="submit">Envoyer</button>
        <br><br>
        <button type="button" class="back-button" onclick="window.location.href='homedoc.php'">Retour</button>

    </form>


</body>

</html>