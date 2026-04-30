/**
 * UI Utilities & Helper Functions
 * Reusable components and utilities for the MUA Booking System
 */

// ==================== TOAST NOTIFICATIONS ====================

class ToastManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Create toast container if doesn't exist
        if (!document.querySelector('.toast-container')) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            document.body.appendChild(this.container);
        } else {
            this.container = document.querySelector('.toast-container');
        }
    }

    show(message, type = 'info', duration = 3000) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        const titles = {
            success: 'Berhasil',
            error: 'Error',
            warning: 'Peringatan',
            info: 'Informasi'
        };

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="toast-icon">${icons[type]}</div>
            <div class="toast-content">
                <div class="toast-title">${titles[type]}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        `;

        this.container.appendChild(toast);

        // Auto remove
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    success(message, duration) {
        this.show(message, 'success', duration);
    }

    error(message, duration) {
        this.show(message, 'error', duration);
    }

    warning(message, duration) {
        this.show(message, 'warning', duration);
    }

    info(message, duration) {
        this.show(message, 'info', duration);
    }
}

const toast = new ToastManager();

// ==================== MODAL MANAGER ====================

class ModalManager {
    constructor() {
        this.activeModal = null;
    }

    open(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error('Modal not found:', modalId);
            return;
        }

        modal.classList.add('active');
        this.activeModal = modal;
        document.body.style.overflow = 'hidden';

        // Close on backdrop click
        const backdrop = modal.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.onclick = () => this.close(modalId);
        }

        // Close on ESC key
        document.addEventListener('keydown', this.handleEscape);
    }

    close(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.remove('active');
        this.activeModal = null;
        document.body.style.overflow = '';
        document.removeEventListener('keydown', this.handleEscape);
    }

    handleEscape = (e) => {
        if (e.key === 'Escape' && this.activeModal) {
            this.close(this.activeModal.id);
        }
    }
}

const modal = new ModalManager();

// ==================== LOADING OVERLAY ====================

class LoadingManager {
    constructor() {
        this.overlay = null;
    }

    show(message = 'Loading...') {
        if (this.overlay) return;

        this.overlay = document.createElement('div');
        this.overlay.className = 'loading-overlay';
        this.overlay.innerHTML = `
            <div style="text-align: center; color: white;">
                <div class="spinner spinner-lg"></div>
                <p style="margin-top: 16px; font-size: 16px;">${message}</p>
            </div>
        `;
        document.body.appendChild(this.overlay);
        document.body.style.overflow = 'hidden';
    }

    hide() {
        if (this.overlay) {
            this.overlay.remove();
            this.overlay = null;
            document.body.style.overflow = '';
        }
    }
}

const loading = new LoadingManager();

// ==================== CONFIRMATION DIALOG ====================

function confirm(message, title = 'Konfirmasi') {
    return new Promise((resolve) => {
        // Create modal
        const modalDiv = document.createElement('div');
        modalDiv.className = 'modal active';
        modalDiv.id = 'confirmModal';
        modalDiv.innerHTML = `
            <div class="modal-backdrop"></div>
            <div class="modal-content" style="max-width: 400px;">
                <div class="modal-header">
                    <h3 class="modal-title">${title}</h3>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="confirmCancel">Batal</button>
                    <button class="btn btn-primary" id="confirmOk">Ya, Lanjutkan</button>
                </div>
            </div>
        `;

        document.body.appendChild(modalDiv);
        document.body.style.overflow = 'hidden';

        const cleanup = () => {
            modalDiv.remove();
            document.body.style.overflow = '';
        };

        document.getElementById('confirmCancel').onclick = () => {
            cleanup();
            resolve(false);
        };

        document.getElementById('confirmOk').onclick = () => {
            cleanup();
            resolve(true);
        };

        modalDiv.querySelector('.modal-backdrop').onclick = () => {
            cleanup();
            resolve(false);
        };
    });
}

// ==================== DATE & TIME UTILITIES ====================

const DateUtils = {
    /**
     * Format date to Indonesian locale
     */
    formatDate(date, format = 'full') {
        if (!date) return '';
        
        const d = date instanceof Date ? date : date.toDate ? date.toDate() : new Date(date);
        
        const options = {
            full: { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' },
            short: { year: 'numeric', month: 'short', day: 'numeric' },
            time: { hour: '2-digit', minute: '2-digit' },
            datetime: { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
        };

        return d.toLocaleDateString('id-ID', options[format] || options.full);
    },

    /**
     * Format time
     */
    formatTime(date) {
        if (!date) return '';
        const d = date instanceof Date ? date : date.toDate ? date.toDate() : new Date(date);
        return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    },

    /**
     * Get relative time (e.g., "2 jam yang lalu")
     */
    getRelativeTime(date) {
        if (!date) return '';
        
        const d = date instanceof Date ? date : date.toDate ? date.toDate() : new Date(date);
        const now = new Date();
        const diff = now - d;
        
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        
        if (days > 7) return this.formatDate(d, 'short');
        if (days > 0) return `${days} hari yang lalu`;
        if (hours > 0) return `${hours} jam yang lalu`;
        if (minutes > 0) return `${minutes} menit yang lalu`;
        return 'Baru saja';
    },

    /**
     * Check if date is today
     */
    isToday(date) {
        const d = date instanceof Date ? date : date.toDate ? date.toDate() : new Date(date);
        const today = new Date();
        return d.toDateString() === today.toDateString();
    },

    /**
     * Get date input value (YYYY-MM-DD)
     */
    toInputValue(date) {
        const d = date instanceof Date ? date : date.toDate ? date.toDate() : new Date(date);
        return d.toISOString().split('T')[0];
    }
};

// ==================== CURRENCY UTILITIES ====================

const CurrencyUtils = {
    /**
     * Format number to IDR currency
     */
    format(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },

    /**
     * Format number with K/M suffix
     */
    formatShort(amount) {
        if (amount >= 1000000) {
            return (amount / 1000000).toFixed(1) + 'M';
        }
        if (amount >= 1000) {
            return (amount / 1000).toFixed(1) + 'K';
        }
        return amount.toString();
    }
};

// ==================== IMAGE UTILITIES ====================

const ImageUtils = {
    /**
     * Validate image file
     */
    validate(file, maxSize = 5 * 1024 * 1024) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        
        if (!allowedTypes.includes(file.type)) {
            throw new Error('Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
        }
        
        if (file.size > maxSize) {
            const maxMB = maxSize / (1024 * 1024);
            throw new Error(`Ukuran file terlalu besar. Maksimal ${maxMB}MB.`);
        }
        
        return true;
    },

    /**
     * Compress image
     */
    async compress(file, maxWidth = 1200, quality = 0.8) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            
            reader.onload = (e) => {
                const img = new Image();
                
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    
                    // Calculate new dimensions
                    if (width > maxWidth) {
                        height = (height * maxWidth) / width;
                        width = maxWidth;
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    canvas.toBlob((blob) => {
                        resolve(new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        }));
                    }, 'image/jpeg', quality);
                };
                
                img.onerror = reject;
                img.src = e.target.result;
            };
            
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    },

    /**
     * Create image preview
     */
    createPreview(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }
};

// ==================== VALIDATION UTILITIES ====================

const ValidationUtils = {
    /**
     * Validate email
     */
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    /**
     * Validate phone number
     */
    isValidPhone(phone) {
        const re = /^(\+62|62|0)[0-9]{9,12}$/;
        return re.test(phone.replace(/\s/g, ''));
    },

    /**
     * Validate password strength
     */
    isStrongPassword(password) {
        return password.length >= 6;
    },

    /**
     * Format phone number
     */
    formatPhone(phone) {
        // Remove all non-digits
        let cleaned = phone.replace(/\D/g, '');
        
        // Convert to 08xx format
        if (cleaned.startsWith('62')) {
            cleaned = '0' + cleaned.substring(2);
        } else if (cleaned.startsWith('+62')) {
            cleaned = '0' + cleaned.substring(3);
        }
        
        return cleaned;
    }
};

// ==================== LOCAL STORAGE UTILITIES ====================

const StorageUtils = {
    /**
     * Save to localStorage
     */
    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (error) {
            console.error('Error saving to localStorage:', error);
            return false;
        }
    },

    /**
     * Get from localStorage
     */
    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (error) {
            console.error('Error reading from localStorage:', error);
            return defaultValue;
        }
    },

    /**
     * Remove from localStorage
     */
    remove(key) {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (error) {
            console.error('Error removing from localStorage:', error);
            return false;
        }
    },

    /**
     * Clear all localStorage
     */
    clear() {
        try {
            localStorage.clear();
            return true;
        } catch (error) {
            console.error('Error clearing localStorage:', error);
            return false;
        }
    }
};

// ==================== QR CODE UTILITIES ====================

const QRUtils = {
    /**
     * Generate QR code
     */
    async generate(data, size = 300) {
        try {
            // Using qrcode.js library
            const qr = qrcode(0, 'M');
            qr.addData(data);
            qr.make();
            return qr.createDataURL(4, size / 33); // 33 modules * 4 = ~132, scale for size
        } catch (error) {
            console.error('Error generating QR code:', error);
            throw error;
        }
    },

    /**
     * Parse QR data
     */
    parse(qrData) {
        try {
            return JSON.parse(qrData);
        } catch (error) {
            console.error('Error parsing QR data:', error);
            return null;
        }
    }
};

// ==================== FORM UTILITIES ====================

const FormUtils = {
    /**
     * Get form data as object
     */
    getData(formElement) {
        const formData = new FormData(formElement);
        const data = {};
        
        for (const [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        return data;
    },

    /**
     * Set form data from object
     */
    setData(formElement, data) {
        for (const [key, value] of Object.entries(data)) {
            const input = formElement.elements[key];
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = value;
                } else if (input.type === 'radio') {
                    const radio = formElement.querySelector(`input[name="${key}"][value="${value}"]`);
                    if (radio) radio.checked = true;
                } else {
                    input.value = value;
                }
            }
        }
    },

    /**
     * Reset form and errors
     */
    reset(formElement) {
        formElement.reset();
        
        // Clear error messages
        const errors = formElement.querySelectorAll('.form-error');
        errors.forEach(error => error.remove());
        
        // Remove error states
        const inputs = formElement.querySelectorAll('.form-input, .form-select, .form-textarea');
        inputs.forEach(input => input.classList.remove('error'));
    },

    /**
     * Show field error
     */
    showError(formElement, fieldName, message) {
        const input = formElement.elements[fieldName];
        if (!input) return;
        
        // Add error class
        input.classList.add('error');
        
        // Remove existing error
        const existingError = input.parentElement.querySelector('.form-error');
        if (existingError) existingError.remove();
        
        // Add new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        errorDiv.textContent = message;
        input.parentElement.appendChild(errorDiv);
    },

    /**
     * Clear field error
     */
    clearError(formElement, fieldName) {
        const input = formElement.elements[fieldName];
        if (!input) return;
        
        input.classList.remove('error');
        const error = input.parentElement.querySelector('.form-error');
        if (error) error.remove();
    }
};

// ==================== DEBOUNCE & THROTTLE ====================

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

function throttle(func, limit = 300) {
    let inThrottle;
    return function executedFunction(...args) {
        if (!inThrottle) {
            func(...args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ==================== ARRAY UTILITIES ====================

const ArrayUtils = {
    /**
     * Chunk array into smaller arrays
     */
    chunk(array, size) {
        const chunks = [];
        for (let i = 0; i < array.length; i += size) {
            chunks.push(array.slice(i, i + size));
        }
        return chunks;
    },

    /**
     * Shuffle array
     */
    shuffle(array) {
        const shuffled = [...array];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }
        return shuffled;
    },

    /**
     * Get unique values
     */
    unique(array) {
        return [...new Set(array)];
    }
};

// ==================== EXPORT ====================

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        toast,
        modal,
        loading,
        confirm,
        DateUtils,
        CurrencyUtils,
        ImageUtils,
        ValidationUtils,
        StorageUtils,
        QRUtils,
        FormUtils,
        debounce,
        throttle,
        ArrayUtils
    };
}
