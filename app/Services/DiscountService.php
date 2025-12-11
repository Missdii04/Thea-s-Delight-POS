<?php
namespace App\Services;

use Money\Money;
use Money\Currency;
use Money\Converter;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Exchange\FixedExchange;
use Money\Currencies\ISOCurrencies; // DecimalMoneyFormatter

class DiscountService
{
    protected Currency $currency;
    protected float $taxRate = 0.12; // 12% standard VAT rate in PH
    protected float $discountRate = 0.20; // 20% SC/PWD discount

    public function __construct()
    {
        $this->currency = new Currency('PHP'); 
    }

    /**
     * Helper to get the Converter for formatting Money objects back to decimals.
     */
    protected function getFormatter(): DecimalMoneyFormatter
    {
        // DecimalMoneyFormatter requires an object implementing Currencies, which ISOCurrencies does.
        return new DecimalMoneyFormatter(new ISOCurrencies());
    }

    /**
     * Calculates the final totals using precise Money objects.
     *
     * @param array $cart The cart array from the session.
     * @param string $discountType 'sc', 'pwd', or 'none'.
     * @return array Calculated totals in decimal format (e.g., 123.45).
     */
    public function calculateTotals(array $cart, string $discountType): array
    {
        // 1. Calculate Gross Total (Total money customer pays before any adjustments)
        $grossCartTotalMoney = new Money(0, $this->currency);
        
        foreach ($cart as $item) {
            // Convert price (e.g., 125.00) to cents (12500) for precision
            // Use (string) cast before round to ensure precise float handling before multiplication
            $grossPriceUnitCents = (int) round($item['price'] * 100); 
            $grossPriceUnitMoney = new Money($grossPriceUnitCents, $this->currency);
            
            $itemGrossTotalMoney = $grossPriceUnitMoney->multiply($item['quantity']);
            $grossCartTotalMoney = $grossCartTotalMoney->add($itemGrossTotalMoney);
        }

        // 2. Reverse Calculate Original VAT and Net Total (Based on 12% VAT)
        // Split Gross Total into 100 parts Net and 12 parts VAT (112 total parts)
        $netParts = 100;
        $vatParts = 12; 
        
        // Allocate automatically handles the division and ensures no cents are lost.
        $allocatedAmounts = $grossCartTotalMoney->allocate([$netParts, $vatParts]);
        
        $netSubtotalMoney = $allocatedAmounts[0]; // The calculated Net Price (Base for discount)
        $originalVatMoney = $allocatedAmounts[1]; // The calculated VAT Price 
        
        
        // Initialize final values
        $vatExemptAmountMoney = new Money(0, $this->currency);
        $discountAmountMoney = new Money(0, $this->currency);
        $finalTotalMoney = $grossCartTotalMoney; // Start with the original gross total
        $vatTaxOutput = $originalVatMoney; // Default VAT charged is the full VAT
        
        
        // 3. Apply Senior Citizen / PWD Discount Logic
        if ($discountType === 'sc' || $discountType === 'pwd') {
            
            // a. Calculate 20% Discount on the NET SUBTOTAL (Tax Base)
            $discountedAllocation = $netSubtotalMoney->allocate([20, 80]);
            
            $discountAmountMoney = $discountedAllocation[0]; // The 20% discount portion
            $netAmountAfterDiscountMoney = $discountedAllocation[1]; // The remaining 80%
            
            // b. VAT Exemption: The original VAT amount is fully exempted/deducted.
            $vatExemptAmountMoney = $originalVatMoney;
            $vatTaxOutput = new Money(0, $this->currency); // VAT charged is zero
            
            // c. Final Total Calculation (VAT Exempt Total - 20% Discount)
            // The final price is the Net Amount after Discount (80% of Net Price)
            $finalTotalMoney = $netAmountAfterDiscountMoney; 
            
        } else {
            // If no discount, final total remains the original gross cart total
            $finalTotalMoney = $grossCartTotalMoney;
        }
        
        // 4. Return results (as strings/decimals for display)
        $formatter = $this->getFormatter();

        return [
            // Net Total before Tax/Discount
            'subTotal'          => $formatter->format($netSubtotalMoney),
            
            // VAT CHARGED (0 if exempted, full VAT otherwise)
            'vatTax'            => $formatter->format($vatTaxOutput), 
            
            // VAT EXEMPTION (Amount saved due to exemption)
            'vatExemptAmount'   => $formatter->format($vatExemptAmountMoney),
            
            // 20% DISCOUNT AMOUNT
            'discountAmount'    => $formatter->format($discountAmountMoney),
            
            // FINAL PAYABLE TOTAL
            'finalTotal'        => $formatter->format($finalTotalMoney),
        ];
    }
}