
<?php
require_once '../config/database.php';
require_once '../pages/cart_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($product_id > 0) {
        // Vérifier que le produit existe et qu'il y a assez de stock
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product && $product['stock_quantity'] >= $quantity) {
            addToCart($product_id, $quantity);
            
            // Réponse JSON pour AJAX
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Produit ajouté au panier',
                    'cart_count' => getCartItemCount()
                ]);
                exit;
            } else {
                // Redirection normale
                header('Location: shop.php?success=added');
                exit;
            }
        } else {
            $error = $product ? 'Stock insuffisant' : 'Produit introuvable';
            
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $error
                ]);
                exit;
            } else {
                header('Location: shop.php?error=' . urlencode($error));
                exit;
            }
        }
    }
}

