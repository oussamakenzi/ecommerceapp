
<?php
require_once '../config/database.php';
require_once '../pages/cart_functions.php';

// Récupérer les produits
$sql = "SELECT p.*, c.name AS category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cart_count = getCartItemCount();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Boutique - Ma Boutique en Ligne</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Navigation (avec compteur panier mis à jour) -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="#!">Ma Boutique</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item"><a class="nav-link active" href="shop.php">Boutique</a></li>
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

        <!-- Header -->
        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">Achetez avec style</h1>
                    <p class="lead fw-normal text-white-50 mb-0">Découvrez notre sélection de produits</p>
                </div>
            </div>
        </header>

        <!-- Alerts -->
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

        <!-- Section produits -->
        <section class="py-5">
            <div class="container px-4 px-lg-5 mt-5">
                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    <?php foreach ($produits as $produit): ?>
                        <div class="col mb-5">
                            <div class="card h-100">
                                <!-- Image du produit -->
                                <?php if (!empty($produit['image_url'])): ?>
                                    <img class="card-img-top" src="<?= htmlspecialchars($produit['image_url']) ?>" alt="<?= htmlspecialchars($produit['name']) ?>" style="height: 250px; object-fit: cover;" />
                                <?php else: ?>
                                    <img class="card-img-top" src="/api/placeholder/450/300" alt="<?= htmlspecialchars($produit['name']) ?>" />
                                <?php endif; ?>
                                
                                <!-- Détails du produit -->
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <h5 class="fw-bolder"><?= htmlspecialchars($produit['name']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars(substr($produit['description'], 0, 100)) ?>...</p>
                                        <div class="mb-2">
                                            <strong><?= number_format($produit['price'], 2) ?> €</strong>
                                        </div>
                                        <?php if (!empty($produit['category_name'])): ?>
                                            <small class="text-muted"><?= htmlspecialchars($produit['category_name']) ?></small>
                                        <?php endif; ?>
                                        
                                        <!-- Gestion du stock -->
                                        <?php if ($produit['stock_quantity'] < 5 && $produit['stock_quantity'] > 0): ?>
                                            <div><small class="text-warning">Plus que <?= (int)$produit['stock_quantity'] ?> en stock !</small></div>
                                        <?php elseif ($produit['stock_quantity'] == 0): ?>
                                            <div><small class="text-danger">Rupture de stock</small></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Actions du produit -->
                                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                    <?php if ($produit['stock_quantity'] > 0): ?>
                                        <!-- Formulaire d'ajout au panier -->
                                        <form class="add-to-cart-form text-center" data-product-id="<?= $produit['id'] ?>">
                                            <div class="input-group mb-2">
                                                <input type="number" class="form-control text-center" name="quantity" value="1" min="1" max="<?= $produit['stock_quantity'] ?>" style="max-width: 80px; margin: 0 auto;">
                                            </div>
                                            <button type="submit" class="btn btn-outline-dark btn-add-to-cart">
                                                <i class="bi-cart-plus me-1"></i>Ajouter au panier
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="text-center">
                                            <button class="btn btn-outline-secondary" disabled>Indisponible</button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-5 bg-dark">
            <div class="container"><p class="m-0 text-center text-white">Copyright &copy; Ma Boutique <?= date('Y') ?></p></div>
        </footer>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Script AJAX pour ajout au panier -->
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
                    button.innerHTML = '<i class="bi-hourglass me-1"></i>Ajout...';
                    
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
                            
                            // Afficher un message de succès
                            showAlert('success', data.message);
                            
                            // Réactiver le bouton
                            button.disabled = false;
                            button.innerHTML = '<i class="bi-cart-plus me-1"></i>Ajouter au panier';
                        } else {
                            showAlert('error', data.message);
                            button.disabled = false;
                            button.innerHTML = '<i class="bi-cart-plus me-1"></i>Ajouter au panier';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showAlert('error', 'Une erreur est survenue');
                        button.disabled = false;
                        button.innerHTML = '<i class="bi-cart-plus me-1"></i>Ajouter au panier';
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
            
            // Supprimer automatiquement après 3 secondes
            setTimeout(() => {
                const alert = document.querySelector('.alert:last-of-type');
                if (alert) {
                    alert.remove();
                }
            }, 3000);
        }
        </script>
    </body>
</html>

// ===== 4. FICHIER: cart.php =====
// Page d'affichage du panier

<?php
require_once '../config/database.php';
require_once 'cart_functions.php';

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
            <a class="navbar-brand" href="shop.php">Ma Boutique</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="shop.php">Continuer mes achats</a>
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

// ===== 5. FICHIER: update_cart.php =====
// Script pour mettre à jour le panier (quantités, suppression)

<?php
require_once 'cart_functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($product_id > 0) {
        switch ($action) {
            case 'update':
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
                updateCartQuantity($product_id, $quantity);
                echo json_encode(['success' => true, 'message' => 'Quantité mise à jour']);
                break;
                
            case 'remove':
                removeFromCart($product_id);
                echo json_encode(['success' => true, 'message' => 'Article supprimé']);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID produit invalide']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>