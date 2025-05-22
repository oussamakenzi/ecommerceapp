<?php
require_once '../config/database.php';
require_once 'cart_functions.php';

// Récupérer l'ID du produit depuis l'URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: index.php?error=produit_introuvable');
    exit;
}

// Récupérer les détails du produit
$sql = "SELECT p.*, c.name AS category_name, c.description AS category_description 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$product_id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    header('Location: index.php?error=produit_introuvable');
    exit;
}

// Récupérer des produits similaires (même catégorie)
$sql_similar = "SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.id != ? 
                LIMIT 4";
$stmt_similar = $pdo->prepare($sql_similar);
$stmt_similar->execute([$produit['category_id'], $product_id]);
$produits_similaires = $stmt_similar->fetchAll(PDO::FETCH_ASSOC);

$cart_count = getCartItemCount();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="<?= htmlspecialchars(substr($produit['description'], 0, 160)) ?>" />
    <title><?= htmlspecialchars($produit['name']) ?> - Ma Boutique</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    
    <!-- Styles personnalisés pour la page produit -->
    <style>
        .product-image {
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product-thumbnail {
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        .product-thumbnail:hover {
            opacity: 0.8;
        }
        .price-large {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }
        .stock-badge {
            font-size: 0.9rem;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="index.php">Ma Boutique</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#!">À propos</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Mon Panier</a></li>
                </ul>
                <form class="d-flex">
                    <a href="cart.php" class="btn btn-outline-dark">
                        <i class="bi-cart-fill me-1"></i>
                        Panier
                        <span class="badge bg-dark text-white ms-1 rounded-pill" id="cart-badge"><?= $cart_count ?></span>
                    </a>
                </form>
            </div>
        </div>
    </nav>

    <!-- Fil d'Ariane -->
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                <?php if (!empty($produit['category_name'])): ?>
                    <li class="breadcrumb-item"><?= htmlspecialchars($produit['category_name']) ?></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($produit['name']) ?></li>
            </ol>
        </nav>
    </div>

    <!-- Alertes -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            Produit ajouté au panier avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            Erreur : <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Détails du produit -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5">
                <!-- Images du produit -->
                <div class="col-md-6">
                    <!-- Image principale -->
                    <div class="mb-3">
                        <?php if (!empty($produit['image_url'])): ?>
                            <img id="main-image" class="img-fluid product-image w-100" 
                                 src="<?= htmlspecialchars($produit['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($produit['name']) ?>" />
                        <?php else: ?>
                            <img id="main-image" class="img-fluid product-image w-100" 
                                 src="/api/placeholder/400/400" 
                                 alt="<?= htmlspecialchars($produit['name']) ?>" />
                        <?php endif; ?>
                    </div>
                    
                    <!-- Miniatures (si vous avez plusieurs images) -->
                    <div class="row">
                        <div class="col-3">
                            <img class="img-fluid product-thumbnail w-100" 
                                 src="<?= !empty($produit['image_url']) ? htmlspecialchars($produit['image_url']) : '/api/placeholder/100/100' ?>" 
                                 alt="Image 1" onclick="changeMainImage(this.src)" />
                        </div>
                        <!-- Vous pouvez ajouter d'autres miniatures ici -->
                    </div>
                </div>

                <!-- Informations du produit -->
                <div class="col-md-6">
                    <div class="small mb-1"><?= htmlspecialchars($produit['category_name']) ?></div>
                    <h1 class="display-5 fw-bolder"><?= htmlspecialchars($produit['name']) ?></h1>
                    
                    <!-- Évaluations (simulées - vous pouvez les rendre dynamiques) -->
                    <div class="fs-5 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="text-warning me-2">
                                <i class="bi-star-fill"></i>
                                <i class="bi-star-fill"></i>
                                <i class="bi-star-fill"></i>
                                <i class="bi-star-fill"></i>
                                <i class="bi-star-half"></i>
                            </div>
                            <span class="text-muted">(4.5/5 - 24 avis)</span>
                        </div>
                    </div>
                    
                    <!-- Prix -->
                    <div class="price-large mb-3"><?= number_format($produit['price'], 2) ?> €</div>
                    
                    <!-- Stock -->
                    <div class="mb-4">
                        <?php if ($produit['stock_quantity'] > 10): ?>
                            <span class="badge bg-success stock-badge">En stock (<?= $produit['stock_quantity'] ?> disponibles)</span>
                        <?php elseif ($produit['stock_quantity'] > 0): ?>
                            <span class="badge bg-warning stock-badge">Stock limité (<?= $produit['stock_quantity'] ?> restants)</span>
                        <?php else: ?>
                            <span class="badge bg-danger stock-badge">Rupture de stock</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="lead"><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
                    </div>
                    
                    <!-- Caractéristiques (optionnel) -->
                    <div class="mb-4">
                        <h6>Caractéristiques</h6>
                        <ul class="list-unstyled">
                            <li><strong>Référence :</strong> PROD-<?= str_pad($produit['id'], 4, '0', STR_PAD_LEFT) ?></li>
                            <li><strong>Catégorie :</strong> <?= htmlspecialchars($produit['category_name']) ?></li>
                            <?php if (isset($produit['created_at'])): ?>
                                <li><strong>Ajouté le :</strong> <?= date('d/m/Y', strtotime($produit['created_at'])) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- Formulaire d'ajout au panier -->
                    <?php if ($produit['stock_quantity'] > 0): ?>
                        <form class="add-to-cart-form mb-4" data-product-id="<?= $produit['id'] ?>">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-2">
                                    <label for="quantity" class="form-label">Quantité :</label>
                                    <input type="number" id="quantity" name="quantity" class="form-control" 
                                           value="1" min="1" max="<?= $produit['stock_quantity'] ?>">
                                </div>
                                <div class="col-md-8 mb-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid gap-2 d-md-flex">
                                        <button type="submit" class="btn btn-primary btn-lg flex-grow-1 btn-add-to-cart">
                                            <i class="bi-cart-plus me-2"></i>Ajouter au panier
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-lg">
                                            <i class="bi-heart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Informations de livraison -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="bi-truck me-2"></i>Livraison</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><i class="bi-check-circle text-success me-2"></i>Livraison gratuite à partir de 50€</li>
                                    <li><i class="bi-check-circle text-success me-2"></i>Expédition sous 24-48h</li>
                                    <li><i class="bi-check-circle text-success me-2"></i>Retour gratuit sous 30 jours</li>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <h6><i class="bi-exclamation-triangle me-2"></i>Produit indisponible</h6>
                            <p class="mb-0">Ce produit est actuellement en rupture de stock. Nous travaillons pour le remettre en ligne rapidement.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Produits similaires -->
    <?php if (!empty($produits_similaires)): ?>
        <section class="py-5 bg-light">
            <div class="container px-4 px-lg-5">
                <h2 class="fw-bolder mb-4">Produits similaires</h2>
                <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4">
                    <?php foreach ($produits_similaires as $similaire): ?>
                        <div class="col mb-5">
                            <div class="card h-100">
                                <!-- Image du produit -->
                                <a href="product_detail.php?id=<?= $similaire['id'] ?>">
                                    <?php if (!empty($similaire['image_url'])): ?>
                                        <img class="card-img-top" src="<?= htmlspecialchars($similaire['image_url']) ?>" 
                                             alt="<?= htmlspecialchars($similaire['name']) ?>" style="height: 200px; object-fit: cover;" />
                                    <?php else: ?>
                                        <img class="card-img-top" src="/api/placeholder/300/200" alt="<?= htmlspecialchars($similaire['name']) ?>" />
                                    <?php endif; ?>
                                </a>
                                
                                <!-- Détails du produit -->
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <h6 class="fw-bolder">
                                            <a href="product_detail.php?id=<?= $similaire['id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($similaire['name']) ?>
                                            </a>
                                        </h6>
                                        <div><?= number_format($similaire['price'], 2) ?> €</div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                    <div class="text-center">
                                        <a class="btn btn-outline-dark btn-sm" href="product_detail.php?id=<?= $similaire['id'] ?>">
                                            Voir le produit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Ma Boutique <?= date('Y') ?></p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    
    <!-- Scripts spécifiques à la page produit -->
    <script>
        // Fonction pour changer l'image principale
        function changeMainImage(src) {
            document.getElementById('main-image').src = src;
        }

        // Gestion de l'ajout au panier
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.add-to-cart-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const productId = this.dataset.productId;
                    const quantity = this.querySelector('input[name="quantity"]').value;
                    const button = this.querySelector('.btn-add-to-cart');
                    const originalText = button.innerHTML;
                    
                    // Désactiver le bouton pendant la requête
                    button.disabled = true;
                    button.innerHTML = '<i class="bi-hourglass me-2"></i>Ajout en cours...';
                    
                    // Envoyer la requête AJAX
                    fetch('add_to_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `product_id=${productId}&quantity=${quantity}&ajax=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mettre à jour le compteur du panier
                            document.getElementById('cart-badge').textContent = data.cart_count;
                            
                            // Animation de succès
                            button.innerHTML = '<i class="bi-check-circle me-2"></i>Ajouté au panier !';
                            button.classList.remove('btn-primary');
                            button.classList.add('btn-success');
                            
                            // Afficher un message de succès
                            showAlert('success', `${quantity} article(s) ajouté(s) au panier`);
                            
                            // Remettre le bouton normal après 3 secondes
                            setTimeout(() => {
                                button.disabled = false;
                                button.innerHTML = originalText;
                                button.classList.remove('btn-success');
                                button.classList.add('btn-primary');
                            }, 3000);
                        } else {
                            showAlert('error', data.message);
                            button.disabled = false;
                            button.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showAlert('error', 'Une erreur est survenue');
                        button.disabled = false;
                        button.innerHTML = originalText;
                    });
                });
            }
        });

        // Fonction pour afficher les alertes
        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show m-3" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Insérer l'alerte après la navigation
            const nav = document.querySelector('nav');
            nav.insertAdjacentHTML('afterend', alertHTML);
            
            // Supprimer automatiquement après 5 secondes
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                if (alerts.length > 0) {
                    alerts[alerts.length - 1].remove();
                }
            }, 5000);
        }

        // Effet de zoom sur l'image principale (optionnel)
        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.getElementById('main-image');
            if (mainImage) {
                mainImage.addEventListener('click', function() {
                    // Vous pouvez ajouter ici un modal pour agrandir l'image
                    console.log('Image cliquée - modal de zoom à implémenter');
                });
            }
        });
    </script>
</body>
</html>