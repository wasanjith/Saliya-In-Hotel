<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;

class PrinterController extends Controller
{
    protected $invoiceService;
    
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    
    /**
     * Print invoice for thermal printer
     */
    public function printThermalInvoice(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id'
            ]);
            
            $order = Order::with('orderItems')->findOrFail($request->order_id);
            
            // Generate thermal printer formatted invoice
            $thermalInvoice = $this->invoiceService->generateThermalInvoice($order);
            
            // Log the print request
            Log::info('Thermal invoice print request:', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'invoice_data' => $thermalInvoice,
                'message' => 'Invoice ready for thermal printer'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Thermal invoice print error:', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating invoice: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Print invoice for web browser
     */
    public function printWebInvoice(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id'
            ]);
            
            $order = Order::with('orderItems')->findOrFail($request->order_id);
            
            // Generate HTML invoice
            $htmlInvoice = $this->invoiceService->generateHtmlInvoice($order);
            
            // Log the print request
            Log::info('Web invoice print request:', [
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'timestamp' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'html_invoice' => $htmlInvoice,
                'message' => 'Invoice ready for web printing'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Web invoice print error:', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating invoice: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show invoice in new window for printing
     */
    public function showInvoice(Request $request, $orderId)
    {
        try {
            $order = Order::with('orderItems')->findOrFail($orderId);
            $htmlInvoice = $this->invoiceService->generateHtmlInvoice($order);
            
            return response($htmlInvoice)->header('Content-Type', 'text/html');
            
        } catch (\Exception $e) {
            Log::error('Show invoice error:', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            
            return response('Invoice not found', 404);
        }
    }
    
    /**
     * Download invoice as text file for thermal printer
     */
    public function downloadThermalInvoice(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id'
            ]);
            
            $order = Order::with('orderItems')->findOrFail($request->order_id);
            $thermalInvoice = $this->invoiceService->generateThermalInvoice($order);
            
            $filename = 'invoice_' . $order->id . '_' . now()->format('Y-m-d_H-i-s') . '.txt';
            
            return response($thermalInvoice)
                ->header('Content-Type', 'text/plain')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            Log::error('Download thermal invoice error:', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error downloading invoice: ' . $e->getMessage()
            ], 500);
        }
    }
} 