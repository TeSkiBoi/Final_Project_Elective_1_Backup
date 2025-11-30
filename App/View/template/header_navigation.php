<?php
// Include auth functions to get user info
require_once __DIR__ . '/../../Config/Auth.php';

$currentUser = getCurrentUserFullName();
$currentUsername = getCurrentUsername();
$roleId = getCurrentUserRole();
?>
<nav class="sb-topnav navbar navbar-expand navbar-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="index.php">
        <img src="../../assets/img/BIGA-LOGO.png" alt="BIGA Logo" height="40" class="me-2">
        BMS Admin
    </a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
        <i class="fas fa-bars"></i>
    </button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" id="navbarSearchForm" onsubmit="return false;">
        <div class="input-group position-relative">
            <input class="form-control" id="navbarSearchInput" type="text" placeholder="Search menu..." aria-label="Search menu" aria-describedby="btnNavbarSearch" autocomplete="off" />
            <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            <!-- Search Results Dropdown -->
            <div id="searchResultsDropdown" class="position-absolute bg-white border rounded shadow-lg" style="display: none; top: 100%; left: 0; right: 0; max-height: 400px; overflow-y: auto; z-index: 9999; margin-top: 2px;">
                <div id="searchResultsList"></div>
            </div>
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i> <?php echo htmlspecialchars($currentUsername); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><span class="dropdown-item-text"><small class="text-muted">Logged in as:</small><br><strong><?php echo htmlspecialchars($currentUser); ?></strong></span></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="profilesetting.php">Profile Settings</a></li>
                <li><a class="dropdown-item" href="activitylog.php">Activity Log</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="logoutModalLabel"><i class="fas fa-sign-out-alt me-2"></i>Confirm Logout</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout?</p>
                <small class="text-muted">You will be redirected to the login page.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmLogoutBtn"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Search Functionality -->
<script>
(function() {
    const searchInput = document.getElementById('navbarSearchInput');
    const searchResults = document.getElementById('searchResultsDropdown');
    const searchResultsList = document.getElementById('searchResultsList');
    const searchBtn = document.getElementById('btnNavbarSearch');
    
    if (!searchInput) return;
    
    // Collect all navigation items
    function getNavigationItems() {
        const items = [];
        const sidebar = document.querySelector('#sidenavAccordion');
        if (!sidebar) return items;
        
        // Get all nav links from sidebar
        const navLinks = sidebar.querySelectorAll('a.nav-link[href]:not([href="#"])');
        navLinks.forEach(link => {
            const text = link.textContent.trim();
            const href = link.getAttribute('href');
            const icon = link.querySelector('i');
            const iconClass = icon ? icon.className : 'fas fa-link';
            
            if (text && href && href !== '#') {
                items.push({
                    text: text,
                    href: href,
                    icon: iconClass
                });
            }
        });
        
        return items;
    }
    
    // Perform search
    function performSearch(query) {
        if (!query || query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }
        
        const items = getNavigationItems();
        const lowerQuery = query.toLowerCase();
        
        // Filter and score results
        const matches = items.filter(item => {
            return item.text.toLowerCase().includes(lowerQuery);
        }).sort((a, b) => {
            // Prioritize items that start with the query
            const aStarts = a.text.toLowerCase().startsWith(lowerQuery);
            const bStarts = b.text.toLowerCase().startsWith(lowerQuery);
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            return a.text.localeCompare(b.text);
        });
        
        displayResults(matches, query);
    }
    
    // Display search results
    function displayResults(matches, query) {
        if (matches.length === 0) {
            searchResultsList.innerHTML = '<div class="p-3 text-muted text-center"><i class="fas fa-search me-2"></i>No results found</div>';
            searchResults.style.display = 'block';
            return;
        }
        
        const html = matches.map(item => {
            // Highlight matching text
            const regex = new RegExp(`(${query})`, 'gi');
            const highlightedText = item.text.replace(regex, '<mark class="bg-warning">$1</mark>');
            
            return `
                <a href="${item.href}" class="d-flex align-items-center text-decoration-none p-3 border-bottom hover-bg-light" style="color: #333; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                    <i class="${item.icon} me-3" style="width: 20px; color: #0d6efd;"></i>
                    <span>${highlightedText}</span>
                </a>
            `;
        }).join('');
        
        searchResultsList.innerHTML = html;
        searchResults.style.display = 'block';
    }
    
    // Event listeners
    searchInput.addEventListener('input', function(e) {
        performSearch(e.target.value);
    });
    
    searchInput.addEventListener('focus', function(e) {
        if (e.target.value.length >= 2) {
            performSearch(e.target.value);
        }
    });
    
    searchBtn.addEventListener('click', function() {
        performSearch(searchInput.value);
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target) && !searchBtn.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
    
    // Handle Enter key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            // Navigate to first result if available
            const firstLink = searchResultsList.querySelector('a');
            if (firstLink) {
                window.location.href = firstLink.getAttribute('href');
            }
        }
        
        // Close on Escape
        if (e.key === 'Escape') {
            searchResults.style.display = 'none';
            searchInput.blur();
        }
    });
})();
</script>

<script>
    // Logout confirmation
    document.getElementById('confirmLogoutBtn').addEventListener('click', async function() {
        try {
            const response = await fetch('../../App/Controller/LogoutController.php?action=logout', {
                method: 'POST'
            });
            
            const result = await response.json();
            
            if (result.success) {
                window.location.href = '../../index.php?logout=success';
            }
        } catch (error) {
            console.error('Error:', error);
            // Force logout by going to index
            window.location.href = '../../index.php';
        }
    });
</script>