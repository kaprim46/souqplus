<template>
  <form
    @submit.prevent="form.id ? onUpdate() : onCreate()"
    class="flex flex-col gap-5"
  >
    <!-- Content -->
    <div
      class="flex flex-col lg:flex-row gap-4 divide-y divide-x-0 lg:divide-x lg:divide-y-0 divide-solid divide-gray-200 h-full"
    >
      <!-- Form Group -->
      <div class="flex-1 grid gap-4 grid-cols-1 md:grid-cols-2 p-2 lg:p-4">
        <!-- Form Group -->
        <div>
          <label for="name" class="block text-sm mb-2 dark:text-white">
            {{ $t('Ad Name') }}
          </label>
          <v-input
            type="text"
            id="name"
            aria-describedby="name-error"
            :value="form.name"
            :placeholder="$t('Ad Name')"
            @update:input="form.name = $event"
          />
          <p
            class="text-xs text-red-600 mt-2"
            id="name-error"
            v-if="errors.name"
          >
            {{ errors.name[0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group (status (active, inactive) ) -->
        <div v-if="userStore.me?.role === 'admin'">
          <label for="status" class="block text-sm mb-2 dark:text-white">
            {{ $t('Status') }}
          </label>
          <v-select
            id="status"
            :value="form.status"
            @update:value="form.status = $event"
          >
            <option value="pending">{{ $t('Pending') }}</option>
            <option value="approved">{{ $t('Approved') }}</option>
            <option value="rejected">{{ $t('Rejected') }}</option>
          </v-select>
          <p
            class="text-xs text-red-600 mt-2"
            id="status-error"
            v-if="errors.status"
          >
            {{ errors.status[0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- When status rejected and user is admin show the reason -->
        <div
          v-if="
            userStore.me?.role === 'admin' &&
            form.status === statusEnum.REJECTED
          "
        >
          <label for="reason" class="block text-sm mb-2 dark:text-white">
            {{ $t('Rejection reason') }}
          </label>
          <v-textarea
            id="reason"
            :value="form.reject_reason"
            :placeholder="$t('Rejection reason')"
            @update:input="form.reject_reason = $event"
          />
          <p
            class="text-xs text-red-600 mt-2"
            id="reason-error"
            v-if="errors.reject_reason"
          >
            {{ errors.reject_reason[0] }}
          </p>
        </div>

        <!-- Form Group -->
        <div>
          <label for="category_id" class="block text-sm mb-2 dark:text-white">
            {{ $t('Category') }}
          </label>
          <v-select
            id="category_id"
            :value="form.category?.id"
            @update:value="
              (id: number | null) => {
                const selected = categoryStore.categories.find(
                  (cat) => cat.id == id
                );
                form.category = { id: id ?? undefined, name: selected ? selected.name : '' };
              }
            "
          >
            <option value="" disabled selected>
              {{ $t('Select Category') }}
            </option>
            <option
              v-for="category in categoryStore.categories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </v-select>
          <p
            class="text-xs text-red-600 mt-2"
            id="category_id-error"
            v-if="errors['category.id']"
          >
            {{ errors['category.id'][0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div v-if="!categoryStore.isLoading && form.category?.id">
          <label
            for="sub_category_id"
            class="block text-sm mb-2 dark:text-white"
          >
            {{ $t('Sub Category') }}
          </label>
          <v-select
            id="sub_category_id"
            :value="form.sub_category?.id"
            @update:value="(value: number | null) => { form.sub_category = { id: value ?? undefined }; }"
          >
            <option value="" disabled selected>
              {{ $t('Select Sub Category') }}
            </option>
            <option
              v-for="category in categoryStore.subCategories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </v-select>
          <p
            class="text-xs text-red-600 mt-2"
            id="sub_category_id-error"
            v-if="errors['sub_category.id']"
          >
            {{ errors['sub_category.id'][0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div v-if="!categoryStore.isLoading && form.sub_category?.id">
          <label
            for="sub_sub_category_id"
            class="block text-sm mb-2 dark:text-white"
          >
            {{ $t('Sub Sub Category') }}
          </label>
          <v-select
            id="sub_sub_category_id"
            :value="form.sub_sub_category?.id"
            @update:value="
              (value: number | null) => {
                form.sub_sub_category = {
                  id: value ?? undefined,
                };
              }
            "
          >
            <option value="" disabled selected>
              {{ $t('Select Sub Sub Category') }}
            </option>
            <option
              v-for="category in subSubCategoryStore.subSubCategories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.name }}
            </option>
          </v-select>
          <p
            class="text-xs text-red-600 mt-2"
            id="sub_sub_category_id-error"
            v-if="errors['sub_sub_category.id']"
          >
            {{ errors['sub_sub_category.id'][0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group country -->
        <div>
          <label for="country_id" class="block text-sm mb-2 dark:text-white">
            {{ $t('Country') }}
          </label>
          <v-select
            id="country_id"
            name="country_id"
            :value="form.country?.id"
            @update:value="(value: number | null) => { form.country = { id: value ?? undefined }; }"
          >
            <option value="0">{{ $t('Select Country') }}</option>
            <option
              v-for="country in countryStore.countries"
              :key="country.id"
              :value="country.id"
            >
              {{ country.name }}
            </option>
          </v-select>
          <p
            class="text-xs text-red-600 mt-2"
            id="country_id-error"
            v-if="errors['country.id']"
          >
            {{ errors['country.id'][0] }}
          </p>
        </div>

        <!-- Form Group city -->
        <div v-if="!countryStore.isLoadingAction && form.country?.id">
          <label for="city" class="block text-sm mb-2 dark:text-white">
            {{ $t('City') }}
          </label>
          <v-select
            id="city_id"
            name="city_id"
            :value="form.city?.id"
            @update:value="(value: number | null) => { form.city = { id: value ?? undefined }; }"
          >
            <option value="0">{{ $t('Select City') }}</option>
            <option
              v-for="city in countryStore.cities"
              :key="city.id"
              :value="city.id"
            >
              {{ city.name }}
            </option>
          </v-select>
          <p
            class="text-xs text-red-600 mt-2"
            id="city-error"
            v-if="errors['city.id']"
          >
            {{ errors['city.id'][0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
          <label for="phone_number" class="block text-sm mb-2 dark:text-white">
            {{ $t('Phone Number') }}
          </label>
          <v-phoneInput
            id="phone_number"
            @update:phoneNumber="form.user.phone_number = $event"
            @update:countryCode="form.user.country_code = $event"
            :value="form.user?.phone_number"
            :valueCountryCode="form.user?.country_code ?? '+212'"
          />
          <p
            class="text-xs text-red-600 mt-2"
            id="country_code-error"
            v-if="errors['user.country_code'] || errors['user.phone_number']"
          >
            {{
              errors['user.country_code'][0] ?? errors['user.phone_number'][0]
            }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
          <label for="price" class="block text-sm mb-2 dark:text-white">
            {{ $t('Price') }}
          </label>
          <v-input
            type="number"
            id="price"
            name="price"
            :value="form.price"
            :placeholder="$t('Price')"
            @update:input="form.price = $event"
            :step="0.01"
          />
          <p
            class="text-xs text-red-600 mt-2"
            id="price-error"
            v-if="errors.price"
          >
            {{ errors.price[0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Custom Fields -->
        <div
          class="col-span-1 md:col-span-2 flex flex-col gap-5"
          v-if="filtersStore.data_filters_category.length"
        >
          <options
            :data="filtersStore.data_filters_category"
            @on:update="onOptionsUpdate"
            :initialData="form.filters"
          />
        </div>
        <!-- End Custom Fields -->

        <!-- Form Group -->
        <div class="col-span-1 md:col-span-2">
          <label for="description" class="block text-sm mb-2 dark:text-white">
            {{ $t('Description') }}
          </label>
          <v-editor
            @update:input="form.description = $event"
            :value="form.description"
            :placeholder="$t('Description')"
          />
          <p
            class="text-xs text-red-600 mt-2"
            id="description-error"
            v-if="errors.description"
          >
            {{ errors.description[0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Media -->
        <div class="col-span-1 md:col-span-2">
          <label for="media" class="block text-sm mb-2 dark:text-white">
            {{ $t('Media') }}
          </label>
          <v-media
            @on:setMedia="onMediaSet"
            @on:unSetMedia="unSetMedia"
            @on:setAsMainMedia="setAsMainMedia"
            :mediaFiles="form.media"
          />
          <p
            class="text-xs text-red-600 mt-2"
            id="media-error"
            v-if="errors.media"
          >
            {{ errors.media[0] }}
          </p>
        </div>
        <!-- End Media -->
      </div>
      <!-- End Form Group -->

      <!-- Map -->
      <div
        class="w-full lg:w-1/3 flex flex-col gap-5 divide-y divide-x-0 lg:divide-x lg:divide-y-0 divide-solid divide-gray-200 h-full"
        v-if="form"
      >
        <!-- Form Group Map -->
        <div class="p-4">
          <label for="map" class="block text-sm mb-2 dark:text-white">
            {{ $t('Map') }}
          </label>
          <div class="relative overflow-hidden rounded-lg shadow-xl">
            <GMapMap
              :center="{
                lat:
                  typeof form.latitude === 'number'
                    ? form.latitude
                    : form.latitude !== null && form.latitude !== undefined
                    ? parseFloat(String(form.latitude))
                    : 15.5527,
                lng:
                  typeof form.longitude === 'number'
                    ? form.longitude
                    : form.longitude !== null && form.longitude !== undefined
                    ? parseFloat(String(form.longitude))
                    : 48.5164,
              }"
              :zoom="10"
              :options="mapOptions"
              class="w-full h-[500px]"
            >
              <GMapMarker
                :key="1"
                :position="{
                  lat:
                    typeof form.latitude === 'number'
                      ? form.latitude
                      : form.latitude !== null && form.latitude !== undefined
                      ? parseFloat(String(form.latitude))
                      : 15.5527,
                  lng:
                    typeof form.longitude === 'number'
                      ? form.longitude
                      : form.longitude !== null && form.longitude !== undefined
                      ? parseFloat(String(form.longitude))
                      : 48.5164,
                }"
                :clickable="true"
                :draggable="true"
                @click="
                  form.latitude = $event.latLng.lat();
                  form.longitude = $event.latLng.lng();
                "
                @dragend="
                  form.latitude = $event.latLng.lat();
                  form.longitude = $event.latLng.lng();
                "
              />
            </GMapMap>
          </div>
          <p
            class="text-xs text-red-600 mt-2"
            id="latitude-error"
            v-if="errors.latitude"
          >
            {{ errors.latitude[0] }}
          </p>
          <p
            class="text-xs text-red-600 mt-2"
            id="longitude-error"
            v-if="errors.longitude"
          >
            {{ errors.longitude[0] }}
          </p>
        </div>
        <!-- End Form Group -->
      </div>
      <!-- End Map -->
    </div>

    <v-button
      type="submit"
      color="blue"
      class="w-fit place-self-end flex items-center gap-1"
      :disabled="isLoading"
      :loading="isLoading"
    >
      <i class="mgc_save_2_line text-xl" />
      {{ form.id ? $t('Update') : $t('Create') }}
    </v-button>
  </form>
</template>

<script lang="ts" setup>
import { onMounted, ref, watch } from 'vue';
import type { PropType } from 'vue';
import { notify } from 'notiwind';
import { wTrans } from 'laravel-vue-i18n';
import { useCategoryStore } from '@/stores/CategoryStore';
import { useUserStore } from '@/stores/UserStore';
import { useCustomerCategoryStore } from '@/stores/Customer/CustomerCategoryStore';
import { useCountryStore } from '@/stores/CountryStore';
import { useFilterStore } from '@/stores/FilterStore';
import { Advertisement, statusEnum } from '@/types/Advertisements';
import axios from '@/axios';
import router from '@/routes';
import Options from '@/views/dashboard/pages/advertisements/components/Options.vue';
import { useSubSubCategoryStore } from '@/stores/SubSubCategoryStore';

/**
 * States
 */
const mapOptions = {
  mapTypeControl: true,
  styles: [
    {
      featureType: 'all',
      elementType: 'labels.text',
      stylers: [
        {
          color: '#878787',
        },
      ],
    },
    {
      featureType: 'all',
      elementType: 'labels.text.stroke',
      stylers: [
        {
          visibility: 'off',
        },
      ],
    },
    {
      featureType: 'landscape',
      elementType: 'all',
      stylers: [
        {
          color: '#f9f5ed',
        },
      ],
    },
    {
      featureType: 'road.highway',
      elementType: 'all',
      stylers: [
        {
          color: '#f5f5f5',
        },
      ],
    },
    {
      featureType: 'road.highway',
      elementType: 'geometry.stroke',
      stylers: [
        {
          color: '#c9c9c9',
        },
      ],
    },
    {
      featureType: 'water',
      elementType: 'all',
      stylers: [
        {
          color: '#aee0f4',
        },
      ],
    },
  ],
};
const userStore = useUserStore();
const categoryStore =
  userStore.me?.role === 'admin'
    ? useCategoryStore()
    : useCustomerCategoryStore();
const countryStore = useCountryStore();
const filtersStore = useFilterStore();
const subSubCategoryStore = useSubSubCategoryStore();
const isLoading = ref<boolean>(false);
const mediaToDelete = ref<number[]>([]);

const props = defineProps({
  form: {
    type: Object as PropType<Advertisement>,
    required: true,
    default: () => ({
      name: '',
      category: {
        id: null,
      },
      sub_category: {
        id: null,
      },
      sub_sub_category: {
        id: null,
      },
      country: {
        id: null,
      },
      city: {
        id: null,
      },
      user: {
        phone_number: '',
        country_code: '+212',
      },
      price: 0,
      description: '',
      media: [],
      filters: [],
      latitude: undefined,
      longitude: undefined,
    }),
  },
  errors: {
    type: Object,
    required: true,
  },
});
const emit = defineEmits(['update:form', 'update:errors']);

/**
 * Watchers
 */

watch(
  () => props.form.category,
  async (value) => {
    if (value && value.id) {
      await filtersStore.getFiltersByCategory(value.id);
      await categoryStore.fetchSubCategories(value.id);
    } else {
      // Reset dependent fields when category is cleared
      props.form.sub_category = { id: null };
      props.form.sub_sub_category = { id: null };
      subSubCategoryStore.subSubCategories = [];
      filtersStore.data_filters_category = [];
    }
  },
  { immediate: true, deep: true }
);

watch(
  () => props.form.sub_category,
  async (value) => {
    if (value && value.id) {
      await filtersStore.getFiltersBySubCategory(value.id);
      await subSubCategoryStore.fetchSubSubCategories(value.id);
    } else {
      // Reset sub-sub-category when sub-category is cleared
      props.form.sub_sub_category = { id: null };
      subSubCategoryStore.subSubCategories = [];
      filtersStore.data_filters_category = [];
    }
  },
  { immediate: true, deep: true }
);

watch(
  () => props.form.country,
  async (value) => {
    if (value && value.id) {
      await countryStore.fetchCities(false, false, value.id);
    } else {
      // Reset city when country is cleared
      props.form.city = { id: null };
    }
  },
  { immediate: true, deep: true }
);

/**
 * Methods
 */

/**
 * Update options
 * @param data
 */
const onOptionsUpdate = (data: any) => {
  props.form.filters = data;
};

/**
 * Set media
 * @param media The uploaded media object with id and is_main
 */
const onMediaSet = (media: { id: number; is_main: boolean }) => {
  // Ensure media array exists and is an array before pushing
  if (!Array.isArray(props.form.media)) {
    props.form.media = [];
  }
  if (!props.form.media.length) {
    props.form.media = [
      {
        id: media.id,
        is_main: true,
      },
    ];
  } else {
    props.form.media.push({
      id: media.id,
      is_main: false,
    });
  }
};

/**
 * Unset media
 * @param media The media object to remove with id
 */
const unSetMedia = (media: { id: number }) => {
  // Ensure media array exists and is an array before filtering
  if (Array.isArray(props.form.media)) {
    // Add the ID of the media to be deleted to the mediaToDelete array
    mediaToDelete.value.push(media.id);
    // Filter the media array to remove the item from the form display
    props.form.media = props.form.media.filter(
      (item: { id: number }) => item.id !== media.id
    );
  }
};

/**
 * Set as main media
 * @param media The media object to set as main with id
 */
const setAsMainMedia = (media: { id: number }) => {
  // Ensure media array exists and is an array before mapping
  if (Array.isArray(props.form.media)) {
    props.form.media = props.form.media.map(
      (item: { id: number; is_main: boolean }) => {
        item.is_main = item.id === media.id;
        return item;
      }
    );
  }
};

/**
 * Create Advertisement
 */
const onCreate = async () => {
  isLoading.value = true;

  try {
    const { data } = await axios.post(
      userStore.me?.role === 'admin'
        ? '/dashboard/advertisements'
        : '/my/advertisements',
      {
        ...props.form,
        mediaToDelete: mediaToDelete.value, // Include mediaToDelete in the request
      }
    );

    if (data.ok) {
      // Emit an empty object to the parent to trigger form reset
      emit('update:form', {} as Advertisement);
      // Navigate after reset
      await router.push({ name: 'my-advertisement', params: {} });
    }

    emit('update:errors', {});

    notify(
      {
        group: 'dashboard',
        ok: data.ok,
        title: data.ok ? wTrans('Success') : wTrans('Error'),
        text: data.message,
      },
      2000
    );
  } catch (error: any) {
    if (error.response.status === 422) {
      emit('update:errors', error.response.data.errors);
    } else {
      // Handle other errors
      notify(
        {
          group: 'dashboard',
          ok: false,
          title: wTrans('Error'),
          text: error.message,
        },
        2000
      );
    }
  } finally {
    isLoading.value = false;
  }
};

/**
 * Update Advertisement
 */
const onUpdate = async () => {
  isLoading.value = true;

  try {
    const { data } = await axios.put(
      userStore.me?.role === 'admin'
        ? `/dashboard/advertisements/${props.form.id}`
        : `/my/advertisements/${props.form.id}`,
      {
        ...props.form,
        mediaToDelete: mediaToDelete.value, // Include mediaToDelete in the request
      }
    );

    // No form reset needed for update, as user stays on the edit page.
    emit('update:errors', {});

    notify(
      {
        group: 'dashboard',
        ok: data.ok,
        title: data.ok ? wTrans('Success') : wTrans('Error'),
        text: data.message,
      },
      2000
    );
  } catch (error: any) {
    if (error.response.status === 422) {
      emit('update:errors', error.response.data.errors);
    } else {
      // Handle other errors
      notify(
        {
          group: 'dashboard',
          ok: false,
          title: wTrans('Error'),
          text: error.message,
        },
        2000
      );
    }
  } finally {
    isLoading.value = false;
  }
};

/**
 * Lifecycle
 */
onMounted(async () => {
  // Ensure media array is an array on mount and clear it to prevent persistence issues when creating a *new* ad
  if (!props.form.id) {
    if (!Array.isArray(props.form.media)) {
      props.form.media = [];
    } else {
      props.form.media = [];
    }
    props.form.latitude = undefined; // Set to undefined for new ads
    props.form.longitude = undefined; // Set to undefined for new ads
  }
  await categoryStore.fetch();
  await countryStore.fetchCountries();
});
</script>
