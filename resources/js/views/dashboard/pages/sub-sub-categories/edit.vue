<template>
    <div class="p-1.5">
        <div class="bg-white border rounded-xl dark:bg-slate-900 dark:border-gray-700">
            <template v-if="!isLoading">
                <!-- Header -->
                <div class="px-6 py-4 border-b dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        <i class="icon mgc_edit_2_fill"></i>
                        {{ $t('Edit') }} - {{ form.name }}
                    </h2>
                </div>
                <!-- End Header -->
                <!-- Form -->
                <div class="px-6 py-4">
                    <data-form :form-data="form" @update:form="form = $event" :errors="errors" @update:errors="errors = $event" />
                </div>
            </template>
            <div v-else class="flex justify-center p-4"><v-loader /></div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from "vue-router";
import DataForm from "../../pages/sub-sub-categories/components/DataForm.vue"
import { SubSubCategory } from "@/types/Category";
import axios from '@/axios';

const form = ref<SubSubCategory>({ name: '', status: 'active', sub_category_id: null });
const errors = ref({})
const route = useRoute()
const router = useRouter()
const isLoading = ref(false)

const getDetails = async () => {
    isLoading.value = true
    try {
        const { data } = await axios.get(`/dashboard/sub-sub-categories/${route.params.id}`);
        form.value = data.data; // Assuming API returns the object in a 'data' key
    } catch (error) {
        await router.back()
    } finally {
        isLoading.value = false
    }
}

onMounted(async () => {
    await getDetails()
})
</script>