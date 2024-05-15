<?php

$servername = "localhost";
$username = "Eliott Exe"; // Utilisateur de la base de données avec des permissions limitées
$password = "1P]n!w*pJpKUVboz"; // Mot de passe fort pour l'accès à la base de données
$dbname = "learnix";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation des données d'entrée
    $first_name = htmlspecialchars($_POST['name1']);
    $last_name = htmlspecialchars($_POST['name2']);
    $username = htmlspecialchars($_POST['username']);
    $birthdate = $_POST['birthdate']; // Valider le format de la date si nécessaire
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Valider les mots de passe
    if ($password !== $confirm_password) {
        die("Les mots de passe ne correspondent pas.");
    }

    // Hachage sécurisé du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Vérifier si le nom d'utilisateur ou l'email existe déjà
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Le nom d'utilisateur ou l'email existe déjà.");
    }
    $stmt->close();

    // Insérer dans la base de données
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, birthdate, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $username, $birthdate, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "Inscription réussie !";
    } else {
        echo "Erreur lors de l'inscription. Veuillez réessayer.";
    }
    $stmt->close();
}

$conn->close();
?>
