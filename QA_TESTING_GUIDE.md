# QA Testing Guide - Hotel POS System

## Overview
This document provides a comprehensive guide for testing the Hotel POS (Point of Sale) system built with Laravel.

## Test Structure

### 1. Authentication Tests (`tests/Feature/AuthenticationTest.php`)
- ✅ User login/logout functionality
- ✅ Access control for POS system
- ✅ Authentication middleware
- ✅ API authentication endpoints

### 2. POS System Tests (`tests/Feature/POSTest.php`)
- ✅ POS interface display
- ✅ Food item categorization
- ✅ Order creation and validation
- ✅ Customer management integration
- ✅ Order calculations and discounts
- ✅ Order details retrieval

### 3. Table Management Tests (`tests/Feature/TableManagementTest.php`)
- ✅ Table assignment and clearing
- ✅ Table status management
- ✅ Order-table association
- ✅ Table availability validation

### 4. Customer Management Tests (`tests/Feature/CustomerManagementTest.php`)
- ✅ Customer CRUD operations
- ✅ Customer search functionality
- ✅ Customer statistics
- ✅ API customer endpoints

### 5. Order Management Tests (`tests/Feature/OrderManagementTest.php`)
- ✅ Order CRUD operations
- ✅ Order search and filtering
- ✅ Order status management
- ✅ Order calculations
- ✅ Order number generation

### 6. Printer Functionality Tests (`tests/Feature/PrinterTest.php`)
- ✅ Thermal invoice printing
- ✅ Web invoice generation
- ✅ Invoice download functionality
- ✅ Print data validation

### 7. Integration Tests (`tests/Feature/IntegrationTest.php`)
- ✅ Complete order workflow
- ✅ Customer-order association
- ✅ Table-order workflow
- ✅ End-to-end scenarios

### 8. Unit Tests (`tests/Unit/OrderTest.php`)
- ✅ Model relationships
- ✅ Data validation
- ✅ Business logic
- ✅ Helper methods

## Running Tests

### Prerequisites
1. Ensure all dependencies are installed:
```bash
composer install
npm install
```

2. Set up testing environment:
```bash
cp .env.example .env.testing
php artisan key:generate --env=testing
```

3. Configure testing database in `.env.testing`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Running All Tests
```bash
# Run all tests
php artisan test

# Run with coverage (if available)
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/AuthenticationTest.php

# Run tests with verbose output
php artisan test --verbose
```

### Running Specific Test Suites
```bash
# Run only feature tests
php artisan test --testsuite=Feature

# Run only unit tests
php artisan test --testsuite=Unit

# Run tests with specific filter
php artisan test --filter="Authentication"
```

## Test Coverage Areas

### Functional Testing
1. **User Authentication**
   - Login with valid/invalid credentials
   - Session management
   - Access control
   - Logout functionality

2. **POS Operations**
   - Menu display and navigation
   - Order creation and modification
   - Payment processing
   - Receipt generation

3. **Table Management**
   - Table assignment
   - Status tracking
   - Order association
   - Table clearing

4. **Customer Management**
   - Customer registration
   - Customer search
   - Order history
   - Customer statistics

5. **Order Processing**
   - Order creation
   - Item management
   - Price calculations
   - Status updates

6. **Printing System**
   - Thermal printing
   - Web invoice generation
   - PDF download
   - Print data formatting

### Integration Testing
1. **End-to-End Workflows**
   - Complete order process
   - Customer registration to order
   - Table assignment to payment
   - Print to completion

2. **Data Flow**
   - Database consistency
   - Relationship integrity
   - Transaction handling
   - Error recovery

### Performance Testing
1. **Response Times**
   - Page load times
   - API response times
   - Database query performance

2. **Concurrent Users**
   - Multiple user sessions
   - Data consistency
   - Resource usage

## Test Data Management

### Factories
All models have corresponding factories for generating test data:
- `UserFactory` - User accounts
- `CategoryFactory` - Food categories
- `FoodItemFactory` - Menu items
- `OrderFactory` - Orders
- `OrderItemFactory` - Order items
- `CustomerFactory` - Customers
- `TableFactory` - Tables

### Seeders
Test data can be seeded using:
```bash
php artisan db:seed --class=TestDataSeeder
```

## Common Test Scenarios

### 1. New Order Creation
```php
// Test creating a new order with multiple items
$orderData = [
    'order_type' => 'dine_in',
    'items' => [
        ['food_item_id' => 1, 'quantity' => 2],
        ['food_item_id' => 2, 'quantity' => 1]
    ],
    'payment_method' => 'cash',
    'customer_name' => 'John Doe'
];
```

### 2. Table Assignment
```php
// Test assigning a table to a customer
$tableData = [
    'table_number' => 'T1',
    'customer_name' => 'John Doe',
    'customer_phone' => '1234567890'
];
```

### 3. Customer Search
```php
// Test customer search functionality
$searchQuery = 'John';
$response = $this->get("/customers/search?q={$searchQuery}");
```

## Error Handling Tests

### 1. Validation Errors
- Required field validation
- Data type validation
- Business rule validation

### 2. Database Errors
- Connection failures
- Constraint violations
- Transaction rollbacks

### 3. Authentication Errors
- Invalid credentials
- Expired sessions
- Unauthorized access

## Security Testing

### 1. Authentication
- Password security
- Session management
- Access control

### 2. Data Protection
- Input validation
- SQL injection prevention
- XSS protection

### 3. Authorization
- Role-based access
- Resource permissions
- API security

## Performance Benchmarks

### Expected Response Times
- Page loads: < 2 seconds
- API responses: < 500ms
- Database queries: < 100ms

### Load Testing
- Concurrent users: 10+
- Orders per minute: 50+
- Database connections: 20+

## Reporting

### Test Results
Tests generate detailed reports including:
- Pass/fail status
- Execution time
- Coverage metrics
- Error details

### Coverage Reports
```bash
# Generate coverage report
php artisan test --coverage-html coverage/
```

## Continuous Integration

### GitHub Actions
Tests are automatically run on:
- Pull requests
- Push to main branch
- Scheduled runs

### Local Development
```bash
# Run tests before committing
./vendor/bin/pest

# Run tests with specific environment
APP_ENV=testing php artisan test
```

## Troubleshooting

### Common Issues
1. **Database Connection**
   - Check `.env.testing` configuration
   - Ensure SQLite is available
   - Verify database permissions

2. **Factory Issues**
   - Check model relationships
   - Verify factory definitions
   - Ensure dependencies are met

3. **Test Failures**
   - Check test data setup
   - Verify assertions
   - Review error messages

### Debug Mode
```bash
# Run tests with debug output
php artisan test --verbose --stop-on-failure
```

## Maintenance

### Regular Tasks
1. Update test data
2. Review test coverage
3. Update test scenarios
4. Performance monitoring

### Test Data Cleanup
```bash
# Clean test database
php artisan migrate:fresh --env=testing
```

## Conclusion

This comprehensive testing suite ensures the Hotel POS system is robust, reliable, and ready for production use. Regular testing helps maintain code quality and prevents regressions.

For questions or issues, please refer to the Laravel testing documentation or contact the development team. 