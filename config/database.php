<?php
$host = 'localhost';       // ou 127.0.0.1
$dbname = 'gestion_de_stock';        // nom de la base
$username = 'root';        // ton utilisateur MySQL
$password = 'kenzi';            // mot de passe (souvent vide en local)

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configurer PDO pour lancer des exceptions en cas d'erreur
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie à la base de données.";
} catch (PDOException $e) {
    echo "❌ Échec de la connexion : " . $e->getMessage();
}
?>
