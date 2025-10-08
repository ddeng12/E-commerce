document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.getElementById('loading');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            submitRegistration();
        }
    });
    
    function validateForm() {
        let isValid = true;
        clearErrors();
        
        const fullName = document.getElementById('full_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const country = document.getElementById('country').value.trim();
        const city = document.getElementById('city').value.trim();
        const contactNumber = document.getElementById('contact_number').value.trim();
        
        if (!fullName) {
            showError('full_name_error', 'Full name is required');
            isValid = false;
        } else if (!/^[a-zA-Z\s]{2,50}$/.test(fullName)) {
            showError('full_name_error', 'Full name must be 2-50 characters, letters and spaces only');
            isValid = false;
        }
        
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
        } else if (!/^.{6,50}$/.test(password)) {
            showError('password_error', 'Password must be 6-50 characters');
            isValid = false;
        }
        
        if (!country) {
            showError('country_error', 'Country is required');
            isValid = false;
        }
        
        if (!city) {
            showError('city_error', 'City is required');
            isValid = false;
        } else if (!/^[a-zA-Z\s]{2,50}$/.test(city)) {
            showError('city_error', 'City must be 2-50 characters, letters and spaces only');
            isValid = false;
        }
        
        if (!contactNumber) {
            showError('contact_number_error', 'Contact number is required');
            isValid = false;
        } else if (!/^[0-9+\-\s()]{7,20}$/.test(contactNumber)) {
            showError('contact_number_error', 'Please enter a valid contact number');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
    
    function clearErrors() {
        const errorElements = document.querySelectorAll('.error');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
    }
    
    function submitRegistration() {
        submitBtn.disabled = true;
        loading.style.display = 'block';
        
        const formData = new FormData(form);
        
        fetch('../actions/register_customer_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            submitBtn.disabled = false;
            
            if (data.success) {
                showSuccess('general_success', data.message);
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                showError('general_error', data.message);
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            submitBtn.disabled = false;
            showError('general_error', 'An error occurred. Please try again.');
        });
    }
    
    function showSuccess(elementId, message) {
        const successElement = document.getElementById(elementId);
        successElement.textContent = message;
        successElement.style.display = 'block';
    }
});