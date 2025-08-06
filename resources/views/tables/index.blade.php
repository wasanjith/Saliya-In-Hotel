<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saliya In Hotel - Table Map</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .table-item {
            transition: all 0.3s ease;
        }
        .table-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .table-occupied {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }
        .table-available {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        .table-reserved {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }
    </style>
</head>
<body class="bg-gray-100" x-data="tableMapSystem()">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <button @click="goBack()" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900" x-text="orderId ? 'Select Table' : 'Table Management'"></h1>
                        <p class="text-sm text-gray-600" x-show="orderInfo">Order #<span x-text="orderInfo?.order_number"></span> - <span x-text="orderInfo?.items_count"></span> items</p>
                        <p class="text-sm text-gray-600" x-show="!orderId">View and manage restaurant tables</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-sm">
                        <div class="flex items-center space-x-1">
                            <div class="w-4 h-4 table-available rounded"></div>
                            <span>Available</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-4 h-4 table-occupied rounded"></div>
                            <span>Occupied</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-4 h-4 table-reserved rounded"></div>
                            <span>Reserved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center items-center h-64">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
            <p class="text-gray-600">Loading tables...</p>
        </div>
    </div>

    <!-- Table Map -->
    <div x-show="!loading" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Restaurant Layout -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <template x-for="table in tables" :key="table.id">
                    <div @click="selectTable(table)" 
                         :class="getTableClass(table)"
                         class="table-item relative p-6 rounded-lg cursor-pointer text-white text-center">
                        
                        <!-- Table Icon -->
                        <div class="mb-4">
                            <i class="fas fa-chair text-3xl"></i>
                        </div>
                        
                        <!-- Table Info -->
                        <div>
                            <h3 class="text-lg font-bold mb-1" x-text="'Table ' + table.number"></h3>
                            <p class="text-sm opacity-90" x-text="table.capacity + ' seats'"></p>
                            <p class="text-xs mt-1 opacity-75" x-text="getTableStatusText(table)"></p>
                        </div>
                        
                        <!-- Status Indicator -->
                        <div class="absolute top-2 right-2">
                            <div :class="getStatusIndicator(table)" class="w-3 h-3 rounded-full"></div>
                        </div>
                        
                        <!-- Occupied Info -->
                        <template x-if="table.status === 'occupied' && table.current_order">
                            <div class="absolute bottom-2 left-2 right-2">
                                <p class="text-xs opacity-75">Order #<span x-text="table.current_order.order_number"></span></p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="mt-8 flex justify-center space-x-4">
            <button @click="goBack()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to POS
            </button>
            <button x-show="selectedTable && orderId" 
                    @click="confirmTableSelection()" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-check mr-2"></i>
                Confirm Table <span x-text="selectedTable?.number"></span>
            </button>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showConfirmModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Table Assignment</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Assign Order #<span x-text="orderInfo?.order_number"></span> to Table <span x-text="selectedTable?.number"></span>?
                </p>
                
                <div class="flex space-x-3">
                    <button @click="showConfirmModal = false" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button @click="assignTableToOrder()" 
                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Info Modal (for management mode) -->
    <div x-show="showTableInfoModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <i class="fas fa-chair text-blue-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Table <span x-text="selectedTableInfo?.number"></span></h3>
                <div class="text-sm text-gray-500 mb-6 space-y-2">
                    <p><strong>Capacity:</strong> <span x-text="selectedTableInfo?.capacity"></span> seats</p>
                    <p><strong>Status:</strong> <span x-text="getTableStatusText(selectedTableInfo)"></span></p>
                    <div x-show="selectedTableInfo?.current_order">
                        <p><strong>Current Order:</strong> #<span x-text="selectedTableInfo?.current_order?.order_number"></span></p>
                        <p><strong>Amount:</strong> Rs. <span x-text="selectedTableInfo?.current_order?.total_amount"></span></p>
                    </div>
                </div>
                
                <div class="flex space-x-3">
                    <button @click="showTableInfoModal = false" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">
                        Close
                    </button>
                    <button x-show="selectedTableInfo?.status === 'occupied'" 
                            @click="openCloseOrderModal()" 
                            class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">
                        Close Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Order Modal -->
    <div x-show="showCloseOrderModal" 
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
                    <h3 class="text-xl font-bold text-gray-900">Close Order - Table <span x-text="selectedTableInfo?.number"></span></h3>
                    <button @click="showCloseOrderModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Order Details</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Order Number:</span>
                            <span class="font-medium" x-text="orderToClose?.order_number"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Table:</span>
                            <span class="font-medium" x-text="selectedTableInfo?.number"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Order Type:</span>
                            <span class="font-medium capitalize" x-text="orderToClose?.order_type?.replace('_', ' ')"></span>
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
                                    <span class="font-medium" x-text="item.item_name"></span>
                                    <span class="text-gray-500 text-sm ml-2">x<span x-text="item.quantity"></span></span>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium">Rs. <span x-text="Math.round(item.total_price)"></span></div>
                                    <div class="text-sm text-gray-500">@ Rs. <span x-text="Math.round(item.unit_price)"></span></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Customer Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                            <input type="text" x-model="customerInfo.name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter customer name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" x-model="customerInfo.phone" 
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
                            <button @click="paymentInfo.method = 'cash'" 
                                    :class="paymentInfo.method === 'cash' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium">
                                Cash
                            </button>
                            <button @click="paymentInfo.method = 'card'" 
                                    :class="paymentInfo.method === 'card' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium">
                                Card
                            </button>
                            <button @click="paymentInfo.method = 'gift'" 
                                    :class="paymentInfo.method === 'gift' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium">
                                Gift Card
                            </button>
                        </div>
                    </div>

                    <!-- Amount Breakdown -->
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">Rs. <span x-text="Math.round(orderToClose?.subtotal || 0)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax (10%):</span>
                            <span class="font-medium">Rs. <span x-text="Math.round((orderToClose?.subtotal || 0) * 0.1)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Discount:</span>
                            <div class="flex items-center space-x-2">
                                <input type="number" x-model="paymentInfo.discount" @input="calculatePayment()"
                                       class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-right"
                                       min="0" step="0.01">
                                <span class="text-sm">Rs.</span>
                            </div>
                        </div>
                        <hr class="border-gray-300">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total Amount:</span>
                            <span class="text-blue-600">Rs. <span x-text="Math.round(paymentInfo.totalAmount)"></span></span>
                        </div>
                    </div>

                    <!-- Payment Input -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount Paid by Customer</label>
                        <input type="number" x-model="paymentInfo.paidAmount" @input="calculatePayment()"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg font-medium"
                               placeholder="Enter amount paid" step="0.01" min="0">
                    </div>

                    <!-- Balance Display -->
                    <div class="mt-4 p-3 rounded-lg" :class="paymentInfo.balance >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                        <div class="flex justify-between items-center">
                            <span class="font-medium" :class="paymentInfo.balance >= 0 ? 'text-green-700' : 'text-red-700'">
                                <span x-show="paymentInfo.balance >= 0">Balance to Return:</span>
                                <span x-show="paymentInfo.balance < 0">Amount Due:</span>
                            </span>
                            <span class="text-lg font-bold" :class="paymentInfo.balance >= 0 ? 'text-green-600' : 'text-red-600'">
                                Rs. <span x-text="Math.round(Math.abs(paymentInfo.balance))"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button @click="showCloseOrderModal = false" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 px-4 rounded-lg font-medium">
                        Cancel
                    </button>
                    <button @click="processPaymentAndCloseOrder()" 
                            :disabled="!customerInfo.name || paymentInfo.paidAmount <= 0"
                            :class="(!customerInfo.name || paymentInfo.paidAmount <= 0) ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600'"
                            class="flex-1 text-white py-3 px-4 rounded-lg font-medium">
                        <i class="fas fa-check mr-2"></i>
                        Complete Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function tableMapSystem() {
            return {
                loading: true,
                tables: [],
                orderInfo: null,
                selectedTable: null,
                showConfirmModal: false,
                showTableInfoModal: false,
                showCloseOrderModal: false,
                selectedTableInfo: null,
                orderToClose: null,
                orderItems: [],
                customerInfo: {
                    name: '',
                    phone: ''
                },
                paymentInfo: {
                    method: 'cash',
                    discount: 0,
                    paidAmount: 0,
                    totalAmount: 0,
                    balance: 0
                },
                orderId: null,
                
                async init() {
                    // Get order ID from URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    this.orderId = urlParams.get('order_id');
                    
                    await this.loadTables();
                    
                    // Only load order info if we have an order ID (coming from dine-in)
                    if (this.orderId) {
                        await this.loadOrderInfo();
                    }
                    
                    this.loading = false;
                },
                
                async loadTables() {
                    try {
                        const response = await fetch('/api/tables');
                        const data = await response.json();
                        this.tables = data.tables || this.generateDefaultTables();
                    } catch (error) {
                        console.error('Error loading tables:', error);
                        this.tables = this.generateDefaultTables();
                    }
                },
                
                async loadOrderInfo() {
                    try {
                        const response = await fetch(`/orders/${this.orderId}`);
                        const data = await response.json();
                        this.orderInfo = data.order;
                    } catch (error) {
                        console.error('Error loading order info:', error);
                    }
                },
                
                generateDefaultTables() {
                    const tables = [];
                    for (let i = 1; i <= 20; i++) {
                        tables.push({
                            id: i,
                            number: i,
                            capacity: i <= 10 ? 4 : (i <= 15 ? 6 : 8),
                            status: Math.random() > 0.7 ? 'occupied' : 'available',
                            current_order: null
                        });
                    }
                    return tables;
                },
                
                selectTable(table) {
                    // If no order ID, just show table info
                    if (!this.orderId) {
                        this.showTableInfo(table);
                        return;
                    }
                    
                    // Order assignment mode
                    if (table.status === 'occupied') {
                        alert('This table is currently occupied.');
                        return;
                    }
                    
                    this.selectedTable = table;
                    this.showConfirmModal = true;
                },
                
                getTableClass(table) {
                    const baseClass = 'table-item relative p-6 rounded-lg cursor-pointer text-white text-center';
                    
                    if (this.selectedTable && this.selectedTable.id === table.id) {
                        return baseClass + ' ring-4 ring-white ring-opacity-50';
                    }
                    
                    switch (table.status) {
                        case 'occupied':
                            return baseClass + ' table-occupied cursor-not-allowed';
                        case 'reserved':
                            return baseClass + ' table-reserved';
                        default:
                            return baseClass + ' table-available hover:opacity-90';
                    }
                },
                
                getTableStatusText(table) {
                    switch (table.status) {
                        case 'occupied':
                            return 'Occupied';
                        case 'reserved':
                            return 'Reserved';
                        default:
                            return 'Available';
                    }
                },
                
                getStatusIndicator(table) {
                    switch (table.status) {
                        case 'occupied':
                            return 'bg-red-400';
                        case 'reserved':
                            return 'bg-yellow-400';
                        default:
                            return 'bg-green-400';
                    }
                },
                
                async confirmTableSelection() {
                    this.showConfirmModal = true;
                },
                
                async assignTableToOrder() {
                    try {
                        const response = await fetch('/api/assign-table', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                order_id: this.orderId,
                                table_number: this.selectedTable.number
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert(`Order successfully assigned to Table ${this.selectedTable.number}!`);
                            window.location.href = '/pos';
                        } else {
                            alert('Error assigning table: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error assigning table. Please try again.');
                    }
                },
                
                showTableInfo(table) {
                    this.selectedTableInfo = table;
                    this.showTableInfoModal = true;
                },
                
                async openCloseOrderModal() {
                    this.showTableInfoModal = false;
                    
                    // Load order details
                    try {
                        const orderId = this.selectedTableInfo.current_order.id;
                        const response = await fetch(`/orders/${orderId}`);
                        const data = await response.json();
                        
                        this.orderToClose = data.order;
                        this.orderItems = data.order_items || [];
                        
                        // Calculate initial amounts
                        this.paymentInfo.totalAmount = parseFloat(this.orderToClose.total_amount || 0);
                        this.paymentInfo.paidAmount = this.paymentInfo.totalAmount;
                        this.calculatePayment();
                        
                        this.showCloseOrderModal = true;
                    } catch (error) {
                        console.error('Error loading order details:', error);
                        alert('Error loading order details. Please try again.');
                    }
                },
                
                calculatePayment() {
                    const subtotal = parseFloat(this.orderToClose?.subtotal || 0);
                    const tax = subtotal * 0.1;
                    const discount = parseFloat(this.paymentInfo.discount || 0);
                    
                    this.paymentInfo.totalAmount = subtotal + tax - discount;
                    this.paymentInfo.balance = parseFloat(this.paymentInfo.paidAmount || 0) - this.paymentInfo.totalAmount;
                },
                
                async processPaymentAndCloseOrder() {
                    if (!this.customerInfo.name || this.paymentInfo.paidAmount <= 0) {
                        alert('Please fill in customer name and payment amount.');
                        return;
                    }
                    
                    try {
                        const response = await fetch('/api/close-order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                order_id: this.orderToClose.id,
                                table_number: this.selectedTableInfo.number,
                                customer_name: this.customerInfo.name,
                                customer_phone: this.customerInfo.phone,
                                payment_method: this.paymentInfo.method,
                                discount_amount: parseFloat(this.paymentInfo.discount || 0),
                                customer_paid: parseFloat(this.paymentInfo.paidAmount),
                                balance_returned: Math.max(0, this.paymentInfo.balance),
                                total_amount: this.paymentInfo.totalAmount
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert('Order completed successfully!');
                            this.showCloseOrderModal = false;
                            
                            // Reset form
                            this.customerInfo = { name: '', phone: '' };
                            this.paymentInfo = { method: 'cash', discount: 0, paidAmount: 0, totalAmount: 0, balance: 0 };
                            
                            // Refresh tables
                            await this.loadTables();
                        } else {
                            alert('Error completing order: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error completing order. Please try again.');
                    }
                },
                
                async clearTable() {
                    if (!confirm('Are you sure you want to clear this table?')) {
                        return;
                    }
                    
                    try {
                        const response = await fetch('/api/clear-table', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                table_number: this.selectedTableInfo.number
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert('Table cleared successfully!');
                            this.showTableInfoModal = false;
                            await this.loadTables(); // Refresh tables
                        } else {
                            alert('Error clearing table: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error clearing table. Please try again.');
                    }
                },
                
                goBack() {
                    window.location.href = '/pos';
                }
            }
        }
    </script>
</body>
</html>