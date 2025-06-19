<?php

namespace App\Services;

class CurrencyConverterService
{
    const FCFA_TO_EUR_RATE = 655.957;
    const FCFA_TO_CNY = 87;

    public function convertCfaToEur(float $amountInCfa): float
    {
        if ($amountInCfa <= 0) {
            return 0.0;
        }
        $amountInEur = $amountInCfa / self::FCFA_TO_EUR_RATE;
        return round($amountInEur, 2);
    }

    // NOUVEAU : Ajout de la conversion inverse
    public function convertEurToCfa(float $amountInEur): float
    {
        if ($amountInEur <= 0) {
            return 0.0;
        }
        $amountInCfa = $amountInEur * self::FCFA_TO_EUR_RATE;
        return round($amountInCfa, 2);
    }

    public function formatEur(float $amountInEur): string
    {
        return number_format($amountInEur, 2, ',', ' ') . ' EUR';
    }

    public function formatCfa(float $amountInCfa): string
    {
        return number_format($amountInCfa, 0, ',', ' ') . ' FCFA';
    }
     // NOUVEAU
     public function cfaToCny(float $cfa): float
     {
         return $cfa > 0 ? round($cfa / self::FCFA_TO_CNY, 2) : 0.0;
     }
 
     public function cnyToCfa(float $cny): float
     {
         return $cny > 0 ? round($cny * self::FCFA_TO_CNY, 2) : 0.0;
     }
}