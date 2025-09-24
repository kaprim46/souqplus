<template>
  <!-- Card -->
  <article
    class="group flex flex-col w-full bg-white border border-gray-200 shadow-sm rounded-xl dark:bg-slate-900 dark:border-gray-700 dark:shadow-slate-700/[.7]"
  >
    <figure
      v-if="advertisement.main_media?.url"
      class="h-[200px] flex flex-col justify-center items-center bg-blue-600 rounded-t-xl relative"
    >
      <img
        class="absolute h-full w-full object-cover rounded-t-xl"
        :src="advertisement.main_media.url"
        :alt="advertisement.name"
      />
    </figure>
    <div class="p-4 md:p-6">
      <span
        class="mb-1 text-xs font-semibold uppercase bg-[#0C74BB] text-white px-2 py-1 rounded-full float-end"
      >
        {{ advertisement.category.name }}
        <span v-if="advertisement.sub_category" class="mx-1"
          >/ {{ advertisement.sub_category.name }}</span
        >
      </span>
      <router-link
        :to="{
          name: 'show-advertisement',
          params: {
            id: advertisement.id,
            slug: advertisement.slug || 'details',
          },
        }"
        class="block text-xl font-semibold text-gray-800 hover:text-[#0C74BB]"
      >
        {{ advertisement.name }}
      </router-link>

      <p class="mt-3 text-gray-500">
        {{ advertisement.description }}
      </p>
    </div>
    <div
      class="mt-auto flex border-t border-gray-200 divide-x divide-gray-200 dark:border-gray-700 dark:divide-gray-700"
    >
      <router-link
        :to="{
          name: 'show-advertisement',
          params: {
            id: advertisement.id,
            slug: advertisement.slug || 'details',
          },
        }"
        class="whitespace-nowrap w-full py-2 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-es-xl shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
        :class="
          customerAdvertisementStore.idExistsInLocalStorage(advertisement.id)
            ? 'bg-fuchsia-800 text-white'
            : 'bg-white text-gray-800'
        "
      >
        <i class="icon mgc_eye_line text-xl"></i>
      </router-link>
      <button
        @click="customerAdvertisementStore.toggleFavorite(advertisement, true)"
        class="w-full py-2 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-ee-xl shadow-sm disabled:opacity-50 disabled:pointer-events-none"
        :class="
          advertisement.is_favorite
            ? 'bg-red-600 text-white hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-red-500'
            : 'bg-white text-gray-800 hover:bg-gray-50 dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600'
        "
      >
        <i
          class="icon text-xl"
          :class="
            advertisement.is_favorite ? 'mgc_heart_fill' : 'mgc_heart_line'
          "
        ></i>
      </button>
    </div>
  </article>
  <!-- End Card -->
</template>

<script lang="ts" setup>
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import { Advertisement } from '@/types/Advertisements';

const props = defineProps<{
  advertisement: Advertisement;
}>();

const customerAdvertisementStore = useCustomerAdvertisementStore();
</script>
