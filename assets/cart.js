/**
 * Cart Management JavaScript
 * Handles all cart UI interactions: adding, removing, updating, and emptying items
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeCart();
});

function initializeCart() {
    // Set up event listeners for quantity updates
    setupQuantityControls();
    
    // Set up remove item buttons
    setupRemoveButtons();
    
    // Set up empty cart button
    setupEmptyCartButton();
}

function setupQuantityControls() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const quantityButtons = document.querySelectorAll('.quantity-btn');
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const cartItemId = this.dataset.cartItemId;
            const quantity = parseInt(this.value);
            
            if (quantity < 1) {
                this.value = 1;
                return;
            }
            
            updateQuantity(cartItemId, quantity);
        });
        
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.blur();
            }
        });
    });
    
    quantityButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartItemId = this.dataset.cartItemId;
            const action = this.dataset.action;
            const input = document.querySelector(`.quantity-input[data-cart-item-id="${cartItemId}"]`);
            
            if (!input) return;
            
            let currentQuantity = parseInt(input.value) || 1;
            
            if (action === 'increase') {
                currentQuantity += 1;
            } else if (action === 'decrease') {
                currentQuantity = Math.max(1, currentQuantity - 1);
            }
            
            input.value = currentQuantity;
            updateQuantity(cartItemId, currentQuantity);
        });
    });
}

function setupRemoveButtons() {
    const removeButtons = document.querySelectorAll('.btn-remove-item');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartItemId = this.dataset.cartItemId;
            removeItem(cartItemId);
        });
    });
}

function setupEmptyCartButton() {
    const emptyCartBtn = document.getElementById('emptyCartBtn');
    
    if (emptyCartBtn) {
        emptyCartBtn.addEventListener('click', function() {
            emptyCart();
        });
    }
}

/**
 * Update quantity of a cart item
 */
function updateQuantity(cartItemId, quantity) {
    if (!cartItemId || quantity < 1) {
        showMessage('Invalid quantity', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('cart_item_id', cartItemId);
    formData.append('quantity', quantity);
    
    // Show loading state
    const itemRow = document.querySelector(`[data-cart-item-id="${cartItemId}"]`).closest('.cart-item-row');
    if (itemRow) {
        itemRow.style.opacity = '0.6';
        itemRow.style.pointerEvents = 'none';
    }
    
    fetch('../actions/update_quantity_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show updated totals
            location.reload();
        } else {
            showMessage(data.message || 'Failed to update quantity', 'error');
            if (itemRow) {
                itemRow.style.opacity = '1';
                itemRow.style.pointerEvents = 'auto';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating quantity. Please try again.', 'error');
        if (itemRow) {
            itemRow.style.opacity = '1';
            itemRow.style.pointerEvents = 'auto';
        }
    });
}

/**
 * Remove an item from the cart
 */
function removeItem(cartItemId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('cart_item_id', cartItemId);
    
    // Show loading state
    const itemRow = document.querySelector(`[data-cart-item-id="${cartItemId}"]`).closest('.cart-item-row');
    if (itemRow) {
        itemRow.style.opacity = '0.6';
        itemRow.style.pointerEvents = 'none';
    }
    
    fetch('../actions/remove_from_cart_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Item removed from cart', 'success');
            // Remove the item from the UI
            if (itemRow) {
                itemRow.style.transition = 'opacity 0.3s ease';
                itemRow.style.opacity = '0';
                setTimeout(() => {
                    itemRow.remove();
                    // Check if cart is now empty
                    const remainingItems = document.querySelectorAll('.cart-item-row');
                    if (remainingItems.length === 0) {
                        location.reload();
                    } else {
                        location.reload(); // Reload to update totals
                    }
                }, 300);
            } else {
                location.reload();
            }
        } else {
            showMessage(data.message || 'Failed to remove item', 'error');
            if (itemRow) {
                itemRow.style.opacity = '1';
                itemRow.style.pointerEvents = 'auto';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error removing item. Please try again.', 'error');
        if (itemRow) {
            itemRow.style.opacity = '1';
            itemRow.style.pointerEvents = 'auto';
        }
    });
}

/**
 * Empty the entire cart
 */
function emptyCart() {
    if (!confirm('Are you sure you want to empty your cart? This action cannot be undone.')) {
        return;
    }
    
    // Show loading state
    const emptyCartBtn = document.getElementById('emptyCartBtn');
    if (emptyCartBtn) {
        emptyCartBtn.disabled = true;
        emptyCartBtn.textContent = 'Emptying...';
    }
    
    fetch('../actions/empty_cart_action.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Cart emptied successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showMessage(data.message || 'Failed to empty cart', 'error');
            if (emptyCartBtn) {
                emptyCartBtn.disabled = false;
                emptyCartBtn.textContent = 'Empty Cart';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error emptying cart. Please try again.', 'error');
        if (emptyCartBtn) {
            emptyCartBtn.disabled = false;
            emptyCartBtn.textContent = 'Empty Cart';
        }
    });
}

/**
 * Show a message to the user
 */
function showMessage(message, type = 'info') {
    // Remove existing messages
    const existingMessage = document.querySelector('.cart-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `cart-message cart-message-${type}`;
    messageDiv.textContent = message;
    
    // Style the message
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease;
    `;
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#10b981';
    } else if (type === 'error') {
        messageDiv.style.backgroundColor = '#ef4444';
    } else {
        messageDiv.style.backgroundColor = '#3b82f6';
    }
    
    // Add animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Add to page
    document.body.appendChild(messageDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        messageDiv.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => {
            messageDiv.remove();
        }, 300);
    }, 3000);
}

