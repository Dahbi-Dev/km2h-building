// assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    // Language switcher
    const langSwitch = document.getElementById('langSwitch');
    if (langSwitch) {
        langSwitch.addEventListener('change', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href;
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'lang';
            input.value = this.value;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        });
    }

    // Contact form validation
    const contactForm = document.querySelector('form[data-form="contact"]');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            // Basic email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                e.preventDefault();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-red-600 text-sm mt-1';
                errorDiv.textContent = 'Please enter a valid email address';
                emailInput.parentNode.appendChild(errorDiv);
                return false;
            }
        });
    }

    // Image preview for admin forms
    const imageInput = document.querySelector('input[type="file"]');
    const previewContainer = document.getElementById('imagePreview');
    if (imageInput && previewContainer) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" 
                             class="max-w-full h-auto max-h-48 rounded-md">
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Dynamic form handling for admin panel
    const adminForms = document.querySelectorAll('form[data-confirm]');
    adminForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const confirmMessage = this.dataset.confirm;
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // AJAX message status updates
    const statusButtons = document.querySelectorAll('[data-message-status]');
    statusButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const messageId = this.dataset.messageId;
            const newStatus = this.dataset.messageStatus;
            
            try {
                const response = await fetch('/admin/messages/update-status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: messageId,
                        status: newStatus
                    })
                });
                
                if (response.ok) {
                    const statusIndicator = document.querySelector(`#status-${messageId}`);
                    if (statusIndicator) {
                        statusIndicator.textContent = newStatus;
                        statusIndicator.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            newStatus === 'read' ? 'bg-blue-100 text-blue-800' : 
                            newStatus === 'replied' ? 'bg-green-100 text-green-800' : 
                            'bg-gray-100 text-gray-800'
                        }`;
                    }
                }
            } catch (error) {
                console.error('Error updating message status:', error);
            }
        });
    });
});