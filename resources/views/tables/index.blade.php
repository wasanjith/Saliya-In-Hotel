<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saliya In Hotel - Tables Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
                   <style>
          .table-card {
              transition: all 0.3s ease;
              position: relative;
          }
          .table-available {
              border-color: #10b981;
              background-color: #f0fdf4;
          }
          .table-occupied {
              border-color: #ef4444;
              background-color: #fef2f2;
          }
          .table-reserved {
              border-color: #f59e0b;
              background-color: #fffbeb;
          }
          .table-maintenance {
              border-color: #6b7280;
              background-color: #f9fafb;
          }
          .table-button {
              position: relative;
              z-index: 30;
              cursor: pointer;
          }
          .table-card button {
              cursor: pointer;
              position: relative;
              z-index: 30;
          }
      </style>
</head>
<body class="bg-gray-100" x-data="tablesSystem()">
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
                    <a href="{{ route('pos.index') }}" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200 cursor-pointer">
                        <i class="fas fa-home mr-3"></i>
                        <span>Home</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200 cursor-pointer">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200 cursor-pointer">
                        <i class="fas fa-shopping-bag mr-3"></i>
                        <span>Orders</span>
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200 cursor-pointer">
                        <i class="fas fa-users mr-3"></i>
                        <span>Customers</span>
                    </a>
                    <a href="{{ route('tables.index') }}" class="flex items-center px-4 py-3 text-blue-400 bg-blue-900 rounded-lg cursor-pointer">
                        <i class="fas fa-table mr-3"></i>
                        <span>Tables</span>
                    </a>
                </div>
            </nav>
            
            <!-- Logout -->
            <div class="mt-auto p-4">
                <a href="#" class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 rounded-lg transition-colors duration-200 cursor-pointer">
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
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Tables Management</h1>
                        <p class="text-gray-600">Manage restaurant tables and seating</p>
                    </div>
                    
                    <!-- Right Icons -->
                    <div class="flex items-center space-x-4">
                        <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200 cursor-pointer">
                            <i class="fas fa-bell text-xl"></i>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200 cursor-pointer">
                            <i class="fas fa-cog text-xl"></i>
                        </button>
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center cursor-pointer">
                            <span class="text-white font-medium">A</span>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <div class="flex-1 p-6">
                <!-- Status Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Available</p>
                                <p class="text-2xl font-bold text-gray-900" x-text="availableCount"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Occupied</p>
                                <p class="text-2xl font-bold text-gray-900" x-text="occupiedCount"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Reserved</p>
                                <p class="text-2xl font-bold text-gray-900" x-text="reservedCount"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tools text-gray-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Maintenance</p>
                                <p class="text-2xl font-bold text-gray-900" x-text="maintenanceCount"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tables Grid -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Restaurant Tables</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <template x-for="table in tables" :key="table.id">
                                <div class="table-card border-2 rounded-lg p-4" 
                                     :class="{
                                         'table-available': table.status === 'available',
                                         'table-occupied': table.status === 'occupied',
                                         'table-reserved': table.status === 'reserved',
                                         'table-maintenance': table.status === 'maintenance'
                                     }">
                                    <div class="flex justify-center mb-4">
                                        <svg viewBox="0 0 64 64" class="h-16 w-16" :class="{
                                            'text-emerald-500': table.status === 'available',
                                            'text-red-500': table.status === 'occupied',
                                            'text-amber-500': table.status === 'reserved',
                                            'text-gray-500': table.status === 'maintenance'
                                        }">
                                            <path d="M58,22H6a2,2,0,0,0-2,2v4a2,2,0,0,0,2,2H58a2,2,0,0,0,2-2V24A2,2,0,0,0,58,22Z" fill="currentColor"/>
                                            <rect x="10" y="30" width="6" height="24" fill="currentColor"/>
                                            <rect x="48" y="30" width="6" height="24" fill="currentColor"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-semibold text-lg" x-text="table.name"></h3>
                                        <span class="text-sm font-medium px-2 py-1 rounded-full"
                                              :class="{
                                                  'bg-green-100 text-green-800': table.status === 'available',
                                                  'bg-red-100 text-red-800': table.status === 'occupied',
                                                  'bg-yellow-100 text-yellow-800': table.status === 'reserved',
                                                  'bg-gray-100 text-gray-800': table.status === 'maintenance'
                                              }"
                                              x-text="table.status.charAt(0).toUpperCase() + table.status.slice(1)"></span>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-chair mr-2"></i>
                                            <span x-text="table.capacity + ' seats'"></span>
                                        </div>
                                        
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-map-marker-alt mr-2"></i>
                                            <span x-text="table.location"></span>
                                        </div>
                                        
                                                                                 <template x-if="table.current_order">
                                             <div class="flex items-center text-sm text-red-600">
                                                 <i class="fas fa-receipt mr-2"></i>
                                                 <span x-text="'Order: ' + table.current_order.order_number"></span>
                                             </div>
                                         </template>
                                         
                                         
                                    </div>
                                    
                                                                                                             <div class="mt-4 flex space-x-2">
                                        <button type="button"
                                                x-show="table.status === 'available'"
                                                @click="assignOrder(table)" 
                                                :disabled="table.assigning"
                                                class="table-button flex-1 bg-blue-500 text-white py-2 px-3 rounded text-sm font-medium hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                            <span x-show="!table.assigning">Assign Order</span>
                                            <span x-show="table.assigning" class="flex items-center">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                                Assigning...
                                            </span>
                                        </button>
                                        
                                        <button type="button"
                                                x-show="table.status === 'occupied'"
                                                @click="viewOrder(table)" 
                                                class="table-button flex-1 bg-green-500 text-white py-2 px-3 rounded text-sm font-medium hover:bg-green-600 transition-colors duration-200">
                                            View Order
                                        </button>
                                        
                                        <button type="button"
                                                @click="changeStatus(table)" 
                                                class="table-button bg-gray-500 text-white py-2 px-3 rounded text-sm font-medium hover:bg-gray-600 transition-colors duration-200">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Status Change Modal -->
    <div x-show="showStatusModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Change Table Status</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Table</label>
                        <p class="text-gray-900" x-text="selectedTable ? selectedTable.name : ''"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="newStatus" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="reserved">Reserved</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex space-x-3 mt-6">
                    <button @click="updateTableStatus" 
                            class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition-colors duration-200 cursor-pointer">
                        Update
                    </button>
                    <button @click="showStatusModal = false" 
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition-colors duration-200 cursor-pointer">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Details Modal -->
    <div x-show="showOrderModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-6 border max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Order Details</h3>
                    <button @click="showOrderModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <template x-if="selectedTable && selectedTable.current_order">
                    <div>
                        <!-- Order Info -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Order Number</p>
                                    <p class="font-semibold text-lg" x-text="selectedTable.current_order.order_number"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Table</p>
                                    <p class="font-semibold text-lg" x-text="selectedTable.name"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                          :class="{
                                              'bg-yellow-100 text-yellow-800': selectedTable.current_order.status === 'pending',
                                              'bg-blue-100 text-blue-800': selectedTable.current_order.status === 'preparing',
                                              'bg-green-100 text-green-800': selectedTable.current_order.status === 'completed'
                                          }"
                                          x-text="selectedTable.current_order.status.charAt(0).toUpperCase() + selectedTable.current_order.status.slice(1)"></span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Order Type</p>
                                    <p class="font-semibold" x-text="selectedTable.current_order.order_type"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-3">Ordered Items</h4>
                            <div class="bg-white border rounded-lg overflow-hidden">
                                <div class="overflow-y-auto max-h-60">
                                    <template x-if="selectedTable && selectedTable.current_order && selectedTable.current_order.order_items && selectedTable.current_order.order_items.length > 0">
                                        <div>
                                            <template x-for="(item, index) in selectedTable.current_order.order_items" :key="index">
                                                <div class="flex justify-between items-center p-4 border-b border-gray-100 last:border-b-0">
                                                    <div class="flex-1">
                                                        <h5 class="font-medium text-gray-900" x-text="item.item_name || item.food_item_name || item.name || ('Item ' + (index + 1))"></h5>
                                                        <p class="text-sm text-gray-600">
                                                            <span x-text="'Qty: ' + (item.quantity || 1)"></span>
                                                            <span class="mx-2">•</span>
                                                            <span x-text="'₨' + parseFloat(item.unit_price || item.price || 0).toFixed(2) + ' each'"></span>
                                                        </p>
                                                        <template x-if="item.notes">
                                                            <p class="text-xs text-gray-500 mt-1" x-text="'Note: ' + item.notes"></p>
                                                        </template>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="font-semibold text-gray-900" 
                                                           x-text="'₨' + parseFloat(
                                                              item.total_price || 
                                                              (item.unit_price && item.quantity ? item.unit_price * item.quantity : 0) || 
                                                              (item.price && item.quantity ? item.price * item.quantity : 0) || 
                                                              0
                                                           ).toFixed(2)"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!selectedTable || !selectedTable.current_order || !selectedTable.current_order.order_items || selectedTable.current_order.order_items.length === 0">
                                        <div class="p-6 text-center">
                                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mb-2"></i>
                                                <h5 class="text-lg font-semibold text-yellow-800 mb-2">Order Created - Items Needed</h5>
                                                <p class="text-yellow-700 text-sm mb-2">This order has been assigned to the table but no food items have been added yet.</p>
                                                <p class="text-xs text-yellow-600" x-show="selectedTable && selectedTable.current_order">Order #<span x-text="selectedTable.current_order.order_number"></span></p>
                                            </div>
                                            
                                            <div class="space-y-3">
                                                <button x-show="selectedTable && selectedTable.current_order" 
                                                        @click="window.location.href = `{{ route('pos.index') }}?table_id=${selectedTable.id}&order_id=${selectedTable.current_order.id}`" 
                                                        class="w-full bg-blue-500 text-white py-3 px-4 rounded-md text-sm font-medium hover:bg-blue-600 transition-colors duration-200 flex items-center justify-center">
                                                    <i class="fas fa-plus mr-2"></i>
                                                    Add Items to Order
                                                </button>
                                                
                                                <p class="text-xs text-gray-500">Click the button above to open the POS system and add food items to this order.</p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-3">Order Summary</h4>
                            <div class="space-y-2">
                                <template x-if="selectedTable && selectedTable.current_order">
                                    <div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Subtotal</span>
                                            <span class="font-medium" x-text="'₨' + parseFloat(selectedTable.current_order.subtotal || 0).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Tax (14%)</span>
                                            <span class="font-medium" x-text="'₨' + parseFloat(selectedTable.current_order.tax_amount || 0).toFixed(2)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Discount (12%)</span>
                                            <span class="font-medium text-green-600" x-text="'-₨' + parseFloat(selectedTable.current_order.discount_amount || 0).toFixed(2)"></span>
                                        </div>
                                        <hr class="my-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                                            <span class="text-xl font-bold text-green-600" 
                                                  x-text="'₨' + parseFloat(
                                                    selectedTable.current_order.total_amount || 
                                                    (parseFloat(selectedTable.current_order.subtotal || 0) + 
                                                     parseFloat(selectedTable.current_order.tax_amount || 0) - 
                                                     parseFloat(selectedTable.current_order.discount_amount || 0))
                                                  ).toFixed(2)"></span>
                                        </div>
                                        <template x-if="parseFloat(selectedTable.current_order.total_amount || 0) === 0">
                                            <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                                <div class="flex items-center justify-center text-amber-700">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    <span class="text-sm font-medium">Order total will appear after adding items</span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!selectedTable || !selectedTable.current_order">
                                    <div class="text-center text-gray-500 py-4">
                                        <p>No order data available</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-3">
                            <template x-if="selectedTable && selectedTable.current_order && selectedTable.current_order.order_items && selectedTable.current_order.order_items.length > 0">
                                <button @click="completeOrder" 
                                        class="flex-1 bg-green-500 text-white py-3 px-4 rounded-md hover:bg-green-600 transition-colors duration-200 cursor-pointer font-medium">
                                    <i class="fas fa-check mr-2"></i>
                                    Pay and Finish Order
                                </button>
                            </template>
                            
                            <template x-if="!selectedTable || !selectedTable.current_order || !selectedTable.current_order.order_items || selectedTable.current_order.order_items.length === 0">
                                <button disabled 
                                        class="flex-1 bg-gray-400 text-white py-3 px-4 rounded-md cursor-not-allowed font-medium opacity-50">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Add Items First
                                </button>
                            </template>
                            
                            <button @click="showOrderModal = false" 
                                    class="bg-gray-300 text-gray-700 py-3 px-4 rounded-md hover:bg-gray-400 transition-colors duration-200 cursor-pointer">
                                Close
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Loading state -->
                <template x-if="loadingOrder || !selectedTable || !selectedTable.current_order">
                    <div class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">Loading order details...</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 z-50">
        <div class="rounded-lg px-4 py-3 shadow-lg"
             :class="{
                 'bg-green-500 text-white': toastType === 'success',
                 'bg-red-500 text-white': toastType === 'error',
                 'bg-blue-500 text-white': toastType === 'info'
             }">
            <div class="flex items-center">
                <i class="fas mr-2"
                   :class="{
                       'fa-check-circle': toastType === 'success',
                       'fa-exclamation-circle': toastType === 'error',
                       'fa-info-circle': toastType === 'info'
                   }"></i>
                <span x-text="toastMessage"></span>
            </div>
        </div>
    </div>
    
    <script>
        function tablesSystem() {
            console.log('Tables system initialized');
            const tablesData = @json($tables);
            
            // Initialize tables with additional properties
            tablesData.forEach(table => {
                table.assigning = false; // Initialize assigning state
            });
            
            console.log('Tables data:', tablesData);
            return {
                tables: tablesData,
                showStatusModal: false,
                showOrderModal: false,
                selectedTable: null,
                newStatus: 'available',
                showToast: false,
                toastMessage: '',
                toastType: 'success',
                loadingOrder: false,
                
                get availableCount() {
                    return this.tables.filter(t => t.status === 'available').length;
                },
                
                get occupiedCount() {
                    return this.tables.filter(t => t.status === 'occupied').length;
                },
                
                get reservedCount() {
                    return this.tables.filter(t => t.status === 'reserved').length;
                },
                
                get maintenanceCount() {
                    return this.tables.filter(t => t.status === 'maintenance').length;
                },
                

                
                showToastMessage(message, type = 'success') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;
                    
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                },
                
                async assignOrder(table) {
                    console.log('Assign order clicked for table:', table);
                    
                    // Prevent multiple clicks
                    if (table.assigning) {
                        console.log('Already processing, ignoring click');
                        return;
                    }
                    
                    // Set loading state
                    table.assigning = true;
                    this.$nextTick(); // Force Alpine to update the UI
                    
                    try {
                        const response = await fetch(`/tables/${table.id}/assign-order`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            credentials: 'same-origin'
                        });
                        
                        if (response.ok) {
                            const result = await response.json();
                            if (result.success) {
                                // Update table status locally
                                table.status = 'occupied';
                                table.current_order = {
                                    ...result.order,
                                    order_items: [], // Initially empty
                                    subtotal: 0,
                                    tax_amount: 0,
                                    discount_amount: 0,
                                    total_amount: 0
                                };
                                
                                // Update the tables array to reflect the change
                                const tableIndex = this.tables.findIndex(t => t.id === table.id);
                                if (tableIndex !== -1) {
                                    this.tables[tableIndex].status = 'occupied';
                                    this.tables[tableIndex].current_order = table.current_order;
                                }
                                
                                // Show success message
                                this.showToastMessage('Order assigned successfully! Redirecting to POS...', 'success');
                                
                                // Redirect to POS with table selected after a short delay
                                setTimeout(() => {
                                    window.location.href = `{{ route('pos.index') }}?table_id=${table.id}&order_id=${result.order.id}`;
                                }, 1500);
                            } else {
                                this.showToastMessage(result.message || 'Failed to assign order', 'error');
                            }
                        } else {
                            const error = await response.json();
                            this.showToastMessage(error.message || 'Failed to assign order', 'error');
                        }
                    } catch (error) {
                        console.error('Error assigning order:', error);
                        this.showToastMessage('Error assigning order to table', 'error');
                    } finally {
                        // Clear loading state
                        table.assigning = false;
                        this.$nextTick(); // Force Alpine to update the UI
                    }
                },
                
                async viewOrder(table) {
                    console.log('ViewOrder called for table:', table);
                    
                    if (!table.current_order) {
                        this.showToastMessage('No active order for this table.', 'error');
                        return;
                    }

                    this.selectedTable = { ...table }; // Create a copy to avoid reference issues
                    this.loadingOrder = true;
                    this.showOrderModal = true; // Show modal immediately to show loading state
                    
                    const orderId = table.current_order.id;
                    console.log('Fetching order details for ID:', orderId);

                    try {
                        // Fetch the detailed order data with items
                        const response = await fetch(`/orders/${orderId}`);
                        if (response.ok) {
                            const orderDetails = await response.json();
                            // Extract order items from the response with better error handling
                            let orderItems = [];
                            
                            // Check different possible formats for order items
                            if (orderDetails.order_items) {
                                if (Array.isArray(orderDetails.order_items)) {
                                    orderItems = orderDetails.order_items;
                                } else if (typeof orderDetails.order_items === 'string') {
                                    try {
                                        orderItems = JSON.parse(orderDetails.order_items);
                                    } catch (e) {
                                        console.error('Error parsing order_items JSON:', e);
                                    }
                                }
                            }
                            
                            // Use the order details as returned from the backend
                            const completeOrderDetails = {
                                ...orderDetails,
                                id: orderDetails.id,
                                order_number: orderDetails.order_number || table.current_order.order_number,
                                status: orderDetails.status || 'pending',
                                order_type: orderDetails.order_type || 'dine_in',
                                order_items: orderItems,
                                subtotal: parseFloat(orderDetails.subtotal || 0),
                                tax_amount: parseFloat(orderDetails.tax_amount || 0),
                                discount_amount: parseFloat(orderDetails.discount_amount || 0),
                                total_amount: parseFloat(orderDetails.total_amount || 0)
                            };
                            
                            // Update the selected table with complete order details
                            this.selectedTable.current_order = completeOrderDetails;
                            
                            // Force Alpine.js to update the view
                            this.$nextTick();
                        } else {
                            console.error('Failed to fetch order details. Status:', response.status);
                            const error = await response.text();
                            console.error('Error response:', error);
                            this.showToastMessage('Failed to fetch order details.', 'error');
                            this.showOrderModal = false;
                        }
                    } catch (error) {
                        console.error('Error fetching order details:', error);
                        this.showToastMessage('An error occurred while fetching order details.', 'error');
                        this.showOrderModal = false;
                    } finally {
                        this.loadingOrder = false;
                    }
                },

                async completeOrder() {
                    if (!this.selectedTable || !this.selectedTable.current_order) return;

                    const orderId = this.selectedTable.current_order.id;

                    try {
                        const response = await fetch(`/orders/${orderId}/complete`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        if (response.ok) {
                            const result = await response.json();
                            if (result.success) {
                                // Update the table in the tables array
                                const tableIndex = this.tables.findIndex(t => t.id === this.selectedTable.id);
                                if (tableIndex !== -1) {
                                    this.tables[tableIndex].status = 'available';
                                    this.tables[tableIndex].current_order = null;
                                }
                                
                                // Update selected table
                                this.selectedTable.status = 'available';
                                this.selectedTable.current_order = null;
                                
                                this.showOrderModal = false;
                                this.showToastMessage('Order completed successfully!', 'success');
                            } else {
                                this.showToastMessage(result.message || 'Failed to complete order.', 'error');
                            }
                        } else {
                            this.showToastMessage('Failed to complete order.', 'error');
                        }
                    } catch (error) {
                        this.showToastMessage('An error occurred while completing the order.', 'error');
                    }
                },
                
                changeStatus(table) {
                    console.log('Change status clicked for table:', table);
                    this.selectedTable = table;
                    this.newStatus = table.status;
                    this.showStatusModal = true;
                },
                
                async updateTableStatus() {
                    if (!this.selectedTable) return;
                    
                    try {
                        const response = await fetch(`/tables/${this.selectedTable.id}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                status: this.newStatus
                            })
                        });
                        
                        if (response.ok) {
                            this.selectedTable.status = this.newStatus;
                            this.showStatusModal = false;
                            this.showToastMessage('Table status updated successfully', 'success');
                        } else {
                            this.showToastMessage('Failed to update table status', 'error');
                        }
                    } catch (error) {
                        console.error('Error updating table status:', error);
                        this.showToastMessage('Error updating table status', 'error');
                    }
                }
            }
        }
    </script>
</body>
</html> 