import { ref } from 'vue';

const notifications = ref([]);
let notificationId = 0;

export function useNotification() {
    const add = (message, type = 'success', duration = 3000) => {
        const id = ++notificationId;
        const notification = {
            id,
            message,
            type,
        };

        notifications.value.push(notification);

        if (duration > 0) {
            setTimeout(() => {
                remove(id);
            }, duration);
        }

        return id;
    };

    const remove = (id) => {
        const index = notifications.value.findIndex((n) => n.id === id);
        if (index > -1) {
            notifications.value.splice(index, 1);
        }
    };

    const success = (message, duration) => add(message, 'success', duration);
    const error = (message, duration) => add(message, 'error', duration);
    const warning = (message, duration) => add(message, 'warning', duration);
    const info = (message, duration) => add(message, 'info', duration);

    return {
        notifications,
        add,
        remove,
        success,
        error,
        warning,
        info,
    };
}
