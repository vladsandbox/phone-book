// Utility functions for form handling and UI management

/**
 * Toggle alert visibility and set message
 * @param {HTMLElement} alertElement - The alert element to toggle
 * @param {boolean} show - Whether to show or hide the alert
 * @param {string} message - The message to display (supports HTML)
 */
function toggleAlert(alertElement, show, message = '') {
    if (show) {
        alertElement.innerHTML = message;
        alertElement.classList.remove('d-none');
    } else {
        alertElement.classList.add('d-none');
    }
}

/**
 * Hide all alert elements
 * @param {HTMLElement[]} alerts - Array of alert elements
 */
function hideAllAlerts(...alerts) {
    alerts.forEach(alert => alert.classList.add('d-none'));
}

/**
 * Set button loading state
 * @param {HTMLButtonElement} button - The button element
 * @param {boolean} loading - Whether button is in loading state
 * @param {string} loadingText - Text to show during loading
 * @param {string} defaultText - Default button text
 */
function setButtonLoading(button, loading, loadingText = 'Loading...', defaultText = 'Submit') {
    button.disabled = loading;
    button.innerHTML = loading
        ? `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText}`
        : defaultText;
}

/**
 * Handle form submission with JSON payload
 * @param {string} url - The API endpoint
 * @param {Object} formData - The form data to send
 * @returns {Promise<Object>} The response data
 */
async function submitForm(url, formData) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    });
    return response.json();
}

/**
 * Handle form submission and UI updates
 * @param {Object} config - Configuration object
 * @param {HTMLFormElement} config.form - The form element
 * @param {string} config.url - The API endpoint
 * @param {Function} config.getFormData - Function to get form data
 * @param {HTMLButtonElement} config.submitBtn - Submit button element
 * @param {HTMLElement} config.errorAlert - Error alert element
 * @param {HTMLElement} config.successAlert - Success alert element
 * @param {string} config.loadingText - Text to show during loading
 * @param {string} config.defaultBtnText - Default button text
 * @param {Function} config.onSuccess - Success callback
 */
async function handleFormSubmission(config) {
    const {
        form,
        url,
        getFormData,
        submitBtn,
        errorAlert,
        successAlert,
        loadingText = 'Submitting...',
        defaultBtnText = 'Submit',
        onSuccess = () => {}
    } = config;

    // Hide previous alerts
    hideAllAlerts(errorAlert, successAlert);

    // Set loading state
    setButtonLoading(submitBtn, true, loadingText, defaultBtnText);

    try {
        const formData = getFormData();
        const data = await submitForm(url, formData);

        if (data.success) {
            toggleAlert(successAlert, true, data.message);
            onSuccess(data);
        } else {
            const errorMessage = data.errors ? data.errors.join('<br>') : 'An error occurred';
            toggleAlert(errorAlert, true, errorMessage);
        }
    } catch (error) {
        console.error('Form submission error:', error);
        toggleAlert(errorAlert, true, 'An error occurred. Please try again later.');
    } finally {
        setButtonLoading(submitBtn, false, loadingText, defaultBtnText);
    }
}

/**
 * Get form data as object
 * @param {string[]} fieldIds - Array of field IDs
 * @returns {Object} Form data object
 */
function getFormDataObject(fieldIds) {
    const data = {};
    fieldIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            data[id] = element.value;
        }
    });
    return data;
}

/**
 * Show alert message (for use in home.js style)
 * @param {string} message - The message to display
 * @param {string} type - Alert type (success, danger, warning, info)
 */
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        console.warn('Alert container not found');
        return;
    }

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alertDiv);

    // Auto-hide after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}