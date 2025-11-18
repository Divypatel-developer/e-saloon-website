// Enhanced Admin Panel JavaScript with Consistent Styling
document.addEventListener('DOMContentLoaded', function() {
    // ============== TOOLTIP INITIALIZATION ==============
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover focus',
            customClass: 'admin-tooltip',
            animation: true
        });
    });

    // ============== FORM VALIDATION ==============
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Add custom invalid styling
                const invalidFields = form.querySelectorAll(':invalid');
                invalidFields.forEach(field => {
                    field.classList.add('is-invalid');
                    const feedback = field.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'block';
                    }
                });
            }
            form.classList.add('was-validated');
        }, false);

        // Reset validation on input
        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    const feedback = this.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'none';
                    }
                }
            });
        });
    });

    // ============== SERVICE SELECTION ==============
    const serviceSelect = document.getElementById('service_id');
    if (serviceSelect) {
        serviceSelect.addEventListener('change', function() {
            const serviceId = this.value;
            const summaryContainer = document.getElementById('service_summary');
            
            if (!serviceId) {
                summaryContainer.innerHTML = '';
                return;
            }
            
            // Add loading state
            summaryContainer.innerHTML = `
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            fetch(`service_details.php?id=${serviceId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok (Status: ' + response.status + ')');
                    return response.text(); // because PHP returns HTML
                })
                .then(html => {
                    summaryContainer.innerHTML = html; // directly insert HTML
                })
                .catch(error => {
                    summaryContainer.innerHTML = `
                        <div class="alert alert-danger py-2 mb-0">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Error loading service details: ${error.message}
                        </div>
                    `;
                    console.error("Fetch error:", error);
                });
        });
    }

    // ============== DATE PICKER RESTRICTIONS ==============
    const dateInput = document.getElementById('appointment_date');
    if (dateInput) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        
        // Format to YYYY-MM-DD
        const minDate = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', minDate);
        
        // Add date picker styling
        dateInput.addEventListener('focus', function() {
            this.classList.add('datepicker-active');
        });
        dateInput.addEventListener('blur', function() {
            this.classList.remove('datepicker-active');
        });
    }

    // ============== AUTO-CLOSING ALERTS ==============
    const alerts = document.querySelectorAll('.alert.auto-close');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.maxHeight = '0';
            alert.style.margin = '0';
            alert.style.padding = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// ============== DYNAMICALLY LOADED STYLES ==============
const style = document.createElement('style');
style.textContent = `
    /* Tooltip styling */
    .admin-tooltip .tooltip-inner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 8px 12px;
        font-size: 0.8rem;
        border-radius: 6px;
    }
    .admin-tooltip .tooltip-arrow {
        color: #667eea;
    }
    
    /* Date picker active state */
    .datepicker-active {
        border-color: #667eea !important;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25) !important;
    }
    
    /* Invalid form fields */
    .is-invalid {
        border-color: #dc3545 !important;
    }
    .invalid-feedback {
        display: none;
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
`;
document.head.appendChild(style);
 