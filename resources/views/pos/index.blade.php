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
        <div class="flex-1 flex flex-col ml-64 h-screen">
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
            <div class="flex-1 flex overflow-hidden">
                <!-- Left Content - Menu -->
                <div class="flex-1 p-4 overflow-y-auto">
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
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
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
                                        
                                        <!-- Portion Information -->
                                        <div class="mb-2">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">Full:</span>
                                                <span class="font-medium text-blue-600" x-text="'Rs. ' + Math.round(orderType === 'takeaway' ? (item.full_portion_takeaway_price || item.takeaway_price) : (item.full_portion_dine_in_price || item.dine_in_price))"></span>
                                            </div>
                                            <div x-show="item.has_half_portion" class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">Half:</span>
                                                <span class="font-medium text-green-600" x-text="'Rs. ' + Math.round(orderType === 'takeaway' ? item.half_portion_takeaway_price : item.half_portion_dine_in_price)"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-lg font-bold text-blue-600" x-text="'Rs. ' + Math.round(orderType === 'takeaway' ? (item.full_portion_takeaway_price || item.takeaway_price) : (item.full_portion_dine_in_price || item.dine_in_price))"></span>
                                                <span x-show="item.has_half_portion" class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Half Available</span>
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
                <div class="w-96 bg-white border-l border-gray-200 flex flex-col h-full">
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
                                <div class="flex justify-between items-center mt-2">
                                    <div class="flex space-x-2">
                                        <button @click="applyDiscount(index)" class="text-xs text-blue-600 hover:underline">Discount</button>
                                        <button @click="removeItem(index)" class="text-xs text-red-600 hover:underline">Remove</button>
                                    </div>
                                    <span class="font-medium text-gray-800" x-text="'Rs. ' + Math.round(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Order Summary - Only show when items exist -->
                        <template x-if="orderItems.length > 0">
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg border">
                                <h4 class="font-semibold text-gray-800 mb-2">Order Summary</h4>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total:</span>
                                        <span class="font-medium" x-text="'Rs. ' + Math.round(subtotal)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- 5. Payment Methods -->
                    <div class="p-3 border-t border-gray-200 flex-shrink-0">
                        <h4 class="font-semibold text-gray-800 mb-2">Payment Method</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <button @click="paymentMethod = 'cash'" 
                                    :class="paymentMethod === 'cash' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-money-bill-wave mr-2"></i>
                                Cash
                            </button>
                            <button @click="paymentMethod = 'card'" 
                                    :class="paymentMethod === 'card' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-credit-card mr-2"></i>
                                Card
                            </button>
                            <button @click="paymentMethod = 'gift'" 
                                    :class="paymentMethod === 'gift' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-gift mr-2"></i>
                                Gift
                            </button>
                            <button @click="paymentMethod = 'other'" 
                                    :class="paymentMethod === 'other' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-ellipsis-h mr-2"></i>
                                Other
                            </button>
                        </div>
                    </div>
                    
                    <!-- 6. Create Take Away Order Button -->
                    <div class="p-3 border-t border-gray-200 flex-shrink-0">
                        <button @click="processOrder()" 
                                :disabled="orderItems.length === 0"
                                class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                            <i class="fas fa-check mr-2"></i>
                            <span x-show="orderType === 'takeaway'">Create Take Away Order</span>
                            <span x-show="orderType === 'dine_in'">Create Dine In Order</span>
                            <span class="ml-2">- Rs. <span x-text="Math.round(total)"></span></span>
                        </button>
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
                            <button @click="takeawayPaymentInfo.method = 'gift'" 
                                    :class="takeawayPaymentInfo.method === 'gift' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium">
                                Gift Card
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
                         @click="selectPortion('full')">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-medium text-gray-900" x-text="selectedFoodItem ? (selectedFoodItem.full_portion_name || 'Full Portion') : 'Full Portion'"></h5>
                                <p class="text-sm text-gray-600">Complete serving size</p>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-blue-600">
                                    Rs. <span x-text="getPortionPrice('full')"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Half Portion (if available) -->
                    <div x-show="selectedFoodItem && selectedFoodItem.has_half_portion"
                         class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors"
                         :class="selectedPortion === 'half' ? 'border-blue-500 bg-blue-50' : ''"
                         @click="selectPortion('half')">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="font-medium text-gray-900" x-text="selectedFoodItem ? (selectedFoodItem.half_portion_name || 'Half Portion') : 'Half Portion'"></h5>
                                <p class="text-sm text-gray-600">Half serving size</p>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-blue-600">
                                    Rs. <span x-text="getPortionPrice('half')"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <div class="flex items-center space-x-3">
                        <button @click="decreasePortionQuantity()" 
                                class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-12 text-center font-medium text-lg" x-text="portionQuantity"></span>
                        <button @click="increasePortionQuantity()" 
                                class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Add to Order Button -->
                <div class="mt-6">
                    <button @click="addToOrderWithPortion()" 
                            class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add to Order - Rs. <span x-text="getPortionPrice(selectedPortion) * portionQuantity"></span>
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
                showPrintOptions: false,
                completedOrderId: null,
                selectedFoodItem: null,
                selectedPortion: 'full',
                portionQuantity: 1,
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
                    // Check if item has half portion option
                    if (item.has_half_portion) {
                        this.showPortionSelection(item);
                    } else {
                        // For items without half portions, add directly with full portion
                        const price = this.orderType === 'takeaway' ? item.takeaway_price : item.dine_in_price;
                        const existingItem = this.orderItems.find(orderItem => orderItem.id === item.id);
                        
                        if (existingItem) {
                            existingItem.quantity++;
                        } else {
                            this.orderItems.push({
                                id: item.id,
                                name: item.name,
                                price: Math.round(parseFloat(price)),
                                quantity: 1,
                                portion: 'full'
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
                
                selectPortion(portion) {
                    this.selectedPortion = portion;
                },
                
                getPortionPrice(portion) {
                    if (!this.selectedFoodItem) return 0;
                    
                    const orderType = this.orderType === 'takeaway' ? 'takeaway' : 'dine_in';
                    const priceField = `${portion}_portion_${orderType}_price`;
                    
                    if (this.selectedFoodItem[priceField] && this.selectedFoodItem[priceField] !== null) {
                        return Math.round(parseFloat(this.selectedFoodItem[priceField]));
                    }
                    
                    // Fallback to legacy pricing
                    if (orderType === 'takeaway') {
                        return Math.round(parseFloat(this.selectedFoodItem.takeaway_price || 0));
                    } else {
                        return Math.round(parseFloat(this.selectedFoodItem.dine_in_price || 0));
                    }
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
                    const itemName = this.selectedFoodItem.name + ' (' + this.getPortionName(this.selectedPortion) + ')';
                    
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
                            portion: this.selectedPortion
                        });
                    }
                    
                    this.showPortionModal = false;
                    this.selectedFoodItem = null;
                    this.selectedPortion = 'full';
                    this.portionQuantity = 1;
                },
                
                getPortionName(portion) {
                    if (!this.selectedFoodItem) return portion === 'half' ? 'Half Portion' : 'Full Portion';
                    
                    if (portion === 'half') {
                        return this.selectedFoodItem.half_portion_name || 'Half Portion';
                    } else {
                        return this.selectedFoodItem.full_portion_name || 'Full Portion';
                    }
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
                
                async processOrder() {
                    if (this.orderItems.length === 0) return;
                    
                    // For takeaway orders, redirect to kitchen slots page first
                    if (this.orderType === 'takeaway') {
                        await this.handleTakeawayClick();
                        return;
                    }
                    
                    // For dine-in orders, redirect to tables page first
                    if (this.orderType === 'dine_in') {
                        await this.handleDineInClick();
                        return;
                    }
                    
                    // For other order types, proceed with normal flow
                    const orderData = {
                        order_type: this.orderType,
                        payment_method: this.paymentMethod,
                        items: this.orderItems.map(item => ({
                            food_item_id: item.id,
                            quantity: item.quantity,
                            portion: item.portion || 'full',
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
                            alert('Order placed successfully!');
                            
                            // Store the completed order ID for printing
                            this.completedOrderId = result.order.id;
                            this.showPrintOptions = true;
                            
                            // Clear order items
                            this.orderItems = [];
                            this.orderNumber = Math.floor(Math.random() * 900000) + 100000;
                        } else {
                            alert('Error placing order: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error placing order. Please try again.');
                    }
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