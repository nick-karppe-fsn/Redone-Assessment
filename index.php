<!-- Initialize -->
<?php
    include "init.php";
    $db = new Database();
?>

<link href="Styles/styles.css" rel="stylesheet">

<!-- Create tables of loaded products from db -->
<!-- (This could be shortened substantially by pulling all products in an array by Product Type => Product List and looping through the array.) -->
<!-- (I opted to pull products individually by type to show inheritance with individual product classes.) -->
<h1>Products</h1>
<hr>

    <!-- Loads an array of all flowers to display in the below table-->
    <?php
        $flowers = Flower::getAllFlowers();
    ?>

    <h2>Flowers</h2>

    <form action="modify_cart.php" method="post" enctype="multipart/form-data">
        <table class="productTable">
            <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Price</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($flowers as $flower):  ?>
                <tr>
                    <!-- Display code, name, and price for each product -->
                    <td><?php echo $flower->code ?></td>
                    <td><?php echo $flower->name ?></td>
                    <td><?php echo "$" . number_format($flower->price, 2) ?></td>

                    <!-- Append field for inputting an amount to cart -->
                    <td><input name="product[<?php echo $flower->code ?>]" type="number"></td>
                </tr>
            <?php endforeach; ?>

            <!-- Add button at end of table to add above inputs to cart -->
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><input type="submit" name="addToCart" value="Add To Cart"></td>
            </tr>
            </tbody>
        </table>
    </form>

    <!-- Loads an array of all seeds to display in the below table -->
    <!-- (Outside of project scope, but seeds added as products just to prove functionality of pulling db info from multiple tables.) -->
    <?php
        $allSeeds = Seeds::getAllSeeds();
    ?>

    <h2>Seeds</h2>

    <form action="modify_cart.php" method="post" enctype="multipart/form-data">
        <table class="productTable">
            <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Price</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($allSeeds as $seeds):  ?>
                <tr>
                    <!-- Display code, name, and price for each product -->
                    <td><?php echo $seeds->code ?></td>
                    <td><?php echo $seeds->name ?></td>
                    <td><?php echo "$" . number_format($seeds->price, 2) ?></td>

                    <!-- Append field for inputting an amount to cart -->
                    <td><input name="product[<?php echo $seeds->code ?>]" type="number"></td>
                </tr>
            <?php endforeach; ?>

            <!-- Add button at end of table to add above inputs to cart -->
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td><input type="submit" name="addToCart" value="Add To Cart"></td>
            </tr>
            </tbody>
        </table>
    </form>

<!-- Create table of current cart contents -->
<h1>Cart</h1>
<hr>

<!-- Get the cart contents in an associative array of Product => Quantity -->
<?php
    $cart_contents = Cart::getContents();
?>

<form action="modify_cart.php"  method="post" enctype="multipart/form-data">
    <table class="productTable">
        <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <?php foreach (array_keys($cart_contents) as $item):  ?>
                <tr>
                    <!-- Load the product data from the database -->
                    <?php
                        $product = Product::getProductFromAllByCode($item);
                    ?>

                    <!-- Display code, name, quantity, and subtotal -->
                    <td><?php echo $product->code ?></td>
                    <td><?php echo $product->name ?></td>
                    <td><?php echo $cart_contents[$item] ?></td>
                    <td><?php echo "$" . number_format($product->price * $cart_contents[$item], 2) ?></td>

                    <!-- Append button to remove all of this product -->
                    <td><input type="submit" name="removeFromCart[<?php echo $product->code ?>]" value="Remove"></td>
                </tr>
            <?php endforeach; ?>

            <!-- Add button to clear all items from the cart -->
            <tr>
                <?php if(!empty($cart_contents)):?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><input type="submit" name="clearCart" value="Clear Cart"></td>
                <?php endif;?>
            </tr>
        </tbody>
    </table>
</form>

<!-- Create table showing cart totals and discounts -->
<h2>Totals</h2>
<hr>

<table class="productTable">
    <tbody>
        <!-- Calculate subtotal -->
        <tr>
            <td>Subtotal</td>
            <td><?php echo "$" . number_format(Cart::getSubtotal(), 2) ?></td>
        </tr>

        <!-- Calculate discounts -->
        <?php $discounts = Cart::getDiscounts() ?>
        <?php foreach (array_keys($discounts) as $discount):  ?>
            <td><?php echo $discount ?></td>
            <td><?php echo "-$" . number_format($discounts[$discount], 2) ?></td>
        <?php endforeach; ?>

        <!-- Calculate shipping -->
        <tr>
            <td>Shipping</td>
            <td><?php echo "$" . (!empty($cart_contents) ? number_format(Cart::getShipping(), 2) : "0.00") ?></td>
        </tr>

        <!-- Calculate total -->
        <tr>
            <td>Total</td>
            <?php $total = floor(number_format(Cart::getTotal(), 3) * 100) / 100 ?>
            <td><?php echo "$" . (!empty($cart_contents) ? $total : "0.00") ?></td>
        </tr>
    </tbody>
</table>
