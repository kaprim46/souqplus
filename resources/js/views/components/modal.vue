<template>
    <div id="hs-modal" class="hs-overlay w-full h-full fixed top-0 start-0 z-[60] overflow-x-hidden overflow-y-auto bg-black bg-opacity-60" :class="{'open': isOpen}" v-if="isOpen">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all md:max-w-2xl md:w-full m-3 md:mx-auto">
            <div class="relative flex flex-col bg-white border shadow-sm rounded-xl overflow-hidden dark:bg-gray-800 dark:border-gray-700">
                <header class="flex justify-between p-3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                        <slot name="title">
                        </slot>
                    </h2>

                    <button
                        @click="close"
                        type="button"
                        class="flex justify-center items-center w-7 h-7 text-sm font-semibold rounded-lg border border-transparent text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-transparent dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" data-hs-overlay="#hs-danger-alert">
                        <span class="sr-only">Close</span>
                        <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </header>

                <div class="p-4 overflow-y-auto">
                    <slot name="content">
                    </slot>
                </div>

                <div class="flex justify-end items-center gap-x-2 py-3 px-4 bg-gray-50 border-t dark:bg-gray-800 dark:border-gray-700" v-if="$slots.footer">
                    <slot name="footer">
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, watch } from 'vue'

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    }
})

const isOpen = ref(props.isOpen)
const emit = defineEmits(['update:closeModal'])

watch(() => props.isOpen, (value) => {
    isOpen.value = value
})

const close = () => {
    emit('update:closeModal', false)
}
</script>
