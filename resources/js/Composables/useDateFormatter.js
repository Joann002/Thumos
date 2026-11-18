// Calendar dates are local civil dates, not UTC instants.
export function parseCalendarDate(value) {
    if (!value) return null;
    if (value instanceof Date) return Number.isNaN(value.getTime()) ? null : new Date(value);
    const text = String(value);
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(text);
    let date;
    if (match) {
        date = new Date(0);
        date.setFullYear(Number(match[1]), Number(match[2]) - 1, Number(match[3]));
        date.setHours(0, 0, 0, 0);
        if (date.getFullYear() !== Number(match[1]) || date.getMonth() !== Number(match[2]) - 1 || date.getDate() !== Number(match[3])) return null;
    } else {
        date = new Date(text);
    }
    return Number.isNaN(date.getTime()) ? null : date;
}

export function useDateFormatter() {
    const formatDate = (value, options = {}) => {
        const date = parseCalendarDate(value);
        return date ? date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric', ...options }) : '';
    };
    const formatDateShort = (value) => formatDate(value, { day: '2-digit', month: '2-digit', year: 'numeric' });
    const calendarDay = (date) => Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()) / 86400000;
    const difference = (value) => {
        const date = parseCalendarDate(value);
        return date ? Math.round(calendarDay(date) - calendarDay(new Date())) : null;
    };
    const formatRelativeTime = (value) => {
        const days = difference(value);
        if (days === null) return '';
        if (days === 0) return "Aujourd'hui";
        if (days === -1) return 'Hier';
        if (days === 1) return 'Demain';
        if (Math.abs(days) < 7) return days > 0 ? `Dans ${days} jours` : `Il y a ${-days} jours`;
        return formatDate(value);
    };
    return { formatDate, formatDateShort, formatRelativeTime,
        isToday: (value) => difference(value) === 0,
        isPast: (value) => { const days = difference(value); return days !== null && days < 0; },
    };
}
