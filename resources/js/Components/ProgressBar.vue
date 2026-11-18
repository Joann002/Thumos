<template>
    <div class="progress">
        <div 
            class="progress__bar" 
            :style="{ width: `${percentage}%` }"
            :aria-label="label"
            :aria-valuenow="percentage"
            aria-valuemin="0"
            aria-valuemax="100"
            role="progressbar"
        >
            <span v-if="showLabel" class="progress__label">{{ percentage }}%</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, default: 'Progression' },
    value: {
        type: Number,
        required: true,
    },
    max: {
        type: Number,
        default: 100,
    },
    showLabel: {
        type: Boolean,
        default: false,
    },
});

const percentage = computed(() => {
    if (!Number.isFinite(props.value) || !Number.isFinite(props.max) || props.max <= 0) return 0;
    return Math.max(0, Math.min(Math.round((props.value / props.max) * 100), 100));
});
</script>

<style scoped>
.progress__label {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 0.7rem;
    font-weight: 600;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.progress__bar {
    position: relative;
}
</style>
