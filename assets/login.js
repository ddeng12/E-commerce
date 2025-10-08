document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.getElementById('loading');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            submitLogin();
        }
    });
    
    function validateForm() {
        let isValid = true;
        clearErrors();
        
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        
        if (!email) {
            showError('email_error', 'Email is required');
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showError('email_error', 'Please enter a valid email address');
            isValid = false;
        }
        
        if (!password) {
            showError('password_error', 'Password is required');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    function clearErrors() {
        const errorElements = document.querySelectorAll('.error');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
    }
    
    function submitLogin() {
        submitBtn.disabled = true;
        if (loading) loading.style.display = 'block';
        
        const formData = new FormData(form);
        
        fetch('../actions/login_customer_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (loading) loading.style.display = 'none';
            submitBtn.disabled = false;
            
            if (data.success) {
                showSuccess('general_success', data.message);
                setTimeout(function() {
                    window.location.href = '../index.php';
                }, 1000);
            } else {
                showError('general_error', data.message);
            }
        })
        .catch(error => {
            if (loading) loading.style.display = 'none';
            submitBtn.disabled = false;
            showError('general_error', 'An error occurred. Please try again.');
        });
    }
    
    function showSuccess(elementId, message) {
        const successElement = document.getElementById(elementId);
        if (successElement) {
            successElement.textContent = message;
            successElement.style.display = 'block';
        }
    }
});
