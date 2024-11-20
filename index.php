<?php

session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Inclure le fichier de configuration de la base de données
include 'config/db.php';

// Récupération des informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

$sql = "SELECT username, role_id FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $role_id);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Erreur de préparation de la requête : " . $conn->error;
    exit();
}

// Affichage des informations utilisateur avec une interface améliorée
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?= htmlspecialchars($username); ?>!</h1>
        <p>Vous êtes connecté en tant que 
            <?php
                if ($role_id == 1) {
                    echo "Client.";
                    echo '<a href="create_ticket.php">Créer un ticket</a>';
                } elseif ($role_id == 2) {
                    echo "Technicien.";
                    echo '<a href="view_tickets.php">Voir les tickets assignés</a>';
                } elseif ($role_id == 3) {
                    echo "Consultant.";
                    echo '<a href="manage_tickets.php">Gérer les tickets</a>';
                } else {
                    echo "Rôle utilisateur non reconnu.";
                }
            ?>
        </p>
    </div>
</body>
</html>


