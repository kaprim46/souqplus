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
                                {{ $t('Cities') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Manage your Cities') }}
                            </p>
                        </div>


                        <div class="flex gap-x-2">
                            <v-input :placeholder="$t('Search')"  />
                            <v-button color="blue" @click="isModalOpen = true" custom-class="whitespace-nowrap">
                                <i class="icon mgc_plus_line"></i>
                                {{ $t('Add City') }}
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
                                        {{ $t('Country') }}
                                    </span>
                                </th>

                                <th scope="col" class="text-end"></th>
                            </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="!countryStore.isLoading" v-for="city in countryStore.cities" :key="city.id">
                                <td class="px-6 py-3">
                                    <span class="text-sm text-gray-800 dark:text-gray-200">
                                        {{ city.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <v-badge color="blue" :text="`<i class='mgc_map_pin_fill'></i> ${city.country?.name}`" />
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex justify-end gap-x-2">
                                        <v-button color="blue" class="whitespace-nowrap" @click="selectedCity = city; isModalOpen = true">
                                            <i class="icon mgc_edit_line"></i>
                                            {{ $t('Edit') }}
                                        </v-button>

                                        <v-button color="red" class="whitespace-nowrap" @click="countryStore.onDeleteCity(city.id as number)">
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
                                    {{ countryStore.paginationCities.total }}
                                </span>
                                {{ $t('Results') }}
                            </p>
                        </div>

                        <div>
                            <div class="inline-flex gap-x-2">
                                <button
                                    @click="countryStore.fetchCities(true, true)"
                                    :disabled="countryStore.paginationCities.current_page === 1"
                                    type="button"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                    <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                                    {{ $t('Previous') }}
                                </button>

                                <button
                                    @click="countryStore.fetchCities(true)"
                                    :disabled="countryStore.paginationCities.current_page === countryStore.paginationCities.last_page"
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
    <v-modal :is-open="isModalOpen" @update:closeModal="isModalOpen = false; selectedCity = {
        name: ''
    }">
        <template v-slot:title>
            {{ selectedCity?.id ? $t('Edit City') : $t('Add City') }}
        </template>

        <template v-slot:content>
            <form class="flex flex-col gap-y-4" @submit.prevent="onSaveCity()">
                <div class="flex flex-col gap-1">
                    <label for="name" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {{ $t('Name') }}
                    </label>
                    <v-input
                        id="name"
                        :placeholder="$t('Name')"
                        :value="selectedCity.name"
                        @update:input="selectedCity.name = $event"
                    />
                </div>

                <div class="flex flex-col gap-1">
                    <label for="country_id" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {{ $t('Country') }}
                    </label>
                    <v-select
                        id="country_id"
                        :value="selectedCity.country_id"
                        @update:value="selectedCity.country_id = $event"
                    >
                        <option value="" disabled selected>{{ $t('Select Country') }}</option>
                        <option
                            v-for="country in countryStore.countries"
                            :key="country.id"
                            :value="country.id"
                        >
                            {{ country.name }}
                        </option>
                    </v-select>
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
import {nextTick, onMounted, ref, watch} from "vue";

interface baseInterface {
    id?: number;
    cities_count?: number;
    name: string;
    country_id?: number;
}

const countryStore      = useCountryStore();
const isModalOpen       = ref(false);
const selectedCity   = ref<baseInterface>({
    name: ''
});

watch(isModalOpen, async (value) => {
    if (value) {
        countryStore.filters.pagination = 0;

        await nextTick(async () => {
            await countryStore.fetchCountries();
        });
    }
});

const onSaveCity = async () => {
    if (selectedCity.value.id) {
        if(await countryStore.onUpdateCity(selectedCity.value)) {
            selectedCity.value = {
                name: ''
            };
            isModalOpen.value = false;
        }
    } else {
        if(await countryStore.onCreateCity(selectedCity.value)) {
            selectedCity.value = {
                name: ''
            };
            isModalOpen.value = false;
        }
    }
};

onMounted(async () => {
    countryStore.filtersCities.pagination = 1;

    await nextTick(async () => {
        await countryStore.fetchCities();
    });
});
</script>
