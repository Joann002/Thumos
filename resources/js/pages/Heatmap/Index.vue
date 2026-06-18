<script setup>
import { computed } from 'vue';

const props = defineProps({
    weeks: {
        type: Array,
        required: true,
    },
    habitCount: {
        type: Number,
        required: true,
    },
    totalCompletions: {
        type: Number,
        required: true,
    },
    rangeLabel: {
        type: String,
        required: true,
    },
});

const CELL = 12;
const GAP = 3;
const STEP = CELL + GAP;

const colors = [
    'var(--heat-0)',
    'var(--heat-1)',
    'var(--heat-2)',
    'var(--heat-3)',
    'var(--heat-4)',
];

const width = computed(() => props.weeks.length * STEP);
const height = 7 * STEP;

const formatDate = (date) =>
    new Date(date).toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
</script>

<template>
    <div>
        <h1 class="page-title">Régularité</h1>
        <p class="muted" style="margin-top: -0.75rem">
            {{ totalCompletions }} complétion(s) sur la période ({{ rangeLabel }}).
        </p>

        <div class="card">
            <div class="heatmap">
                <svg :width="width" :height="height" :viewBox="`0 0 ${width} ${height}`" role="img" aria-label="Heatmap de régularité">
                    <template v-for="(week, wi) in weeks" :key="wi">
                        <rect
                            v-for="(cell, di) in week"
                            :key="cell.date"
                            :x="wi * STEP"
                            :y="di * STEP"
                            :width="CELL"
                            :height="CELL"
                            :fill="cell.future ? 'transparent' : colors[cell.level]"
                            :stroke="cell.future ? 'transparent' : 'var(--heat-line)'"
                            stroke-width="1"
                        >
                            <title v-if="!cell.future">{{ formatDate(cell.date) }} — {{ cell.count }} habitude(s)</title>
                        </rect>
                    </template>
                </svg>
            </div>

            <div class="heatmap__legend">
                <span>Moins</span>
                <svg :width="5 * STEP" :height="CELL">
                    <rect
                        v-for="(color, i) in colors"
                        :key="i"
                        :x="i * STEP"
                        y="0"
                        :width="CELL"
                        :height="CELL"
                        :fill="color"
                        stroke="var(--heat-line)"
                        stroke-width="1"
                    />
                </svg>
                <span>Plus</span>
            </div>
        </div>
    </div>
</template>
