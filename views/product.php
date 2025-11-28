<?php
require_once '../core.php';

// Check if user is logged in and is admin
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
        /* Admin-specific styles */
        .admin-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
        }
        
        .admin-header h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .admin-header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .admin-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-nav a {
            color: #dc3545;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            margin-right: 10px;
            display: inline-block;
        }
        
        .admin-nav a:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .admin-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-section h2 {
            color: #dc3545;
            font-size: 1.8em;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(220, 53, 69, 0.2);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #dc3545;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.15), 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }
        
        .btn-admin-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .btn-admin-secondary:hover {
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
        }
        
        .products-table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        .products-table th {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            font-weight: 600;
            padding: 20px;
            text-align: left;
        }
        
        .products-table td {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .products-table tr:hover {
            background: rgba(220, 53, 69, 0.05);
        }
        
        .product-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .status-inactive {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-small {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 8px;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-size: 18px;
        }
        
        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .success {
            color: #28a745;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border: none;
            border-radius: 20px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            line-height: 1;
        }
        
        .close:hover {
            color: #000;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .admin-nav a {
                display: block;
                margin-bottom: 10px;
                margin-right: 0;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>Product CRUD Operations</h1>
            <p>Admin Dashboard - Create, Read, Update, Delete Products</p>
        </div>
        
        <div class="admin-nav">
            <a href="admin.php">Admin Panel</a>
            <a href="category.php">Category CRUD</a>
            <a href="brand.php">Brand CRUD</a>
            <a href="../assets/logout.php">Logout</a>
        </div>
        
        <div class="admin-section">
            <h2>Add New Product</h2>
            <form id="addProductForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="product_title">Product Title *</label>
                        <input type="text" id="add_title" name="title" required>
                        <div class="error" id="title_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_price">Price *</label>
                        <input type="number" id="add_price" name="price" step="0.01" min="0" required>
                        <div class="error" id="price_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="product_description">Description</label>
                        <textarea id="add_description" name="description" placeholder="Enter product description..." rows="4"></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="product_category">Category *</label>
                        <select id="add_category_id" name="category_id" required>
                            <option value="">Select Category</option>
                        </select>
                        <div class="error" id="category_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_brand">Brand *</label>
                        <select id="add_brand_id" name="brand_id" required>
                            <option value="">Select Brand</option>
                        </select>
                        <div class="error" id="brand_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="add_keyword">Keywords</label>
                    <input type="text" id="add_keyword" name="keyword" placeholder="Enter keywords separated by commas">
                </div>
                
                <div class="form-group">
                    <label for="product_image">Product Image</label>
                    <input type="file" id="add_image" name="image" accept="image/*">
                    <div class="error" id="image_error"></div>
                </div>
                
                <button type="submit" class="btn-admin" id="addBtn">Add Product</button>
                <button type="button" class="btn-admin btn-admin-secondary" onclick="clearForm()">Clear Form</button>
            </form>
        </div>
        
        <div class="admin-section">
            <h2>Product List</h2>
            <div id="productsContainer" class="loading">
                Loading products...
            </div>
        </div>
        
        <!-- Edit Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Edit Product</h2>
                <form id="editProductForm">
                    <input type="hidden" id="edit_product_id" name="id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_product_title">Product Title *</label>
                            <input type="text" id="edit_product_title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_product_price">Price *</label>
                            <input type="number" id="edit_product_price" name="price" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_product_description">Description</label>
                        <textarea id="edit_product_description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_product_category">Category *</label>
                            <select id="edit_product_category" name="category_id" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_product_brand">Brand *</label>
                            <select id="edit_product_brand" name="brand_id" required>
                                <option value="">Select Brand</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_product_keyword">Keywords</label>
                        <input type="text" id="edit_product_keyword" name="keyword">
                    </div>
                    
                    <button type="submit" class="btn-admin">Update Product</button>
                    <button type="button" class="btn-admin btn-admin-secondary" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../assets/product.js"></script>
    <script>
        // Set session data for product.js
        window.sessionData = {
            user_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; ?>
        };
        
        // Load categories and brands on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
            loadBrands();
            // loadProducts() is already called in product.js
            // Category change handlers are also in product.js
        });
        
        function loadCategories() {
            fetch('../actions/fetch_category_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const categorySelect = document.getElementById('add_category_id');
                        const editCategorySelect = document.getElementById('edit_product_category');
                        categorySelect.innerHTML = '<option value="">Select Category</option>';
                        editCategorySelect.innerHTML = '<option value="">Select Category</option>';
                        data.data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            categorySelect.appendChild(option);
                            
                            const editOption = document.createElement('option');
                            editOption.value = category.id;
                            editOption.textContent = category.name;
                            editCategorySelect.appendChild(editOption);
                        });
                    }
                })
                .catch(error => console.error('Error loading categories:', error));
        }
        
        function loadBrands() {
            fetch('../actions/fetch_brand_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const brandSelect = document.getElementById('add_brand_id');
                        const editBrandSelect = document.getElementById('edit_product_brand');
                        brandSelect.innerHTML = '<option value="">Select Brand</option>';
                        editBrandSelect.innerHTML = '<option value="">Select Brand</option>';
                        
                        // Store all brands globally
                        window.allBrands = data.data;
                        
                        data.data.forEach(brand => {
                            const option = document.createElement('option');
                            option.value = brand.id;
                            option.textContent = brand.name;
                            brandSelect.appendChild(option);
                            
                            const editOption = document.createElement('option');
                            editOption.value = brand.id;
                            editOption.textContent = brand.name;
                            editBrandSelect.appendChild(editOption);
                        });
                    }
                })
                .catch(error => console.error('Error loading brands:', error));
        }
        
        function loadProducts() {
            fetch('../actions/fetch_product_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayProducts(data.data);
                    } else {
                        document.getElementById('productsContainer').innerHTML = 
                            '<div class="error">Error loading products: ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    document.getElementById('productsContainer').innerHTML = 
                        '<div class="error">Error loading products: ' + error.message + '</div>';
                });
        }
        
        function displayProducts(products) {
            const container = document.getElementById('productsContainer');
            
            if (products.length === 0) {
                container.innerHTML = '<div class="loading">No products found. Add your first product above!</div>';
                return;
            }
            
            let html = '<table class="products-table"><thead><tr><th>ID</th><th>Title</th><th>Price</th><th>Category</th><th>Brand</th><th>Actions</th></tr></thead><tbody>';
            
            products.forEach(product => {
                html += `
                    <tr>
                        <td>${product.id}</td>
                        <td><strong>${product.title}</strong></td>
                        <td>$${parseFloat(product.price).toFixed(2)}</td>
                        <td>${product.category_name || 'N/A'}</td>
                        <td>${product.brand_name || 'N/A'}</td>
                        <td>
                            <button class="btn-admin btn-small" onclick="editProduct(${product.id})">Edit</button>
                            <button class="btn-admin btn-small btn-admin-secondary" onclick="deleteProduct(${product.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }
        
        function editProduct(id) {
            fetch('../actions/fetch_product_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.data.find(p => p.id == id);
                        if (product) {
                            document.getElementById('edit_product_id').value = product.id;
                            document.getElementById('edit_product_title').value = product.title;
                            document.getElementById('edit_product_price').value = product.price;
                            document.getElementById('edit_product_description').value = product.description || '';
                            document.getElementById('edit_product_category').value = product.category_id;
                            document.getElementById('edit_product_brand').value = product.brand_id;
                            document.getElementById('edit_product_keyword').value = product.keyword || '';
                            document.getElementById('editModal').style.display = 'block';
                        }
                    }
                })
                .catch(error => alert('Error loading product: ' + error.message));
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Handle edit form submission
        document.getElementById('editProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => data[key] = value);
            
            fetch('../actions/update_product_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(data).toString()
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Product updated successfully!');
                    closeModal();
                    loadProducts();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => alert('Error: ' + error.message));
        });
        
        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                fetch('../actions/delete_product_action.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Product deleted successfully!');
                        loadProducts();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => alert('Error: ' + error.message));
            }
        }
    </script>
</body>
</html>