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
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Details</h3>
                <div x-if="selectedTable && selectedTable.current_order">
                    <p><strong>Order ID:</strong> <span x-text="selectedTable.current_order.id"></span></p>
                    <p><strong>Total:</strong> <span x-text="selectedTable.current_order.total_amount"></span></p>
                    <p><strong>Status:</strong> <span x-text="selectedTable.current_order.status"></span></p>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button @click="completeOrder" 
                            class="flex-1 bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition-colors duration-200 cursor-pointer">
                        Pay and Finish
                    </button>
                    <button @click="showOrderModal = false" 
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition-colors duration-200 cursor-pointer">
                        Cancel
                    </button>
                </div>
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
                                table.current_order = result.order;
                                
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
                    if (!table.current_order) {
                        this.showToastMessage('No active order for this table.', 'error');
                        return;
                    }

                    this.selectedTable = table;
                    const orderId = table.current_order.id;

                    try {
                        const response = await fetch(`/orders/${orderId}`);
                        if (response.ok) {
                            const orderDetails = await response.json();
                            this.selectedTable.current_order = orderDetails;
                            this.showOrderModal = true;
                        } else {
                            this.showToastMessage('Failed to fetch order details.', 'error');
                        }
                    } catch (error) {
                        this.showToastMessage('An error occurred while fetching order details.', 'error');
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