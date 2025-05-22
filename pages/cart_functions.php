<?php
// ===== 1. FICHIER: cart_functions.php =====
// Fonctions utilitaires pour le panier

session_start();

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

/**
 * Ajouter un produit au panier
 */
function addToCart($product_id, $quantity = 1) {
    // Vérifier si le produit existe déjà dans le panier
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = array(
            'product_id' => $product_id,
            'quantity' => $quantity
        );
    }
}

/**
 * Supprimer un produit du panier
 */
function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

/**
 * Mettre à jour la quantité d'un produit
 */
function updateCartQuantity($product_id, $quantity) {
    if ($quantity <= 0) {
        removeFromCart($product_id);
    } else {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
}

/**
 * Obtenir le nombre total d'articles dans le panier
 */
function getCartItemCount() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

/**
 * Obtenir le contenu détaillé du panier avec infos produits
 */
function getCartDetails($pdo) {
    $cart_details = array();
    $total = 0;
    
    if (!empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
        
        $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($product_ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $product) {
            $product_id = $product['id'];
            $quantity = $_SESSION['cart'][$product_id]['quantity'];
            $subtotal = $product['price'] * $quantity;
            
            $cart_details[] = array(
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            );
            
            $total += $subtotal;
        }
    }
    
    return array('items' => $cart_details, 'total' => $total);
}

/**
 * Vider le panier
 */
function clearCart() {
    $_SESSION['cart'] = array();
}
