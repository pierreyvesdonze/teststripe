<?php

namespace App\Service;

class UserRateManager
{
    public function calculAverage($userRates) {
        $productAverageRate  = 0;
        $totalAverageProduct = 0;
        foreach ($userRates as $userRate) {
            $productAverageRate += $userRate->getRate();
        }

        if ($productAverageRate > 0) {
            $totalAverageProduct = $productAverageRate / count($userRates);
        } else {
            $totalAverageProduct = 0;
        }

        return $totalAverageProduct;
    }
}
