<template>
    <div class="relative flex items-center gap-2">
        <input
            type="checkbox"
            :id="id"
            class="peer relative shrink-0 w-11 h-6 p-px bg-gray-100 border-transparent text-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:ring-blue-600 disabled:opacity-50 disabled:pointer-events-none checked:bg-none checked:text-blue-600 checked:border-blue-600 focus:checked:border-blue-600 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-600 before:inline-block before:w-5 before:h-5 before:bg-white checked:before:bg-blue-200 before:translate-x-0 checked:before:translate-x-full before:rounded-full before:shadow before:transform before:ring-0 before:transition before:ease-in-out before:duration-200 dark:before:bg-gray-400 dark:checked:before:bg-blue-200"
            v-model="checked"
        />
        <label :for="id" class="peer-checked:text-blue-600 text-gray-500 text-sm font-medium cursor-pointer transition-colors ease-in-out duration-200">
            {{ id }}
        </label>
        <span v-if="checked" class="peer-checked:text-white text-gray-500 w-5 h-5 absolute top-[3px] start-0.5 flex justify-center items-center pointer-events-none transition-colors ease-in-out duration-200">
          <svg class="flex-shrink-0 w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
          </svg>
        </span>
        <span v-else class="peer-checked:text-blue-600 text-gray-500 w-5 h-5 absolute top-[3px] end-0.5 flex justify-center items-center pointer-events-none transition-colors ease-in-out duration-200">
          <svg class="flex-shrink-0 w-3 h-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12" />
          </svg>
    </span>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const { id } = defineProps(['id']);

const checked = ref(false);
</script>
