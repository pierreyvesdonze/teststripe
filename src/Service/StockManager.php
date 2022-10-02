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
}
