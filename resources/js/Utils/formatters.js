/**
 * Format a number with thousand separators
 */
export function formatNumber(number) {
    return new Intl.NumberFormat('fr-FR').format(number);
}

/**
 * Format a percentage
 */
export function formatPercentage(value, decimals = 1) {
    return `${value.toFixed(decimals)} %`;
}

/**
 * Format duration in seconds to human readable
 */
export function formatDuration(seconds) {
    if (seconds < 60) {
        return `${seconds}s`;
    }

    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) {
        return `${minutes}min`;
    }

    const hours = Math.floor(minutes / 60);
    const remainingMinutes = minutes % 60;
    
    if (remainingMinutes === 0) {
        return `${hours}h`;
    }
    
    return `${hours}h ${remainingMinutes}min`;
}

/**
 * Truncate text to a maximum length
 */
export function truncate(text, maxLength = 100, suffix = '...') {
    if (!text || text.length <= maxLength) {
        return text;
    }

    return text.substring(0, maxLength).trim() + suffix;
}

/**
 * Format file size
 */
export function formatFileSize(bytes) {
    const units = ['o', 'Ko', 'Mo', 'Go'];
    let size = bytes;
    let unitIndex = 0;

    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }

    return `${size.toFixed(1)} ${units[unitIndex]}`;
}

/**
 * Pluralize French words
 */
export function pluralize(count, singular, plural) {
    return count <= 1 ? singular : plural || `${singular}s`;
}
