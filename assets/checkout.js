/**
 * Checkout JavaScript
 * Manages the payment modal and checkout process
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeCheckout();
});

function initializeCheckout() {
    bindPaymentTriggers();
    attachModalEventHandlers();
}

function bindPaymentTriggers() {
    const triggerIds = ['completePaymentBtn', 'simulatePaymentBtn'];
    triggerIds.forEach(id => {
        const trigger = document.getElementById(id);
        if (trigger && !trigger.dataset.initialized) {
            trigger.addEventListener('click', function(event) {
                event.preventDefault();
                showPaymentModal();
            });
            trigger.dataset.initialized = 'true';
        }
    });
}

function attachModalEventHandlers() {
    const modal = document.getElementById('paymentModal');
    if (!modal) {
        return;
    }

    const closeButtons = modal.querySelectorAll('.modal-close, .modal-cancel');
    closeButtons.forEach(button => {
        if (!button.dataset.initialized) {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                closePaymentModal();
            });
            button.dataset.initialized = 'true';
        }
    });

    const confirmBtn = modal.querySelector('#confirmPaymentBtn');
    if (confirmBtn && !confirmBtn.dataset.initialized) {
        confirmBtn.addEventListener('click', function(event) {
            event.preventDefault();
            processCheckout();
        });
        confirmBtn.dataset.initialized = 'true';
    }

    if (!modal.dataset.initialized) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePaymentModal();
            }
        });
        modal.dataset.initialized = 'true';
    }

    if (!document.body.dataset.checkoutKeyListener) {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePaymentModal();
            }
        });
        document.body.dataset.checkoutKeyListener = 'true';
    }
}

/**
 * Show the payment modal
 */
function showPaymentModal() {
    let modal = document.getElementById('paymentModal');
    if (!modal) {
        createPaymentModal();
        modal = document.getElementById('paymentModal');
    }

    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        attachModalEventHandlers();
    }
}

/**
 * Close the payment modal
 */
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

/**
 * Create the payment modal if it doesn't exist
 */
function createPaymentModal() {
    const modalHTML = `
        <div id="paymentModal" class="payment-modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Confirm Payment</h2>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Please confirm that payment has been completed for this order.</p>
                    <div class="payment-info">
                        <p><strong>Next step:</strong> Select “Confirm Payment” to finalize your purchase.</p>
                        <p><strong>Need to review?</strong> You can cancel to go back to your cart.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary modal-cancel">Cancel</button>
                    <button class="btn btn-primary" id="confirmPaymentBtn">Confirm Payment</button>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

/**
 * Process the checkout after payment confirmation
 */
function processCheckout() {
    const confirmBtn = document.getElementById('confirmPaymentBtn');

    // Show loading state
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';
    }

    // Disable cancel button
    const cancelBtn = document.querySelector('.modal-cancel');
    if (cancelBtn) {
        cancelBtn.disabled = true;
    }

    fetch('../actions/process_checkout_action.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closePaymentModal();
            showSuccessMessage(data);
            setTimeout(() => {
                window.location.href = 'checkout.php?success=1&order_ref=' + encodeURIComponent(data.order_reference);
            }, 2000);
        } else {
            showErrorMessage(data.message || 'Checkout failed. Please try again.');
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm Payment';
            }
            if (cancelBtn) {
                cancelBtn.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Error processing checkout. Please try again.');
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm Payment';
        }
        if (cancelBtn) {
                cancelBtn.disabled = false;
        }
    });
}

/**
 * Show success message with order details
 */
function showSuccessMessage(data) {
    const messageHTML = `
        <div id="checkoutSuccessMessage" class="checkout-success-message">
            <div class="success-content">
                <div class="success-icon">✓</div>
                <h2>Order Placed Successfully!</h2>
                <p><strong>Order Reference:</strong> ${data.order_reference}</p>
                <p><strong>Transaction Reference:</strong> ${data.transaction_reference}</p>
                <p><strong>Total Amount:</strong> $${parseFloat(data.total_amount).toFixed(2)}</p>
                <p>Thank you for your purchase!</p>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', messageHTML);
}

/**
 * Show error message
 */
function showErrorMessage(message) {
    const existingMessage = document.querySelector('.checkout-error-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    const messageHTML = `
        <div class="checkout-error-message">
            <div class="error-content">
                <div class="error-icon">✗</div>
                <h3>Checkout Failed</h3>
                <p>${message}</p>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', messageHTML);

    setTimeout(() => {
        const errorMsg = document.querySelector('.checkout-error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }, 5000);
}

