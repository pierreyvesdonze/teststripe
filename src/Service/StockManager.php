<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StockManager extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository
    )
    {
        
    }
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

    public function totalProductsInStock()
    {
        $totalStock = $this->productRepository->createQueryBuilder('p')
            ->select('SUM(p.stock)')
            ->getQuery()
            ->getSingleScalarResult();

            return $totalStock;
    }
}
