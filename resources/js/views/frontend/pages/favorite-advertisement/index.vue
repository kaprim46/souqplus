<template>
    <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto flex flex-col gap-5">
        <template v-if="!customerAdvertisementStore.isLoading">
            <!-- Header -->
          <h1 class="text-xl md:text-4xl font-extrabold text-prussianblue dark:text-gray-300 flex items-center gap-1">
                <i class="icon mgc_heart_line"></i>
                <span class="block">
                    {{ $t('Favorite ads') }}
                </span>
            </h1>

            <hr />
            <!-- End Header -->

            <!-- Data -->
            <div v-if="customerAdvertisementStore.advertisements"  class="flex flex-col divide-y divide-gray-200 ads-list w-full">
              <v-advertisement
                               v-for="advertisement in customerAdvertisementStore.favorites"
                               :key="advertisement.id"
                               :advertisement="advertisement"
                               :is-from-array="false"
              />
            </div>
            <!-- End Data -->

            <!-- Empty -->
            <img v-if="!customerAdvertisementStore.isLoading && !customerAdvertisementStore.favorites.length"
                 :src="`${$assetUrl}/empty-trash.svg`" alt="Empty" class="w-[40rem]" />
            <!-- End Empty -->

            <!-- Footer -->
            <div
                v-if="!customerAdvertisementStore.isLoading && customerAdvertisementStore.favorites.length"
                class=" py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-50 dark:border-gray-700 w-full">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-semibold text-gray-800 dark:text-gray-200">
                            {{ customerAdvertisementStore.pagination.total }}
                        </span>
                        {{ $t('Results') }}
                    </p>
                </div>

                <div>
                    <div class="inline-flex gap-x-2">
                        <button
                            @click="customerAdvertisementStore.fetchFavoriteAdvertisements(true, true)"
                            :disabled="customerAdvertisementStore.pagination.current_page === 1"
                            type="button"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                            <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                            {{ $t('Previous') }}
                        </button>

                        <button
                            @click="customerAdvertisementStore.fetchFavoriteAdvertisements(true)"
                            :disabled="customerAdvertisementStore.pagination.current_page === customerAdvertisementStore.pagination.last_page"
                            type="button"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                            {{ $t('Next') }}
                            <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- End Footer -->
        </template>
        <div v-else class="flex justify-center items-center">
            <v-loader color="blue" :size="8"></v-loader>
        </div>
    </div>

</template>

<script lang="ts" setup>
import {useCustomerAdvertisementStore} from "@/stores/Customer/CustomerAdvertisementStore";
import {nextTick, onMounted} from "vue";
import {useSettingsStore} from "@/stores/SettingsStore";
import VAdvertisement from "@/views/components/advertisement.vue";

const customerAdvertisementStore = useCustomerAdvertisementStore();
const settingsStore = useSettingsStore();

onMounted(async () => {
    customerAdvertisementStore.filters.pagination = 1;

    await nextTick();

    await customerAdvertisementStore.fetchFavoriteAdvertisements();
});
</script>
