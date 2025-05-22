<?php
$host = 'localhost';
$dbname = 'ecommerce';
$username = 'root';
$password = 'kenzi'; // adapte à ton environnement

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Connexion réussie à la base de données.";
} catch (PDOException $e) {
    echo "❌ Échec de la connexion : " . $e->getMessage();
    exit();
}
?>