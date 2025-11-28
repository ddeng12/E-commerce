document.addEventListener('DOMContentLoaded', function() {
    loadBrands();
    loadCategories();
    
    // Add brand form submission
    document.getElementById('addBrandForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addBrand();
    });
    
    // Edit brand form submission
    document.getElementById('editBrandForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateBrand();
    });
    
    // Close modal when clicking X
    document.querySelector('.close').addEventListener('click', closeEditModal);
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('editModal');
        if (e.target === modal) {
            closeEditModal();
        }
    });
});

function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayBrands(data.data);
            } else {
                showMessage('Error loading brands: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error loading brands', 'error');
        });
}

function loadCategories() {
    fetch('../actions/fetch_category_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateCategoryDropdowns(data.data);
            } else {
                console.error('Error loading categories:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function populateCategoryDropdowns(categories) {
    const addDropdown = document.getElementById('add_category_id');
    const editDropdown = document.getElementById('edit_category_id');
    
    // Clear existing options
    addDropdown.innerHTML = '<option value="">Select Category</option>';
    editDropdown.innerHTML = '<option value="">Select Category</option>';
    
    categories.forEach(category => {
        const option1 = document.createElement('option');
        option1.value = category.id;
        option1.textContent = category.name;
        addDropdown.appendChild(option1);
        
        const option2 = document.createElement('option');
        option2.value = category.id;
        option2.textContent = category.name;
        editDropdown.appendChild(option2);
    });
}

function displayBrands(brands) {
    const container = document.getElementById('brandsList');
    
    if (brands.length === 0) {
        container.innerHTML = '<p>No brands found.</p>';
        return;
    }
    
    // Group brands by category
    const groupedBrands = {};
    brands.forEach(brand => {
        const categoryName = brand.category_name;
        if (!groupedBrands[categoryName]) {
            groupedBrands[categoryName] = [];
        }
        groupedBrands[categoryName].push(brand);
    });
    
    let html = '<table><thead><tr><th>Category</th><th>Brand Name</th><th>Created</th><th>Actions</th></tr></thead><tbody>';
    
    Object.keys(groupedBrands).sort().forEach(categoryName => {
        groupedBrands[categoryName].forEach(brand => {
            html += `
                <tr>
                    <td>${categoryName}</td>
                    <td>${brand.name}</td>
                    <td>${new Date(brand.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn" onclick="editBrand(${brand.id})">Edit</button>
                        <button class="btn btn-danger" onclick="deleteBrand(${brand.id})">Delete</button>
                    </td>
                </tr>
            `;
        });
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
}

function addBrand() {
    const form = document.getElementById('addBrandForm');
    const formData = new FormData(form);
    
    // Validate form
    const name = formData.get('name').trim();
    const categoryId = formData.get('category_id');
    
    if (!name) {
        showMessage('Brand name is required', 'error');
        return;
    }
    
    if (!categoryId) {
        showMessage('Please select a category', 'error');
        return;
    }
    
    fetch('../actions/add_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            form.reset();
            loadBrands();
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error adding brand', 'error');
    });
}

function editBrand(brandId) {
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const brand = data.data.find(b => b.id == brandId);
                if (brand) {
                    document.getElementById('edit_brand_id').value = brand.id;
                    document.getElementById('edit_brand_name').value = brand.name;
                    document.getElementById('edit_category_id').value = brand.category_id;
                    document.getElementById('editModal').style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error loading brand details', 'error');
        });
}

function updateBrand() {
    const form = document.getElementById('editBrandForm');
    const formData = new FormData(form);
    
    // Validate form
    const name = formData.get('name').trim();
    const categoryId = formData.get('category_id');
    
    if (!name) {
        showMessage('Brand name is required', 'error');
        return;
    }
    
    if (!categoryId) {
        showMessage('Please select a category', 'error');
        return;
    }
    
    fetch('../actions/update_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            closeEditModal();
            loadBrands();
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating brand', 'error');
    });
}

function deleteBrand(brandId) {
    if (!confirm('Are you sure you want to delete this brand?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', brandId);
    
    fetch('../actions/delete_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            loadBrands();
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error deleting brand', 'error');
    });
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.message');
    existingMessages.forEach(msg => msg.remove());
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
        ${type === 'success' ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'}
    `;
    
    // Insert at the top of the page
    const body = document.querySelector('body');
    body.insertBefore(messageDiv, body.firstChild);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}
