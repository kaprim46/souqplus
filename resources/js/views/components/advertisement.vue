<template>
  <article
    class="advertisement p-2 lg:p-5 flex flex-col md:flex-row justify-between gap-4 rounded-lg"
  >
    <figure
      class="h-60 md:h-20 w-full md:w-20 flex flex-col justify-center items-center rounded-xl shadow relative overflow-hidden"
      v-if="advertisement.main_media?.url"
    >
      <router-link
        :to="{
          name: 'show-advertisement',
          params: { id: advertisement.id, slug: advertisement.slug },
        }"
      >
        <img
          class="absolute h-full w-full object-cover inset-0"
          :src="advertisement.main_media.url"
          :alt="advertisement.name"
        />
      </router-link>
    </figure>

    <div class="flex-1 flex flex-col gap-4">
      <header class="flex flex-col">
        <router-link
          :to="{
            name: 'show-advertisement',
            params: {
              id: advertisement.id,
              slug: advertisement.slug || 'details',
            },
          }"
          class="block text-lg: lg:text-xl font-semibold hover:text-blue-700"
          :class="
            !advertisementStore.idExistsInLocalStorage(advertisement.id)
              ? 'text-[#0C74BB]'
              : 'text-fuchsia-800'
          "
        >
          {{ advertisement.name }}
        </router-link>

        <p class="text-base text-gray-500 dark:text-gray-400">
          {{ advertisement.description }}
        </p>
      </header>

      <!-- Information -->
      <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1 md:gap-5 items-center"
      >
        <!-- Category -->
        <span class="flex items-center gap-2 text-sm lg:text-base">
          <i class="icon mgc_sitemap_line text-base lg:text-xl"></i>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">{{
              advertisement.category.name
            }}</span>
            <span
              v-if="advertisement.sub_category"
              class="text-sm text-gray-500"
              >/ {{ advertisement.sub_category.name }}</span
            >
            <span
              v-if="advertisement.sub_sub_category"
              class="text-sm text-gray-500"
              >/ {{ advertisement.sub_sub_category.name }}</span
            >
          </div>
        </span>

        <!-- Created At -->
        <span class="flex items-center gap-2 text-sm lg:text-base">
          <i class="icon mgc_time_line text-base lg:text-xl"></i>
          {{ advertisement.created_at }}
        </span>

        <!-- Country if exists -->
        <span
          class="flex items-center gap-2 text-sm lg:text-base"
          v-if="advertisement.country"
        >
          <i class="icon mgc_location_line text-base lg:text-xl"></i>
          {{ advertisement.country.name }}
          <span v-if="advertisement.city?.id">
            / {{ advertisement.city.name }}</span
          >
        </span>

        <!-- Username -->
        <router-link
          class="flex items-center gap-2 text-sm lg:text-base"
          :to="{
            name: advertisement.user.is_business ? 'store.show' : 'profile_uid',
            params: {
              id: advertisement.user.id,
              slug: advertisement.user.slug,
            },
          }"
        >
          <i class="icon mgc_user_4_line text-base lg:text-xl"></i>
          {{ advertisement.user.name }}
        </router-link>

        <!-- Favorite -->
        <button
          @click="advertisementStore.toggleFavorite(advertisement, isFromArray)"
          :class="
            advertisement.is_favorite
              ? 'text-red-600 hover:text-black'
              : 'text-black hover:text-red-600'
          "
          class="flex items-center gap-2 text-sm lg:text-base"
        >
          <i
            class="icon text-base lg:text-xl"
            :class="
              !advertisement.is_favorite ? 'mgc_heart_line' : 'mgc_heart_fill'
            "
          ></i>
          {{
            $t(
              advertisement.is_favorite
                ? 'Remove from favorites'
                : 'Add to favorites'
            )
          }}
        </button>

        <!-- Views -->
        <span class="flex items-center gap-2 text-sm lg:text-base">
          <i class="icon mgc_eye_line text-base lg:text-xl"></i>
          {{
            $t('This advertisement has been viewed :count times', {
              count: advertisement.views,
            })
          }}
        </span>
      </div>
    </div>
  </article>
</template>

<script lang="ts" setup>
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import { Advertisement } from '@/types/Advertisements';
import { PropType } from 'vue';

const props = defineProps({
  advertisement: {
    type: Object as PropType<Advertisement>,
    required: true,
  },
  isFromArray: {
    type: Boolean as PropType<boolean>,
    required: false,
    default: true,
  },
});

const advertisementStore = useCustomerAdvertisementStore();
</script>
