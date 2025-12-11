<?php

namespace App\Services;

class PaymentService
{
    /**
     * Simulates an external credit card API charge.
     * In a real application, this would interact with Stripe, PayMongo, etc.
     */
    public function chargeCard(array $details): bool
    {
        // Simple simulation: fail if card number is '000'
        if ($details['card_number'] === '0000000000000000') {
            return false;
        }
        // Assume success otherwise
        return true; 
    }
}