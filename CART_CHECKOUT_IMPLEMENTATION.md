# Cart Management and Checkout Implementation

## Overview
This document describes the complete cart and checkout workflow implementation for the e-commerce system.

## Design Decision: Cart Management Approach

### Chosen Approach: **Logged-in Users Only**

**Decision:** The cart system is tied exclusively to logged-in users (via `user_id` in the `cart_items` table).

**Rationale:**
1. **Data Persistence**: Cart data is stored in the database and persists across sessions, devices, and browsers.
2. **User Experience**: Users can access their cart from any device after logging in.
3. **Order Processing**: Simplifies the checkout process since user information is already available.
4. **Security**: Prevents cart manipulation and ensures data integrity.
5. **Simplicity**: Reduces complexity by not needing to handle guest sessions, cookies, or IP addresses.

**Alternative Considered:**
- **Guest Carts (via cookies/IP)**: This approach was considered but rejected because:
  - Requires additional session management
  - Cart data is lost when cookies are cleared
  - Cannot easily sync across devices
  - More complex to implement securely

## Implementation Details

### Database Schema

#### New Tables Created:

1. **orders**
   - Stores order information
   - Fields: `id`, `customer_id`, `order_reference`, `total_amount`, `status`, `created_at`, `updated_at`
   - Unique `order_reference` for tracking

2. **orderdetails**
   - Stores individual product items in each order (normalized)
   - Fields: `id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`, `created_at`
   - Links to `orders` and `products` tables

3. **payments**
   - Stores payment information for orders
   - Fields: `id`, `order_id`, `payment_method`, `amount`, `payment_status`, `transaction_reference`, `created_at`
   - Links to `orders` table

### Files Created/Modified

#### Models:
- `models/order_class.php` - Handles all database operations for orders, order details, and payments

#### Controllers:
- `controllers/order_controller.php` - Wraps order_class methods for use by action scripts

#### Actions:
- `actions/update_quantity_action.php` - Updates quantity of cart items
- `actions/empty_cart_action.php` - Empties the entire cart
- `actions/process_checkout_action.php` - Handles the complete checkout workflow

#### JavaScript:
- `assets/cart.js` - Manages cart UI interactions (add, remove, update, empty)
- `assets/checkout.js` - Manages checkout modal and payment simulation

#### Views:
- `views/cart.php` - Complete cart view with quantity controls, empty cart, and checkout button
- `views/checkout.php` - Checkout page with order summary and payment simulation

### Key Features Implemented

#### 1. Cart Management
- ✅ Add products to cart (handles duplicates by incrementing quantity)
- ✅ View all cart items with images, prices, and quantities
- ✅ Update item quantities (increase/decrease buttons + direct input)
- ✅ Remove individual items from cart
- ✅ Empty entire cart
- ✅ Real-time subtotal calculations
- ✅ Dynamic UI updates without full page refresh

#### 2. Duplicate Product Handling
The system automatically handles scenarios where a user adds a product that already exists in their cart:
- Checks if product exists in cart for the user
- If exists: Increments the quantity instead of creating a duplicate
- If not exists: Adds as a new cart item
- Implemented in `cart_class.php::add_to_cart()` method

#### 3. Checkout Process
- ✅ Displays order summary with all cart items
- ✅ Shows total amount calculation
- ✅ Simulated payment modal
- ✅ Processes checkout after payment confirmation:
  1. Generates unique order reference
  2. Creates order in `orders` table
  3. Adds order details to `orderdetails` table
  4. Records payment in `payments` table
  5. Clears the cart
  6. Returns order confirmation with references

#### 4. Order Reference Generation
- Format: `ORD-YYYYMMDD-XXXXXXXX` (e.g., `ORD-20250115-A1B2C3D4`)
- Transaction Reference: `TXN-YYYYMMDDHHMMSS-XXXXXX`
- Ensures uniqueness for tracking

### Workflow

#### Cart to Checkout Flow:
1. User adds products to cart (from product pages)
2. User views cart (`cart.php`)
3. User can update quantities or remove items
4. User clicks "Proceed to Checkout"
5. User reviews order summary on checkout page
6. User clicks "Simulate Payment"
7. Payment modal appears
8. User confirms payment ("Yes, I've paid")
9. System processes checkout:
   - Creates order
   - Adds order details
   - Records payment
   - Clears cart
10. Success message displayed with order reference
11. User can continue shopping

### Error Handling
- All actions return JSON responses with `success` and `message` fields
- User-friendly error messages displayed via modals/alerts
- Database errors are caught and returned appropriately
- Validation for quantity (must be >= 1)
- Cart must not be empty to checkout

### Security Considerations
- All actions require user authentication (session check)
- User can only manage their own cart items
- SQL injection prevention via prepared statements
- Input validation on all user inputs
- CSRF protection via session-based authentication

## Testing Checklist

- [ ] Add product to cart
- [ ] Add same product again (should increment quantity)
- [ ] Update quantity using +/- buttons
- [ ] Update quantity using direct input
- [ ] Remove individual item from cart
- [ ] Empty entire cart
- [ ] Proceed to checkout with items in cart
- [ ] Complete checkout process
- [ ] Verify order created in database
- [ ] Verify order details created
- [ ] Verify payment recorded
- [ ] Verify cart is cleared after checkout
- [ ] Test with empty cart (should redirect)
- [ ] Test without login (should redirect to login)

## Database Setup

Run the SQL from `db.php` to create all necessary tables:
- `orders`
- `orderdetails`
- `payments`

The existing `cart_items` table is already set up.

## Notes

- The system uses a simulated payment process (no actual payment gateway integration)
- Payment method is stored as "simulated" in the database
- Payment status is set to "completed" automatically
- Order status defaults to "pending" (can be extended for admin approval workflow)

