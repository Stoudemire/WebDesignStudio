// Reino de Habbo - Optimized Main JavaScript

// API Configuration
const API_BASE = 'api/auth.php';

// Optimized utility functions with caching
const Utils = {
    cache: new Map(),

    createCard(className, content) {
        const card = document.createElement('div');
        card.className = `glass glass-hover rounded-2xl p-6 ${className}`;
        card.innerHTML = content;
        return card;
    },

    getRankIcon(rankName) {
        if (this.cache.has(`icon_${rankName}`)) {
            return this.cache.get(`icon_${rankName}`);
        }

        const icons = {
            'Campesino': '🌾',
            'Comerciante': '💰',
            'Caballero': '⚔️',
            'Noble': '🏰',
            'Duque': '👑',
            'Rey': '👑',
            'Reina': '👑'
        };

        const icon = icons[rankName] || '🛡️';
        this.cache.set(`icon_${rankName}`, icon);
        return icon;
    },

    getRankColor(rankName) {
        if (this.cache.has(`color_${rankName}`)) {
            return this.cache.get(`color_${rankName}`);
        }

        const colors = {
            'Campesino': 'text-yellow-700',
            'Comerciante': 'text-blue-400',
            'Caballero': 'text-green-400',
            'Noble': 'text-purple-400',
            'Duque': 'text-red-400',
            'Rey': 'text-yellow-400',
            'Reina': 'text-yellow-400'
        };

        const color = colors[rankName] || 'text-gray-400';
        this.cache.set(`color_${rankName}`, color);
        return icon;
    }
};

// Optimized message functions
const MessageManager = {
    show(containerId, message, type = 'error') {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `<div class="${type === 'success' ? 'success-message' : 'error-message'} p-3 rounded mb-4">${message}</div>`;
        }
    },

    clear(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = '';
        }
    }
};

// Role management utilities
const RoleManager = {
    validRoles: ['usuario', 'operador', 'administrador', 'desarrollador'],

    displayNames: {
        'usuario': 'Usuario',
        'operador': 'Operador',
        'administrador': 'Administrador',
        'desarrollador': 'Desarrollador'
    },

    isValid(role) {
        return this.validRoles.includes(role);
    },

    getDisplayName(role) {
        return this.displayNames[role] || role;
    }
};

// Password validation with better performance
function isPasswordSecure(password) {
    return password.length >= 8 &&
           /[a-z]/.test(password) &&
           /[A-Z]/.test(password) &&
           /\d/.test(password) &&
           /[!@#$%^&*(),.?":{}|<>]/.test(password);
}

// Optimized modal functions with hardware acceleration
const ModalManager = {
    open(modalId) {
        console.log('Opening modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            console.log('Modal found, showing...');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Add animation classes if they exist
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.add('show');
            }
            modal.classList.add('show');
        } else {
            console.error('Modal not found:', modalId);
        }
    },

    close(modalId) {
        console.log('Closing modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.remove('show');
            }
            modal.classList.remove('show');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 150);
        }
    }
};

// Optimized sidebar with throttled events
const SidebarManager = {
    initialize() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        if (!sidebar || !mainContent) return;

        // Initialize as collapsed for better performance
        sidebar.classList.add('collapsed');
        sidebar.classList.remove('expanded');
        mainContent.style.marginLeft = '4rem';

        let isExpanded = false;
        let timeoutId = null;

        // Throttled hover handlers
        const handleMouseEnter = () => {
            if (timeoutId) clearTimeout(timeoutId);
            if (!isExpanded) {
                isExpanded = true;
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('expanded');
                mainContent.style.marginLeft = '16rem';
            }
        };

        const handleMouseLeave = () => {
            timeoutId = setTimeout(() => {
                if (isExpanded) {
                    isExpanded = false;
                    sidebar.classList.add('collapsed');
                    sidebar.classList.remove('expanded');
                    mainContent.style.marginLeft = '4rem';
                }
            }, 300);
        };

        sidebar.addEventListener('mouseenter', handleMouseEnter, { passive: true });
        sidebar.addEventListener('mouseleave', handleMouseLeave, { passive: true });

        // Optimized resize handler
        const handleResize = () => {
            if (window.innerWidth < 768) {
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('expanded');
                mainContent.style.marginLeft = '4rem';
                isExpanded = false;
            }
        };

        window.addEventListener('resize', handleResize, { passive: true });
        handleResize();
    }
};

// Section switching with optimized performance
function switchSection(sectionName) {
    // Use more efficient query and batch DOM operations
    const sections = document.querySelectorAll('.content-section');
    const sidebarItems = document.querySelectorAll('.sidebar-item');

    // Batch hide operations
    sections.forEach(section => section.classList.add('hidden'));
    sidebarItems.forEach(item => item.classList.remove('active'));

    // Show target section
    const targetSection = document.getElementById(`${sectionName}-section`);
    if (targetSection) {
        targetSection.classList.remove('hidden');
    }

    // Update active sidebar item
    const activeItem = document.querySelector(`[data-section="${sectionName}"]`);
    if (activeItem) {
        activeItem.classList.add('active');
    }
}

// Authentication Manager
const AuthManager = {
    initialized: false,

    initialize() {
        if (this.initialized) return;
        this.initialized = true;

        console.log('Initializing AuthManager...');

        // Modal triggers
        const userMenuBtn = document.getElementById('user-menu-btn');
        const registerBtnNav = document.getElementById('register-btn-nav');

        console.log('User menu btn:', userMenuBtn);
        console.log('Register btn:', registerBtnNav);

        if (userMenuBtn) {
            userMenuBtn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Opening login modal');
                ModalManager.open('login-modal');
            });
        }

        if (registerBtnNav) {
            registerBtnNav.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Opening register modal');
                ModalManager.open('register-modal');
            });
        }

        // Close modal buttons
        const closeLogin = document.getElementById('close-login');
        const closeRegister = document.getElementById('close-register');

        if (closeLogin) {
            closeLogin.addEventListener('click', (e) => {
                e.preventDefault();
                ModalManager.close('login-modal');
            });
        }

        if (closeRegister) {
            closeRegister.addEventListener('click', (e) => {
                e.preventDefault();
                ModalManager.close('register-modal');
            });
        }

        // Switch between modals
        const switchToRegister = document.getElementById('switch-to-register');
        const switchToLogin = document.getElementById('switch-to-login');

        if (switchToRegister) {
            switchToRegister.addEventListener('click', (e) => {
                e.preventDefault();
                ModalManager.close('login-modal');
                setTimeout(() => ModalManager.open('register-modal'), 100);
            });
        }

        if (switchToLogin) {
            switchToLogin.addEventListener('click', (e) => {
                e.preventDefault();
                ModalManager.close('register-modal');
                setTimeout(() => ModalManager.open('login-modal'), 100);
            });
        }

        // Form submissions
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const verifyBtn = document.getElementById('verify-account-btn');

        if (loginForm) {
            loginForm.addEventListener('submit', this.handleLogin.bind(this));
        }

        if (registerForm) {
            registerForm.addEventListener('submit', this.handleRegister.bind(this));
        }

        if (verifyBtn) {
            verifyBtn.addEventListener('click', this.handleVerification.bind(this));
        }

        // Click outside to close modals
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                ModalManager.close('login-modal');
                ModalManager.close('register-modal');
            }
        });

        // Escape key to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                ModalManager.close('login-modal');
                ModalManager.close('register-modal');
            }
        });
    },

    handleLogin(e) {
        e.preventDefault();
        MessageManager.clear('login-messages');

        const username = document.getElementById('login-username').value.trim();
        const password = document.getElementById('login-password').value;

        if (!username || !password) {
            MessageManager.show('login-messages', 'Por favor completa todos los campos');
            return;
        }

        // Show loading state immediately
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        if (submitBtn) submitBtn.textContent = 'Verificando...';

        fetch('api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'login',
                username: username,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Instant redirect without delay or success message
                window.location.replace('pages/dashboard.php');
            } else {
                if (submitBtn) submitBtn.textContent = originalText;
                MessageManager.show('login-messages', data.message);
            }
        })
        .catch(error => {
            if (submitBtn) submitBtn.textContent = originalText;
            console.error('Error:', error);
            MessageManager.show('login-messages', 'Error de conexión');
        });
    },

    handleRegister(e) {
        e.preventDefault();
        MessageManager.clear('register-messages');

        const username = document.getElementById('register-username').value.trim();
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;

        if (!username || !password || !confirmPassword) {
            MessageManager.show('register-messages', 'Por favor completa todos los campos');
            return;
        }

        if (password !== confirmPassword) {
            MessageManager.show('register-messages', 'Las contraseñas no coinciden');
            return;
        }

        if (password.length < 8) {
            MessageManager.show('register-messages', 'La contraseña debe tener al menos 8 caracteres');
            return;
        }

        fetch('api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'register',
                username: username,
                password: password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store verification data
                window.currentVerification = {
                    username: username,
                    code: data.data.verification_code
                };

                // Show verification code
                document.getElementById('verification-code-display').textContent = data.data.verification_code;
                document.getElementById('register-step-1').classList.add('hidden');
                document.getElementById('register-step-2').classList.remove('hidden');
            } else {
                MessageManager.show('register-messages', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            MessageManager.show('register-messages', 'Error de conexión');
        });
    },

    handleVerification() {
        if (!window.currentVerification) {
            MessageManager.show('register-messages', 'Error: No hay verificación pendiente');
            return;
        }

        fetch('api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'verify_account',
                username: window.currentVerification.username
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                MessageManager.show('register-messages', 'Cuenta verificada exitosamente. Redirigiendo...', 'success');
                setTimeout(() => {
                    window.location.href = 'pages/dashboard.php';
                }, 1500);
            } else {
                MessageManager.show('register-messages', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            MessageManager.show('register-messages', 'Error de conexión');
        });
    },

    logout() {
        // Clear client-side data instantly
        sessionStorage.clear();
        localStorage.clear();
        
        // Instant redirect - bypass server processing for speed
        document.cookie = "PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        window.location.replace('../index.php');
    }
};

// Content Editor Functions
const ContentEditor = {
    initialize() {
        const form = document.getElementById('content-editor-form');
        const resetBtn = document.getElementById('reset-content');

        if (form) {
            form.addEventListener('submit', this.saveContent);
            this.loadCurrentContent();
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', this.resetContent);
        }
    },

    loadCurrentContent() {
        fetch('../api/endpoints.php?action=get_content', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const content = data.data;
                if (content.main_title) document.getElementById('main-title').value = content.main_title;
                if (content.main_description) document.getElementById('main-description').value = content.main_description;
                if (content.feature_1) document.getElementById('feature-1').value = content.feature_1;
                if (content.feature_2) document.getElementById('feature-2').value = content.feature_2;
                if (content.feature_3) document.getElementById('feature-3').value = content.feature_3;
                if (content.footer_text) document.getElementById('footer-text').value = content.footer_text;
            }
        })
        .catch(error => {
            console.error('Error loading content:', error);
        });
    },

    saveContent(e) {
        e.preventDefault();

        const formData = {
            main_title: document.getElementById('main-title').value,
            main_description: document.getElementById('main-description').value,
            feature_1: document.getElementById('feature-1').value,
            feature_2: document.getElementById('feature-2').value,
            feature_3: document.getElementById('feature-3').value,
            footer_text: document.getElementById('footer-text').value
        };

        // Send data to server with correct relative path
        fetch('../api/endpoints.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update_content',
                data: formData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ContentEditor.showMessage('Contenido actualizado exitosamente', 'success');
                
                // Update the navigation title immediately
                const newTitle = document.getElementById('main-title').value;
                const navTitle = document.querySelector('nav h1');
                if (navTitle && newTitle) {
                    navTitle.textContent = newTitle;
                }
                
                // Update page title
                if (newTitle) {
                    document.title = newTitle + ' - Kingdom of Habbo';
                }
            } else {
                ContentEditor.showMessage(data.message || 'Error al actualizar contenido', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            ContentEditor.showMessage('Error de conexión', 'error');
        });
    },

    resetContent() {
        if (confirm('¿Estás seguro de que quieres restablecer todos los campos a sus valores predeterminados?')) {
            document.getElementById('main-title').value = 'Welcome to Reino de Habbo';
            document.getElementById('main-description').value = 'Enter a legendary medieval kingdom where honor defines your destiny. Rise through ancient ranks, forge powerful alliances, and command respect in the most prestigious virtual realm ever created. Your journey from commoner to royalty starts here.';
            document.getElementById('feature-1').value = 'Medieval Combat';
            document.getElementById('feature-2').value = 'Kingdom Building';
            document.getElementById('feature-3').value = 'Royal Hierarchy';
            document.getElementById('footer-text').value = 'Una comunidad medieval virtual';

            ContentEditor.showMessage('Contenido restablecido', 'success');
        }
    },

    showMessage(message, type) {
        const existingMessage = document.querySelector('.content-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `content-message ${type === 'success' ? 'success-message' : 'error-message'} mb-4 p-3 rounded`;
        messageDiv.textContent = message;

        const form = document.getElementById('content-editor-form');
        if (form) {
            form.insertBefore(messageDiv, form.firstChild);

            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    }
};

// Copy mission text function
function copyMission() {
    const missionText = document.getElementById('mission-text')?.textContent;
    if (missionText) {
        navigator.clipboard.writeText(missionText).then(function() {
            const button = event.target.closest('button');
            if (button) {
                const originalHTML = button.innerHTML;
                button.innerHTML = `
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                `;
                button.classList.add('text-green-400');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('text-green-400');
                    button.classList.add('text-yellow-400');
                }, 1500);
            }
        }).catch(function(err) {
            console.error('Error al copiar: ', err);
        });
    }
}

// Initialize event listeners
function initializeEventListeners() {
    const body = document.body;

    // Use event delegation for better performance
    body.addEventListener('click', (e) => {
        const target = e.target.closest('[data-action]');
        if (target) {
            const action = target.dataset.action;
            switch (action) {
                case 'logout':
                    if (AuthManager.logout) {
                        AuthManager.logout();
                    } else {
                        // Fallback logout
                        window.location.href = '../api/auth.php?action=logout';
                    }
                    break;
                case 'switch-section':
                    const section = target.getAttribute('data-section');
                    if (section) switchSection(section);
                    break;
            }
            return;
        }

        // Check for sidebar items with data-section attribute
        const sidebarItem = e.target.closest('.sidebar-item[data-section]');
        if (sidebarItem) {
            e.preventDefault();
            const section = sidebarItem.getAttribute('data-section');
            if (section) {
                switchSection(section);
            }
        }
    }, { passive: false });

    // Optimized smooth scrolling
    body.addEventListener('click', (e) => {
        const anchor = e.target.closest('a[href^="#"]');
        if (anchor) {
            e.preventDefault();
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    }, { passive: false });
}

// DOM Ready Handler
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing app...');

    // Initialize all managers in sequence
    setTimeout(() => {
        try {
            AuthManager.initialize();
            console.log('AuthManager initialized');
            
            SidebarManager.initialize();
            console.log('SidebarManager initialized');
            
            ContentEditor.initialize();
            console.log('ContentEditor initialized');
            
            initializeEventListeners();
            console.log('Event listeners initialized');
        } catch (error) {
            console.error('Initialization error:', error);
        }
    }, 100);
});

// Utility Functions
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