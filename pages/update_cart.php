<?php
require_once '../pages/cart_functions.php';

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