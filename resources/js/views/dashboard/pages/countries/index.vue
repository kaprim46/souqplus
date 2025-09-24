<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <i class="icon mgc_map_pin_fill"></i>
                                {{ $t('Countries') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Manage your countries') }}
                            </p>
                        </div>


                        <div class="flex gap-x-2">
                            <v-input :placeholder="$t('Search')"  />
                            <v-button color="blue" @click="isModalOpen = true" custom-class="whitespace-nowrap">
                                <i class="icon mgc_plus_line"></i>
                                {{ $t('Add Country') }}
                            </v-button>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="relative overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-slate-800 rtl:text-right ltr:text-left">
                            <tr>
                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                      {{ $t('Name') }}
                                    </span>
                                </th>

                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                        {{ $t('How many cities') }}
                                    </span>
                                </th>

                                <th scope="col" class="text-end"></th>
                            </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="!countryStore.isLoading" v-for="country in countryStore.countries" :key="country.id">
                                <td class="px-6 py-3">
                                    <span class="text-sm text-gray-800 dark:text-gray-200">
                                        {{ country.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <v-badge :color="country.cities_count ? 'green' : 'red'" :text="country.cities_count ?? 0" />
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex justify-end gap-x-2">
                                        <v-button color="blue" class="whitespace-nowrap" @click="selectedCountry = country; isModalOpen = true">
                                            <i class="icon mgc_edit_line"></i>
                                            {{ $t('Edit') }}
                                        </v-button>

                                        <v-button color="red" class="whitespace-nowrap" @click="countryStore.onDeleteCountry(country.id as number)">
                                            <i class="icon mgc_delete_line"></i>
                                            {{ $t('Delete') }}
                                        </v-button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Loader -->
                            <tr v-else>
                                <td colspan="6">
                                    <div class="flex items-center justify-center p-4">
                                        <v-loader :size="4" :dark="true" />
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                    <!-- Footer -->
                    <div
                        v-if="!countryStore.isLoading"
                        class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">

                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ countryStore.pagination.total }}
                                </span>
                                {{ $t('Results') }}
                            </p>
                        </div>

                        <div>
                            <div class="inline-flex gap-x-2">
                                <button
                                    @click="countryStore.fetchCountries(true, true)"
                                    :disabled="countryStore.pagination.current_page === 1"
                                    type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                    {{ $t('Previous') }}
                                </button>

                                <button
                                    @click="countryStore.fetchCountries(true)"
                                    :disabled="countryStore.pagination.current_page === countryStore.pagination.last_page"
                                    type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    {{ $t('Next') }}
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End Footer -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <v-modal :is-open="isModalOpen" @update:closeModal="isModalOpen = false; selectedCountry = {
        name: ''
    }">
        <template v-slot:title>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                {{ selectedCountry?.id ? $t('Edit Country') : $t('Add Country') }}
            </h3>
        </template>

        <template v-slot:content>
            <form class="flex flex-col gap-y-4" @submit.prevent="onSaveCountry()">
                <div class="flex flex-col gap-1">
                    <label for="name" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {{ $t('Name') }}
                    </label>
                    <v-input
                        id="name"
                        :placeholder="$t('Name')"
                        :value="selectedCountry.name"
                        @update:input="selectedCountry.name = $event"
                    />
                </div>


                <v-button color="blue" type="submit" class="whitespace-nowrap" :disabled="countryStore.isLoadingAction" :loading="countryStore.isLoadingAction">
                    <i class="icon mgc_save_line"></i>
                    {{ $t('Save') }}
                </v-button>
            </form>
        </template>
    </v-modal>
</template>

<script lang="ts" setup>
import {useCountryStore} from "@/stores/CountryStore";
import {nextTick, onMounted, ref} from "vue";

interface baseInterface {
    id?: number;
    cities_count?: number;
    name: string;
}

const countryStore      = useCountryStore();
const isModalOpen       = ref(false);
const selectedCountry   = ref<baseInterface>({
    name: ''
});

const onSaveCountry = async () => {
    if (selectedCountry.value.id) {
        if(await countryStore.onUpdateCountry(selectedCountry.value)) {
            isModalOpen.value = false;
            selectedCountry.value = {
                name: ''
            };
        }
    } else {
        if(await countryStore.onCreateCountry(selectedCountry.value)) {
            isModalOpen.value = false;
            selectedCountry.value = {
                name: ''
            };
        }
    }
};

onMounted(async () => {
    countryStore.filters.pagination = 1;

    await nextTick(async () => {
        await countryStore.fetchCountries();
    });
});
</script>
