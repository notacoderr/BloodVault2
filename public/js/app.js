/**
 * LifeVault Application JavaScript
 * Main functionality for the blood bank management system
 */

(function() {
    'use strict';

    // Global variables
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let isAuthenticated = document.querySelector('body')?.classList.contains('authenticated');

    // Global notification functions - define these first
    window.showSuccess = function(message) {
        showNotification(message, 'success');
    };

    window.showError = function(message) {
        showNotification(message, 'danger');
    };

    window.showWarning = function(message) {
        showNotification(message, 'warning');
    };

    window.showInfo = function(message) {
        showNotification(message, 'info');
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeApp();
    });

    /**
     * Initialize the application
     */
    function initializeApp() {
        setupCSRF();
        setupTooltips();
        setupFormValidation();
        setupRealTimeUpdates();
        setupSmoothScrolling();
        setupLoadingStates();
        setupNotifications();
        setupAJAXHandlers();
    }

    /**
     * Setup CSRF token for AJAX requests
     */
    function setupCSRF() {
        if (csrfToken) {
            // Add CSRF token to all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        }
    }

    /**
     * Initialize Bootstrap tooltips
     */
    function setupTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Setup form validation
     */
    function setupFormValidation() {
        // Custom validation for password confirmation
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('password_confirmation');
        
        if (passwordField && confirmPasswordField) {
            confirmPasswordField.addEventListener('input', function() {
                if (this.value !== passwordField.value) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            });
        }

        // Form submission handling
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                this.classList.add('was-validated');
            });
        });
    }

    /**
     * Setup real-time updates
     */
    function setupRealTimeUpdates() {
        // Update dashboard stats every 30 seconds
        if (document.querySelector('#total-requests')) {
            setInterval(updateDashboardStats, 30000);
            updateDashboardStats(); // Initial load
        }

        // Update recent activities every minute
        if (document.querySelector('#recent-requests') || document.querySelector('#recent-donations')) {
            setInterval(updateRecentActivities, 60000);
            updateRecentActivities(); // Initial load
        }
    }

    /**
     * Update dashboard statistics
     */
    function updateDashboardStats() {
        if (!isAuthenticated) return;

        fetch('/user/dashboard-stats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('total-requests').textContent = data.stats.total_requests || 0;
                    document.getElementById('total-donations').textContent = data.stats.total_donations || 0;
                    document.getElementById('total-appointments').textContent = data.stats.total_appointments || 0;
                }
            })
            .catch(error => {
                console.error('Error updating dashboard stats:', error);
            });
    }

    /**
     * Update recent activities
     */
    function updateRecentActivities() {
        if (!isAuthenticated) return;

        // Update recent requests
        const recentRequestsContainer = document.getElementById('recent-requests');
        if (recentRequestsContainer) {
            fetch('/user/my-requests?limit=5')
                .then(response => response.text())
                .then(html => {
                    // Extract the table body content
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const tableBody = doc.querySelector('tbody');
                    if (tableBody) {
                        recentRequestsContainer.innerHTML = tableBody.outerHTML;
                    }
                })
                .catch(error => {
                    console.error('Error updating recent requests:', error);
                });
        }

        // Update recent donations
        const recentDonationsContainer = document.getElementById('recent-donations');
        if (recentDonationsContainer) {
            fetch('/user/my-donations?limit=5')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const tableBody = doc.querySelector('tbody');
                    if (tableBody) {
                        recentDonationsContainer.innerHTML = tableBody.outerHTML;
                    }
                })
                .catch(error => {
                    console.error('Error updating recent donations:', error);
                });
        }
    }

    /**
     * Setup smooth scrolling for anchor links
     */
    function setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Setup loading states for buttons and forms
     */
    function setupLoadingStates() {
        // Form submission loading states
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Processing...';
                    submitBtn.disabled = true;
                    
                    // Reset button after form submission (success or error)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 5000);
                }
            });
        });

        // Button click loading states
        document.querySelectorAll('.btn-loading').forEach(btn => {
            btn.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="loading-spinner me-2"></span>Loading...';
                this.disabled = true;
                
                // Reset button after action completes
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 3000);
            });
        });
    }

    /**
     * Setup notification system
     */
    function setupNotifications() {
        // Auto-hide only dismissible alerts (do not touch non-dismissible alerts like persistent notes)
        document.querySelectorAll('.alert.alert-dismissible').forEach(alert => {
            const autoHideAttr = alert.getAttribute('data-autohide');
            if (autoHideAttr === 'false') return;
            setTimeout(() => {
                try {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                } catch (e) {
                    if (alert && alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }
            }, 5000);
        });
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${getIconForType(type)} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.container');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alertDiv);
                bsAlert.close();
            }, 5000);
        }
    }

    /**
     * Get icon for notification type
     */
    function getIconForType(type) {
        const icons = {
            success: 'check-circle',
            danger: 'exclamation-triangle',
            warning: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    /**
     * Setup AJAX handlers
     */
    function setupAJAXHandlers() {
        // Global AJAX error handler
        $(document).ajaxError(function(event, xhr, settings, error) {
            if (xhr.status === 419) {
                // CSRF token mismatch
                showError('Session expired. Please refresh the page and try again.');
            } else if (xhr.status === 500) {
                showError('Server error occurred. Please try again later.');
            } else if (xhr.status === 404) {
                showError('Requested resource not found.');
            } else if (xhr.status === 403) {
                showError('Access denied. You do not have permission to perform this action.');
            }
        });

        // Global AJAX success handler
        $(document).ajaxSuccess(function(event, xhr, settings) {
            // Handle success responses
            if (xhr.responseJSON && xhr.responseJSON.message) {
                showSuccess(xhr.responseJSON.message);
            }
        });
    }

    /**
     * Utility function to format dates
     */
    window.formatDate = function(dateString) {
        if (!dateString) return 'N/A';
        
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return 'Invalid Date';
        
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    /**
     * Utility function to format currency
     */
    window.formatCurrency = function(amount) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(amount);
    }

    /**
     * Utility function to confirm actions
     */
    window.confirmAction = function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }

    /**
     * Utility function to debounce function calls
     */
    window.debounce = function(func, wait) {
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

    /**
     * Utility function to throttle function calls
     */
    window.throttle = function(func, limit) {
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

    /**
     * Export functions to global scope
     */
    window.LifeVault = {
        showSuccess,
        showError,
        showWarning,
        showInfo,
        formatDate,
        formatCurrency,
        confirmAction,
        debounce,
        throttle
    };

})();
