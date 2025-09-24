<template>
    <div :class="[baseClass, colorClass]" role="alert">
        <span class="font-bold">{{ title }}</span> {{ message }}
        <slot></slot>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        color: {
            type: String as PropType<string>,
            default: 'gray', // Default color
        },
        title: {
            type: String as PropType<string>,
            default: 'Alert',
        },
        message: {
            type: String as PropType<string>,
            default: 'You should check in on some of those fields below.',
        },
    },
    computed: {
        baseClass(): string {
            return 'text-sm text-white rounded-lg p-4';
        },
        colorClass(): string {
            switch (this.color) {
                case 'red':
                    return 'bg-red-600 dark:bg-red-500';
                case 'green':
                    return 'bg-green-600 dark:bg-green-500';
                case 'blue':
                    return 'bg-blue-600 dark:bg-blue-500';
                case 'yellow':
                    return 'bg-yellow-600 dark:bg-yellow-500';
                case 'indigo':
                    return 'bg-indigo-600 dark:bg-indigo-500';
                case 'purple':
                    return 'bg-purple-600 dark:bg-purple-500';
                case 'pink':
                    return 'bg-pink-600 dark:bg-pink-500';
                default:
                    return 'bg-gray-600 dark:bg-gray-500';
            }
        },
    },
});
</script>

<style scoped>
/* Add your custom styles here */
</style>
