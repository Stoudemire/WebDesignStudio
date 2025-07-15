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

// Logo Editor Functions
const LogoEditor = {
    initialize() {
        const uploadInput = document.getElementById('logo-upload');
        const previewDiv = document.getElementById('logo-preview');
        const previewImg = document.getElementById('preview-image');
        const saveBtn = document.getElementById('save-logo');
        const resetBtn = document.getElementById('reset-logo');
        const testFloatBtn = document.getElementById('test-float-effect');

        if (uploadInput) {
            uploadInput.addEventListener('change', this.handleFileUpload.bind(this));
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', this.saveLogo.bind(this));
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', this.resetLogo.bind(this));
        }

        if (testFloatBtn) {
            testFloatBtn.addEventListener('click', this.testFloatEffect.bind(this));
        }

        this.loadCurrentLogo();
    },

    handleFileUpload(event) {
        const file = event.target.files[0];
        const previewDiv = document.getElementById('logo-preview');
        const previewImg = document.getElementById('preview-image');
        const saveBtn = document.getElementById('save-logo');

        if (file) {
            // Validate file type
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                this.showMessage('Por favor selecciona un archivo PNG, JPG, SVG o GIF', 'error');
                return;
            }

            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                this.showMessage('El archivo es demasiado grande. Máximo 2MB', 'error');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('hidden');
                previewDiv.classList.add('logo-preview');
                saveBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    },

    saveLogo() {
        const fileInput = document.getElementById('logo-upload');
        const file = fileInput.files[0];

        if (!file) {
            this.showMessage('Por favor selecciona un archivo', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('logo', file);
        formData.append('action', 'upload_logo');

        fetch('../api/endpoints.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showMessage('Logo actualizado exitosamente', 'success');
                this.loadCurrentLogo();

                // Update navigation logo immediately
                this.updateNavigationLogo(data.logo_url);

                // Reset form
                fileInput.value = '';
                document.getElementById('logo-preview').classList.add('hidden');
                document.getElementById('save-logo').disabled = true;
            } else {
                this.showMessage(data.message || 'Error al subir el logo', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showMessage('Error de conexión', 'error');
        });
    },

    loadCurrentLogo() {
        fetch('../api/endpoints.php?action=get_logo')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.logo_url) {
                const currentLogoDiv = document.getElementById('current-logo');
                currentLogoDiv.innerHTML = `<img src="${data.logo_url}" alt="Logo actual" class="max-h-32 max-w-full object-contain">`;
            }
        })
        .catch(error => {
            console.error('Error loading current logo:', error);
        });
    },

    resetLogo() {
        if (confirm('¿Estás seguro de que quieres restablecer el logo por defecto?')) {
            fetch('../api/endpoints.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'reset_logo'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showMessage('Logo restablecido exitosamente', 'success');
                    this.loadCurrentLogo();
                    this.updateNavigationLogo(null); // Reset to default
                } else {
                    this.showMessage(data.message || 'Error al restablecer el logo', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showMessage('Error de conexión', 'error');
            });
        }
    },

    testFloatEffect() {
        // Create a temporary splash logo for testing with current logo
        fetch('../api/endpoints.php?action=get_logo')
        .then(response => response.json())
        .then(data => {
            let logoContent;
            if (data.success && data.logo_url) {
                logoContent = `<img src="${data.logo_url}" alt="Logo">`;
            } else {
                logoContent = `
                    <svg viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400">
                        <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                    </svg>
                `;
            }

            const testSplash = document.createElement('div');
            testSplash.className = 'splash-logo';
            testSplash.innerHTML = `
                <div class="splash-logo-content">
                    ${logoContent}
                </div>
            `;

            document.body.appendChild(testSplash);

            // Remove after animation completes (5.3 seconds for smooth cleanup)
            setTimeout(() => {
                testSplash.style.transition = 'opacity 0.3s ease';
                testSplash.style.opacity = '0';
                setTimeout(() => {
                    testSplash.remove();
                }, 300);
            }, 5300);
        })
        .catch(error => {
            console.error('Error testing splash logo:', error);
        });
    },

    updateNavigationLogo(logoUrl) {
        const navLogo = document.querySelector('nav .crown-icon');
        if (navLogo) {
            if (logoUrl) {
                navLogo.innerHTML = `<img src="${logoUrl}" alt="Logo" class="w-8 h-8 object-contain">`;
            } else {
                navLogo.innerHTML = `
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                    </svg>
                `;
            }
        }
    },

    showMessage(message, type) {
        const existingMessage = document.querySelector('.logo-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `logo-message ${type === 'success' ? 'success-message' : 'error-message'} mb-4 p-3 rounded`;
        messageDiv.textContent = message;

        const section = document.getElementById('logo-section');
        const firstCard = section.querySelector('.glass');
        if (firstCard) {
            firstCard.insertAdjacentElement('afterend', messageDiv);

            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
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

// Splash Logo Manager for Index Page - Optimized
const SplashLogoManager = {
    initialize() {
        // Only run on index page
        if (window.location.pathname.includes('index.php') || window.location.pathname === '/' || window.location.pathname.endsWith('/')) {
            this.preparePageForSplash();
            this.createOptimizedSplashLogo();
        }
    },

    preparePageForSplash() {
        // Hide main content immediately with CSS class
        const elements = document.querySelectorAll('main, nav, footer');
        elements.forEach(element => {
            if (element) {
                element.classList.add('splash-hidden');
            }
        });
    },

    showMainContent() {
        // Show main content with fast, smooth animations
        const elements = document.querySelectorAll('main, nav, footer');
        let delay = 0;

        elements.forEach((element, index) => {
            if (element) {
                setTimeout(() => {
                    element.classList.remove('splash-hidden');
                    element.classList.add('content-reveal');
                    // Force hardware acceleration for each element
                    element.style.transform = 'translate3d(0,0,0)';
                }, delay);
                delay += 50; // Much faster stagger for immediate reveal
            }
        });
    },

    async createOptimizedSplashLogo() {
        try {
            // Create splash immediately with default logo
            const splashDiv = document.createElement('div');
            splashDiv.className = 'splash-logo';

            // Default logo content
            let logoContent = `
                <svg viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400">
                    <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                </svg>
            `;

            // Get page title from navigation or use default
            const navTitle = document.querySelector('nav h1');
            const pageTitle = navTitle ? navTitle.textContent : 'Reino de Habbo';

            splashDiv.innerHTML = `
                <div class="splash-title-content">
                    <h1 class="splash-title">${pageTitle}</h1>
                </div>
                <div class="splash-logo-content">
                    ${logoContent}
                </div>
            `;

            // Add to DOM immediately with forced hardware acceleration
            document.body.appendChild(splashDiv);

            // Force GPU acceleration
            splashDiv.style.transform = 'translate3d(0, 0, 0)';

            // Try to load custom logo in background (non-blocking)
            fetch('api/endpoints.php?action=get_logo')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.logo_url) {
                        const logoContainer = splashDiv.querySelector('.splash-logo-content');
                        if (logoContainer) {
                            logoContainer.innerHTML = `<img src="${data.logo_url}" alt="Logo">`;
                        }
                    }
                })
                .catch(() => {
                    console.log('Using default logo');
                });

            // Try to load custom title in background (non-blocking)
            fetch('api/endpoints.php?action=get_content')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && data.data.main_title) {
                        const titleContainer = splashDiv.querySelector('.splash-title');
                        if (titleContainer) {
                            titleContainer.textContent = data.data.main_title;
                        }
                    }
                })
                .catch(() => {
                    console.log('Using default title');
                });

            // Start showing content early while splash is still animating
            setTimeout(() => {
                this.showMainContent();
            }, 2000);

            // Remove splash after animation completes (3 seconds total)
            setTimeout(() => {
                splashDiv.style.transition = 'opacity 0.3s ease-out';
                splashDiv.style.opacity = '0';

                setTimeout(() => {
                    if (splashDiv.parentNode) {
                        splashDiv.parentNode.removeChild(splashDiv);
                    }
                }, 300);
            }, 3000);

        } catch (error) {
            console.error('Error creating splash logo:', error);
            // If there's an error, show main content immediately
            this.showMainContent();
        }
    }
};

// DOM Ready Handler
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing app...');

    // Initialize splash logo for index page
    SplashLogoManager.initialize();

    // Initialize all managers in sequence
    setTimeout(() => {
        try {
            AuthManager.initialize();
            console.log('AuthManager initialized');

            SidebarManager.initialize();
            console.log('SidebarManager initialized');

            ContentEditor.initialize();
            console.log('ContentEditor initialized');

            LogoEditor.initialize();
            console.log('LogoEditor initialized');

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