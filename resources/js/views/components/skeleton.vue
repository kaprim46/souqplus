<template>
    <div class="animate-pulse">
        <h3 class="h-4 bg-gray-200 rounded-full dark:bg-gray-700" style="width: 40%;"></h3>

        <ul class="mt-5 space-y-3">
            <li class="w-full h-4 bg-gray-200 rounded-full dark:bg-gray-700" v-for="i in loopNumber" :key="i"></li>
        </ul>
    </div>
</template>

<script lang="ts" setup>
const props = defineProps({
    loopNumber: {
        type: Number,
        default: 10,
    },
});
</script>
