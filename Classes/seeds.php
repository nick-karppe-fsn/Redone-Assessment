<?php
class Seeds extends Product {
    public static $db_table = "seeds";

    public function __construct($code, $name, $price) {
        $this->code = $code;
        $this->name = $name;
        $this->price = $price;
    }

    public static function getAllSeeds() {
        $query_result = self::getAllProductsFromCategory(self::$db_table);

        $allSeeds = array();
        foreach ($query_result as $product) {
            $seeds = new Seeds($product->code, $product->name, $product->price);
            $allSeeds[$product->code] = $seeds;
        }

        return $allSeeds;
    }

    public static function getSeeds($code) {
        $seeds = self::getProductFromCategoryByCode(self::$db_table, $code);
        return new Seeds($seeds->code, $seeds->name, $seeds->price);
    }
}
?>