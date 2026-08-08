// Global JavaScript for Travel Journey

// Mobile menu toggle (if needed in future)
function toggleMenu() {
    const nav = document.querySelector('nav ul');
    nav.classList.toggle('active');
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let valid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            valid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });

    return valid;
}

// Image upload preview
function previewImages(input, previewContainer) {
    const container = document.getElementById(previewContainer);
    container.innerHTML = '';

    if (input.files) {
        Array.from(input.files).forEach(file => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px';
                    img.style.margin = '5px';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Confirmation dialog for deletions
function confirmDelete(message = 'Are you sure you want to delete this?') {
    return confirm(message);
}

// AJAX wrapper
function ajaxRequest(url, method = 'GET', data = null) {
    return fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: data ? JSON.stringify(data) : null
    })
    .then(response => response.json());
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add form validation to all forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form.id)) {
                e.preventDefault();
            }
        });
    });

    // Add image preview to file inputs
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = input.id + 'Preview';
            if (document.getElementById(previewId)) {
                previewImages(input, previewId);
            }
        });
    });
});