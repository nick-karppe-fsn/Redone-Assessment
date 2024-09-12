<?php
    class Flower extends Product {
        public static $db_table = "flowers";

        public function __construct($code, $name, $price) {
            $this->code = $code;
            $this->name = $name;
            $this->price = $price;
        }

        public static function getAllFlowers() {
            $query_result = self::getAllProductsFromCategory(self::$db_table);

            $flowers = array();
            foreach ($query_result as $product) {
                $flower = new Flower($product->code, $product->name, $product->price);
                $flowers[$product->code] = $flower;
            }

            return $flowers;
        }

        public static function getFlower($code) {
            $flower = self::getProductFromCategoryByCode(self::$db_table, $code);
            return new Flower($flower->code, $flower->code, $flower->price);
        }
    }
?>