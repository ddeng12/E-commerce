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
    <title>Product View</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        nav {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        nav a {
            color: #007bff;
            text-decoration: none;
            margin-right: 20px;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        nav a:hover {
            background-color: #e9ecef;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .price {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Product Catalog</h1>
        
        <nav>
            <a href="index.php">← Back to Home</a>
            <a href="admin.php">Admin Panel</a>
            <a href="category.php">Categories</a>
            <a href="brand.php">Brands</a>
            <a href="product.php">Add Product</a>
            <a href="../assets/logout.php">Logout</a>
        </nav>
        
        <div id="productsList">
            <div class="loading">Loading products...</div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
        });

        function loadProducts() {
            fetch('../actions/fetch_product_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayProducts(data.data);
                    } else {
                        document.getElementById('productsList').innerHTML = '<p>Error loading products: ' + data.message + '</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('productsList').innerHTML = '<p>Error loading products</p>';
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
            
            let html = '<table><thead><tr><th>Category</th><th>Brand</th><th>Product</th><th>Description</th><th>Price</th><th>Image</th><th>Keywords</th><th>Created</th></tr></thead><tbody>';
            
            Object.keys(groupedProducts).sort().forEach(categoryName => {
                Object.keys(groupedProducts[categoryName]).sort().forEach(brandName => {
                    groupedProducts[categoryName][brandName].forEach(product => {
                        const imageHtml = product.image_path ? 
                            `<img src="../${product.image_path}" alt="${product.title}" class="product-image">` : 
                            '<span style="color: #999;">No image</span>';
                        
                        html += `
                            <tr>
                                <td>${categoryName}</td>
                                <td>${brandName}</td>
                                <td><strong>${product.title}</strong></td>
                                <td>${product.description || 'No description'}</td>
                                <td class="price">$${parseFloat(product.price).toFixed(2)}</td>
                                <td>${imageHtml}</td>
                                <td>${product.keyword || 'No keywords'}</td>
                                <td>${new Date(product.created_at).toLocaleDateString()}</td>
                            </tr>
                        `;
                    });
                });
            });
            
            html += '</tbody></table>';
            container.innerHTML = html;
        }
    </script>
</body>
</html>
