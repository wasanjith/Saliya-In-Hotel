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
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            background: white;
            border: 2px solid transparent;
        }
        
        .table-item:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .table-item:active {
            transform: translateY(-2px) scale(1.01);
        }
        
        .table-item.available {
            border-color: #10b981;
            background: linear-gradient(145deg, #f0fdf4, #dcfce7);
        }
        
        .table-item.available:hover {
            border-color: #059669;
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.2);
        }
        
        .table-item.occupied {
            border-color: #ef4444;
            background: linear-gradient(145deg, #fef2f2, #fee2e2);
        }
        
        .table-item.occupied:hover {
            border-color: #dc2626;
            box-shadow: 0 20px 40px rgba(239, 68, 68, 0.2);
        }
        
        .table-item.reserved {
            border-color: #f59e0b;
            background: linear-gradient(145deg, #fffbeb, #fef3c7);
        }
        
        .table-item.reserved:hover {
            border-color: #d97706;
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.2);
        }
        
        .table-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin: 0 auto;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
            transition: transform 0.3s ease;
        }
        
        .table-item:hover .table-image {
            transform: scale(1.1);
        }
        
        .table-status-badge {
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
        
        .status-reserved {
            background: #f59e0b;
            color: white;
        }
        
        .table-info-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            margin-top: 12px;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .table-item:hover .table-info-card {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }
        
        .table-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }
        
        .table-capacity {
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 8px;
        }
        
        .order-info {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            padding: 8px;
            margin-top: 8px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .legend-image {
            width: 24px;
            height: 24px;
            object-fit: contain;
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
<body class="bg-gray-100" x-data="tableMapSystem()">
    <div class="flex">
        <!-- Left Sidebar -->
        <div class="w-64 bg-gray-800 text-white flex flex-col fixed top-0 left-0 h-screen z-50">
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
                    <a href="/pos" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-home mr-3"></i>
                        <span>Home</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="/orders" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        <span>Orders</span>
                    </a>
                    <a href="/customers" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                        <i class="fas fa-users mr-3"></i>
                        <span>Customers</span>
                    </a>
                    <a href="/tables" class="flex items-center px-4 py-3 text-blue-400 bg-blue-900 rounded-lg">
                        <i class="fas fa-chair mr-3"></i>
                        <span>Tables</span>
                    </a>
                </div>
            </nav>
            
            <!-- Logout -->
            <div class="mt-auto p-4">
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <i class="fas fa-power-off mr-3"></i>
                    <span>Logout</span>
                </a>
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
                            <h1 class="text-2xl font-bold text-gray-900" x-text="orderId ? 'Select Table' : 'Table Management'"></h1>
                            <p class="text-sm text-gray-600" x-show="orderInfo">Order #<span x-text="orderInfo?.order_number"></span> - <span x-text="orderInfo?.items_count"></span> items</p>
                            <p class="text-sm text-gray-600" x-show="!orderId">View and manage restaurant tables</p>
                        </div>
                    </div>
                    
                    <!-- Right Icons -->
                    <div class="flex items-center space-x-4">
                        <!-- Legend -->
                        <div class="flex items-center space-x-3 text-sm">
                            <div class="legend-item">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Ccircle cx='100' cy='60' r='35' fill='%23c7f5c7' stroke='%23333' stroke-width='4'/%3E%3Cpath d='M75 85 L90 95 L125 65' stroke='%23333' stroke-width='6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3Crect x='50' y='110' width='100' height='15' rx='7' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3Crect x='60' y='125' width='80' height='8' fill='%2390c695' stroke='%23333' stroke-width='2'/%3E%3Crect x='70' y='133' width='12' height='50' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3Crect x='118' y='133' width='12' height='50' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3C/svg%3E" 
                                     alt="Available" class="legend-image">
                                <span class="font-medium text-green-700">Available</span>
                            </div>
                            <div class="legend-item">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect x='50' y='80' width='100' height='15' rx='7' fill='%23ffb366' stroke='%23333' stroke-width='3'/%3E%3Crect x='60' y='95' width='80' height='8' fill='%23ffb366' stroke='%23333' stroke-width='2'/%3E%3Crect x='70' y='103' width='12' height='50' fill='%23ffb366' stroke='%23333' stroke-width='3'/%3E%3Crect x='118' y='103' width='12' height='50' fill='%23ffb366' stroke='%23333' stroke-width='3'/%3E%3Ccircle cx='85' cy='45' r='12' fill='%23ffcc99' stroke='%23333' stroke-width='2'/%3E%3Cpath d='M85 57 Q85 65 85 70' stroke='%23333' stroke-width='2' fill='none'/%3E%3Cpath d='M75 65 Q85 75 95 65' stroke='%23333' stroke-width='2' fill='none'/%3E%3Ccircle cx='100' cy='40' r='12' fill='%23ffcc99' stroke='%23333' stroke-width='2'/%3E%3Cpath d='M100 52 Q100 60 100 65' stroke='%23333' stroke-width='2' fill='none'/%3E%3Cpath d='M90 60 Q100 70 110 60' stroke='%23333' stroke-width='2' fill='none'/%3E%3Ccircle cx='115' cy='45' r='12' fill='%23ffcc99' stroke='%23333' stroke-width='2'/%3E%3Cpath d='M115 57 Q115 65 115 70' stroke='%23333' stroke-width='2' fill='none'/%3E%3Cpath d='M105 65 Q115 75 125 65' stroke='%23333' stroke-width='2' fill='none'/%3E%3C/svg%3E" 
                                     alt="Occupied" class="legend-image">
                                <span class="font-medium text-red-700">Occupied</span>
                            </div>
                            <div class="legend-item">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Ccircle cx='100' cy='50' r='20' fill='%23ffd700' stroke='%23333' stroke-width='3'/%3E%3Ctext x='100' y='58' text-anchor='middle' font-family='Arial' font-size='24' font-weight='bold' fill='%23333'%3E%24%3C/text%3E%3Crect x='70' y='70' width='60' height='12' rx='2' fill='%23ffb84d' stroke='%23333' stroke-width='2'/%3E%3Ctext x='100' y='80' text-anchor='middle' font-family='Arial' font-size='8' font-weight='bold' fill='%23333'%3ERESERVED%3C/text%3E%3Crect x='50' y='90' width='100' height='15' rx='7' fill='%23ffb84d' stroke='%23333' stroke-width='3'/%3E%3Crect x='60' y='105' width='80' height='8' fill='%23ffb84d' stroke='%23333' stroke-width='2'/%3E%3Crect x='70' y='113' width='12' height='50' fill='%23ffb84d' stroke='%23333' stroke-width='3'/%3E%3Crect x='118' y='113' width='12' height='50' fill='%23ffb84d' stroke='%23333' stroke-width='3'/%3E%3C/svg%3E" 
                                     alt="Reserved" class="legend-image">
                                <span class="font-medium text-orange-700">Reserved</span>
                            </div>
                        </div>
                        
                        <!-- Header Icons -->
                        <i class="fas fa-globe text-gray-600 text-xl"></i>
                        <i class="fas fa-bell text-gray-600 text-xl"></i>
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">A</span>
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
                    <p class="text-gray-600 font-medium">Loading restaurant tables...</p>
                    <p class="text-gray-400 text-sm mt-1">Please wait a moment</p>
                </div>
            </div>

            <!-- Table Map -->
            <div x-show="!loading" class="px-6 py-8">
        <!-- Restaurant Layout -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl shadow-xl p-8 relative overflow-hidden">
            <!-- Decorative background pattern -->
            <div class="absolute inset-0 opacity-5">
                <div class="absolute top-10 left-10 w-20 h-20 bg-blue-500 rounded-full"></div>
                <div class="absolute top-32 right-20 w-16 h-16 bg-green-500 rounded-full"></div>
                <div class="absolute bottom-20 left-32 w-12 h-12 bg-orange-500 rounded-full"></div>
                <div class="absolute bottom-10 right-10 w-24 h-24 bg-purple-500 rounded-full"></div>
            </div>
            
            <div class="relative z-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Restaurant Floor Plan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                <template x-for="table in tables" :key="table.id">
                    <div @click="selectTable(table)" 
                         :class="getTableClass(table)"
                         class="table-item relative p-6 cursor-pointer text-center">
                        
                        <!-- Status Badge -->
                        <div :class="getStatusBadgeClass(table)" class="table-status-badge">
                            <span x-text="getTableStatusText(table)"></span>
                        </div>
                        
                        <!-- Table Image -->
                        <div class="mb-4">
                            <img :src="getTableImage(table)" :alt="getTableStatusText(table)" class="table-image">
                        </div>
                        
                        <!-- Table Info Card -->
                        <div class="table-info-card">
                            <div class="table-number" x-text="'Table ' + table.number"></div>
                            <div class="table-capacity">
                                <i class="fas fa-users mr-1"></i>
                                <span x-text="table.capacity + ' seats'"></span>
                            </div>
                            
                            <!-- Occupied Info -->
                            <template x-if="table.status === 'occupied' && table.current_order">
                                <div class="order-info">
                                    <div class="text-xs font-medium text-gray-700 mb-1">Current Order</div>
                                    <div class="text-xs text-gray-600">
                                        #<span x-text="table.current_order.order_number"></span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        Rs. <span x-text="Math.round(table.current_order.total_amount || 0)"></span>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Available Info -->
                            <template x-if="table.status === 'available'">
                                <div class="text-xs text-green-600 font-medium mt-2">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Ready for guests
                                </div>
                            </template>
                            
                            <!-- Reserved Info -->
                            <template x-if="table.status === 'reserved'">
                                <div class="text-xs text-orange-600 font-medium mt-2">
                                    <i class="fas fa-clock mr-1"></i>
                                    Reserved for guest
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
                    <button x-show="selectedTable && orderId" 
                            @click="confirmTableSelection()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                        <i class="fas fa-check mr-2"></i>
                        Confirm Table <span x-text="selectedTable?.number"></span>
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
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                            <div class="relative">
                                <input type="text" 
                                       x-model="customerInfo.name" 
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
                customerSuggestions: [], // New for customer suggestions
                showCustomerSuggestions: false, // New for customer suggestions
                
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
                    let classes = 'table-item relative p-6 cursor-pointer text-center';
                    
                    // Add status-specific classes
                    switch (table.status) {
                        case 'occupied':
                            classes += ' occupied';
                            if (table.status === 'occupied') {
                                classes += ' cursor-not-allowed';
                            }
                            break;
                        case 'reserved':
                            classes += ' reserved';
                            break;
                        default:
                            classes += ' available';
                    }
                    
                    // Add selection ring
                    if (this.selectedTable && this.selectedTable.id === table.id) {
                        classes += ' ring-4 ring-blue-400 ring-opacity-50';
                    }
                    
                    return classes;
                },
                
                getTableImage(table) {
                    // SVG data URLs for each status with beautiful illustrations
                    switch (table.status) {
                        case 'available':
                            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Ccircle cx='100' cy='60' r='35' fill='%23c7f5c7' stroke='%23333' stroke-width='4'/%3E%3Cpath d='M75 85 L90 95 L125 65' stroke='%23333' stroke-width='6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3Crect x='50' y='110' width='100' height='15' rx='7' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3Crect x='60' y='125' width='80' height='8' fill='%2390c695' stroke='%23333' stroke-width='2'/%3E%3Crect x='70' y='133' width='12' height='50' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3Crect x='118' y='133' width='12' height='50' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3C/svg%3E";
                        case 'occupied':
                            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect x='50' y='80' width='100' height='15' rx='7' fill='%23ffb366' stroke='%23333' stroke-width='3'/%3E%3Crect x='60' y='95' width='80' height='8' fill='%23ffb366' stroke='%23333' stroke-width='2'/%3E%3Crect x='70' y='103' width='12' height='50' fill='%23ffb366' stroke='%23333' stroke-width='3'/%3E%3Crect x='118' y='103' width='12' height='50' fill='%23ffb366' stroke='%23333' stroke-width='3'/%3E%3Ccircle cx='85' cy='45' r='12' fill='%23ffcc99' stroke='%23333' stroke-width='2'/%3E%3Cpath d='M85 57 Q85 65 85 70' stroke='%23333' stroke-width='2' fill='none'/%3E%3Cpath d='M75 65 Q85 75 95 65' stroke='%23333' stroke-width='2' fill='none'/%3E%3Ccircle cx='100' cy='40' r='12' fill='%23ffcc99' stroke='%23333' stroke-width='2'/%3E%3Cpath d='M100 52 Q100 60 100 65' stroke='%23333' stroke-width='2' fill='none'/%3E%3Cpath d='M90 60 Q100 70 110 60' stroke='%23333' stroke-width='2' fill='none'/%3E%3Ccircle cx='115' cy='45' r='12' fill='%23ffcc99' stroke='%23333' stroke-width='2'/%3E%3Cpath d='M115 57 Q115 65 115 70' stroke='%23333' stroke-width='2' fill='none'/%3E%3Cpath d='M105 65 Q115 75 125 65' stroke='%23333' stroke-width='2' fill='none'/%3E%3C/svg%3E";
                        case 'reserved':
                            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Ccircle cx='100' cy='50' r='20' fill='%23ffd700' stroke='%23333' stroke-width='3'/%3E%3Ctext x='100' y='58' text-anchor='middle' font-family='Arial' font-size='24' font-weight='bold' fill='%23333'%3E%24%3C/text%3E%3Crect x='70' y='70' width='60' height='12' rx='2' fill='%23ffb84d' stroke='%23333' stroke-width='2'/%3E%3Ctext x='100' y='80' text-anchor='middle' font-family='Arial' font-size='8' font-weight='bold' fill='%23333'%3ERESERVED%3C/text%3E%3Crect x='50' y='90' width='100' height='15' rx='7' fill='%23ffb84d' stroke='%23333' stroke-width='3'/%3E%3Crect x='60' y='105' width='80' height='8' fill='%23ffb84d' stroke='%23333' stroke-width='2'/%3E%3Crect x='70' y='113' width='12' height='50' fill='%23ffb84d' stroke='%23333' stroke-width='3'/%3E%3Crect x='118' y='113' width='12' height='50' fill='%23ffb84d' stroke='%23333' stroke-width='3'/%3E%3C/svg%3E";
                        default:
                            return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Ccircle cx='100' cy='60' r='35' fill='%23c7f5c7' stroke='%23333' stroke-width='4'/%3E%3Cpath d='M75 85 L90 95 L125 65' stroke='%23333' stroke-width='6' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3Crect x='50' y='110' width='100' height='15' rx='7' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3Crect x='60' y='125' width='80' height='8' fill='%2390c695' stroke='%23333' stroke-width='2'/%3E%3Crect x='70' y='133' width='12' height='50' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3Crect x='118' y='133' width='12' height='50' fill='%2390c695' stroke='%23333' stroke-width='3'/%3E%3C/svg%3E";
                    }
                },
                
                getStatusBadgeClass(table) {
                    switch (table.status) {
                        case 'occupied':
                            return 'status-occupied';
                        case 'reserved':
                            return 'status-reserved';
                        default:
                            return 'status-available';
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
                
                // New functions for customer suggestions
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
                        console.error('Error searching customers:', error);
                        this.customerSuggestions = [];
                        this.showCustomerSuggestions = false;
                    }
                },

                selectCustomer(customer) {
                    this.customerInfo.name = customer.name;
                    this.customerInfo.phone = customer.phone;
                    this.customerSuggestions = [];
                    this.showCustomerSuggestions = false;
                },

                addNewCustomer() {
                    this.customerInfo.name = '';
                    this.customerInfo.phone = '';
                    this.customerSuggestions = [];
                    this.showCustomerSuggestions = false;
                },

                async saveNewCustomer() {
                    if (!this.customerInfo.name || !this.customerInfo.phone) {
                        alert('Customer name and phone number are required.');
                        return;
                    }
                    try {
                        const response = await fetch('/api/customers', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                name: this.customerInfo.name,
                                phone: this.customerInfo.phone
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert('New customer added successfully!');
                            this.customerSuggestions.push(data.customer); // Add to suggestions
                        } else {
                            alert('Error adding new customer: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error saving new customer:', error);
                        alert('Error saving new customer. Please try again.');
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
                        if (!this.selectedTableInfo || !this.selectedTableInfo.current_order || !this.selectedTableInfo.current_order.id) {
                            alert('No order found for this table.');
                            return;
                        }
                        
                        const orderId = this.selectedTableInfo.current_order.id;
                        console.log('Loading order details for order ID:', orderId);
                        
                        const response = await fetch(`/orders/${orderId}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        // Handle both successful and error responses
                        if (data.success === false) {
                            throw new Error(data.message || 'Failed to load order details');
                        }
                        
                        this.orderToClose = data.order;
                        this.orderItems = data.order_items || [];
                        
                        // Set customer info if available from the order
                        if (this.orderToClose && this.orderToClose.customer_name) {
                            this.customerInfo.name = this.orderToClose.customer_name;
                            this.customerInfo.phone = this.orderToClose.customer_phone || '';
                        }
                        
                        // Calculate initial amounts
                        if (this.orderToClose) {
                            this.paymentInfo.totalAmount = parseFloat(this.orderToClose.total_amount || 0);
                            this.paymentInfo.paidAmount = this.paymentInfo.totalAmount;
                            this.calculatePayment();
                        }
                        
                        this.showCloseOrderModal = true;
                    } catch (error) {
                        console.error('Error loading order details:', error);
                        alert('Error loading order details: ' + error.message);
                        // Don't show modal on error
                    }
                },
                
                calculatePayment() {
                    if (!this.orderToClose) {
                        return;
                    }
                    
                    const subtotal = parseFloat(this.orderToClose.subtotal || 0);
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
                    
                    if (!this.orderToClose || !this.orderToClose.id) {
                        alert('No order found to close.');
                        return;
                    }
                    
                    if (!this.selectedTableInfo || !this.selectedTableInfo.number) {
                        alert('No table information found.');
                        return;
                    }
                    
                    try {
                        console.log('Closing order:', this.orderToClose.id);
                        
                        const requestData = {
                            order_id: this.orderToClose.id,
                            table_number: this.selectedTableInfo.number,
                            customer_name: this.customerInfo.name,
                            customer_phone: this.customerInfo.phone || '',
                            payment_method: this.paymentInfo.method,
                            discount_amount: parseFloat(this.paymentInfo.discount || 0),
                            customer_paid: parseFloat(this.paymentInfo.paidAmount),
                            balance_returned: Math.max(0, this.paymentInfo.balance),
                            total_amount: this.paymentInfo.totalAmount
                        };
                        
                        console.log('Request data:', requestData);
                        
                        const response = await fetch('/api/close-order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(requestData)
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
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
                        alert('Error completing order: ' + error.message);
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