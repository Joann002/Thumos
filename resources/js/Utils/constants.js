export const GOAL_STATUSES = {
    PLANNED: 'planned',
    IN_PROGRESS: 'in_progress',
    COMPLETED: 'completed',
    ARCHIVED: 'archived',
};

export const GOAL_STATUS_LABELS = {
    [GOAL_STATUSES.PLANNED]: 'Planifié',
    [GOAL_STATUSES.IN_PROGRESS]: 'En cours',
    [GOAL_STATUSES.COMPLETED]: 'Terminé',
    [GOAL_STATUSES.ARCHIVED]: 'Archivé',
};

export const HABIT_FREQUENCIES = {
    DAILY: 'daily',
    WEEKLY: 'weekly',
};

export const HABIT_FREQUENCY_LABELS = {
    [HABIT_FREQUENCIES.DAILY]: 'Quotidien',
    [HABIT_FREQUENCIES.WEEKLY]: 'Hebdomadaire',
};

export const MOODS = {
    EXCELLENT: 'excellent',
    GOOD: 'good',
    NEUTRAL: 'neutral',
    BAD: 'bad',
    TERRIBLE: 'terrible',
};

export const MOOD_LABELS = {
    [MOODS.EXCELLENT]: 'Excellent',
    [MOODS.GOOD]: 'Bien',
    [MOODS.NEUTRAL]: 'Neutre',
    [MOODS.BAD]: 'Mauvais',
    [MOODS.TERRIBLE]: 'Terrible',
};

export const MOOD_COLORS = {
    [MOODS.EXCELLENT]: '#10b981',
    [MOODS.GOOD]: '#22d3ee',
    [MOODS.NEUTRAL]: '#fbbf24',
    [MOODS.BAD]: '#f87171',
    [MOODS.TERRIBLE]: '#dc2626',
};

export const DAYS_OF_WEEK = {
    MONDAY: 1,
    TUESDAY: 2,
    WEDNESDAY: 3,
    THURSDAY: 4,
    FRIDAY: 5,
    SATURDAY: 6,
    SUNDAY: 7,
};

export const DAY_LABELS = {
    [DAYS_OF_WEEK.MONDAY]: 'Lundi',
    [DAYS_OF_WEEK.TUESDAY]: 'Mardi',
    [DAYS_OF_WEEK.WEDNESDAY]: 'Mercredi',
    [DAYS_OF_WEEK.THURSDAY]: 'Jeudi',
    [DAYS_OF_WEEK.FRIDAY]: 'Vendredi',
    [DAYS_OF_WEEK.SATURDAY]: 'Samedi',
    [DAYS_OF_WEEK.SUNDAY]: 'Dimanche',
};

export const DAY_LABELS_SHORT = {
    [DAYS_OF_WEEK.MONDAY]: 'Lun',
    [DAYS_OF_WEEK.TUESDAY]: 'Mar',
    [DAYS_OF_WEEK.WEDNESDAY]: 'Mer',
    [DAYS_OF_WEEK.THURSDAY]: 'Jeu',
    [DAYS_OF_WEEK.FRIDAY]: 'Ven',
    [DAYS_OF_WEEK.SATURDAY]: 'Sam',
    [DAYS_OF_WEEK.SUNDAY]: 'Dim',
};
