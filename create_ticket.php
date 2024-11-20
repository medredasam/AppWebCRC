<?php
session_start();
include('config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) { // 1 = client role
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $category = $_POST['category'];
    $client_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tickets (description, dateCreation, statut, priorite, categorie, client_id) VALUES (?, NOW(), 'créé', ?, ?, ?)");
    $stmt->bind_param("sssi", $description, $priority, $category, $client_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input, textarea, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .navbar {
            background-color: #4CAF50;
            padding: 10px;
            text-align: center;
            color: white;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin: 0 10px;
        }

        .navbar a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="navbar">
       
        <a href="viewticket.php">Voir les Tickets</a>
        <a href="logout.php">Déconnexion</a>
    </div>
    <div class="container">
        <h2>Créer un Ticket</h2>
        <form method="post" action="create_ticket.php">
            <label for="description">Description :</label>
            <textarea name="description" id="description" required></textarea>
            <label for="priority">Priorité :</label>
            <select name="priority" id="priority" required>
                <option value="basse">Basse</option>
                <option value="moyenne">Moyenne</option>
                <option value="haute">Haute</option>
            </select>
            <label for="category">Catégorie :</label>
            <input type="text" name="category" id="category" required>
            <button type="submit">Créer un Ticket</button>
        </form>
    </div>
</body>
</html>

