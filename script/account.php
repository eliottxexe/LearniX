<?php

$servername = "localhost";
$username = "Eliott Exe";
$password = "1P]n!w*pJpKUVboz";
$dbname = "learnix";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['name1'];
    $last_name = $_POST['name2'];
    $username = $_POST['username'];
    $birthdate = $_POST['birthdate'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input and check for errors
    $errors = [];
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Le nom d'utilisateur ou l'email existe déjà.";
    }
    $stmt->close();

    // If no errors, insert into database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, birthdate, email, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $username, $birthdate, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "Inscription réussie !";
        } else {
            echo "Erreur: " . $stmt->error;
        }
        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<p>" . $error . "</p>";
        }
    }
}

$conn->close();
?>
