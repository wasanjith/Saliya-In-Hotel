<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Orders - Saliya In Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-gradient {
            background: linear-gradient(135deg, #008200 0%, #006600 50%, #004400 100%);
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        .status-confirmed {
            background: #dbeafe;
            color: #2563eb;
        }
        .status-completed {
            background: #dcfce7;
            color: #16a34a;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Left Sidebar -->
        <div class="w-64 sidebar-gradient text-white flex flex-col fixed top-0 left-0 h-screen z-50">
            <!-- Logo -->
            <div class="p-4 border-b border-green-700">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logoo.saliya.png') }}" alt="Saliya Inn Logo" class="w-10 h-10 rounded-lg object-cover">
                    <span class="text-xl font-bold">Saliya Inn</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    <a href="/pos" class="flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-home mr-3"></i>
                        <span>Home</span>
                    </a>
                    <a href="/admin" class="flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/orders" class="flex items-center px-4 py-3 text-green-200 bg-green-800 rounded-lg">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        <span>Orders</span>
                    </a>
                    <a href="/customers" class="flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-users mr-3"></i>
                        <span>Customers</span>
                    </a>
                    <a href="/tables" class="flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-chair mr-3"></i>
                        <span>Tables</span>
                    </a>
                    <a href="/kitchen-slots" class="flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-utensils mr-3"></i>
                        <span>Kitchen Slots</span>
                    </a>
                </div>
            </nav>
            
            <!-- Logout -->
            <div class="mt-auto p-4">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-power-off mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64 h-screen">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b flex-shrink-0">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
                            <p class="text-sm text-gray-600">View and manage all restaurant orders</p>
                        </div>
                    </div>
                    <!-- User Profile -->
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-user-circle text-gray-600 text-2xl"></i>
                            <span class="text-gray-700 font-medium">Hello, {{ Auth::user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-800 ml-2">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="max-w-md">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Search orders by number, customer name or phone..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">All Orders</h2>
                        <div class="text-sm text-gray-500">
                            <span id="orderCount">{{ $orders->total() }}</span> orders found
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="ordersTableBody">
                                @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 order-row" data-order-id="{{ $order->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-receipt text-blue-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                                <div class="text-sm text-gray-500">#{{ $order->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $order->customer_name ?: ($order->customer->name ?? 'N/A') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $order->customer_phone ?: ($order->customer->phone ?? 'N/A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $order->order_type === 'dine_in' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                                        </span>
                                        @if($order->table_number)
                                        <div class="text-xs text-gray-500 mt-1">Table {{ $order->table_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge status-{{ $order->status }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @php
                                                $totalItems = 0;
                                                $itemNames = [];
                                                foreach($order->orderItems as $orderItem) {
                                                    if (is_array($orderItem->items)) {
                                                        foreach($orderItem->items as $item) {
                                                            $totalItems += $item['quantity'] ?? 0;
                                                            if (count($itemNames) < 2) {
                                                                $itemNames[] = $item['item_name'] ?? 'Unknown Item';
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            {{ $totalItems }} items
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ implode(', ', $itemNames) }}
                                            @if($totalItems > 2)
                                                +{{ $totalItems - 2 }} more
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">Rs. {{ number_format($order->total_amount, 2) }}</div>
                                        @if($order->payment_method)
                                        <div class="text-xs text-gray-500">{{ ucfirst($order->payment_method) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $order->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs">{{ $order->created_at->format('h:i A') }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        <div class="py-8">
                                            <i class="fas fa-shopping-bag text-4xl mb-4 text-gray-300"></i>
                                            <p>No orders found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $orders->links() }}
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        let searchTimeout;
        let currentSearchQuery = '';

        // Enhanced search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.trim();
            currentSearchQuery = query;
            
            clearTimeout(searchTimeout);
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            } else if (query.length === 0) {
                // Reset to show all orders
                resetSearch();
            }
        });

        function performSearch(query) {
            if (query.length < 2) {
                resetSearch();
                return;
            }
            
            fetch(`/orders/search?query=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }

        function displaySearchResults(orders) {
            const tableBody = document.getElementById('ordersTableBody');
            
            if (orders.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            <div class="py-8">
                                <i class="fas fa-search text-4xl mb-4 text-gray-300"></i>
                                <p>No orders found</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            // Update the main table with search results
            tableBody.innerHTML = '';
            
            orders.forEach(order => {
                const row = createOrderRow(order);
                tableBody.appendChild(row);
            });

            // Update order count
            document.getElementById('orderCount').textContent = orders.length;
        }

        function createOrderRow(order) {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 order-row';
            row.setAttribute('data-order-id', order.id);
            
            const orderDate = new Date(order.created_at).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
            
            const orderTime = new Date(order.created_at).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            const orderType = order.order_type.replace('_', ' ');
            const orderTypeClass = order.order_type === 'dine_in' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800';
            
            const statusClass = `status-${order.status}`;
            
            // Calculate total items and item names
            let totalItems = 0;
            let itemNames = [];
            
            if (order.order_items && order.order_items.length > 0) {
                order.order_items.forEach(orderItem => {
                    if (orderItem.items && Array.isArray(orderItem.items)) {
                        orderItem.items.forEach(item => {
                            totalItems += item.quantity || 0;
                            if (itemNames.length < 2) {
                                itemNames.push(item.item_name || 'Unknown Item');
                            }
                        });
                    }
                });
            }
            
            const itemsText = itemNames.join(', ');
            const moreItems = totalItems > 2 ? ` +${totalItems - 2} more` : '';

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-receipt text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${order.order_number}</div>
                            <div class="text-sm text-gray-500">#${order.id}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${order.customer_name || (order.customer?.name || 'N/A')}</div>
                    <div class="text-sm text-gray-500">${order.customer_phone || (order.customer?.phone || 'N/A')}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${orderTypeClass}">
                        ${orderType.charAt(0).toUpperCase() + orderType.slice(1)}
                    </span>
                    ${order.table_number ? `<div class="text-xs text-gray-500 mt-1">Table ${order.table_number}</div>` : ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="status-badge ${statusClass}">
                        ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${totalItems} items</div>
                    <div class="text-xs text-gray-500">${itemsText}${moreItems}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">Rs. ${parseFloat(order.total_amount || 0).toFixed(2)}</div>
                    ${order.payment_method ? `<div class="text-xs text-gray-500">${order.payment_method.charAt(0).toUpperCase() + order.payment_method.slice(1)}</div>` : ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div>${orderDate}</div>
                    <div class="text-xs">${orderTime}</div>
                </td>
            `;
            
            return row;
        }

        function resetSearch() {
            // Reload the page to show all orders
            window.location.reload();
        }
    </script>
</body>
</html> 