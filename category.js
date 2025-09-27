document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.getElementById('addCategoryForm');
    const editForm = document.getElementById('editCategoryForm');
    const editModal = document.getElementById('editModal');
    const categoriesList = document.getElementById('categoriesList');
    
    // Load categories on page load
    loadCategories();
    
    // Add category form submission
    addForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateCategoryForm(addForm)) {
            addCategory();
        }
    });
    
    // Edit category form submission
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateCategoryForm(editForm)) {
            updateCategory();
        }
    });
    
    // Close modal when clicking X
    document.querySelector('.close').addEventListener('click', closeEditModal);
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target == editModal) {
            closeEditModal();
        }
    });
    
    function validateCategoryForm(form) {
        let isValid = true;
        clearErrors(form);
        
        const nameInput = form.querySelector('input[name="name"]');
        const name = nameInput.value.trim();
        
        if (!name) {
            showError(form, 'name', 'Category name is required');
            isValid = false;
        } else if (name.length > 100) {
            showError(form, 'name', 'Category name must be 100 characters or less');
            isValid = false;
        } else if (!/^[a-zA-Z0-9\s\-_]+$/.test(name)) {
            showError(form, 'name', 'Category name can only contain letters, numbers, spaces, hyphens, and underscores');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showError(form, fieldName, message) {
        const errorElement = form.querySelector('#' + fieldName + '_error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    function clearErrors(form) {
        const errorElements = form.querySelectorAll('.error');
        errorElements.forEach(element => {
            element.textContent = '';
            element.style.display = 'none';
        });
    }
    
    function showMessage(message, type = 'success') {
        const messageDiv = document.createElement('div');
        messageDiv.className = type;
        messageDiv.textContent = message;
        messageDiv.style.padding = '10px';
        messageDiv.style.margin = '10px 0';
        messageDiv.style.borderRadius = '4px';
        
        if (type === 'success') {
            messageDiv.style.backgroundColor = '#d4edda';
            messageDiv.style.color = '#155724';
            messageDiv.style.border = '1px solid #c3e6cb';
        } else {
            messageDiv.style.backgroundColor = '#f8d7da';
            messageDiv.style.color = '#721c24';
            messageDiv.style.border = '1px solid #f5c6cb';
        }
        
        // Insert message at the top of the page
        const body = document.body;
        body.insertBefore(messageDiv, body.firstChild);
        
        // Remove message after 5 seconds
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
    
    function loadCategories() {
        fetch('fetch_category_action.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCategories(data.data);
                } else {
                    categoriesList.innerHTML = '<p>Error loading categories: ' + data.message + '</p>';
                }
            })
            .catch(error => {
                categoriesList.innerHTML = '<p>Error loading categories</p>';
            });
    }
    
    function displayCategories(categories) {
        if (categories.length === 0) {
            categoriesList.innerHTML = '<p>No categories found. Create your first category!</p>';
            return;
        }
        
        let html = '<table><thead><tr><th>ID</th><th>Name</th><th>Created</th><th>Actions</th></tr></thead><tbody>';
        
        categories.forEach(category => {
            html += `
                <tr>
                    <td>${category.id}</td>
                    <td>${escapeHtml(category.name)}</td>
                    <td>${new Date(category.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn" onclick="editCategory(${category.id}, '${escapeHtml(category.name)}')">Edit</button>
                        <button class="btn btn-danger" onclick="deleteCategory(${category.id})">Delete</button>
                    </td>
                </tr>
            `;
        });
        
        html += '</tbody></table>';
        categoriesList.innerHTML = html;
    }
    
    function addCategory() {
        const formData = new FormData(addForm);
        
        fetch('add_category_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                addForm.reset();
                loadCategories();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('An error occurred while adding the category', 'error');
        });
    }
    
    function updateCategory() {
        const formData = new FormData(editForm);
        
        fetch('update_category_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                closeEditModal();
                loadCategories();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('An error occurred while updating the category', 'error');
        });
    }
    
    window.editCategory = function(id, name) {
        document.getElementById('edit_category_id').value = id;
        document.getElementById('edit_category_name').value = name;
        editModal.style.display = 'block';
    };
    
    window.deleteCategory = function(id) {
        if (confirm('Are you sure you want to delete this category?')) {
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('delete_category_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    loadCategories();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('An error occurred while deleting the category', 'error');
            });
        }
    };
    
    window.closeEditModal = function() {
        editModal.style.display = 'none';
        editForm.reset();
        clearErrors(editForm);
    };
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
