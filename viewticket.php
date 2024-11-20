<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir les Tickets</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
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
        <a href="logout.php">Déconnexion</a>
        
        
    </div>
    <div class="container">
        <h1>Liste des Tickets</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Description</th>
                    <th>Date de Création</th>
                    <th>Statut</th>
                    <th>Priorité</th>
                </tr>
            </thead>
            <tbody>
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

                // Récupérer les tickets pour le client connecté
                $sql = "SELECT t.id, u.username AS client_nom, t.description, t.dateCreation, t.statut, t.priorite
                        FROM tickets t
                        JOIN users u ON t.client_id = u.id
                        WHERE t.client_id = ?";
                
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['client_nom']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['dateCreation']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['statut']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['priorite']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Aucun ticket trouvé</td></tr>";
                    }

                    $stmt->close();
                } else {
                    echo "<tr><td colspan='6'>Erreur de préparation de la requête : " . $conn->error . "</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
