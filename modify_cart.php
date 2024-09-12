<?php
    include "init.php";

    // Add the selected product to the cart from the posted amount and code
    if(isset($_POST["addToCart"])){
        if(isset($_POST["product"])) {

            // Iterate through each product in the form
            foreach (array_keys($_POST["product"]) as $key) {

                // If the product has an input value associated with its key, and it's greater than 0, add it to the cart
                if(!empty($_POST["product"][$key]) && $_POST["product"][$key] > 0) {
                    $product = Product::getProductFromAllByCode($key);
                    Cart::addToCart($product, $_POST["product"][$key]);
                }
            }
        }

        header("Location: index.php");
    }
    // Remove the selected product from the cart
    else if(isset($_POST["removeFromCart"])){
        foreach (array_keys($_POST["removeFromCart"]) as $key) {
            Cart::removeFromCart($key);
        }

        header("Location: index.php");
    }
    // Clear all products from the cart
    else if(isset($_POST["clearCart"])){
        Cart::clearCart();

        header("Location: index.php");
    }
    // Return to the purchase page if the page is entered accidentally or no data is posted
    else {
        header("Location: index.php");
    }
?>