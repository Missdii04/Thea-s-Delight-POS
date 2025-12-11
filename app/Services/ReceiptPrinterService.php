<?php
namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class ReceiptPrinterService
{
    // ⭐️ REQUIRED: CHANGE THIS IP TO YOUR PRINTER'S NETWORK IP ⭐️
    private const PRINTER_IP = "192.168.1.87"; 
    private const PRINTER_PORT = 9100;

    public function printEscposReceipt(array $receiptDetails): bool
    {
        try {
            $connector = new NetworkPrintConnector(self::PRINTER_IP, self::PRINTER_PORT);
            $printer = new Printer($connector);

            // --- ESC/POS COMMANDS ---
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("SWEET TREATS POS\n");
            $printer->text("--------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Receipt #: {$receiptDetails['receiptNo']}\n");
            $printer->text("Date: " . date('Y-m-d H:i:s') . "\n");
            $printer->text("--------------------------------\n");
            
            // Items List
            $printer->text(str_pad("ITEM", 20) . str_pad("QTY", 5) . "TOTAL\n");
            foreach ($receiptDetails['items'] as $item) {
                // Ensure name is short enough
                $name = substr($item['name'], 0, 19);
                $qty = $item['quantity'];
                
                // Calculate item gross total from cart data
                $total = number_format($item['price'] * $item['quantity'], 2);
                
                $printer->text(str_pad($name, 20) . str_pad($qty, 5, ' ', STR_PAD_LEFT) . str_pad($total, 7, ' ', STR_PAD_LEFT) . "\n");
            }
            
            // Totals
            $printer->text("--------------------------------\n");
            $printer->text("SUBTOTAL: " . str_pad(number_format($receiptDetails['totals']['subTotal'], 2), 15, ' ', STR_PAD_LEFT) . "\n");
            $printer->text("DISCOUNT: " . str_pad(number_format($receiptDetails['totals']['discountAmount'], 2), 15, ' ', STR_PAD_LEFT) . "\n");
            $printer->text("VAT EXEMPT: " . str_pad(number_format($receiptDetails['totals']['vatExemptAmount'], 2), 15, ' ', STR_PAD_LEFT) . "\n");
            $printer->text("FINAL TOTAL: " . str_pad(number_format($receiptDetails['totals']['finalTotal'], 2), 15, ' ', STR_PAD_LEFT) . "\n");
            
            $printer->cut();
            $printer->close();

            return true;

        } catch (\Exception $e) {
            logger()->error('POS Printer Error: ' . $e->getMessage());
            return false;
        }
    }
}