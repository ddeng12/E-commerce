document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    loadCategories();
    loadBrands();
    
    // Add product form submission
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addProduct();
    });
    
    // Edit product form submission
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateProduct();
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
    
    // Category change handler for brand dropdown
    document.getElementById('add_category_id').addEventListener('change', function() {
        loadBrandsByCategory(this.value, 'add_brand_id');
    });
    
    document.getElementById('edit_product_category').addEventListener('change', function() {
        loadBrandsByCategory(this.value, 'edit_product_brand');
    });
});

function loadProducts() {
    fetch('../actions/fetch_product_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.data);
            } else {
                showMessage('Error loading products: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error loading products', 'error');
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

function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.allBrands = data.data; // Store for later use
            } else {
                console.error('Error loading brands:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function loadBrandsByCategory(categoryId, dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    
    // Clear existing options
    dropdown.innerHTML = '<option value="">Select Brand</option>';
    
    if (!categoryId) {
        return;
    }
    
    // Always fetch fresh from database to ensure new brands are included
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.allBrands = data.data; // Update the cached brands
                const categoryBrands = data.data.filter(brand => brand.category_id == categoryId);
                categoryBrands.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.id;
                    option.textContent = brand.name;
                    dropdown.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading brands:', error);
            dropdown.innerHTML = '<option value="">Error loading brands</option>';
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

function displayProducts(products) {
    const container = document.getElementById('productsList');
    
    if (products.length === 0) {
        container.innerHTML = '<p>No products found.</p>';
        return;
    }
    
    // Group products by category and brand
    const groupedProducts = {};
    products.forEach(product => {
        const categoryName = product.category_name;
        if (!groupedProducts[categoryName]) {
            groupedProducts[categoryName] = {};
        }
        const brandName = product.brand_name;
        if (!groupedProducts[categoryName][brandName]) {
            groupedProducts[categoryName][brandName] = [];
        }
        groupedProducts[categoryName][brandName].push(product);
    });
    
    let html = '<table><thead><tr><th>Category</th><th>Brand</th><th>Product</th><th>Price</th><th>Image</th><th>Created</th><th>Actions</th></tr></thead><tbody>';
    
    Object.keys(groupedProducts).sort().forEach(categoryName => {
        Object.keys(groupedProducts[categoryName]).sort().forEach(brandName => {
            groupedProducts[categoryName][brandName].forEach(product => {
                const imageHtml = product.image_path ? 
                    `<img src="../${product.image_path}" alt="${product.title}" style="width: 50px; height: 50px; object-fit: cover;">` : 
                    'No image';
                
                html += `
                    <tr>
                        <td>${categoryName}</td>
                        <td>${brandName}</td>
                        <td>${product.title}</td>
                        <td>$${parseFloat(product.price).toFixed(2)}</td>
                        <td>${imageHtml}</td>
                        <td>${new Date(product.created_at).toLocaleDateString()}</td>
                        <td>
                            <button class="btn" onclick="editProduct(${product.id})">Edit</button>
                            <button class="btn btn-danger" onclick="deleteProduct(${product.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
        });
    });
    
    html += '</tbody></table>';
    container.innerHTML = html;
}

function addProduct() {
    const form = document.getElementById('addProductForm');
    const formData = new FormData(form);
    
    // Validate form
    const title = formData.get('title').trim();
    const price = formData.get('price');
    const categoryId = formData.get('category_id');
    const brandId = formData.get('brand_id');
    
    if (!title) {
        showMessage('Product title is required', 'error');
        return;
    }
    
    if (!price || isNaN(price) || parseFloat(price) <= 0) {
        showMessage('Valid price is required', 'error');
        return;
    }
    
    if (!categoryId) {
        showMessage('Please select a category', 'error');
        return;
    }
    
    if (!brandId) {
        showMessage('Please select a brand', 'error');
        return;
    }
    
    // Upload image first if provided
    const imageFile = document.getElementById('add_image').files[0];
    if (imageFile) {
        uploadImage(imageFile, null, function(imagePath) {
            formData.append('image_path', imagePath);
            formData.append('created_by', getUserId());
            submitProductForm(formData, 'add');
        });
    } else {
        formData.append('created_by', getUserId());
        submitProductForm(formData, 'add');
    }
}

// Helper function to get user ID from session or default
function getUserId() {
    // Try to get user ID from session data if available
    if (window.sessionData && window.sessionData.user_id) {
        return window.sessionData.user_id;
    }
    return 1; // Default fallback
}

function editProduct(productId) {
    fetch('../actions/fetch_product_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.data.find(p => p.id == productId);
                if (product) {
                    document.getElementById('edit_product_id').value = product.id;
                    document.getElementById('edit_product_title').value = product.title;
                    document.getElementById('edit_product_description').value = product.description || '';
                    document.getElementById('edit_product_price').value = product.price;
                    document.getElementById('edit_category_id').value = product.category_id;
                    
                    // Load brands for the selected category
                    loadBrandsByCategory(product.category_id, 'edit_brand_id');
                    
                    // Set brand after brands are loaded
                    setTimeout(() => {
                        document.getElementById('edit_brand_id').value = product.brand_id;
                    }, 100);
                    
                    document.getElementById('edit_product_keyword').value = product.keyword || '';
                    document.getElementById('editModal').style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Error loading product details', 'error');
        });
}

function updateProduct() {
    const form = document.getElementById('editProductForm');
    const formData = new FormData(form);
    
    // Validate form
    const title = formData.get('title').trim();
    const price = formData.get('price');
    const categoryId = formData.get('category_id');
    const brandId = formData.get('brand_id');
    
    if (!title) {
        showMessage('Product title is required', 'error');
        return;
    }
    
    if (!price || isNaN(price) || parseFloat(price) <= 0) {
        showMessage('Valid price is required', 'error');
        return;
    }
    
    if (!categoryId) {
        showMessage('Please select a category', 'error');
        return;
    }
    
    if (!brandId) {
        showMessage('Please select a brand', 'error');
        return;
    }
    
    // Upload image first if provided
    const imageFile = document.getElementById('edit_image').files[0];
    if (imageFile) {
        uploadImage(imageFile, formData.get('id'), function(imagePath) {
            formData.append('image_path', imagePath);
            submitProductForm(formData, 'update');
        });
    } else {
        submitProductForm(formData, 'update');
    }
}

function uploadImage(file, productId, callback) {
    const uploadFormData = new FormData();
    uploadFormData.append('image', file);
    uploadFormData.append('product_id', productId || 'new');
    
    fetch('../actions/upload_product_image_action.php', {
        method: 'POST',
        body: uploadFormData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            callback(data.image_path);
        } else {
            showMessage('Error uploading image: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error uploading image', 'error');
    });
}

function submitProductForm(formData, action) {
    const url = action === 'add' ? '../actions/add_product_action.php' : '../actions/update_product_action.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            if (action === 'add') {
                document.getElementById('addProductForm').reset();
            } else {
                closeEditModal();
            }
            loadProducts();
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error ' + (action === 'add' ? 'adding' : 'updating') + ' product', 'error');
    });
}

function deleteProduct(productId) {
    if (!confirm('Are you sure you want to delete this product?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('id', productId);
    
    fetch('../actions/delete_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            loadProducts();
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error deleting product', 'error');
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
