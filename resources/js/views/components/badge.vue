<template>
  <span :class="badgeClasses" v-html="text"></span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
    text: {
        type: [String, Number],
        default: "Badge",
    },
    color: {
        type: String,
        default: "red",
    },
    darkMode: {
        type: Boolean,
        default: false,
    },
});

const badgeClasses = computed(() => {
    const colorClass = (() => {
        switch (props.color) {
            case 'teal':
                return props.darkMode ? 'bg-teal-800/30' : 'bg-teal-200 text-teal-800';
            case 'blue':
                return props.darkMode ? 'bg-blue-800/30' : 'bg-blue-200 text-blue-800';
            case 'red':
                return props.darkMode ? 'bg-red-800/30' : 'bg-red-200 text-red-800';
            case 'yellow':
                return props.darkMode ? 'bg-yellow-800/30' : 'bg-yellow-200 text-yellow-800';
            case 'green':
                return props.darkMode ? 'bg-green-800/30' : 'bg-green-200 text-green-800';
            case 'purple':
                return props.darkMode ? 'bg-purple-800/30' : 'bg-purple-200 text-purple-800';
            case 'pink':
                return props.darkMode ? 'bg-pink-800/30' : 'bg-pink-200 text-pink-800';
            case 'indigo':
                return props.darkMode ? 'bg-indigo-800/30' : 'bg-indigo-200 text-indigo-800';
            case 'gray':
                return props.darkMode ? 'bg-gray-800/30' : 'bg-gray-200 text-gray-800';
            // Add more cases as needed
            default:
                return props.darkMode ? 'bg-gray-800/30' : 'bg-gray-200 text-gray-800';
        }
    })();

    return {
        'w-fit': true,
        'inline-flex': true,
        'items-center': true,
        'gap-x-1.5': true,
        'py-1.5': true,
        'px-3': true,
        'rounded-full': true,
        'text-xs': true,
        'font-medium': true,
        'whitespace-nowrap': true,
        [colorClass]: true,
        'dark:bg-white/[.05]': props.darkMode,
        'bg-gray-50': !props.darkMode,
    };
});
</script>

<style scoped>
/* Add any additional styles specific to this component */
</style>
