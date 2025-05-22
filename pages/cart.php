
<?php
require_once '../config/database.php';
require_once '../pages/cart_functions.php';

$cart_details = getCartDetails($pdo);
$cart_count = getCartItemCount();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Mon Panier - Ma Boutique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="../pages/shop.php">Ma Boutique</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="../pages/shop.php">Continuer mes achats</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="mb-4"><i class="bi-cart3 me-2"></i>Mon Panier</h2>
        
        <?php if (empty($cart_details['items'])): ?>
            <!-- Panier vide -->
            <div class="text-center py-5">
                <i class="bi-cart-x display-1 text-muted"></i>
                <h3 class="mt-3">Votre panier est vide</h3>
                <p class="text-muted">Découvrez nos produits et ajoutez-les à votre panier</p>
                <a href="shop.php" class="btn btn-primary">Voir nos produits</a>
            </div>
        <?php else: ?>
            <!-- Contenu du panier -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($cart_details['items'] as $item): ?>
                                <div class="row align-items-center border-bottom py-3">
                                    <div class="col-md-2">
                                        <?php if (!empty($item['product']['image_url'])): ?>
                                            <img src="<?= htmlspecialchars($item['product']['image_url']) ?>" 
                                                 class="img-fluid rounded" alt="<?= htmlspecialchars($item['product']['name']) ?>"
                                                 style="max-height: 80px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="/api/placeholder/80/80" class="img-fluid rounded" alt="Produit">
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['product']['name']) ?></h6>
                                        <small class="text-muted"><?= number_format($item['product']['price'], 2) ?> € l'unité</small>
                                    </div>
                                    <div class="col-md-3">
                                        <form class="update-quantity-form" data-product-id="<?= $item['product']['id'] ?>">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary btn-sm qty-decrease">-</button>
                                                <input type="number" class="form-control form-control-sm text-center" 
                                                       name="quantity" value="<?= $item['quantity'] ?>" min="1" 
                                                       max="<?= $item['product']['stock_quantity'] ?>">
                                                <button type="button" class="btn btn-outline-secondary btn-sm qty-increase">+</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-2">
                                        <strong><?= number_format($item['subtotal'], 2) ?> €</strong>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-outline-danger btn-sm remove-item" 
                                                data-product-id="<?= $item['product']['id'] ?>">
                                            <i class="bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Résumé de la commande -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Résumé de la commande</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Sous-total (<?= $cart_count ?> articles)</span>
                                <span><?= number_format($cart_details['total'], 2) ?> €</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Livraison</span>
                                <span>Gratuite</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong><?= number_format($cart_details['total'], 2) ?> €</strong>
                            </div>
                            <button class="btn btn-success w-100 mb-2">Passer la commande</button>
                            <a href="shop.php" class="btn btn-outline-secondary w-100">Continuer mes achats</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Gestion des quantités et suppression d'articles
    document.addEventListener('DOMContentLoaded', function() {
        // Boutons + et -
        document.querySelectorAll('.qty-decrease, .qty-increase').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input[name="quantity"]');
                let currentValue = parseInt(input.value);
                
                if (this.classList.contains('qty-decrease') && currentValue > 1) {
                    input.value = currentValue - 1;
                } else if (this.classList.contains('qty-increase') && currentValue < parseInt(input.max)) {
                    input.value = currentValue + 1;
                }
                
                updateQuantity(input);
            });
        });
        
        // Changement direct de quantité
        document.querySelectorAll('input[name="quantity"]').forEach(input => {
            input.addEventListener('change', function() {
                updateQuantity(this);
            });
        });
        
        // Suppression d'articles
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                removeFromCart(productId);
            });
        });
    });

    function updateQuantity(input) {
        const form = input.closest('.update-quantity-form');
        const productId = form.dataset.productId;
        const quantity = input.value;
        
        fetch('update_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}&action=update`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recharger la page pour mettre à jour les totaux
            }
        });
    }

    function removeFromCart(productId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&action=remove`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }
    </script>
</body>
</html>
