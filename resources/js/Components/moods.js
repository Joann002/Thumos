export const moods = [
    { value: 'great', label: 'Excellent', emoji: '😄' },
    { value: 'good', label: 'Bien', emoji: '🙂' },
    { value: 'neutral', label: 'Neutre', emoji: '😐' },
    { value: 'bad', label: 'Difficile', emoji: '😕' },
    { value: 'awful', label: 'Mauvais', emoji: '😞' },
];

export const moodMap = Object.fromEntries(moods.map((m) => [m.value, m]));
