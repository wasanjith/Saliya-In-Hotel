<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saliya In Hotel - POS System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .category-item.active {
            background-color: #3b82f6;
            color: white;
        }
        .food-item-card {
            transition: all 0.3s ease;
        }
        .food-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .order-item {
            transition: all 0.2s ease;
        }
        .order-item:hover {
            background-color: #f3f4f6;
        }
        .sidebar-gradient {
            background: linear-gradient(135deg, #008200 0%, #006600 50%, #004400 100%);
        }
        
        /* Customer suggestions dropdown styles */
        .customer-suggestions {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            max-height: 240px;
            overflow-y: auto;
        }
        
        .customer-suggestion-item {
            transition: all 0.2s ease;
        }
        
        .customer-suggestion-item:hover {
            background-color: #f3f4f6;
            transform: translateX(2px);
        }
        
        .customer-suggestion-item:active {
            background-color: #e5e7eb;
        }
        
        /* Responsive layout fixes */
        @media (max-width: 1400px) {
            .food-items-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
        
        /* Ensure content doesn't overflow */
        .main-content-wrapper {
            min-width: 0;
            overflow-x: hidden;
        }
        
        /* Prevent horizontal scrollbar */
        body {
            overflow-x: hidden;
        }
        
        /* Rice selection modal improvements */
        .rice-option-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .rice-option-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background-color: #f8fafc;
            border-color: #3b82f6;
        }
        
        .rice-option-card:active {
            transform: translateY(0);
            background-color: #e2e8f0;
        }
        
        .rice-quantity-controls {
            transition: all 0.2s ease;
        }
        
        .rice-quantity-controls button:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-100" x-data="posSystem()">
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
                    <a href="#" class="flex items-center px-4 py-3 text-green-200 bg-green-800 rounded-lg">
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
        <div class="flex-1 flex flex-col ml-64 h-screen min-w-0 main-content-wrapper">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b flex-shrink-0">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Date and Time -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-calendar-alt text-blue-600"></i>
                            <span class="text-gray-700 font-medium" id="current-date"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock text-green-600"></i>
                            <span class="text-gray-700 font-medium" id="current-time"></span>
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
            <div class="flex-1 flex overflow-hidden min-w-0">
                <!-- Left Content - Menu -->
                <div class="flex-1 p-4 overflow-y-auto min-w-0">
                    <!-- Categories -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Choose Category</h3>
                        <div class="flex space-x-4 overflow-x-auto pb-2">
                            <button @click="selectCategory('all')" 
                                    :class="selectedCategory === 'all' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'"
                                    class="category-item flex flex-col items-center p-4 rounded-lg shadow-sm hover:shadow-md transition-all min-w-[100px]">
                                <i class="fas fa-th-large text-2xl mb-2"></i>
                                <span class="text-sm font-medium">All</span>
                            </button>
                            
                            @foreach($categories as $category)
                            <button @click="selectCategory({{ $category->id }})" 
                                    :class="selectedCategory === {{ $category->id }} ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'"
                                    class="category-item flex flex-col items-center p-4 rounded-lg shadow-sm hover:shadow-md transition-all min-w-[100px]">
                                @if($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-8 h-8 mb-2 rounded object-cover">
                                @else
                                    <img src="{{ asset('images/placeholder-category.svg') }}" alt="{{ $category->name }}" class="w-8 h-8 mb-2 rounded object-cover">
                                @endif
                                <span class="text-sm font-medium">{{ $category->name }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Food Items -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <h3 class="text-lg font-semibold">
                                    <span x-text="getCategoryName()"></span>
                                    <span class="text-gray-500">(<span x-text="filteredItems.length"></span> Items)</span>
                                </h3>
                                <button @click="showFavorites = !showFavorites" 
                                        :class="showFavorites ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="flex items-center px-3 py-1 rounded-full text-sm">
                                    <i class="fas fa-star mr-1"></i>
                                    <span>Favorite (<span x-text="favoriteItems.length"></span> Items)</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Food Items Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4">
                            <template x-for="item in filteredItems" :key="item.id">
                                <div class="food-item-card bg-white rounded-lg shadow-sm hover:shadow-md overflow-hidden cursor-pointer"
                                     @click="addToOrder(item)">
                                    <div class="h-32 bg-gray-200 flex items-center justify-center overflow-hidden">
                                        <template x-if="item.image">
                                            <img :src="'/storage/' + item.image" :alt="item.name" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                        </template>
                                        <template x-if="!item.image">
                                            <img src="/images/placeholder-food.svg" :alt="item.name" class="w-full h-full object-cover">
                                        </template>
                                    </div>
                                    <div class="p-3">
                                        <h4 class="font-medium text-gray-800 mb-1" x-text="item.name"></h4>
                                        
                                        <!-- Starting Price Display -->
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center space-x-2">
                                                <!-- Show "Price: Rs." for items without multiple pricing options -->
                                                <template x-if="!hasMultiplePricingOptions(item)">
                                                    <div class="text-sm text-gray-600">
                                                        <span>Price: </span>
                                                        <span class="text-lg font-bold text-blue-600" x-text="'Rs. ' + Math.round(getStartingPrice(item))"></span>
                                                    </div>
                                                </template>
                                                <!-- Show "From Rs." for items with multiple pricing options -->
                                                <template x-if="hasMultiplePricingOptions(item)">
                                                    <div class="text-sm text-gray-600">
                                                        <span>From </span>
                                                        <span class="text-lg font-bold text-blue-600" x-text="'Rs. ' + Math.round(getStartingPrice(item))"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <button @click.stop="toggleFavorite(item.id)" 
                                                    :class="isFavorite(item.id) ? 'text-yellow-500' : 'text-gray-400'"
                                                    class="hover:text-yellow-500">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Right Sidebar - Order -->
                <div class="w-96 bg-white border-l border-gray-200 flex flex-col h-full flex-shrink-0">
                    <!-- 1. Top Order Item Header -->
                    <div class="p-3 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Order Items</h3>
                            </div>
                            <i class="fas fa-shopping-cart text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- 2. Invoice Number -->
                    <div class="p-3 border-b border-gray-200 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Invoice Number:</span>
                            <span class="text-sm font-bold text-blue-600">#<span x-text="orderNumber"></span></span>
                        </div>
                    </div>
                    
                    <!-- 3. Dine In and Take Away Buttons -->
                    <div class="p-3 border-b border-gray-200 flex-shrink-0">
                        <div class="flex space-x-2">
                            <button @click="handleDineInClick()" 
                                    :class="orderType === 'dine_in' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-utensils mr-2"></i>
                                Dine In
                            </button>
                            <button @click="handleTakeawayClick()" 
                                    :class="orderType === 'takeaway' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-shopping-bag mr-2"></i>
                                Takeaway
                            </button>
                        </div>
                    </div>
                    
                    <!-- 4. Order Items -->
                    <div class="flex-1 overflow-y-auto p-3 min-h-0">
                        <template x-if="orderItems.length === 0">
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-300"></i>
                                <p class="text-gray-400">No items in order</p>
                                <p class="text-sm text-gray-300 mt-2">Add items from the menu</p>
                            </div>
                        </template>
                        
                        <!-- Order Items List -->
                        <template x-for="(item, index) in orderItems" :key="index">
                            <div class="order-item bg-white rounded-lg p-3 mb-3 border border-gray-200 shadow-sm">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-utensils text-gray-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-800" x-text="item.name"></h4>
                                        <p class="text-sm text-gray-600" x-text="'Rs. ' + Math.round(item.price)"></p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button @click="decreaseQuantity(index)" 
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-8 text-center font-medium" x-text="item.quantity"></span>
                                        <button @click="increaseQuantity(index)" 
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                {{-- <div class="flex justify-between items-center mt-2">
                                    <div class="flex space-x-2">
                                        <button @click="applyDiscount(index)" class="text-xs text-blue-600 hover:underline">Discount</button>
                                        <button @click="removeItem(index)" class="text-xs text-red-600 hover:underline">Remove</button>
                                    </div>
                                    <span class="font-medium text-gray-800" x-text="'Rs. ' + Math.round(item.price * item.quantity)"></span>
                                </div> --}}
                            </div>
                        </template>
                        
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Takeaway Payment Modal -->
    <div x-show="showTakeawayModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
        
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 my-8 max-h-screen overflow-y-auto">
            
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Takeaway Order Payment</h3>
                    <button @click="showTakeawayModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Order Summary</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Order Number:</span>
                            <span class="font-medium" x-text="orderNumber"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Order Type:</span>
                            <span class="font-medium capitalize">Takeaway</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Items Count:</span>
                            <span class="font-medium" x-text="orderItems.length + ' items'"></span>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-white border rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Items Ordered</h4>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        <template x-for="item in orderItems" :key="item.id">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <div class="flex-1">
                                    <span class="font-medium" x-text="item.name"></span>
                                    <span class="text-gray-500 text-sm ml-2">x<span x-text="item.quantity"></span></span>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">Rs. <span x-text="Math.round(item.price * item.quantity)"></span></div>
                                    <div class="text-sm text-gray-500">@ Rs. <span x-text="Math.round(item.price)"></span></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Customer Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                            <div class="relative">
                                <input type="text" 
                                       x-model="takeawayCustomerInfo.name" 
                                       @input="searchCustomers($event.target.value)"
                                       @focus="showCustomerSuggestions = true"
                                       @blur="setTimeout(() => showCustomerSuggestions = false, 200)"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enter customer name or search existing customers">
                                
                                <!-- Customer Suggestions Dropdown -->
                                <div x-show="showCustomerSuggestions && customerSuggestions.length > 0" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto customer-suggestions">
                                    <template x-for="customer in customerSuggestions" :key="customer.id">
                                        <div @click="selectCustomer(customer)" 
                                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0 customer-suggestion-item">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="font-medium text-gray-900" x-text="customer.name"></div>
                                                    <div class="text-sm text-gray-500" x-text="customer.phone"></div>
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    <span x-text="customer.orders_qty + ' orders'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- Add New Customer Option -->
                                    <div @click="addNewCustomer()" 
                                         class="px-4 py-2 hover:bg-blue-50 cursor-pointer border-t border-gray-200 bg-gray-50">
                                        <div class="flex items-center text-blue-600">
                                            <i class="fas fa-plus mr-2"></i>
                                            <span class="font-medium">Add New Customer</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" x-model="takeawayCustomerInfo.phone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter phone number">
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Payment Details</h4>
                    
                    <!-- Payment Method -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <div class="flex space-x-2">
                            <button @click="takeawayPaymentInfo.method = 'cash'" 
                                    :class="takeawayPaymentInfo.method === 'cash' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium">
                                Cash
                            </button>
                            <button @click="takeawayPaymentInfo.method = 'card'" 
                                    :class="takeawayPaymentInfo.method === 'card' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium">
                                Card
                            </button>
                        </div>
                    </div>

                    <!-- Amount Breakdown -->
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Amount:</span>
                            <span class="text-lg font-bold text-blue-600">Rs. <span x-text="Math.round(takeawayPaymentInfo.totalAmount)"></span></span>
                        </div>
                    </div>

                    <!-- Payment Input -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount Paid by Customer</label>
                        <input type="number" x-model="takeawayPaymentInfo.paidAmount" @input="calculateTakeawayBalance()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg font-medium"
                               placeholder="Enter amount paid" step="0.01" min="0">
                    </div>

                    <!-- Balance Display -->
                    <div class="mt-4 p-3 rounded-lg" :class="takeawayPaymentInfo.balance >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                        <div class="flex justify-between items-center">
                            <span class="font-medium" :class="takeawayPaymentInfo.balance >= 0 ? 'text-green-700' : 'text-red-700'">
                                <span x-show="takeawayPaymentInfo.balance >= 0">Balance to Return:</span>
                                <span x-show="takeawayPaymentInfo.balance < 0">Amount Due:</span>
                            </span>
                            <span class="text-lg font-bold" :class="takeawayPaymentInfo.balance >= 0 ? 'text-green-600' : 'text-red-600'">
                                Rs. <span x-text="Math.round(Math.abs(takeawayPaymentInfo.balance))"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button @click="showTakeawayModal = false" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 px-4 rounded-lg font-medium">
                        Cancel
                    </button>
                    <button @click="processTakeawayOrder()" 
                            :disabled="!takeawayCustomerInfo.name || takeawayPaymentInfo.paidAmount <= 0"
                            :class="(!takeawayCustomerInfo.name || takeawayPaymentInfo.paidAmount <= 0) ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'"
                            class="flex-1 text-white py-3 px-4 rounded-lg font-medium">
                        <i class="fas fa-check mr-2"></i>
                        Complete Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Portion Selection Modal -->
    <div x-show="showPortionModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
        
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-lg p-6 max-w-md w-full mx-4 my-8 max-h-screen overflow-y-auto">
            
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Select Portion</h3>
                    <button @click="showPortionModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Food Item Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="selectedFoodItem">
                    <div class="flex items-center space-x-3">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                            <template x-if="selectedFoodItem.image">
                                <img :src="'/storage/' + selectedFoodItem.image" :alt="selectedFoodItem.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedFoodItem.image">
                                <img src="/images/placeholder-food.svg" :alt="selectedFoodItem.name" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900" x-text="selectedFoodItem.name"></h4>
                            <p class="text-sm text-gray-600" x-text="selectedFoodItem.description"></p>
                        </div>
                    </div>
                </div>

                <!-- Portion Options -->
                <div class="space-y-3">
                    <!-- Full Portion -->
                    <div class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                         :class="selectedPortion === 'full' ? 'border-blue-500 bg-blue-50' : ''"
                         @click="selectPortionAndAddToCart('full')">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-medium text-gray-900">Full Portion</h5>
                                <p class="text-sm text-gray-600">Complete serving size</p>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-blue-600">
                                    Rs. <span x-text="getPortionPrice('full')"></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-600 mt-2">
                            <i class="fas fa-plus-circle mr-1"></i>
                            Click to add
                        </div>
                    </div>

                    <!-- Half Portion (if available) -->
                    <div x-show="selectedFoodItem && selectedFoodItem.has_half_portion"
                         class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                         :class="selectedPortion === 'half' ? 'border-blue-500 bg-blue-50' : ''"
                         @click="selectPortionAndAddToCart('half')">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-medium text-gray-900">Half Portion</h5>
                                <p class="text-sm text-gray-600">Half serving size</p>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-blue-600">
                                    Rs. <span x-text="getPortionPrice('half')"></span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center text-sm text-gray-600 mt-2">
                            <i class="fas fa-plus-circle mr-1"></i>
                            Click to add
                        </div>
                    </div>
                </div>

                <!-- Info Text -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <i class="fas fa-mouse-pointer mr-1"></i>
                    Click on a portion option to add to cart
                </div>
            </div>
        </div>
    </div>

    <!-- Beverage Size Selection Modal -->
    <div x-show="showBeverageSizeModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
        
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-lg p-6 max-w-md w-full mx-4 my-8 max-h-screen overflow-y-auto">
            
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Select Beverage Size</h3>
                    <button @click="showBeverageSizeModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Food Item Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="selectedFoodItem">
                    <div class="flex items-center space-x-3">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                            <template x-if="selectedFoodItem.image">
                                <img :src="'/storage/' + selectedFoodItem.image" :alt="selectedFoodItem.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedFoodItem.image">
                                <img src="/images/placeholder-food.svg" :alt="selectedFoodItem.name" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900" x-text="selectedFoodItem.name"></h4>
                            <p class="text-sm text-gray-600" x-text="selectedFoodItem.description"></p>
                        </div>
                    </div>
                </div>

                <!-- Beverage Size Options -->
                <div class="space-y-3">
                    <template x-for="option in getBeverageSizeOptions()" :key="option.size">
                        <div class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                             :class="selectedBeverageSize === option.size ? 'border-blue-500 bg-blue-50' : ''"
                             @click="selectBeverageSizeAndAddToCart(option.size)">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h5 class="font-medium text-gray-900" x-text="option.size"></h5>
                                    <p class="text-sm text-gray-600">Beverage size</p>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-blue-600">
                                        Rs. <span x-text="Math.round(option.price)"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center text-sm text-gray-600 mt-2">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Click to add
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Info Text -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <i class="fas fa-mouse-pointer mr-1"></i>
                    Click on a size option to add to cart
                </div>
            </div>
        </div>
    </div>

    <!-- Rice Selection Modal -->
    <div x-show="showRiceTypeModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto">
        
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 my-8 max-h-screen overflow-y-auto">
            
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Select Rice Type & Portion</h3>
                    <button @click="showRiceTypeModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Food Item Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6" x-show="selectedFoodItem">
                    <div class="flex items-center space-x-3">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                            <template x-if="selectedFoodItem.image">
                                <img :src="'/storage/' + selectedFoodItem.image" :alt="selectedFoodItem.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedFoodItem.image">
                                <img src="/images/placeholder-food.svg" :alt="selectedFoodItem.name" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900" x-text="selectedFoodItem.name"></h4>
                            <p class="text-sm text-gray-600" x-text="selectedFoodItem.description"></p>
                        </div>
                    </div>
                </div>

                <!-- Rice Options Grid -->
                <div class="mb-4">
                    <p class="text-sm text-gray-600 text-center mb-3">
                        <i class="fas fa-mouse-pointer mr-1"></i>
                        Click on a rice type card to add to cart
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Samba Rice Options -->
                    <div class="space-y-3">
                        <h5 class="font-semibold text-gray-800 text-center pb-2 border-b border-gray-200">Samba Rice</h5>
                        
                        <!-- Samba Full Portion -->
                        <div class="border border-gray-200 rounded-lg p-4 rice-option-card cursor-pointer" 
                             @click="addRiceItemToCart('samba', 'full')">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="font-medium text-gray-900">Full Portion</div>
                                    <div class="text-sm text-gray-600">Complete serving size</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-amber-700">Rs. <span x-text="Math.round(selectedFoodItem.full_samba_price || 0)"></span></div>
                                </div>
                            </div>
                            <div class="text-center text-sm text-gray-600 mt-2">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Click to add
                            </div>
                        </div>

                        <!-- Samba Half Portion (if available) -->
                        <div x-show="selectedFoodItem && selectedFoodItem.half_samba_price" 
                             class="border border-gray-200 rounded-lg p-4 rice-option-card cursor-pointer"
                             @click="addRiceItemToCart('samba', 'half')">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="font-medium text-gray-900">Half Portion</div>
                                    <div class="text-sm text-gray-600">Half serving size</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-amber-700">Rs. <span x-text="Math.round(selectedFoodItem.half_samba_price || 0)"></span></div>
                                </div>
                            </div>
                            <div class="text-center text-sm text-gray-600 mt-2">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Click to add
                            </div>
                        </div>
                    </div>

                    <!-- Basmathi Rice Options -->
                    <div class="space-y-3">
                        <h5 class="font-semibold text-gray-800 text-center pb-2 border-b border-gray-200">Basmathi Rice</h5>
                        
                        <!-- Basmathi Full Portion -->
                        <div class="border border-gray-200 rounded-lg p-4 rice-option-card cursor-pointer"
                             @click="addRiceItemToCart('basmathi', 'full')">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="font-medium text-gray-900">Full Portion</div>
                                    <div class="text-sm text-gray-600">Complete serving size</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-amber-700">Rs. <span x-text="Math.round(selectedFoodItem.full_basmathi_price || 0)"></span></div>
                                </div>
                            </div>
                            <div class="text-center text-sm text-gray-600 mt-2">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Click to add
                            </div>
                        </div>

                        <!-- Basmathi Half Portion (if available) -->
                        <div x-show="selectedFoodItem && selectedFoodItem.half_basmathi_price" 
                             class="border border-gray-200 rounded-lg p-4 rice-option-card cursor-pointer"
                             @click="addRiceItemToCart('basmathi', 'half')">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="font-medium text-gray-900">Half Portion</div>
                                    <div class="text-sm text-gray-600">Half serving size</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-amber-700">Rs. <span x-text="Math.round(selectedFoodItem.half_basmathi_price || 0)"></span></div>
                                </div>
                            </div>
                            <div class="text-center text-sm text-gray-600 mt-2">
                                <i class="fas fa-plus-circle mr-1"></i>
                                Click to add
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Text -->
                <div class="text-center text-sm text-gray-600 mb-6">
                    <i class="fas fa-info-circle mr-1"></i>
                    Click on any rice type card to add it directly to your cart
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button @click="showRiceTypeModal = false" 
                            class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 px-4 rounded-lg font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Invoice Section (shown after successful payment) - Moved outside modal -->
    <div x-show="showPrintOptions" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Print Invoice</h3>
                <p class="text-gray-600">Order #<span x-text="completedOrderId"></span> completed successfully!</p>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <button @click="printThermalInvoice()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg text-sm font-medium">
                    <i class="fas fa-print mr-2"></i>
                    Thermal Printer
                </button>
                <button @click="printWebInvoice()" 
                        class="bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg text-sm font-medium">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Web Print
                </button>
                <button @click="downloadThermalInvoice()" 
                        class="bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg text-sm font-medium">
                    <i class="fas fa-download mr-2"></i>
                    Download TXT
                </button>
                <button @click="closePrintOptions()" 
                        class="bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-lg text-sm font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function posSystem() {
            return {
                selectedCategory: 'all',
                orderType: 'takeaway',
                paymentMethod: 'cash',
                orderItems: [],
                favoriteItems: [],
                showFavorites: false,
                orderNumber: Math.floor(Math.random() * 900000) + 100000,
                showTakeawayModal: false,
                showPortionModal: false,
                showRiceTypeModal: false,
                showBeverageSizeModal: false, // New modal for beverage size selection
                showPrintOptions: false,
                completedOrderId: null,
                selectedFoodItem: null,
                selectedPortion: 'full',
                selectedRiceType: null, // 'samba' | 'basmathi'
                selectedBeverageSize: null, // New property for selected beverage size
                portionQuantity: 1,
                beverageQuantity: 1, // New property for beverage quantity
                riceQuantities: {
                    samba: { full: 0, half: 0 },
                    basmathi: { full: 0, half: 0 }
                },
                takeawayCustomerInfo: {
                    name: '',
                    phone: ''
                },
                takeawayPaymentInfo: {
                    method: 'cash',
                    discount: 0,
                    paidAmount: 0,
                    totalAmount: 0,
                    balance: 0,
                    subtotal: 0,
                    tax: 0
                },
                
                // Customer search functionality
                customerSuggestions: [],
                showCustomerSuggestions: false,
                
                // Food items data from backend
                allItems: @json($foodItems),
                categories: @json($categories),
                
                get filteredItems() {
                    if (this.showFavorites) {
                        return this.allItems.filter(item => this.isFavorite(item.id));
                    }
                    
                    if (this.selectedCategory === 'all') {
                        return this.allItems;
                    }
                    
                    return this.allItems.filter(item => item.category_id == this.selectedCategory);
                },
                
                get subtotal() {
                    return this.orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                
                get total() {
                    return this.subtotal;
                },
                
                selectCategory(categoryId) {
                    this.selectedCategory = categoryId;
                    this.showFavorites = false;
                },
                
                getCategoryName() {
                    if (this.selectedCategory === 'all') return 'All Items';
                    const category = this.categories.find(c => c.id == this.selectedCategory);
                    return category ? category.name : 'All Items';
                },
                
                addToOrder(item) {
                    // Rice and Fried Rice categories require rice type selection
                    if (this.isRiceCategory(item)) {
                        this.showRiceSelection(item);
                        return;
                    }

                    // Beverage items with multiple sizes require size selection
                    if (this.isBeverageWithSizes(item)) {
                        this.showBeverageSizeSelection(item);
                        return;
                    }

                    // Check if item has half portion option
                    if (item.has_half_portion) {
                        this.showPortionSelection(item);
                    } else {
                        // For items without half portions, add directly with base price
                        const price = item.price || 0;
                        const existingItem = this.orderItems.find(orderItem => orderItem.id === item.id);
                        
                        if (existingItem) {
                            existingItem.quantity++;
                        } else {
                            this.orderItems.push({
                                id: item.id,
                                name: item.name,
                                price: Math.round(parseFloat(price)),
                                quantity: 1,
                                portion: 'full',
                                rice_type: null,
                                beverage_size: null
                            });
                        }
                    }
                },
                
                showPortionSelection(item) {
                    this.selectedFoodItem = item;
                    this.selectedPortion = 'full';
                    this.portionQuantity = 1;
                    this.showPortionModal = true;
                },

                // Rice type selection flow
                isRiceCategory(item) {
                    return item && item.category && (item.category.name === 'Fried Rice' || item.category.name === 'Rice');
                },
                
                // Beverage detection and size selection flow
                isBeverageWithSizes(item) {
                    return item && item.category && item.category.name === 'Drinks' && item.has_drink_sizes && item.beverage_prices;
                },
                
                showBeverageSizeSelection(item) {
                    this.selectedFoodItem = item;
                    this.selectedBeverageSize = null;
                    this.beverageQuantity = 1;
                    this.showBeverageSizeModal = true;
                },
                
                getBeverageSizePrice(size) {
                    if (!this.selectedFoodItem || !this.selectedFoodItem.beverage_prices) return 0;
                    return this.selectedFoodItem.beverage_prices[size] || 0;
                },
                
                getBeverageSizeOptions() {
                    if (!this.selectedFoodItem || !this.selectedFoodItem.beverage_prices) return [];
                    return Object.keys(this.selectedFoodItem.beverage_prices).map(size => ({
                        size: size,
                        price: this.selectedFoodItem.beverage_prices[size]
                    }));
                },
                
                showRiceSelection(item) {
                    this.selectedFoodItem = item;
                    this.resetRiceQuantities();
                    this.showRiceTypeModal = true;
                },
                resetRiceQuantities() {
                    this.riceQuantities = {
                        samba: { full: 0, half: 0 },
                        basmathi: { full: 0, half: 0 }
                    };
                },
                increaseRiceQuantity(riceType, portion) {
                    this.riceQuantities[riceType][portion]++;
                },
                decreaseRiceQuantity(riceType, portion) {
                    if (this.riceQuantities[riceType][portion] > 0) {
                        this.riceQuantities[riceType][portion]--;
                    }
                },
                getRiceQuantity(riceType, portion) {
                    return this.riceQuantities[riceType][portion] || 0;
                },
                getTotalRiceItems() {
                    let total = 0;
                    Object.values(this.riceQuantities).forEach(portions => {
                        Object.values(portions).forEach(qty => {
                            total += qty;
                        });
                    });
                    return total;
                },
                getTotalRiceAmount() {
                    if (!this.selectedFoodItem) return 0;
                    let total = 0;
                    
                    // Samba rice totals
                    total += (this.selectedFoodItem.full_samba_price || 0) * this.riceQuantities.samba.full;
                    total += (this.selectedFoodItem.half_samba_price || 0) * this.riceQuantities.samba.half;
                    
                    // Basmathi rice totals
                    total += (this.selectedFoodItem.full_basmathi_price || 0) * this.riceQuantities.basmathi.full;
                    total += (this.selectedFoodItem.half_basmathi_price || 0) * this.riceQuantities.basmathi.half;
                    
                    return total;
                },
                
                getLowestRicePrice(item) {
                    if (!item || !item.category || (item.category.name !== 'Rice' && item.category.name !== 'Fried Rice')) return 0;
                    
                    const prices = [];
                    
                    // Add all available rice prices to the array
                    if (item.full_samba_price) prices.push(parseFloat(item.full_samba_price));
                    if (item.half_samba_price) prices.push(parseFloat(item.half_samba_price));
                    if (item.full_basmathi_price) prices.push(parseFloat(item.full_basmathi_price));
                    if (item.half_basmathi_price) prices.push(parseFloat(item.half_basmathi_price));
                    
                    // Return the lowest price, or 0 if no prices available
                    return prices.length > 0 ? Math.min(...prices) : 0;
                },
                
                getStartingPrice(item) {
                    if (!item) return 0;
                    
                    const prices = [];
                    
                    // For Rice and Fried Rice categories, check all rice prices
                    if (item.category && (item.category.name === 'Rice' || item.category.name === 'Fried Rice')) {
                        if (item.full_samba_price) prices.push(parseFloat(item.full_samba_price));
                        if (item.half_samba_price) prices.push(parseFloat(item.half_samba_price));
                        if (item.full_basmathi_price) prices.push(parseFloat(item.full_basmathi_price));
                        if (item.half_basmathi_price) prices.push(parseFloat(item.half_basmathi_price));
                    }
                    // For items with half portions, check both full and half prices
                    else if (item.has_half_portion) {
                        if (item.price) prices.push(parseFloat(item.price));
                        if (item.half_price) prices.push(parseFloat(item.half_price));
                    }
                    // For beverage items with sizes, check beverage prices
                    else if (item.category && item.category.name === 'Drinks' && item.beverage_prices) {
                        Object.values(item.beverage_prices).forEach(price => {
                            if (price) prices.push(parseFloat(price));
                        });
                    }
                    // For regular items, use base price
                    else {
                        if (item.price) prices.push(parseFloat(item.price));
                    }
                    
                    // Return the lowest price, or 0 if no prices available
                    return prices.length > 0 ? Math.min(...prices) : 0;
                },
                
                hasMultiplePricingOptions(item) {
                    if (!item) return false;
                    
                    // Rice and Fried Rice categories always have multiple pricing options
                    if (item.category && (item.category.name === 'Rice' || item.category.name === 'Fried Rice')) {
                        return true;
                    }
                    
                    // Items with half portions have multiple pricing options
                    if (item.has_half_portion) {
                        return true;
                    }
                    
                    // Beverage items with multiple sizes have multiple pricing options
                    if (item.category && item.category.name === 'Drinks' && item.beverage_prices && Object.keys(item.beverage_prices).length > 1) {
                        return true;
                    }
                    
                    // Regular items without multiple pricing options
                    return false;
                },
                addAllRiceItemsToOrder() {
                    if (!this.selectedFoodItem) return;
                    
                    // Add Samba rice items
                    if (this.riceQuantities.samba.full > 0) {
                        this.addRiceItemToOrder('samba', 'full', this.riceQuantities.samba.full);
                    }
                    if (this.riceQuantities.samba.half > 0 && this.selectedFoodItem.half_samba_price) {
                        this.addRiceItemToOrder('samba', 'half', this.riceQuantities.samba.half);
                    }
                    
                    // Add Basmathi rice items
                    if (this.riceQuantities.basmathi.full > 0) {
                        this.addRiceItemToOrder('basmathi', 'full', this.riceQuantities.basmathi.full);
                    }
                    if (this.riceQuantities.basmathi.half > 0 && this.selectedFoodItem.half_basmathi_price) {
                        this.addRiceItemToOrder('basmathi', 'half', this.riceQuantities.basmathi.half);
                    }
                    
                    // Close modal and reset
                    this.showRiceTypeModal = false;
                    this.selectedFoodItem = null;
                    this.resetRiceQuantities();
                },
                addRiceItemToOrder(riceType, portion, quantity) {
                    if (!this.selectedFoodItem || quantity <= 0) return;
                    
                    let price = 0;
                    let itemName = '';
                    
                    if (riceType === 'samba') {
                        price = portion === 'half' ? this.selectedFoodItem.half_samba_price : this.selectedFoodItem.full_samba_price;
                        itemName = `${this.selectedFoodItem.name} - Samba Rice (${portion === 'half' ? 'Half' : 'Full'} Portion)`;
                    } else {
                        price = portion === 'half' ? this.selectedFoodItem.half_basmathi_price : this.selectedFoodItem.full_basmathi_price;
                        itemName = `${this.selectedFoodItem.name} - Basmathi Rice (${portion === 'half' ? 'Half' : 'Full'} Portion)`;
                    }
                    
                    // Check if item with same name already exists
                    const existingItem = this.orderItems.find(orderItem => 
                        orderItem.name === itemName
                    );
                    
                    if (existingItem) {
                        existingItem.quantity += quantity;
                    } else {
                        this.orderItems.push({
                            id: this.selectedFoodItem.id,
                            name: itemName,
                            price: Math.round(parseFloat(price || 0)),
                            quantity: quantity,
                            portion: portion,
                            rice_type: riceType,
                            beverage_size: null // Rice items don't have beverage sizes
                        });
                    }
                },
                
                selectPortion(portion) {
                    this.selectedPortion = portion;
                },
                
                getPortionPrice(portion) {
                    if (!this.selectedFoodItem) return 0;

                    // If rice category and rice type chosen, use rice price regardless of portion
                    if (this.isRiceCategory(this.selectedFoodItem) && this.selectedRiceType) {
                        return this.getRicePrice(this.selectedFoodItem);
                    }
                    
                    // No per-portion generic prices anymore; fall back to base price
                    return Math.round(parseFloat(this.selectedFoodItem.price || 0));
                },
                
                increasePortionQuantity() {
                    this.portionQuantity++;
                },
                
                decreasePortionQuantity() {
                    if (this.portionQuantity > 1) {
                        this.portionQuantity--;
                    }
                },
                
                addToOrderWithPortion() {
                    if (!this.selectedFoodItem) return;
                    
                    const price = this.getPortionPrice(this.selectedPortion);
                    const riceLabel = (this.isRiceCategory(this.selectedFoodItem) && this.selectedRiceType) ? (' - ' + (this.selectedRiceType === 'samba' ? 'Samba' : 'Basmathi') + ' Rice') : '';
                    const itemName = this.selectedFoodItem.name + riceLabel + ' (' + this.getPortionName(this.selectedPortion) + ')';
                    
                    // Check if item with same name and portion already exists
                    const existingItem = this.orderItems.find(orderItem => 
                        orderItem.id === this.selectedFoodItem.id && 
                        orderItem.portion === this.selectedPortion
                    );
                    
                    if (existingItem) {
                        existingItem.quantity += this.portionQuantity;
                    } else {
                        this.orderItems.push({
                            id: this.selectedFoodItem.id,
                            name: itemName,
                            price: price,
                            quantity: this.portionQuantity,
                            portion: this.selectedPortion,
                            rice_type: this.selectedRiceType,
                            beverage_size: null // Portion items don't have beverage sizes
                        });
                    }
                    
                    this.showPortionModal = false;
                    this.selectedFoodItem = null;
                    this.selectedPortion = 'full';
                    this.selectedRiceType = null;
                    this.portionQuantity = 1;
                },
                
                addToOrderWithBeverageSize() {
                    if (!this.selectedFoodItem || !this.selectedBeverageSize) return;
                    
                    const price = this.getBeverageSizePrice(this.selectedBeverageSize);
                    const itemName = this.selectedFoodItem.name + ' (' + this.selectedBeverageSize + ')';
                    
                    // Check if item with same name and size already exists
                    const existingItem = this.orderItems.find(orderItem => 
                        orderItem.id === this.selectedFoodItem.id && 
                        orderItem.beverage_size === this.selectedBeverageSize
                    );
                    
                    if (existingItem) {
                        existingItem.quantity += this.beverageQuantity;
                    } else {
                        this.orderItems.push({
                            id: this.selectedFoodItem.id,
                            name: itemName,
                            price: price,
                            quantity: this.beverageQuantity,
                            portion: 'full',
                            rice_type: null,
                            beverage_size: this.selectedBeverageSize
                        });
                    }
                    
                    this.showBeverageSizeModal = false;
                    this.selectedFoodItem = null;
                    this.selectedBeverageSize = null;
                    this.beverageQuantity = 1;
                },
                
                getPortionName(portion) {
                    return portion === 'half' ? 'Half Portion' : 'Full Portion';
                },
                
                increaseQuantity(index) {
                    this.orderItems[index].quantity++;
                },
                
                decreaseQuantity(index) {
                    if (this.orderItems[index].quantity > 1) {
                        this.orderItems[index].quantity--;
                    } else {
                        this.removeItem(index);
                    }
                },
                
                removeItem(index) {
                    this.orderItems.splice(index, 1);
                },
                
                toggleFavorite(itemId) {
                    const index = this.favoriteItems.indexOf(itemId);
                    if (index > -1) {
                        this.favoriteItems.splice(index, 1);
                    } else {
                        this.favoriteItems.push(itemId);
                    }
                },
                
                isFavorite(itemId) {
                    return this.favoriteItems.includes(itemId);
                },
                
                applyDiscount(index) {
                    // Implement discount logic
                    alert('Discount feature coming soon!');
                },
                
                async handleDineInClick() {
                    this.orderType = 'dine_in';
                    
                    // Check if there are items in the cart
                    if (this.orderItems.length === 0) {
                        alert('Please add items to your order first!');
                        return;
                    }
                    
                    // Save the order first
                    const orderData = {
                        order_type: 'dine_in',
                        payment_method: 'cash', // Default for dine-in
                        items: this.orderItems.map(item => ({
                            food_item_id: item.id,
                            quantity: item.quantity,
                            portion: item.portion || 'full',
                            rice_type: item.rice_type || null,
                            beverage_size: item.beverage_size || null, // Include beverage size
                            notes: item.notes || null
                        }))
                    };
                    
                    try {
                        const response = await fetch('/pos/order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(orderData)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Redirect to tables page with the order ID for table assignment
                            window.location.href = `/tables?order_id=${result.order.id}`;
                        } else {
                            alert('Error creating order: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error creating order. Please try again.');
                    }
                },
                
                async handleTakeawayClick() {
                    this.orderType = 'takeaway';
                    
                    // Check if there are items in the cart
                    if (this.orderItems.length === 0) {
                        alert('Please add items to your order first!');
                        return;
                    }
                    
                    // Store order items in session storage for kitchen slots page
                    const orderData = {
                        order_type: 'takeaway',
                        items: this.orderItems.map(item => ({
                            food_item_id: item.id,
                            quantity: item.quantity,
                            portion: item.portion || 'full',
                            rice_type: item.rice_type || null,
                            beverage_size: item.beverage_size || null, // Include beverage size
                            notes: item.notes || null
                        })),
                        total_amount: this.total,
                        subtotal: this.subtotal
                    };
                    
                    // Store order data in session storage
                    sessionStorage.setItem('pendingTakeawayOrder', JSON.stringify(orderData));
                    
                    // Redirect to kitchen slots page for slot selection
                    window.location.href = '/kitchen-slots?order_type=takeaway';
                },
                
                showTakeawayPaymentModal() {
                    // Calculate totals
                    this.calculateTakeawayTotals();
                    this.showTakeawayModal = true;
                },
                
                calculateTakeawayTotals() {
                    const subtotal = this.orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                    
                    this.takeawayPaymentInfo.subtotal = subtotal;
                    this.takeawayPaymentInfo.totalAmount = subtotal;
                    this.takeawayPaymentInfo.paidAmount = this.takeawayPaymentInfo.totalAmount;
                    this.calculateTakeawayBalance();
                },
                
                calculateTakeawayBalance() {
                    this.takeawayPaymentInfo.balance = parseFloat(this.takeawayPaymentInfo.paidAmount || 0) - this.takeawayPaymentInfo.totalAmount;
                },
                
                async processTakeawayOrder() {
                    if (!this.takeawayCustomerInfo.name || this.takeawayPaymentInfo.paidAmount <= 0) {
                        alert('Please fill in customer name and payment amount.');
                        return;
                    }
                    
                    const orderData = {
                        order_type: 'takeaway',
                        payment_method: this.takeawayPaymentInfo.method,
                        customer_name: this.takeawayCustomerInfo.name,
                        customer_phone: this.takeawayCustomerInfo.phone,
                        customer_paid: parseFloat(this.takeawayPaymentInfo.paidAmount),
                        balance_returned: Math.max(0, this.takeawayPaymentInfo.balance),
                        total_amount: this.takeawayPaymentInfo.totalAmount,
                        items: this.orderItems.map(item => ({
                            food_item_id: item.id,
                            quantity: item.quantity,
                            portion: item.portion || 'full',
                            rice_type: item.rice_type || null,
                            beverage_size: item.beverage_size || null, // Include beverage size
                            notes: item.notes || null
                        }))
                    };
                    
                    try {
                        const response = await fetch('/pos/order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(orderData)
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Redirect to kitchen slots page with the order ID for slot assignment
                            window.location.href = `/kitchen-slots?order_id=${result.order.id}`;
                        } else {
                            alert('Error completing order: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error completing order. Please try again.');
                    }
                },

                // Customer search functionality
                async searchCustomers(query) {
                    if (query.length < 2) {
                        this.customerSuggestions = [];
                        this.showCustomerSuggestions = false;
                        return;
                    }

                    try {
                        const response = await fetch(`/api/customers?q=${encodeURIComponent(query)}`);
                        const data = await response.json();
                        this.customerSuggestions = data.customers || [];
                        this.showCustomerSuggestions = this.customerSuggestions.length > 0;
                    } catch (error) {
                        console.error('Error fetching customers:', error);
                        this.customerSuggestions = [];
                        this.showCustomerSuggestions = false;
                    }
                },

                selectCustomer(customer) {
                    this.takeawayCustomerInfo.name = customer.name;
                    this.takeawayCustomerInfo.phone = customer.phone;
                    this.customerSuggestions = [];
                    this.showCustomerSuggestions = false;
                },

                addNewCustomer() {
                    this.takeawayCustomerInfo.name = '';
                    this.takeawayCustomerInfo.phone = '';
                    this.customerSuggestions = [];
                    this.showCustomerSuggestions = false;
                },
                
                // Print Invoice Functions
                async printThermalInvoice() {
                    if (!this.completedOrderId) {
                        alert('No completed order found to print.');
                        return;
                    }
                    
                    try {
                        const response = await fetch('/print/thermal-invoice', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                order_id: this.completedOrderId
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Open a new window to show the thermal printer formatted text
                            const printWindow = window.open('', '_blank', 'width=400,height=600,scrollbars=yes');
                            printWindow.document.write(`
                                <!DOCTYPE html>
                                <html>
                                <head>
                                    <title>Thermal Printer Preview - Order #${this.completedOrderId}</title>
                                    <style>
                                        body {
                                            font-family: 'Courier New', monospace;
                                            font-size: 12px;
                                            line-height: 1.2;
                                            background: #f5f5f5;
                                            margin: 20px;
                                            padding: 20px;
                                        }
                                        .preview-container {
                                            background: white;
                                            border: 2px solid #333;
                                            border-radius: 8px;
                                            padding: 20px;
                                            max-width: 400px;
                                            margin: 0 auto;
                                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                                        }
                                        .preview-header {
                                            text-align: center;
                                            margin-bottom: 20px;
                                            padding-bottom: 10px;
                                            border-bottom: 1px solid #ddd;
                                        }
                                        .preview-content {
                                            white-space: pre-wrap;
                                            font-family: 'Courier New', monospace;
                                            font-size: 11px;
                                            line-height: 1.1;
                                            background: #fafafa;
                                            padding: 15px;
                                            border: 1px solid #ddd;
                                            border-radius: 4px;
                                            max-height: 400px;
                                            overflow-y: auto;
                                        }
                                        .print-button {
                                            display: block;
                                            width: 100%;
                                            background: #007cba;
                                            color: white;
                                            border: none;
                                            padding: 10px;
                                            border-radius: 4px;
                                            margin-top: 15px;
                                            cursor: pointer;
                                            font-size: 14px;
                                        }
                                        .print-button:hover {
                                            background: #005a87;
                                        }
                                        .info-text {
                                            font-size: 11px;
                                            color: #666;
                                            text-align: center;
                                            margin-top: 10px;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class="preview-container">
                                        <div class="preview-header">
                                            <h2>Thermal Printer Preview</h2>
                                            <p>Order #${this.completedOrderId} - ${new Date().toLocaleString()}</p>
                                        </div>
                                        <div class="preview-content">${result.invoice_data}</div>
                                        <button class="print-button" onclick="window.print()">
                                            <i class="fas fa-print"></i> Print Preview
                                        </button>
                                        <div class="info-text">
                                            This shows how the invoice will appear on your 80mm thermal printer.<br>
                                            The text is formatted for 32-character width thermal paper.
                                        </div>
                                    </div>
                                </body>
                                </html>
                            `);
                            printWindow.document.close();
                        } else {
                            alert('Error printing invoice: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error printing invoice. Please try again.');
                    }
                },
                
                async printWebInvoice() {
                    if (!this.completedOrderId) {
                        alert('No completed order found to print.');
                        return;
                    }
                    
                    try {
                        const response = await fetch('/print/web-invoice', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                order_id: this.completedOrderId
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            const printWindow = window.open('', '_blank');
                            printWindow.document.write(result.html_invoice);
                            printWindow.document.close();
                            printWindow.print();
                        } else {
                            alert('Error printing invoice: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error printing invoice. Please try again.');
                    }
                },
                
                downloadThermalInvoice() {
                    if (!this.completedOrderId) {
                        alert('No completed order found to download.');
                        return;
                    }
                    
                    // Download the thermal invoice as text file
                    window.open(`/print/download-thermal/${this.completedOrderId}`, '_blank');
                },
                
                closePrintOptions() {
                    this.showPrintOptions = false;
                    this.completedOrderId = null;
                },
                selectBeverageSizeAndAddToCart(size) {
                    this.selectedBeverageSize = size;
                    this.beverageQuantity = 1; // Default to 1 since we removed quantity controls
                    this.addToOrderWithBeverageSize();
                    this.showBeverageSizeModal = false;
                },
                
                selectPortionAndAddToCart(portion) {
                    this.selectedPortion = portion;
                    this.portionQuantity = 1; // Default to 1 since we removed quantity controls
                    this.addToOrderWithPortion();
                    this.showPortionModal = false;
                },
                
                addRiceItemToCart(riceType, portion) {
                    if (!this.selectedFoodItem) return;
                    
                    let price = 0;
                    let itemName = '';
                    
                    if (riceType === 'samba') {
                        price = portion === 'half' ? this.selectedFoodItem.half_samba_price : this.selectedFoodItem.full_samba_price;
                        itemName = `${this.selectedFoodItem.name} - Samba Rice (${portion === 'half' ? 'Half' : 'Full'} Portion)`;
                    } else {
                        price = portion === 'half' ? this.selectedFoodItem.half_basmathi_price : this.selectedFoodItem.full_basmathi_price;
                        itemName = `${this.selectedFoodItem.name} - Basmathi Rice (${portion === 'half' ? 'Half' : 'Full'} Portion)`;
                    }
                    
                    // Add item directly to cart
                    const existingItem = this.orderItems.find(orderItem => 
                        orderItem.name === itemName
                    );
                    
                    if (existingItem) {
                        existingItem.quantity++;
                    } else {
                        this.orderItems.push({
                            id: this.selectedFoodItem.id,
                            name: itemName,
                            price: Math.round(parseFloat(price || 0)),
                            quantity: 1,
                            portion: portion,
                            rice_type: riceType,
                            beverage_size: null
                        });
                    }
                    
                    // Close modal and show success feedback
                    this.showRiceTypeModal = false;
                    this.selectedFoodItem = null;
                    
                    // Optional: Show a brief success message
                    setTimeout(() => {
                        // You can add a toast notification here if desired
                    }, 100);
                }
            }
        }

        // Date and Time Update Function
        function updateDateTime() {
            const now = new Date();
            
            // Format date
            const dateOptions = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            const dateString = now.toLocaleDateString('en-US', dateOptions);
            
            // Format time
            const timeOptions = { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true 
            };
            const timeString = now.toLocaleTimeString('en-US', timeOptions);
            
            // Update the DOM elements
            const dateElement = document.getElementById('current-date');
            const timeElement = document.getElementById('current-time');
            
            if (dateElement) {
                dateElement.textContent = dateString;
            }
            if (timeElement) {
                timeElement.textContent = timeString;
            }
        }

        // Update date and time immediately and then every second
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
        });
    </script>
</body>
</html> 