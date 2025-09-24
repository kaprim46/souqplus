<template>
  <div class="flex flex-col">
    <div class="-m-1.5 overflow-x-auto">
      <div class="p-1.5 min-w-full inline-block align-middle">
        <div
          class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700"
        >
          <!-- Header & form -->
          <template v-if="!isLoading">
            <!-- Header -->
            <div
              class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700"
            >
              <div>
                <h2
                  class="text-xl font-semibold text-gray-800 dark:text-gray-200"
                >
                  <i class="icon mgc_edit_2_fill"></i>
                  {{ $t('Edit advertisement') }} - {{ form.name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ $t('Edit your advertisement') }}
                </p>
              </div>
            </div>
            <!-- End Header -->

            <!-- Form -->
            <div class="px-6 py-4">
              <advertisement-form
                :form="form"
                @update:form="form = $event"
                :errors="errors"
                @update:errors="errors = $event"
              />
            </div>
            <!-- End Form -->
          </template>

          <!--Loader-->
          <div v-if="isLoading" class="flex items-center justify-center p-4">
            <v-loader :size="4" :dark="true" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue';
import { Advertisement } from '@/types/Advertisements';
import { useRoute, useRouter } from 'vue-router';
import { useUserStore } from '@/stores/UserStore';
import { useSubSubCategoryStore } from '@/stores/SubSubCategoryStore';
import axios from '@/axios';
import AdvertisementForm from '@/views/dashboard/pages/advertisements/components/DataForm.vue';

const userStore = useUserStore();
const subSubCategoryStore = useSubSubCategoryStore();
const form = ref({} as Advertisement);
const errors = ref({});

const route = useRoute();
const router = useRouter();
const isLoading = ref(false);

const getDetails = async () => {
  if (!route.params.id) return;

  isLoading.value = true;

  try {
    const { data } = await axios.get(`/advertisements/${route.params.id}`);

    if (data.ok) {
      form.value = data.advertisement;

      // If we have a sub-category and sub-sub-category, fetch the sub-sub-categories
      if (form.value.sub_category?.id) {
        await subSubCategoryStore.fetchSubSubCategories(
          form.value.sub_category.id
        );
      }

      isLoading.value = false;
      return;
    }

    /**
     * Redirect back
     */
    await router.push({ name: 'advertisements.index' });
  } catch (error) {
    await router.push({ name: 'advertisements.index' });
  }
};

onMounted(async () => {
  await getDetails();
});
</script>
