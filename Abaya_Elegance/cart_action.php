<?php
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add Item to Cart
if (isset($_GET['add'])) {
    $product_id = $_GET['add'];
    $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
    $size = isset($_GET['size']) ? $_GET['size'] : 'M'; // Default M if not specified (e.g. from quick add)

    // Check if product exists in cart (matching ID and Size)
    $found = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id && $item['size'] == $size) {
            $_SESSION['cart'][$key]['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    // If not found, add new item
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'quantity' => $quantity,
            'size' => $size
        ];
    }
    
    // Redirect back (to cart or previous page)
    header("Location: cart.php");
    exit();
}

// Remove Item
if (isset($_GET['remove'])) {
    $key_to_remove = $_GET['remove']; // We use array index as key
    if (isset($_SESSION['cart'][$key_to_remove])) {
        unset($_SESSION['cart'][$key_to_remove]);
        // Re-index array to fill gaps
        $_SESSION['cart'] = array_values($_SESSION['cart']); 
    }
    header("Location: cart.php");
    exit();
}

// Update Quantity
if (isset($_POST['action']) && $_POST['action'] == 'update_cart') {
    $quantities = $_POST['quantity'];
    foreach ($quantities as $key => $qty) {
        if ($qty > 0) {
            $_SESSION['cart'][$key]['quantity'] = (int)$qty;
        } else {
            // Remove if quantity is 0
            unset($_SESSION['cart'][$key]);
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

// Clear Cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}
?>
