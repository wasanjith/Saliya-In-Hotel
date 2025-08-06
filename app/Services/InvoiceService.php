<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;

class InvoiceService
{
    private $printerWidth = 32; // 80mm thermal printer typically has 32 characters width
    
    /**
     * Generate thermal printer formatted invoice
     */
    public function generateThermalInvoice(Order $order): string
    {
        $invoice = '';
        
        // Header with company info
        $invoice .= $this->centerText("SALIYA INN HOTEL") . "\n";
        $invoice .= $this->centerText("Restaurant & Bar") . "\n";
        $invoice .= $this->centerText("No. 123, Main Street") . "\n";
        $invoice .= $this->centerText("Colombo, Sri Lanka") . "\n";
        $invoice .= $this->centerText("Tel: +94 11 234 5678") . "\n";
        $invoice .= $this->centerText("Email: info@saliyainn.com") . "\n";
        $invoice .= str_repeat("=", $this->printerWidth) . "\n";
        
        // Invoice details
        $invoice .= "INVOICE\n";
        $invoice .= "Order #: " . str_pad($order->id, 20) . "\n";
        $invoice .= "Date: " . str_pad($order->created_at->format('d/m/Y'), 20) . "\n";
        $invoice .= "Time: " . str_pad($order->created_at->format('H:i:s'), 20) . "\n";
        $invoice .= "Type: " . str_pad(ucfirst(str_replace('_', ' ', $order->order_type)), 20) . "\n";
        
        // Customer info if available
        if ($order->customer_name) {
            $invoice .= str_repeat("-", $this->printerWidth) . "\n";
            $invoice .= "CUSTOMER DETAILS\n";
            $invoice .= "Name: " . str_pad(substr($order->customer_name, 0, 25), 25) . "\n";
            if ($order->customer_phone) {
                $invoice .= "Phone: " . str_pad($order->customer_phone, 25) . "\n";
            }
        }
        
        $invoice .= str_repeat("-", $this->printerWidth) . "\n";
        $invoice .= "ITEMS ORDERED\n";
        $invoice .= str_repeat("-", $this->printerWidth) . "\n";
        
        // Order items
        $items = $this->getOrderItems($order);
        foreach ($items as $item) {
            $itemName = substr($item['item_name'], 0, 20);
            $quantity = $item['quantity'];
            $unitPrice = number_format($item['unit_price'], 2);
            $totalPrice = number_format($item['total_price'], 2);
            
            $invoice .= $itemName . "\n";
            $invoice .= "  " . $quantity . " x Rs. " . str_pad($unitPrice, 8) . " = Rs. " . str_pad($totalPrice, 10) . "\n";
        }
        
        $invoice .= str_repeat("-", $this->printerWidth) . "\n";
        
        // Totals
        $invoice .= "Subtotal:" . str_pad("Rs. " . number_format($order->subtotal, 2), 20) . "\n";
        
        if ($order->discount_amount > 0) {
            $invoice .= "Discount:" . str_pad("Rs. " . number_format($order->discount_amount, 2), 20) . "\n";
        }
        
        $invoice .= "TOTAL:" . str_pad("Rs. " . number_format($order->total_amount, 2), 20) . "\n";
        
        // Payment details
        $invoice .= str_repeat("-", $this->printerWidth) . "\n";
        $invoice .= "PAYMENT DETAILS\n";
        $invoice .= "Method: " . str_pad(ucfirst($order->payment_method), 20) . "\n";
        $invoice .= "Paid: " . str_pad("Rs. " . number_format($order->customer_paid, 2), 20) . "\n";
        
        if ($order->balance_returned > 0) {
            $invoice .= "Change: " . str_pad("Rs. " . number_format($order->balance_returned, 2), 20) . "\n";
        }
        
        $invoice .= str_repeat("=", $this->printerWidth) . "\n";
        
        // Footer
        $invoice .= $this->centerText("Thank you for dining with us!") . "\n";
        $invoice .= $this->centerText("Please visit again") . "\n";
        $invoice .= $this->centerText("www.saliyainn.com") . "\n";
        $invoice .= str_repeat("=", $this->printerWidth) . "\n";
        $invoice .= $this->centerText("Powered by Saliya Inn POS") . "\n";
        $invoice .= "\n\n\n"; // Extra spacing for paper cut
        
        return $invoice;
    }
    
    /**
     * Generate HTML invoice for web printing
     */
    public function generateHtmlInvoice(Order $order): string
    {
        $items = $this->getOrderItems($order);
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Invoice #' . $order->id . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .invoice { max-width: 400px; margin: 0 auto; border: 1px solid #ccc; padding: 20px; }
                .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                .company-name { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
                .company-details { font-size: 12px; color: #666; }
                .invoice-details { margin-bottom: 20px; }
                .customer-details { margin-bottom: 20px; padding: 10px; background: #f9f9f9; }
                .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .items-table th, .items-table td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                .items-table th { background: #f5f5f5; font-weight: bold; }
                .totals { border-top: 2px solid #000; padding-top: 10px; }
                .total-row { display: flex; justify-content: space-between; margin: 5px 0; }
                .total-row.final { font-weight: bold; font-size: 18px; }
                .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
                @media print {
                    body { margin: 0; }
                    .invoice { border: none; }
                }
            </style>
        </head>
        <body>
            <div class="invoice">
                <div class="header">
                    <div class="company-name">SALIYA INN HOTEL</div>
                    <div class="company-details">
                        Restaurant & Bar<br>
                        No. 123, Main Street<br>
                        Colombo, Sri Lanka<br>
                        Tel: +94 11 234 5678<br>
                        Email: info@saliyainn.com
                    </div>
                </div>
                
                <div class="invoice-details">
                    <strong>INVOICE</strong><br>
                    Order #: ' . $order->id . '<br>
                    Date: ' . $order->created_at->format('d/m/Y') . '<br>
                    Time: ' . $order->created_at->format('H:i:s') . '<br>
                    Type: ' . ucfirst(str_replace('_', ' ', $order->order_type)) . '
                </div>';
        
        if ($order->customer_name) {
            $html .= '
                <div class="customer-details">
                    <strong>CUSTOMER DETAILS</strong><br>
                    Name: ' . htmlspecialchars($order->customer_name) . '<br>';
            if ($order->customer_phone) {
                $html .= 'Phone: ' . htmlspecialchars($order->customer_phone) . '<br>';
            }
            $html .= '</div>';
        }
        
        $html .= '
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($items as $item) {
            $html .= '
                        <tr>
                            <td>' . htmlspecialchars($item['item_name']) . '</td>
                            <td>' . $item['quantity'] . '</td>
                            <td>Rs. ' . number_format($item['unit_price'], 2) . '</td>
                            <td>Rs. ' . number_format($item['total_price'], 2) . '</td>
                        </tr>';
        }
        
        $html .= '
                    </tbody>
                </table>
                
                <div class="totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>Rs. ' . number_format($order->subtotal, 2) . '</span>
                    </div>';
        
        if ($order->discount_amount > 0) {
            $html .= '
                    <div class="total-row">
                        <span>Discount:</span>
                        <span>Rs. ' . number_format($order->discount_amount, 2) . '</span>
                    </div>';
        }
        
        $html .= '
                    <div class="total-row final">
                        <span>TOTAL:</span>
                        <span>Rs. ' . number_format($order->total_amount, 2) . '</span>
                    </div>
                    <div class="total-row">
                        <span>Payment Method:</span>
                        <span>' . ucfirst($order->payment_method) . '</span>
                    </div>
                    <div class="total-row">
                        <span>Amount Paid:</span>
                        <span>Rs. ' . number_format($order->customer_paid, 2) . '</span>
                    </div>';
        
        if ($order->balance_returned > 0) {
            $html .= '
                    <div class="total-row">
                        <span>Change:</span>
                        <span>Rs. ' . number_format($order->balance_returned, 2) . '</span>
                    </div>';
        }
        
        $html .= '
                </div>
                
                <div class="footer">
                    Thank you for dining with us!<br>
                    Please visit again<br>
                    www.saliyainn.com<br><br>
                    Powered by Saliya Inn POS
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Get order items in a structured format
     */
    private function getOrderItems(Order $order): array
    {
        $items = [];
        
        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->items && is_array($orderItem->items)) {
                foreach ($orderItem->items as $item) {
                    if (is_array($item) && isset($item['food_item_id'])) {
                        $items[] = [
                            'food_item_id' => $item['food_item_id'],
                            'item_name' => $item['item_name'] ?? 'Unknown Item',
                            'quantity' => $item['quantity'] ?? 1,
                            'unit_price' => $item['unit_price'] ?? 0,
                            'total_price' => $item['total_price'] ?? 0,
                            'portion' => $item['portion'] ?? 'full',
                            'notes' => $item['notes'] ?? null,
                        ];
                    }
                }
            }
        }
        
        return $items;
    }
    
    /**
     * Center text for thermal printer
     */
    private function centerText(string $text): string
    {
        $textLength = strlen($text);
        $padding = max(0, ($this->printerWidth - $textLength) / 2);
        return str_repeat(" ", floor($padding)) . $text;
    }
} 