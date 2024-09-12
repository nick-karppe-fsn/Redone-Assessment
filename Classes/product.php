<?php
    class Product {
        public $code;
        public $name;
        public $price;
        public static $productTypes = array('flowers', 'seeds');

        public function __construct($code, $name, $price) {
            $this->code = $code;
            $this->name = $name;
            $this->price = $price;
        }

        // Returns an array of type Product Type => Products from the db in every declared table in $productTypes
        public static function getAllProducts() {
            $products = array();
            foreach (self::$productTypes as $type) {
                $query = "SELECT * FROM " . $type;
                $result = Database::$instance->makeQuery($query);
                $products[$type] = self::convertProductResultsToArray($result);
            }

            return $products;
        }

        // Returns an array of Products from the db in the provided table
        public static function getAllProductsFromCategory($db_table) {
            $query = "SELECT * FROM " . $db_table . " order by 'name' asc";
            $result = Database::$instance->makeQuery($query);

            return self::convertProductResultsToArray($result);
        }

        // Returns a Product from the db from the provided table where the code == the provided code
        public static function getProductFromCategoryByCode($db_table, $code) {
            $db = new Database();
            $query = "SELECT * FROM " . $db_table . " WHERE code = '$code'";
            $result = mysqli_fetch_array($db::$instance->makeQuery($query));
            return new Product($result['code'], $result['name'], $result['price']);
        }

        // Scans the entire db using the declared product tables to find a product with the provided code
        public static function getProductFromAllByCode($code) {
            $db = new Database();

            $query = "SELECT * FROM (SELECT * FROM " . static::$productTypes[0];
            for($i = 1; $i < sizeof(self::$productTypes); $i++) {
                $query .= " UNION SELECT * FROM " . self::$productTypes[$i];
            }
            $query .= ") as products WHERE products.code = '$code'";
            $result = mysqli_fetch_array($db::$instance->makeQuery($query));
            return new Product($result['code'], $result['name'], $result['price']);
        }

        // Converts a result from product tables into a Product array
        public static function convertProductResultsToArray($queryResults) {
            $products = array();
            foreach($queryResults as $row) {
                $products[] = new Product($row['code'], $row['name'], $row['price']);
            }
            return $products;
        }
    }
?>