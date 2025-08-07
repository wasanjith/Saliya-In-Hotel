<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saliya In Hotel - Kitchen Slots</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .slot-item {
            transition: all 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            background: white;
            border: 2px solid transparent;
        }
        
        .slot-item:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .slot-item.available {
            border-color: #10b981;
            background: linear-gradient(145deg, #f0fdf4, #dcfce7);
        }
        
        .slot-item.available:hover {
            border-color: #059669;
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.2);
        }
        
        .slot-item.occupied {
            border-color: #ef4444;
            background: linear-gradient(145deg, #fef2f2, #fee2e2);
        }
        
        .slot-item.occupied:hover {
            border-color: #dc2626;
            box-shadow: 0 20px 40px rgba(239, 68, 68, 0.2);
        }
        
        .slot-item.maintenance {
            border-color: #f59e0b;
            background: linear-gradient(145deg, #fffbeb, #fef3c7);
        }
        
        .slot-item.maintenance:hover {
            border-color: #d97706;
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.2);
        }
        
        .slot-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin: 0 auto;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
            transition: transform 0.3s ease;
        }
        
        .slot-item:hover .slot-image {
            transform: scale(1.1);
        }
        
        .slot-status-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .status-available {
            background: #10b981;
            color: white;
        }
        
        .status-occupied {
            background: #ef4444;
            color: white;
        }
        
        .status-maintenance {
            background: #f59e0b;
            color: white;
        }
        
        .slot-info-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            margin-top: 12px;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .slot-item:hover .slot-info-card {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }
        
        .slot-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .order-info {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            padding: 8px;
            margin-top: 8px;
        }
        
        .sidebar-gradient {
            background: linear-gradient(135deg, #008200 0%, #006600 50%, #004400 100%);
        }
    </style>
</head>
<body class="bg-gray-100" x-data="kitchenSlotSystem()">
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
                    <a href="/customers" class="flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-users mr-3"></i>
                        <span>Customers</span>
                    </a>
                    <a href="/tables" class="flex items-center px-4 py-3 text-green-200 hover:bg-green-700 rounded-lg">
                        <i class="fas fa-chair mr-3"></i>
                        <span>Tables</span>
                    </a>
                    <a href="/kitchen-slots" class="flex items-center px-4 py-3 text-green-200 bg-green-800 rounded-lg">
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
        <div class="flex-1 flex flex-col ml-64">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <button @click="goBack()" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900" x-text="(orderId || pendingOrderData) ? 'Select Kitchen Slot' : 'Kitchen Slot Management'"></h1>
                            <p class="text-sm text-gray-600" x-show="orderInfo">Order #<span x-text="orderInfo?.order_number"></span> - <span x-text="orderInfo?.items_count"></span> items</p>
                            <p class="text-sm text-gray-600" x-show="pendingOrderData && !orderInfo">New Takeaway Order - <span x-text="pendingOrderData?.items?.length || 0"></span> items - Rs. <span x-text="Math.round(pendingOrderData?.total_amount || 0)"></span></p>
                            <p class="text-sm text-gray-600" x-show="!orderId && !pendingOrderData">View and manage kitchen slots</p>
                        </div>
                    </div>
                    
                    <!-- Right Icons -->
                    <div class="flex items-center space-x-4">
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
                        
                        <!-- Legend -->
                        <div class="flex items-center space-x-3 text-sm">
                            <div class="flex items-center space-x-2 bg-white px-3 py-2 rounded-lg shadow-sm">
                                <div class="w-4 h-4 bg-green-500 rounded-full"></div>
                                <span class="font-medium text-green-700">Available</span>
                            </div>
                            <div class="flex items-center space-x-2 bg-white px-3 py-2 rounded-lg shadow-sm">
                                <div class="w-4 h-4 bg-red-500 rounded-full"></div>
                                <span class="font-medium text-red-700">Occupied</span>
                            </div>
                            <div class="flex items-center space-x-2 bg-white px-3 py-2 rounded-lg shadow-sm">
                                <div class="w-4 h-4 bg-orange-500 rounded-full"></div>
                                <span class="font-medium text-orange-700">Maintenance</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Loading State -->
            <div x-show="loading" class="flex justify-center items-center h-64">
                <div class="text-center">
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin mx-auto mb-4"></div>
                        <div class="absolute inset-0 w-16 h-16 border-4 border-transparent border-r-green-500 rounded-full animate-ping mx-auto"></div>
                    </div>
                    <p class="text-gray-600 font-medium">Loading kitchen slots...</p>
                    <p class="text-gray-400 text-sm mt-1">Please wait a moment</p>
                </div>
            </div>

            <!-- Kitchen Slots -->
            <div x-show="!loading" class="px-6 py-8">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl shadow-xl p-8 relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Kitchen Slots</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                            <template x-for="slot in slots" :key="slot.id">
                                <div @click="selectSlot(slot)" 
                                     :class="getSlotClass(slot)"
                                     class="slot-item relative p-6 cursor-pointer text-center">
                                    
                                    <!-- Status Badge -->
                                    <div :class="getStatusBadgeClass(slot)" class="slot-status-badge">
                                        <span x-text="getSlotStatusText(slot)"></span>
                                    </div>
                                    
                                    <!-- Slot Image -->
                                    <div class="mb-4">
                                        <img :src="getSlotImage(slot)" :alt="getSlotStatusText(slot)" class="slot-image">
                                    </div>
                                    
                                    <!-- Slot Info Card -->
                                    <div class="slot-info-card">
                                        <div class="slot-number" x-text="'Slot ' + slot.slot_number"></div>
                                        
                                        <!-- Occupied Info -->
                                        <template x-if="slot.status === 'occupied' && slot.current_order">
                                            <div class="order-info">
                                                <div class="text-xs font-medium text-gray-700 mb-1">Current Order</div>
                                                <div class="text-xs text-gray-600">
                                                    #<span x-text="slot.current_order.order_number"></span>
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    <span x-text="slot.current_order.customer_name"></span>
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    Rs. <span x-text="Math.round(slot.current_order.total_amount || 0)"></span>
                                                </div>
                                            </div>
                                        </template>
                                        
                                        <!-- Available Info -->
                                        <template x-if="slot.status === 'available'">
                                            <div class="text-xs text-green-600 font-medium mt-2">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Ready for orders
                                            </div>
                                        </template>
                                        
                                        <!-- Maintenance Info -->
                                        <template x-if="slot.status === 'maintenance'">
                                            <div class="text-xs text-orange-600 font-medium mt-2">
                                                <i class="fas fa-tools mr-1"></i>
                                                Under maintenance
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="mt-8 flex justify-center space-x-4">
                    <button @click="goBack()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to POS
                    </button>
                    <button x-show="selectedSlot && orderId" 
                            @click="confirmSlotSelection()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-check mr-2"></i>
                        Confirm Slot <span x-text="selectedSlot?.slot_number"></span>
                    </button>
                    <button x-show="selectedSlot && selectedSlot.status === 'occupied'" 
                            @click="completeOrder()" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-check-double mr-2"></i>
                        Complete Order
                    </button>
                </div>
            </div>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Slot Assignment</h3>
                <p class="text-sm text-gray-500 mb-6">
                    <span x-show="orderInfo">Assign Order #<span x-text="orderInfo?.order_number"></span> to Kitchen Slot <span x-text="selectedSlot?.slot_number"></span>?</span>
                    <span x-show="pendingOrderData && !orderInfo">Assign new takeaway order (<span x-text="pendingOrderData?.items?.length || 0"></span> items - Rs. <span x-text="Math.round(pendingOrderData?.total_amount || 0)"></span>) to Kitchen Slot <span x-text="selectedSlot?.slot_number"></span>?</span>
                </p>
                
                <div class="flex space-x-3">
                    <button @click="showConfirmModal = false" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button @click="assignSlotToOrder()" 
                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function kitchenSlotSystem() {
            return {
                loading: true,
                slots: [],
                orderInfo: null,
                selectedSlot: null,
                showConfirmModal: false,
                orderId: null,
                orderType: null, // Added for takeaway flow
                pendingOrderData: null, // Added for takeaway flow
                
                async init() {
                    // Get order ID and order type from URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    this.orderId = urlParams.get('order_id');
                    this.orderType = urlParams.get('order_type');
                    
                    await this.loadSlots();
                    
                    // Load order info if we have an order ID (coming from existing order)
                    if (this.orderId) {
                        await this.loadOrderInfo();
                    }
                    
                    // Load pending order data if coming from takeaway flow
                    if (this.orderType === 'takeaway' && !this.orderId) {
                        await this.loadPendingOrderData();
                    }
                    
                    this.loading = false;
                },
                
                async loadPendingOrderData() {
                    try {
                        const pendingOrderData = sessionStorage.getItem('pendingTakeawayOrder');
                        if (pendingOrderData) {
                            this.pendingOrderData = JSON.parse(pendingOrderData);
                            console.log('Loaded pending order data:', this.pendingOrderData);
                        } else {
                            console.log('No pending order data found');
                        }
                    } catch (error) {
                        console.error('Error loading pending order data:', error);
                    }
                },
                
                async loadSlots() {
                    try {
                        const response = await fetch('/api/kitchen-slots');
                        const data = await response.json();
                        this.slots = data.slots || this.generateDefaultSlots();
                    } catch (error) {
                        console.error('Error loading slots:', error);
                        this.slots = this.generateDefaultSlots();
                    }
                },
                
                async loadOrderInfo() {
                    try {
                        console.log('Loading order info for order ID:', this.orderId);
                        const response = await fetch(`/orders/${this.orderId}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        if (data.success === false) {
                            throw new Error(data.message || 'Failed to load order details');
                        }
                        
                        this.orderInfo = data.order;
                        console.log('Order info loaded:', this.orderInfo);
                    } catch (error) {
                        console.error('Error loading order info:', error);
                        alert('Error loading order information: ' + error.message);
                    }
                },
                
                generateDefaultSlots() {
                    const slots = [];
                    for (let i = 1; i <= 10; i++) {
                        slots.push({
                            id: i,
                            slot_number: `K${i.toString().padStart(2, '0')}`,
                            status: Math.random() > 0.7 ? 'occupied' : 'available',
                            current_order: null
                        });
                    }
                    return slots;
                },
                
                selectSlot(slot) {
                    // If no order ID and no pending order data, just show slot info
                    if (!this.orderId && !this.pendingOrderData) {
                        this.showSlotInfo(slot);
                        return;
                    }
                    
                    // Order assignment mode
                    if (slot.status === 'occupied') {
                        alert('This kitchen slot is currently occupied.');
                        return;
                    }
                    
                    if (slot.status === 'maintenance') {
                        alert('This kitchen slot is under maintenance.');
                        return;
                    }
                    
                    this.selectedSlot = slot;
                    this.showConfirmModal = true;
                },
                
                getSlotClass(slot) {
                    let classes = 'slot-item relative p-6 cursor-pointer text-center';
                    
                    // Add status-specific classes
                    switch (slot.status) {
                        case 'occupied':
                            classes += ' occupied';
                            break;
                        case 'maintenance':
                            classes += ' maintenance';
                            break;
                        default:
                            classes += ' available';
                    }
                    
                    // Add selection ring
                    if (this.selectedSlot && this.selectedSlot.id === slot.id) {
                        classes += ' ring-4 ring-blue-400 ring-opacity-50';
                    }
                    
                    return classes;
                },
                
                getSlotImage(slot) {
                    // SVG data URLs for each status
                    switch (slot.status) {
                        case 'available':
                            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect x='40' y='40' width='120' height='120' rx='10' fill='%23c7f5c7' stroke='%23333' stroke-width='4'/%3E%3Cpath d='M70 100 L90 120 L130 80' stroke='%23333' stroke-width='6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ctext x='100' y='180' text-anchor='middle' font-family='Arial' font-size='16' font-weight='bold' fill='%23333'%3ESLOT%3C/text%3E%3C/svg%3E";
                        case 'occupied':
                            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect x='40' y='40' width='120' height='120' rx='10' fill='%23ffb366' stroke='%23333' stroke-width='4'/%3E%3Ccircle cx='80' cy='80' r='15' fill='%23ffcc99' stroke='%23333' stroke-width='2'/%3E%3Ccircle cx='120' cy='80' r='15' fill='%23ffcc99' stroke='%23333' stroke-width='2'/%3E%3Cpath d='M70 100 Q100 120 130 100' stroke='%23333' stroke-width='3' fill='none'/%3E%3Ctext x='100' y='180' text-anchor='middle' font-family='Arial' font-size='16' font-weight='bold' fill='%23333'%3ESLOT%3C/text%3E%3C/svg%3E";
                        case 'maintenance':
                            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect x='40' y='40' width='120' height='120' rx='10' fill='%23ffd700' stroke='%23333' stroke-width='4'/%3E%3Cpath d='M70 80 L130 120 M130 80 L70 120' stroke='%23333' stroke-width='6' fill='none' stroke-linecap='round'/%3E%3Ctext x='100' y='180' text-anchor='middle' font-family='Arial' font-size='16' font-weight='bold' fill='%23333'%3ESLOT%3C/text%3E%3C/svg%3E";
                        default:
                            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect x='40' y='40' width='120' height='120' rx='10' fill='%23c7f5c7' stroke='%23333' stroke-width='4'/%3E%3Cpath d='M70 100 L90 120 L130 80' stroke='%23333' stroke-width='6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ctext x='100' y='180' text-anchor='middle' font-family='Arial' font-size='16' font-weight='bold' fill='%23333'%3ESLOT%3C/text%3E%3C/svg%3E";
                    }
                },
                
                getStatusBadgeClass(slot) {
                    switch (slot.status) {
                        case 'occupied':
                            return 'status-occupied';
                        case 'maintenance':
                            return 'status-maintenance';
                        default:
                            return 'status-available';
                    }
                },
                
                getSlotStatusText(slot) {
                    switch (slot.status) {
                        case 'occupied':
                            return 'Occupied';
                        case 'maintenance':
                            return 'Maintenance';
                        default:
                            return 'Available';
                    }
                },
                
                showSlotInfo(slot) {
                    // For now, just show an alert
                    alert(`Kitchen Slot ${slot.slot_number} - ${this.getSlotStatusText(slot)}`);
                },
                
                confirmSlotSelection() {
                    this.showConfirmModal = true;
                },
                
                async assignSlotToOrder() {
                    try {
                        let orderId = this.orderId;
                        
                        // If we have pending order data, create the order first
                        if (this.pendingOrderData && !orderId) {
                            console.log('Creating new takeaway order...');
                            const createOrderResponse = await fetch('/pos/order', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(this.pendingOrderData)
                            });
                            
                            const createOrderResult = await createOrderResponse.json();
                            
                            if (!createOrderResult.success) {
                                throw new Error('Error creating order: ' + createOrderResult.message);
                            }
                            
                            orderId = createOrderResult.order.id;
                            console.log('Order created with ID:', orderId);
                            
                            // Clear pending order data from session storage
                            sessionStorage.removeItem('pendingTakeawayOrder');
                        }
                        
                        // Now assign the order to the slot
                        const response = await fetch('/api/assign-kitchen-slot', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                order_id: orderId,
                                slot_number: this.selectedSlot.slot_number
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert(`Order successfully assigned to Kitchen Slot ${this.selectedSlot.slot_number}!`);
                            window.location.href = '/pos';
                        } else {
                            alert('Error assigning slot: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error assigning slot. Please try again.');
                    }
                },
                
                async completeOrder() {
                    if (!this.selectedSlot || this.selectedSlot.status !== 'occupied') {
                        alert('Please select an occupied slot to complete the order.');
                        return;
                    }
                    
                    if (!confirm(`Are you sure you want to complete the order in Kitchen Slot ${this.selectedSlot.slot_number}?`)) {
                        return;
                    }
                    
                    try {
                        const response = await fetch('/api/complete-kitchen-order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                slot_number: this.selectedSlot.slot_number
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            alert(`Order completed successfully in Kitchen Slot ${this.selectedSlot.slot_number}!`);
                            await this.loadSlots(); // Refresh slots
                            this.selectedSlot = null;
                        } else {
                            alert('Error completing order: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error completing order. Please try again.');
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