<?php
require_once '../config/database.php';
require_once '../pages/cart_functions.php'; // AJOUT : Inclure les fonctions du panier

// Récupérer les produits
$sql = "SELECT p.*, c.name AS category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// AJOUT : Récupérer le nombre d'articles dans le panier
$cart_count = getCartItemCount();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Boutique - Ma Boutique en Ligne</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <!-- AJOUT : Bootstrap CSS pour assurer la compatibilité -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="index.php">Ma Boutique</a> <!-- MODIFIÉ -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Accueil</a></li> <!-- MODIFIÉ -->
                        <li class="nav-item"><a class="nav-link" href="#!">À propos</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Boutique</a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="index.php">Tous les produits</a></li> <!-- MODIFIÉ -->
                                <li><hr class="dropdown-divider" /></li>
                                <li><a class="dropdown-item" href="#!">Articles populaires</a></li>
                                <li><a class="dropdown-item" href="#!">Nouveautés</a></li>
                            </ul>
                        </li>
                        <!-- AJOUT : Lien vers le panier -->
                        <li class="nav-item"><a class="nav-link" href="../pages/cart.php">Mon Panier</a></li>
                    </ul>
                    <!-- MODIFIÉ : Bouton panier avec compteur dynamique -->
                    <form class="d-flex">
                        <a href="../pages/cart.php" class="btn btn-outline-dark">
                            <i class="bi-cart-fill me-1"></i>
                            Panier
                            <span class="badge bg-dark text-white ms-1 rounded-pill" id="cart-badge"><?= $cart_count ?></span>
                        </a>
                    </form>
                </div>
            </div>
        </nav>
        
        <!-- Header-->
        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Achetez avec style</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Découvrez notre sélection de produits</p>
                </div>
            </div>
        </header>

        <!-- AJOUT : Alertes pour les messages de succès/erreur -->
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
        
        <!-- Section-->
        <section class="py-5">
            <div class="container px-4 px-lg-5 mt-5">
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <?php if (!empty($produits)): ?>
                        <?php foreach ($produits as $produit): ?>
                            <div class="col mb-5">
                                <div class="card h-100">
                                    <!-- Badge de promotion si le produit est en promotion -->
                                    <?php if (isset($produit['is_on_sale']) && $produit['is_on_sale']): ?>
                                        <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Promo</div>
                                    <?php endif; ?>
                                    
                                    <!-- Image du produit -->
                                    <?php if (!empty($produit['image_url'])): ?>
                                        <img class="card-img-top" src="<?= htmlspecialchars($produit['image_url']) ?>" alt="<?= htmlspecialchars($produit['name']) ?>" style="height: 250px; object-fit: cover;" />
                                    <?php else: ?>
                                        <img class="card-img-top" src="/api/placeholder/450/300" alt="<?= htmlspecialchars($produit['name']) ?>" />
                                    <?php endif; ?>
                                    
                                    <!-- Détails du produit -->
                                    <div class="card-body p-4">
                                        <div class="text-center">
                                            <!-- Nom du produit -->
                                            <h5 class="fw-bolder"><?= htmlspecialchars($produit['name']) ?></h5>
                                            
                                            <!-- AJOUT : Description courte -->
                                            <p class="card-text small text-muted mb-2">
                                                <?= htmlspecialchars(substr($produit['description'], 0, 80)) ?>...
                                            </p>
                                            
                                            <!-- Prix du produit -->
                                            <div class="mb-2">
                                                <strong><?= number_format($produit['price'], 2) ?> €</strong>
                                            </div>
                                            
                                            <!-- Catégorie (optionnel) -->
                                            <?php if (!empty($produit['category_name'])): ?>
                                                <small class="text-muted"><?= htmlspecialchars($produit['category_name']) ?></small>
                                            <?php endif; ?>
                                            
                                            <!-- AJOUT : Gestion du stock -->
                                            <?php if ($produit['stock_quantity'] < 5 && $produit['stock_quantity'] > 0): ?>
                                                <div><small class="text-warning">Plus que <?= (int)$produit['stock_quantity'] ?> en stock !</small></div>
                                            <?php elseif ($produit['stock_quantity'] == 0): ?>
                                                <div><small class="text-danger">Rupture de stock</small></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- MODIFIÉ : Actions du produit avec formulaire d'ajout au panier -->
                                    <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                        <?php if ($produit['stock_quantity'] > 0): ?>
                                            <!-- NOUVEAU : Formulaire d'ajout au panier -->
                                            <form class="add-to-cart-form text-center" data-product-id="<?= $produit['id'] ?>">
                                                <div class="row mb-2">
                                                    <div class="col-6">
                                                        <input type="number" class="form-control form-control-sm text-center" 
                                                               name="quantity" value="1" min="1" 
                                                               max="<?= $produit['stock_quantity'] ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="submit" class="btn btn-outline-dark btn-sm btn-add-to-cart w-100">
                                                            <i class="bi-cart-plus me-1"></i>Panier
                                                        </button>
                                                    </div>
                                                </div>
                                                <!-- Bouton voir détails (optionnel) -->
                                                <a class="btn btn-outline-secondary btn-sm w-100" href="../pages/product_detail.php?id=<?= $produit['id'] ?>">
                                                    Voir détails
                                                </a>
                                            </form>
                                        <?php else: ?>
                                            <div class="text-center">
                                                <button class="btn btn-outline-secondary btn-sm" disabled>Indisponible</button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Message si aucun produit -->
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <h4>Aucun produit disponible</h4>
                                <p>Revenez bientôt pour découvrir nos nouveaux produits !</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        <!-- Footer-->
        <footer class="py-5 bg-dark">
            <div class="container"><p class="m-0 text-center text-white">Copyright &copy; Ma Boutique <?= date('Y') ?></p></div>
        </footer>
        
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        
        <!-- AJOUT : Script AJAX pour ajout au panier -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gérer l'ajout au panier via AJAX
            document.querySelectorAll('.add-to-cart-form').forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const productId = this.dataset.productId;
                    const quantity = this.querySelector('input[name="quantity"]').value;
                    const button = this.querySelector('.btn-add-to-cart');
                    
                    // Désactiver le bouton pendant la requête
                    button.disabled = true;
                    button.innerHTML = '<i class="bi-hourglass me-1"></i>...';
                    
                    // Envoyer la requête AJAX
                    fetch('../pages/add_to_cart.php', {
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
                            
                            // Afficher un message de succès
                            showAlert('success', data.message);
                            
                            // Animation du bouton
                            button.innerHTML = '<i class="bi-check me-1"></i>Ajouté !';
                            button.classList.remove('btn-outline-dark');
                            button.classList.add('btn-success');
                            
                            // Remettre le bouton normal après 2 secondes
                            setTimeout(() => {
                                button.disabled = false;
                                button.innerHTML = '<i class="bi-cart-plus me-1"></i>Panier';
                                button.classList.remove('btn-success');
                                button.classList.add('btn-outline-dark');
                            }, 2000);
                        } else {
                            showAlert('error', data.message);
                            button.disabled = false;
                            button.innerHTML = '<i class="bi-cart-plus me-1"></i>Panier';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showAlert('error', 'Une erreur est survenue');
                        button.disabled = false;
                        button.innerHTML = '<i class="bi-cart-plus me-1"></i>Panier';
                    });
                });
            });
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
            
            // Supprimer automatiquement après 4 secondes
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                if (alerts.length > 0) {
                    alerts[alerts.length - 1].remove();
                }
            }, 4000);
        }
        </script>
    </body>
</html>