<template>
    <div class="p-1.5">
        <div class="bg-white border rounded-xl dark:bg-slate-900 dark:border-gray-700">
            <!-- Header -->
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    <i class="icon mgc_new_folder_line"></i>
                    {{ $t('Create new sub sub category') }}
                </h2>
            </div>
            <!-- End Header -->

            <!-- Form -->
            <div class="px-6 py-4">
                <data-form :form-data="form" @update:form="form = $event" :errors="errors" @update:errors="errors = $event" />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import DataForm from "../../pages/sub-sub-categories/components/DataForm.vue"
import { SubSubCategory } from "@/types/Category";
import { useRoute } from 'vue-router'

const route = useRoute();
const errors = ref({})

const form = ref<SubSubCategory>({
    name: '',
    status: 'active',
    sub_category_id: Number(route.params.sub_category_id)
});
</script>