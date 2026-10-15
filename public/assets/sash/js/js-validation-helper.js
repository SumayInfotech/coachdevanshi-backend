/**
 * jsValidate('required', { field: 'Email' })
 * jsValidate('min', { field: 'Name', min: 2 })
 */
window.jsValidate = function (key, params = {}) {
    let message = window.jsValidationMessages[key] || '';

    Object.keys(params).forEach(param => {
        message = message.replace(`{${param}}`, params[param]);
    });

    return message;
};