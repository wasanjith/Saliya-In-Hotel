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
    </style>
</head>
<body class="bg-gray-100" x-data="posSystem()">
    <div class="flex h-screen">
        <!-- Left Sidebar -->
        <div class="w-64 bg-gray-800 text-white">
            <!-- Logo -->
            <div class="p-4 border-b border-gray-700">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-fish text-blue-400 text-2xl"></i>
                    <span class="text-xl font-bold">SEAFOOD</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    <a href="#" class="flex items-center px-4 py-3 text-blue-400 bg-blue-900 rounded-lg">
                        <i class="fas fa-home mr-3"></i>
                        <span>Home</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        <span>Orders</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-users mr-3"></i>
                        <span>Customers</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-table mr-3"></i>
                        <span>Tables</span>
                    </a>
                </div>
            </nav>
            
            <!-- Logout -->
            <div class="absolute bottom-4 left-4 right-4">
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-power-off mr-3"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Search in menu" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-barcode absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Right Icons -->
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-globe text-gray-600 text-xl"></i>
                        <i class="fas fa-bell text-gray-600 text-xl"></i>
                        <div class="flex items-center space-x-2">
                            <img src="https://via.placeholder.com/32x32" alt="Profile" class="w-8 h-8 rounded-full">
                            <span class="text-gray-700">Hello, Osama ali</span>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <div class="flex-1 flex">
                <!-- Left Content - Menu -->
                <div class="flex-1 p-6 overflow-y-auto">
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
                                        <p class="text-sm text-gray-600 mb-2" x-text="item.description"></p>
                                        <div class="flex justify-between items-center">
                                                                                         <span class="text-lg font-bold text-blue-600" x-text="'Rs. ' + Math.round(orderType === 'takeaway' ? item.takeaway_price : item.dine_in_price)"></span>
                                            <button @click.stop="toggleFavorite(item)" 
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
                <div class="w-96 bg-white border-l border-gray-200 flex flex-col">
                    <!-- Order Header -->
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Order list</h3>
                            <div class="text-sm text-gray-500">
                                Transaction #<span x-text="orderNumber"></span>
                            </div>
                            <i class="fas fa-ellipsis-v text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Order Type -->
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex space-x-2">
                            <button @click="orderType = 'delivery'" 
                                    :class="orderType === 'delivery' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium">
                                Delivery
                            </button>
                            <button @click="orderType = 'dine_in'" 
                                    :class="orderType === 'dine_in' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium">
                                Dine In
                            </button>
                            <button @click="orderType = 'takeaway'" 
                                    :class="orderType === 'takeaway' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="flex-1 py-2 px-3 rounded-lg text-sm font-medium">
                                Takeaway
                            </button>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="flex-1 overflow-y-auto p-4">
                        <template x-if="orderItems.length === 0">
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                                <p>No items in order</p>
                            </div>
                        </template>
                        
                        <template x-for="(item, index) in orderItems" :key="index">
                            <div class="order-item bg-gray-50 rounded-lg p-3 mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-utensils text-gray-400"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium" x-text="item.name"></h4>
                                                                                 <p class="text-sm text-gray-600" x-text="'Rs. ' + Math.round(item.price)"></p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button @click="decreaseQuantity(index)" 
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-8 text-center font-medium" x-text="item.quantity"></span>
                                        <button @click="increaseQuantity(index)" 
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <div class="flex space-x-2">
                                        <button @click="applyDiscount(index)" class="text-xs text-blue-600 hover:underline">Discount</button>
                                        <button @click="removeItem(index)" class="text-xs text-red-600 hover:underline">Remove</button>
                                    </div>
                                                                         <span class="font-medium" x-text="'Rs. ' + Math.round(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="p-4 border-t border-gray-200">
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between">
                                <span>Sub total:</span>
                                                                 <span x-text="'Rs. ' + Math.round(subtotal)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tax:</span>
                                <span>14%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Discount:</span>
                                <span>12%</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-blue-600">
                                <span>Total:</span>
                                                                 <span x-text="'Rs. ' + Math.round(total)"></span>
                            </div>
                        </div>
                        
                        <!-- Payment Methods -->
                        <div class="mb-4">
                            <div class="flex space-x-2">
                                <button @click="paymentMethod = 'cash'" 
                                        :class="paymentMethod === 'cash' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="flex-1 py-2 px-3 rounded-lg text-sm font-medium">
                                    Cash
                                </button>
                                <button @click="paymentMethod = 'card'" 
                                        :class="paymentMethod === 'card' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="flex-1 py-2 px-3 rounded-lg text-sm font-medium">
                                    Card
                                </button>
                                <button @click="paymentMethod = 'gift'" 
                                        :class="paymentMethod === 'gift' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="flex-1 py-2 px-3 rounded-lg text-sm font-medium">
                                    Gift
                                </button>
                                <button @click="paymentMethod = 'other'" 
                                        :class="paymentMethod === 'other' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'"
                                        class="flex-1 py-2 px-3 rounded-lg text-sm font-medium">
                                    Other
                                </button>
                            </div>
                        </div>
                        
                        <!-- Pay Button -->
                        <button @click="processOrder()" 
                                :disabled="orderItems.length === 0"
                                class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                                         Pay Rs. <span x-text="Math.round(total)"></span>
                        </button>
                    </div>
                </div>
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
                     const tax = Math.round(this.subtotal * 0.14);
                     const discount = Math.round(this.subtotal * 0.12);
                     return this.subtotal + tax - discount;
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
                    const existingItem = this.orderItems.find(orderItem => orderItem.id === item.id);
                    
                    if (existingItem) {
                        existingItem.quantity++;
                    } else {
                                                 const price = this.orderType === 'takeaway' ? item.takeaway_price : item.dine_in_price;
                         this.orderItems.push({
                             id: item.id,
                             name: item.name,
                             price: Math.round(parseFloat(price)),
                             quantity: 1
                         });
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
                
                async processOrder() {
                    if (this.orderItems.length === 0) return;
                    
                    const orderData = {
                        order_type: this.orderType,
                        payment_method: this.paymentMethod,
                        items: this.orderItems.map(item => ({
                            food_item_id: item.id,
                            quantity: item.quantity
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
                            this.orderItems = [];
                            this.orderNumber = Math.floor(Math.random() * 900000) + 100000;
                        } else {
                            alert('Error placing order: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error placing order. Please try again.');
                    }
                }
            }
        }
    </script>
</body>
</html> 