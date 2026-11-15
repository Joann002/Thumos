/**
 * Validate email format
 */
export function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Validate required field
 */
export function isRequired(value) {
    if (typeof value === 'string') {
        return value.trim().length > 0;
    }
    return value !== null && value !== undefined;
}

/**
 * Validate minimum length
 */
export function minLength(value, min) {
    return value && value.length >= min;
}

/**
 * Validate maximum length
 */
export function maxLength(value, max) {
    return !value || value.length <= max;
}

/**
 * Validate URL format
 */
export function isValidUrl(url) {
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
}

/**
 * Validate date format (YYYY-MM-DD)
 */
export function isValidDate(dateString) {
    const regex = /^\d{4}-\d{2}-\d{2}$/;
    if (!regex.test(dateString)) return false;

    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

/**
 * Validate hex color
 */
export function isValidHexColor(color) {
    const regex = /^#[0-9A-Fa-f]{6}$/;
    return regex.test(color);
}

/**
 * Run multiple validators
 */
export function validate(value, validators) {
    const errors = [];

    for (const validator of validators) {
        const result = validator(value);
        if (result !== true) {
            errors.push(result);
        }
    }

    return errors.length === 0 ? true : errors;
}
