<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <i class="icon mgc_new_folder_line"></i>
                                {{ $t('Create New Page') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Create a new page and add it to your database') }}
                            </p>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Form -->
                    <div class="px-6 py-4">
                        <data-form
                            :form="form"
                            @update:form="form = $event"
                            :errors="errors"
                            @update:errors="errors = $event"
                        />
                    </div>
                    <!-- End Form -->
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import DataForm from "./components/DataForm.vue"
import { Page, statusEnum } from "@/types/Page";

// This provides the initial state for your working DataForm component.
// `icon: null` is important so the v-if in your form component works correctly.
const form = ref<Page>({
    name: '',
    status: statusEnum.inactive,
    content: '',
    slug: '',
    icon: null, // Initial state for the image is null
})

const errors = ref({})
</script>