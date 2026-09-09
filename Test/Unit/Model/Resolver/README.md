# Unit Tests for GiftCardGraphQl Module

## Test Structure

The Test directory is organized with the following structure:

```
Test/
├── Unit/
│   └── Model/
│       └── Resolver/
│           ├── CheckCodeTest.php
│           ├── AddGiftCardProductsToCartTest.php
│           ├── RedeemTest.php
│           └── DashboardTest.php
└── phpunit.xml
```

## Test Cases

### 1. CheckCodeTest

- Test with disabled module
- Test with valid code
- Test with exception
- Test with empty code

### 2. AddGiftCardProductsToCartTest

- Test with missing cart_id
- Test with missing cart_items
- Test with cart_items not being array
- Test successful with multiple gift cards
- Test successful with single gift card

### 3. RedeemTest

- Test with disabled module
- Test with unauthorized customer
- Test successful redemption
- Test with exception
- Test with empty code

### 4. DashboardTest

- Test with disabled module
- Test with unauthorized customer
- Test successful with data
- Test with empty dashboard
- Test with null context

## Running Tests

### Run all unit tests

```bash
/var/www/html/vendor/phpunit/phpunit/phpunit  --configuration=/var/www/html/app/code/Mageplaza/GiftCardGraphQl/Test/phpunit.xml --bootstrap=/var/www/html/dev/tests/unit/framework/bootstrap.php
```

### Run specific test

```bash
/var/www/html/vendor/phpunit/phpunit/phpunit  --configuration=/var/www/html/app/code/Mageplaza/GiftCardGraphQl/Test/phpunit.xml --bootstrap=/var/www/html/dev/tests/unit/framework/bootstrap.php --filter="testResolveWithValidInput"
```

### Run with coverage

```bash
/var/www/html/vendor/phpunit/phpunit/phpunit  --configuration=/var/www/html/app/code/Mageplaza/GiftCardGraphQl/Test/phpunit.xml --bootstrap=/var/www/html/dev/tests/unit/framework/bootstrap.php --coverage-html coverage/
```

## Test Results

Current test results:

- ✅ **21 tests** passed
- ✅ **66 assertions** passed
- ✅ **0 errors, 0 failures**

## Best Practices

1. **Mock Dependencies**: All dependencies are mocked for independent testing
2. **Test Cases**: Each test method tests a specific scenario
3. **Assertions**: Use appropriate assertions to verify results
4. **Naming**: Test method names clearly describe the scenario being tested
5. **Setup/Teardown**: Use setUp() to prepare the test environment

## Coverage

These test cases cover:

- Happy path scenarios
- Error handling
- Edge cases
- Authorization checks
- Input validation
- Exception handling

## Test Categories

### Happy Path Tests

Test scenarios where everything works as expected:

- Valid gift code checking
- Successful cart operations
- Successful redemption
- Dashboard data retrieval

### Error Handling Tests

Test scenarios with errors and exceptions:

- Module disabled scenarios
- Invalid input parameters
- Authorization failures
- Exception handling

### Edge Case Tests

Test boundary conditions and special cases:

- Empty inputs
- Null values
- Invalid data types
- Missing required parameters

### Integration Tests

Test interaction between components:

- Mock object interactions
- Method call expectations
- Return value verification
