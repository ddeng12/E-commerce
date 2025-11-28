document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadBrands();
    
    // Set up event listeners
    setupEventListeners();
    
    // Initialize search functionality
    initializeSearch();
});

function setupEventListeners() {
    // Search form submission
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
    }
    
    // Category filter change
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            loadBrandsByCategory(this.value);
        });
    }
    
    // Real-time search
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2) {
                    performLiveSearch(this.value);
                }
            }, 500);
        });
    }
}

function loadCategories() {
    fetch('../actions/product_actions.php?action=get_categories')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateCategoryDropdowns(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
}

function loadBrands() {
    fetch('../actions/product_actions.php?action=get_brands')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.allBrands = data.data;
                populateBrandDropdowns(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading brands:', error);
        });
}

function loadBrandsByCategory(categoryId) {
    const brandDropdown = document.getElementById('brandFilter');
    if (!brandDropdown) return;
    
    // Clear existing options
    brandDropdown.innerHTML = '<option value="">All Brands</option>';
    
    if (!categoryId) {
        // Show all brands
        if (window.allBrands) {
            populateBrandDropdowns(window.allBrands);
        }
        return;
    }
    
    // Filter brands by category
    if (window.allBrands) {
        const categoryBrands = window.allBrands.filter(brand => brand.category_id == categoryId);
        categoryBrands.forEach(brand => {
            const option = document.createElement('option');
            option.value = brand.id;
            option.textContent = brand.name;
            brandDropdown.appendChild(option);
        });
    }
}

function populateCategoryDropdowns(categories) {
    const dropdowns = document.querySelectorAll('#categoryFilter, select[name="category_id"]');
    
    dropdowns.forEach(dropdown => {
        // Clear existing options except the first one
        const firstOption = dropdown.querySelector('option[value=""]');
        dropdown.innerHTML = '';
        if (firstOption) {
            dropdown.appendChild(firstOption);
        }
        
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            dropdown.appendChild(option);
        });
    });
}

function populateBrandDropdowns(brands) {
    const dropdowns = document.querySelectorAll('#brandFilter, select[name="brand_id"]');
    
    dropdowns.forEach(dropdown => {
        // Clear existing options except the first one
        const firstOption = dropdown.querySelector('option[value=""]');
        dropdown.innerHTML = '';
        if (firstOption) {
            dropdown.appendChild(firstOption);
        }
        
        brands.forEach(brand => {
            const option = document.createElement('option');
            option.value = brand.id;
            option.textContent = brand.name;
            dropdown.appendChild(option);
        });
    });
}

function performSearch() {
    const form = document.getElementById('searchForm');
    if (!form) return;
    
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    // Add all form data to URL parameters
    for (let [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            params.append(key, value);
        }
    }
    
    // Redirect to search results page
    window.location.href = 'product_search_result.php?' + params.toString();
}

function performLiveSearch(query) {
    // Show loading state
    showLoadingState();
    
    fetch('../actions/product_actions.php?action=search&q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displaySearchResults(data.data);
            } else {
                showNoResults();
            }
        })
        .catch(error => {
            console.error('Error performing live search:', error);
            showError('Search failed. Please try again.');
        });
}

function displaySearchResults(products) {
    const container = document.getElementById('productsContainer');
    if (!container) return;
    
    if (products.length === 0) {
        showNoResults();
        return;
    }
    
    let html = '<div class="products-grid">';
    
    products.forEach(product => {
        const imageHtml = product.image_path ? 
            `<img src="../${product.image_path}" alt="${product.title}" class="product-image">` : 
            '<div class="product-image"></div>';
        
        html += `
            <div class="product-card">
                <div class="product-id">#${product.id}</div>
                ${imageHtml}
                <div class="product-info">
                    <h3 class="product-title">${product.title}</h3>
                    <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
                    <div class="product-meta">
                        <span class="product-category">${product.category_name}</span>
                        <span class="product-brand">${product.brand_name}</span>
                    </div>
                    <button class="add-to-cart" onclick="addToCart(${product.id})">
                        Add to Cart
                    </button>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

function showNoResults() {
    const container = document.getElementById('productsContainer');
    if (!container) return;
    
    container.innerHTML = `
        <div class="no-products">
            <h3>No products found</h3>
            <p>Try adjusting your search criteria or browse all products.</p>
        </div>
    `;
}

function showLoadingState() {
    const container = document.getElementById('productsContainer');
    if (!container) return;
    
    container.innerHTML = `
        <div class="loading">
            <h3>Searching...</h3>
            <p>Please wait while we find the best products for you.</p>
        </div>
    `;
}

function showError(message) {
    const container = document.getElementById('productsContainer');
    if (!container) return;
    
    container.innerHTML = `
        <div class="no-products">
            <h3>Error</h3>
            <p>${message}</p>
        </div>
    `;
}

function clearFilters() {
    // Clear all form inputs
    const form = document.getElementById('searchForm');
    if (form) {
        form.reset();
    }
    
    // Reset dropdowns
    const categoryDropdown = document.getElementById('categoryFilter');
    const brandDropdown = document.getElementById('brandFilter');
    
    if (categoryDropdown) categoryDropdown.value = '';
    if (brandDropdown) {
        brandDropdown.innerHTML = '<option value="">All Brands</option>';
        if (window.allBrands) {
            populateBrandDropdowns(window.allBrands);
        }
    }
    
    // Reload all products
    window.location.href = 'all_product.php';
}

function filterByCategory(categoryId) {
    const categoryDropdown = document.getElementById('categoryFilter');
    if (categoryDropdown) {
        categoryDropdown.value = categoryId;
        loadBrandsByCategory(categoryId);
    }
    
    // Perform search with category filter
    const form = document.getElementById('searchForm');
    if (form) {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        if (categoryId) {
            params.append('category_id', categoryId);
        }
        
        window.location.href = 'product_search_result.php?' + params.toString();
    }
}

function filterByBrand(brandId) {
    const brandDropdown = document.getElementById('brandFilter');
    if (brandDropdown) {
        brandDropdown.value = brandId;
    }
    
    // Perform search with brand filter
    const form = document.getElementById('searchForm');
    if (form) {
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        if (brandId) {
            params.append('brand_id', brandId);
        }
        
        window.location.href = 'product_search_result.php?' + params.toString();
    }
}

function initializeSearch() {
    // Add search suggestions functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Add autocomplete functionality
        searchInput.addEventListener('focus', function() {
            this.style.borderColor = '#667eea';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.borderColor = '#e1e5e9';
        });
    }
}

function addToCart(productId) {
    console.log('Adding product to cart. Product ID:', productId, 'Type:', typeof productId);
    
    // Create a simple XMLHttpRequest instead of fetch
    var xhr = new XMLHttpRequest();
    var formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', 1);
    
    console.log('Sending product_id:', productId);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log('Response status:', xhr.status);
            console.log('Response text:', xhr.responseText);
            
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    console.log('Response data:', data);
                    if (data.success) {
                        alert('Product added to cart successfully!');
                    } else {
                        alert(data.message || 'Failed to add product to cart');
                    }
                } catch(e) {
                    console.error('JSON parse error:', e);
                    alert('Error: ' + xhr.responseText);
                }
            } else {
                alert('HTTP Error: ' + xhr.status);
            }
        }
    };
    
    xhr.open('POST', '../actions/add_to_cart_action.php', true);
    xhr.send(formData);
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Search with keyboard navigation
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('searchInput');
        if (searchInput && document.activeElement === searchInput) {
            searchInput.value = '';
            searchInput.blur();
        }
    }
});

// Smooth scrolling for pagination
function smoothScrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add click handlers for pagination links
document.addEventListener('click', function(e) {
    if (e.target.matches('.pagination a')) {
        smoothScrollToTop();
    }
});
