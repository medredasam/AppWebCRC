<?php
session_start();
include 'config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtenir les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

// Vérifier si l'utilisateur est technicien
$is_technicien = $role_id == 2;

if ($is_technicien) {
    // Obtenir l'identifiant du technicien
    $sql = "SELECT id FROM techniciens WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($technician_id);
    $stmt->fetch();
    $stmt->close();

    // Obtenir les tickets assignés à ce technicien
    $sql = "SELECT t.*, u1.username AS client_nom, u2.username AS technician_nom 
            FROM tickets t 
            LEFT JOIN clients c ON t.client_id = c.user_id 
            LEFT JOIN users u1 ON c.user_id = u1.id
            LEFT JOIN techniciens te ON t.technician_id = te.user_id
            LEFT JOIN users u2 ON te.user_id = u2.id
            WHERE t.technician_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $technician_id);
} else {
    // Obtenir tous les tickets (pour les autres rôles, par exemple les consultants)
    $sql = "SELECT t.*, u1.username AS client_nom, u2.username AS technician_nom 
            FROM tickets t 
            LEFT JOIN clients c ON t.client_id = c.user_id 
            LEFT JOIN users u1 ON c.user_id = u1.id
            LEFT JOIN techniciens te ON t.technician_id = te.user_id
            LEFT JOIN users u2 ON te.user_id = u2.id";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

?>

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
            max-width: 1200px;
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
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
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

        .no-tickets {
            text-align: center;
            color: #555;
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
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['client_nom'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['dateCreation'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['statut'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['priorite'] ?? ''); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="no-tickets">Aucun ticket trouvé</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>


