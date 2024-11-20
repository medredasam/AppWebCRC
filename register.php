<?php
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role_id'];

    // Vérifier si l'utilisateur existe déjà
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo "L'utilisateur ou l'email existe déjà.";
            $stmt->close();
            exit();
        }
        $stmt->close();
    }

    // Insérer l'utilisateur dans la table users
    $sql = "INSERT INTO users (username, password, email, role_id) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $username, $password, $email, $role_id);
        if ($stmt->execute()) {
            // Récupérer l'ID de l'utilisateur inséré
            $user_id = $stmt->insert_id;

            // Insérer dans la table appropriée en fonction du rôle
            if ($role_id == 1) {
                $sql = "INSERT INTO clients (user_id) VALUES (?)";
            } elseif ($role_id == 2) {
                $sql = "INSERT INTO techniciens (user_id) VALUES (?)";
            } elseif ($role_id == 3) {
                $sql = "INSERT INTO consultants (user_id) VALUES (?)";
            }

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    echo "Inscription réussie.";
                } else {
                    echo "Erreur lors de l'inscription : " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            echo "Erreur lors de l'inscription : " . $stmt->error;
        }
       // $stmt->close();
    } else {
        echo "Erreur de préparation de la requête : " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
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

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button, input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover, input[type="submit"]:hover {
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
        <a href="login.php">Connexion</a>
        <a href="register.php">Inscription</a>
        
    </div>
    <div class="container">
        <h1>Inscription</h1>
        <form method="POST" action="">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" name="username" id="username" required>
            
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>
            
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
            
            <label for="role_id">Type d'utilisateur :</label>
            <select name="role_id" id="role_id" required>
                <option value="1">Client</option>
                <option value="2">Technicien</option>
                <option value="3">Consultant</option>
            </select>
            
            <input type="submit" value="S'inscrire">
        </form>
    </div>
</body>
</html>
