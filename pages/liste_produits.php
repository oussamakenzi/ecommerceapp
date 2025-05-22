<?php
require_once '../config/database.php';

// Récupérer les produits
$sql = "SELECT p.*, c.name AS category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des produits</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">🛍️ Liste des produits</h2>

    <div class="row">
        <?php foreach ($produits as $produit): ?>
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <?php if (!empty($produit['image_url'])): ?>
                        <img src="<?= htmlspecialchars($produit['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produit['name']) ?>" style="height: 250px; object-fit: cover;">
                    <?php else: ?>
                        <img src="../images/default.png" class="card-img-top" alt="Pas d'image" style="height: 250px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($produit['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($produit['description']) ?></p>
                        <p><strong>Prix :</strong> <?= number_format($produit['price'], 2) ?> €</p>
                        <p><strong>Catégorie :</strong> <?= htmlspecialchars($produit['category_name']) ?></p>
                        <p><strong>Stock :</strong> <?= (int)$produit['stock_quantity'] ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
