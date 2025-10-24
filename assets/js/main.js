/**
 * Main JavaScript file for FoodHub Food Ordering System
 * Contains common utility functions and animations
 */

// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});

/**
 * Show notification/toast message
 * @param {string} message - The message to display
 * @param {string} type - Type of notification (success, error, info, warning)
 * @param {number} duration - How long to show the notification (ms)
 */
function showNotification(message, type = 'info', duration = 3000) {
    // Remove existing notifications
    const existing = document.querySelector('.notification-toast');
    if (existing) {
        existing.remove();
    }
    
    // Color classes based on type
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle',
        warning: 'fa-exclamation-triangle'
    };
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-20 right-4 px-6 py-4 rounded-lg shadow-lg text-white z-50 ${colors[type] || colors.info} animate-slide-in`;
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${icons[type] || icons.info}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after duration
    setTimeout(() => {
        notification.classList.add('animate-slide-out');
        setTimeout(() => notification.remove(), 300);
    }, duration);
}

/**
 * Confirm dialog with custom styling
 * @param {string} message - The confirmation message
 * @param {function} onConfirm - Callback function on confirmation
 */
function confirmDialog(message, onConfirm) {
    if (confirm(message)) {
        onConfirm();
    }
}

/**
 * Format price with currency symbol
 * @param {number} price - The price to format
 * @returns {string} Formatted price string
 */
function formatPrice(price) {
    return '$' + parseFloat(price).toFixed(2);
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} True if valid email
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Debounce function for search inputs
 * @param {function} func - Function to debounce
 * @param {number} wait - Wait time in ms
 * @returns {function} Debounced function
 */
function debounce(func, wait = 300) {
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
 * Loading spinner overlay
 */
const LoadingSpinner = {
    show: function() {
        if (!document.getElementById('loading-spinner')) {
            const spinner = document.createElement('div');
            spinner.id = 'loading-spinner';
            spinner.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            spinner.innerHTML = `
                <div class="bg-white rounded-lg p-8">
                    <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-primary mx-auto"></div>
                    <p class="text-gray-700 mt-4">Loading...</p>
                </div>
            `;
            document.body.appendChild(spinner);
        }
    },
    hide: function() {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.remove();
        }
    }
};

/**
 * Form validation helper
 * @param {HTMLFormElement} form - The form to validate
 * @returns {boolean} True if form is valid
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
        
        // Email validation
        if (input.type === 'email' && input.value) {
            if (!isValidEmail(input.value)) {
                input.classList.add('border-red-500');
                isValid = false;
            }
        }
    });
    
    return isValid;
}

/**
 * Add animation classes
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slide-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
    
    .animate-slide-out {
        animation: slide-out 0.3s ease-in;
    }
`;
document.head.appendChild(style);

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showNotification,
        confirmDialog,
        formatPrice,
        isValidEmail,
        debounce,
        LoadingSpinner,
        validateForm
    };
}
