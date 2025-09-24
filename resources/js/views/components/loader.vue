<template>
    <div :class="loaderClasses" role="status" aria-label="loading" :style="`width: ${props.size}rem; height: ${props.size}rem;`">
        <span class="sr-only">Loading...</span>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: Number,
        default: 6,
    },
    color: {
        type: String,
        default: 'blue',
    },
    dark: {
        type: Boolean,
        default: false,
    },
});

const loaderClasses = computed(() => {
    const colorClass = (() => {
        switch (props.color) {
            case 'red':
                return 'text-red-600';
            case 'blue':
                return 'text-blue-600';
            case 'yellow':
                return 'text-yellow-600';
            case 'green':
                return 'text-green-600';
            case 'purple':
                return 'text-purple-600';
            case 'pink':
                return 'text-pink-600';
            case 'indigo':
                return 'text-indigo-600';
            case 'gray':
                return 'text-gray-800';
            case 'orange':
                return 'text-orange-600';
            // Add more cases as needed
            default:
                return `text-${props.color}`;
        }
    })();

    return {
        'animate-spin': true,
        'inline-block': true,
        'border-[3px]': true,
        'border-current': true,
        'border-t-transparent': true,
        [colorClass]: true,
        'rounded-full': true,
        'dark:text-white': props.dark,
    };
});
</script>

<style scoped>
/* Add any additional styles specific to this component */
</style>
