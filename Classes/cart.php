<?php
session_start();
    class Cart {

        // Adds the passed product object to the session variable 'cart' based on its quantity
        public static function addToCart($product, $quantity) {
            //Creates the cart session variable if it doesn't exist yet
            if(!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = array();
            }

            // If the product code already exists in the cart's associative array, add the quantity, else create the product code and quantity
            if(isset($_SESSION['cart'][$product->code])) {
                $_SESSION['cart'][$product->code] += (int)$quantity;
            }
            else {
                $_SESSION['cart'][$product->code] = (int)$quantity;
            }

            // Unsets the product from the session's cart if there is 0 or less of the product in the cart
            if($_SESSION['cart'][$product->code] <= 0) {
                unset($_SESSION['cart'][$product->code]);
            }
        }

        // Scans the cart for the input product code and unsets it if it exists
        public static function removeFromCart($product_code) {
            if(isset($_SESSION['cart'][$product_code])) {
                unset($_SESSION['cart'][$product_code]);
            }
        }

        // Returns an associative array in the format Product Code (str) => Quantity (int)
        public static function getContents() {
            if(!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = array();
            }

            $session_contents = $_SESSION['cart'];
            $cart_contents = array();
            foreach (array_keys($session_contents) as $product_key) {
                $cart_contents[$product_key] = $session_contents[$product_key];
            }

            return $cart_contents;
        }

        // Scans through the session's cart an unsets all set products by key
        public static function clearCart() {
            $contents = self::getContents();
            foreach (array_keys($contents) as $product_key) {
                unset($_SESSION['cart'][$product_key]);
            }
        }

        // Returns the subtotal of all cart items, calculated by the quantity in session and the price from the db
        public static function getSubtotal() {
            $subtotal = 0;

            $contents = self::getContents();
            foreach (array_keys($contents) as $product_key) {
                $product = Product::getProductFromAllByCode($product_key);
                $subtotal += $product->price * $contents[$product_key];
            }

            return $subtotal;
        }

        // Returns the applicable discount amounts from the coupon db
        public static function getDiscounts() {
            $discount = array();
            $contents = self::getContents();

            $db = new Database();
            $query = "SELECT * from coupons";
            $coupons = $db->makeQuery($query);

            // Iterates through all coupons in the table, formated with coupon_name, required_products, discount_amount, and apply_multiple flag
            foreach ($coupons as $coupon) {
                $coupon_product = $coupon['required_product'];
                $product_amount = $coupon['required_amount'];

                // Add the discount if we meet the cart requirements
                if(key_exists($coupon_product, $contents) && $contents[$coupon_product] >= $product_amount) {
                    // If the coupon is set to apply multiple times, add it based on the multiple number of said product in the cart
                    if($coupon['apply_multiple']) {
                        $discount[$coupon['coupon_name']] = $coupon['discount_amount'] * (int)($contents[$coupon_product] / $product_amount);
                    }
                    // Else only add the coupon once
                    else {
                        $discount[$coupon_product] = $coupon['discount_amount'];
                    }
                }
            }

            return $discount;
        }

        // Returns the subtotal reduced by any applicable discounts
        public static function getSubtotalWithDiscounts() {
            $subtotal = self::getSubtotal();
            $discounts = self::getDiscounts();

            $total_discount = 0;
            foreach ($discounts as $discount) {
                $total_discount += $discount;
            }

            return $subtotal - $total_discount;
        }

        // Returns the calculated shipping amount based on the overall subtotal and discounts of the cart
        public static function getShipping() {
            $subtotal = self::getSubtotalWithDiscounts();
            $shipping = 0;

            if($subtotal < 50) {
                $shipping = 4.95;
            }
            else if ($subtotal < 90) {
                $shipping = 2.95;
            }

            return $shipping;
        }

        // Return the subtotal minus discounts, plus the shipping based on that amount
        public static function getTotal() {
            $subtotal = self::getSubtotal();
            $discounts = self::getDiscounts();
            $shipping = self::getShipping();

            $total_discount = 0;
            foreach ($discounts as $discount) {
                $total_discount += $discount;
            }

            return $subtotal - $total_discount + $shipping;
        }
    }
?>