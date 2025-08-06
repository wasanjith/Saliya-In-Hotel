<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index()
    {
        $customers = Customer::withCount('orders')
            ->orderBy('orders_qty', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'orders_qty' => 0,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully!');
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $customer->load(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
        ]);

        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer deleted successfully!'
                ]);
            }
            
            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully!');
                
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting customer: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('customers.index')
                ->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }

    /**
     * Search customers by name or phone
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $customers = Customer::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->withCount('orders')
            ->orderBy('orders_qty', 'desc')
            ->limit(10)
            ->get();

        return response()->json($customers);
    }

    /**
     * Get customer statistics
     */
    public function statistics()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'customers_with_orders' => Customer::where('orders_qty', '>', 0)->count(),
            'top_customers' => Customer::orderBy('orders_qty', 'desc')->limit(5)->get(),
            'recent_customers' => Customer::orderBy('created_at', 'desc')->limit(10)->get(),
        ];

        return view('customers.statistics', compact('stats'));
    }

    /**
     * API Search customers by name or phone
     */
    public function apiSearch(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json(['customers' => []]);
        }
        
        $customers = Customer::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->withCount('orders')
            ->orderBy('orders_qty', 'desc')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();

        return response()->json(['customers' => $customers]);
    }

    /**
     * API Store a new customer
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'orders_qty' => 0,
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer->loadCount('orders'),
            'message' => 'Customer created successfully!'
        ]);
    }
}
