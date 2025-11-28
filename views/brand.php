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
    <title>Brand Management - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
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
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .form-group input:focus, .form-group select:focus {
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
        
        .brands-table {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        
        .brands-table th {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            font-weight: 600;
            padding: 20px;
            text-align: left;
        }
        
        .brands-table td {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .brands-table tr:hover {
            background: rgba(220, 53, 69, 0.05);
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
            <h1>Brand CRUD Operations</h1>
            <p>Admin Dashboard - Create, Read, Update, Delete Brands</p>
        </div>
        
        <div class="admin-nav">
            <a href="admin.php">Admin Panel</a>
            <a href="product.php">Product CRUD</a>
            <a href="category.php">Category CRUD</a>
            <a href="../assets/logout.php">Logout</a>
        </div>
        
        <div class="form-section">
            <h2>Add New Brand</h2>
            <form id="addBrandForm">
                <div class="form-group">
                    <label for="brand_name">Brand Name *</label>
                    <input type="text" id="brand_name" name="name" required>
                    <div class="error" id="name_error"></div>
                </div>
                
                <div class="form-group">
                    <label for="add_category_id">Category *</label>
                    <select id="add_category_id" name="category_id" required>
                        <option value="">Select Category</option>
                    </select>
                    <div class="error" id="category_error"></div>
                </div>
                
                <button type="submit" class="btn btn-success" id="addBtn">Add Brand</button>
            </form>
        </div>
    </div>
    
    <script>
        // Load categories for the form
        document.addEventListener('DOMContentLoaded', function() {
            loadCategories();
        });
        
        function loadCategories() {
            fetch('../actions/fetch_category_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const categorySelect = document.getElementById('add_category_id');
                        categorySelect.innerHTML = '<option value="">Select Category</option>';
                        data.data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            categorySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error loading categories:', error));
        }
    </script>
    <script>
        // Handle add brand form submission
        document.getElementById('addBrandForm').addEventListener('submit', function(e) {
            e.preventDefault();
            addBrand();
        });
        
        function addBrand() {
            const form = document.getElementById('addBrandForm');
            const formData = new FormData(form);
            
            fetch('../actions/add_brand_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    form.reset();
                    // Reload categories
                    loadCategories();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding brand: ' + error.message);
            });
        }
    </script>
</body>
</html>
