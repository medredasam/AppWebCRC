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

// Vérifier si l'utilisateur est consultant pour afficher les actions appropriées
$is_consultant = $role_id == 3;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $ticket_id = $_POST['ticket_id'];
    $action = $_POST['action'];

    if ($action == 'update_status') {
        $new_status = $_POST['status'];
        $sql = "UPDATE tickets SET statut = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $ticket_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'update_priority') {
        $new_priority = $_POST['priority'];
        $sql = "UPDATE tickets SET priorite = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_priority, $ticket_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action == 'assign_technician') {
        $technician_id = $_POST['technician_id'];

        if ($technician_id) {
            $sql = "UPDATE tickets SET technician_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $technician_id, $ticket_id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Technician not found.";
        }
    } elseif ($action == 'update_category') {
        $new_category = $_POST['category'];
        $sql = "UPDATE tickets SET categorie = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_category, $ticket_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Obtenir la liste des tickets
$sql = "SELECT t.*, u1.username AS client_nom, u2.username AS technician_nom 
        FROM tickets t 
        LEFT JOIN clients c ON t.client_id = c.user_id 
        LEFT JOIN users u1 ON c.user_id = u1.id
        LEFT JOIN techniciens te ON t.technician_id = te.id
        LEFT JOIN users u2 ON te.user_id = u2.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Tickets</title>
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

        .actions form {
            display: inline-block;
        }

        .actions select, .actions button {
            margin-top: 5px;
            margin-right: 5px;
        }

        .actions button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .actions button:hover {
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
        
        <a href="logout.php">Déconnexion</a>
    </div>
    <div class="container">
        <h1>Gestion des Tickets</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Description</th>
                    <th>Date de Création</th>
                    <th>Statut</th>
                    <th>Priorité</th>
                    <th>Catégorie</th>
                    <th>Technicien</th>
                    <?php if ($is_consultant): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['client_nom'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['dateCreation'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['statut'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['priorite'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['categorie'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['technician_nom'] ?? ''); ?></td>
                        <?php if ($is_consultant): ?>
                            <td class="actions">
                                <form method="POST" action="">
                                    <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <label for="status">Statut :</label>
                                    <select name="status">
                                        <option value="Créé" <?php if ($row['statut'] == 'Créé') echo 'selected'; ?>>Créé</option>
                                        <option value="En cours" <?php if ($row['statut'] == 'En cours') echo 'selected'; ?>>En cours</option>
                                        <option value="Résolu" <?php if ($row['statut'] == 'Résolu') echo 'selected'; ?>>Résolu</option>
                                        <option value="Fermé" <?php if ($row['statut'] == 'Fermé') echo 'selected'; ?>>Fermé</option>
                                    </select>
                                    <br>
                                    <label for="priority">Priorité :</label>
                                    <select name="priority">
                                        <option value="Basse" <?php if ($row['priorite'] == 'Basse') echo 'selected'; ?>>Basse</option>
                                        <option value="Moyenne" <?php if ($row['priorite'] == 'Moyenne') echo 'selected'; ?>>Moyenne</option>
                                        <option value="Haute" <?php if ($row['priorite'] == 'Haute') echo 'selected'; ?>>Haute</option>
                                    </select>
                                    <br>
                                    <label for="category">Catégorie :</label>
                                    <select name="category">
                                        <option value="Technique" <?php if ($row['categorie'] == 'Technique') echo 'selected'; ?>>Technique</option>
                                        <option value="Support" <?php if ($row['categorie'] == 'Support') echo 'selected'; ?>>Support</option>
                                        <option value="Commercial" <?php if ($row['categorie'] == 'Commercial') echo 'selected'; ?>>Commercial</option>
                                        <option value="Autre" <?php if ($row['categorie'] == 'Autre') echo 'selected'; ?>>Autre</option>
                                    </select>
                                    <br>
                                    <label for="technician_id">Technicien :</label>
                                    <select name="technician_id">
                                        <option value="">Sélectionner un technicien</option>
                                        <?php
                                        // Obtenir la liste des techniciens
                                        $tech_sql = "SELECT te.id, u.username 
                                                     FROM techniciens te 
                                                     JOIN users u ON te.user_id = u.id";
                                        $tech_result = $conn->query($tech_sql);
                                        while ($tech_row = $tech_result->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($tech_row['id']); ?>" <?php if ($row['technician_id'] == $tech_row['id']) echo 'selected'; ?>><?php echo htmlspecialchars($tech_row['username']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <br>
                                    <button type="submit" name="action" value="update_status">Mettre à jour le statut</button>
                                    <button type="submit" name="action" value="update_priority">Mettre à jour la priorité</button>
                                    <button type="submit" name="action" value="update_category">Mettre à jour la catégorie</button>
                                    <button type="submit" name="action" value="assign_technician">Assigner un technicien</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
