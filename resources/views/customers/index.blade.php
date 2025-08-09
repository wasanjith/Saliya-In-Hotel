<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customers - Saliya In Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-gradient {
            background: linear-gradient(135deg, #008200 0%, #006600 50%, #004400 100%);
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
                    <a href="/orders" class="flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        <span>Orders</span>
                    </a>
                    <a href="/customers" class="flex items-center px-4 py-3 text-green-200 bg-green-800 rounded-lg">
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
                            <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
                            <p class="text-sm text-gray-600">Manage your restaurant customers</p>
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
                            <input type="text" id="searchInput" placeholder="Search customers by name or phone..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <div id="searchResults" class="absolute w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden z-50"></div>
                        </div>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">All Customers</h2>
                        <div class="text-sm text-gray-500">
                            <span id="customerCount">{{ $customers->total() }}</span> customers found
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="customersTableBody">
                                @forelse($customers as $customer)
                                <tr class="hover:bg-gray-50 customer-row" data-customer-id="{{ $customer->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold">{{ substr($customer->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $customer->phone ?: 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $customer->orders_qty }} orders
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $customer->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        <div class="py-8">
                                            <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                            <p>No customers found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($customers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $customers->links() }}
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
                // Reset to show all customers
                resetSearch();
            }
        });

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#searchInput') && !e.target.closest('#searchResults')) {
                document.getElementById('searchResults').classList.add('hidden');
            }
        });

        function performSearch(query) {
            if (query.length < 2) {
                resetSearch();
                return;
            }
            
            fetch(`/api/customers?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    displaySearchResults(data.customers || []);
                })
                .catch(error => {
                    console.error('Search error:', error);
                    // Show error message to user
                    const searchResults = document.getElementById('searchResults');
                    searchResults.innerHTML = '<div class="p-4 text-red-500 text-center">Error searching customers. Please try again.</div>';
                    searchResults.classList.remove('hidden');
                });
        }

        function displaySearchResults(customers) {
            const searchResults = document.getElementById('searchResults');
            const tableBody = document.getElementById('customersTableBody');
            
            if (customers.length === 0) {
                searchResults.innerHTML = '<div class="p-4 text-gray-500 text-center">No customers found</div>';
                searchResults.classList.remove('hidden');
                return;
            }

            // Update the main table with search results
            tableBody.innerHTML = '';
            
            customers.forEach(customer => {
                const row = createCustomerRow(customer);
                tableBody.appendChild(row);
            });

            // Update customer count
            document.getElementById('customerCount').textContent = customers.length;
            
            // Hide search results dropdown since we're updating the main table
            searchResults.classList.add('hidden');
        }

        function createCustomerRow(customer) {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 customer-row';
            row.setAttribute('data-customer-id', customer.id);
            
            const joinedDate = new Date(customer.created_at).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">${customer.name.charAt(0)}</span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${customer.name}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${customer.phone || 'N/A'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ${customer.orders_qty} orders
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${joinedDate}
                </td>
            `;
            
            return row;
        }

        function resetSearch() {
            // Reload the page to show all customers
            window.location.reload();
        }
    </script>
</body>
</html> 
