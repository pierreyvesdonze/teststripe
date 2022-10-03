<?php

namespace App\Service;

class StockManager
{
    public function removeOneFromStock($product)
    {
        return $product->setStock($product->getStock() - 1);
    }

    public function addOneToStock($product)
    {
        return $product->setStock($product->getStock() + 1);
    }

    public function updateQuantityInCart($quantity, $stock)
    {
        $newQuantity = $quantity;

        if ($quantity > $stock) {
            $newQuantity = $stock;
        }

        return $newQuantity;
    }
}
