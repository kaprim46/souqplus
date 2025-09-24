<template>
  <div class="flex flex-col">
    <div class="-m-1.5 overflow-x-auto">
      <div class="p-1.5 min-w-full inline-block align-middle">
        <div
          class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700"
        >
          <!-- Header -->
          <div
            class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700"
          >
            <div>
              <h2
                class="text-xl font-semibold text-gray-800 dark:text-gray-200"
              >
                <i class="icon mgc_store_line"></i>
                {{ $t('Create new advertisement') }}
              </h2>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('Create a new advertisement for your business') }}
              </p>
            </div>
          </div>
          <!-- End Header -->

          <!-- Form -->
          <div>
            <advertisement-form
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
import { ref } from 'vue';
import { Advertisement, statusEnum } from '@/types/Advertisements';
import AdvertisementForm from './components/DataForm.vue';

const form = ref<Advertisement>({
  category: {},
  city: {},
  comments_count: 0,
  country: {},
  created_at: '',
  description: '',
  filters: [],
  id: 0,
  is_favorite: false,
  latitude: 0,
  longitude: 0,
  main_media: {},
  media: [],
  name: '',
  price: 0,
  slug: '',
  sub_category: {},
  user: { country_code: '', phone_number: '' },
  status: statusEnum.PENDING,
});
const errors = ref({});
</script>
