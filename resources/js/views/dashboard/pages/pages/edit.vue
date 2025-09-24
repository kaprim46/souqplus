<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header & form -->
                    <template v-if="!pageStore.isLoading">
                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                    <i class="icon mgc_edit_2_fill"></i>
                                    {{ $t('Edit page') }} - {{ pageStore.page.name }}
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $t('Update page informations and save it to your database') }}
                                </p>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Form -->
                        <div class="px-6 py-4">
                            <!-- Your working DataForm is rendered here with the fetched data -->
                            <data-form
                                :form="pageStore.page"
                                @update:form="pageStore.page = $event"
                                :errors="errors"
                                @update:errors="errors = $event"
                            />
                        </div>
                        <!-- End Form -->
                    </template>

                    <!--Loader-->
                    <div v-if="pageStore.isLoading" class="flex items-center justify-center p-8">
                        <v-loader :size="6" :dark="true" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
// NO CHANGES TO YOUR SCRIPT LOGIC
import { onMounted, ref } from 'vue'
import { useRoute } from "vue-router";
import DataForm from "./components/DataForm.vue"
import { usePageStore } from "@/stores/PageStore";

const errors = ref({})
const route = useRoute()

// States
const pageStore = usePageStore()

onMounted(async () => {
    if (!route.params.id) {
        return
    }
    // This fetches the page data, including the 'icon_url', which your DataForm will use
    await pageStore.fetchPage(route.params.id as any)
})
</script>