<template>
  <div class="container mx-auto py-10 flex flex-col gap-5">
    <!-- Favorite Button -->
    <div class="flex justify-end" v-if="!customerAdvertisementStore.isLoading">
      <v-button
        :color="
          customerAdvertisementStore.advertisement?.is_favorite
            ? 'coralred'
            : 'gray'
        "
        :loading="customerAdvertisementStore.toggleFavoriteLoading"
        @click="
          customerAdvertisementStore.toggleFavorite(
            customerAdvertisementStore.advertisement,
            false,
            true
          )
        "
      >
        <i class="icon mgc_heart_fill"></i>
        {{
          customerAdvertisementStore.advertisement?.is_favorite
            ? $t('Remove from favorites')
            : $t('Add to favorites')
        }}
      </v-button>
    </div>

    <div class="flex flex-col-reverse md:flex-row gap-5 lg:gap-8">
      <aside class="w-full md:w-[15.4rem] lg:w-[20rem] flex flex-col gap-5">
        <!-- Is Loading -->
        <template v-if="!customerAdvertisementStore.isLoading">
          <section
            class="bg-white rounded shadow divide-y"
            v-if="customerAdvertisementStore.advertisement.filters?.length"
          >
            <header
              class="text-xl font-semibold text-gray-700 p-4 flex items-center gap-1"
            >
              <i class="icon mgc_more_2_fill"></i>
              {{ $t('More information') }}
            </header>

            <div class="p-5">
              <div class="flex flex-col gap-2">
                <div
                  v-for="filter in customerAdvertisementStore.advertisement
                    .filters"
                  :key="filter.name.toLowerCase()"
                  class="flex justify-between text-sm"
                >
                  <div class="text-gray-700 dark:text-gray-200 font-semibold">
                    {{ filter.name }} :
                  </div>
                  <div class="text-gray-700 dark:text-gray-200">
                    {{ filter.value }}
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Contact Owner -->
          <section class="bg-white rounded shadow divide-y overflow-hidden">
            <header
              class="text-xl font-semibold text-gray-700 p-4 flex items-center gap-1"
            >
              <i class="icon mgc_user_star_fill"></i>
              {{ $t('Contact owner') }}
            </header>
            <div class="p-5 text-center">
              <img
                class="h-32 w-32 rounded-full border-4 border-white dark:border-gray-800 mx-auto"
                :src="
                  customerAdvertisementStore.advertisement?.user?.avatar_url
                "
                alt=""
              />
              <div class="py-2">
                <router-link
                  class="block font-bold text-2xl text-gray-800 hover:text-blue-700 mb-1"
                  :to="{
                    name: customerAdvertisementStore.advertisement?.user
                      ?.is_business
                      ? 'store.show'
                      : 'profile_uid',
                    params: {
                      id: customerAdvertisementStore.advertisement?.user?.id,
                      slug: customerAdvertisementStore.advertisement?.user
                        ?.slug,
                    },
                  }"
                >
                  {{ customerAdvertisementStore.advertisement?.user?.name }}

                  <i
                    v-if="
                      customerAdvertisementStore.advertisement?.user
                        ?.is_verified
                    "
                    class="icon mgc_check_2_fill text-blue-600 dark:text-blue-400 text-lg"
                  ></i>
                </router-link>
                <div
                  class="inline-flex text-gray-600 dark:text-gray-300 items-center font-bold text-[13.3px]"
                  dir="ltr"
                >
                  ({{
                    customerAdvertisementStore.advertisement?.user
                      ?.country_code
                  }})
                  {{
                    customerAdvertisementStore.advertisement?.user?.phone_number
                  }}
                </div>
              </div>
            </div>
            <div class="flex flex-col divide-y divide-gray-200">
              <template
                v-if="
                  userStore.me &&
                  userStore.me.id !==
                    customerAdvertisementStore.advertisement?.user?.id
                "
              >
                <v-button
                  color="gray"
                  rounded="none"
                  custom-class="flex items-start justify-start py-3"
                  @click="
                    openWhatsApp(
                      `${customerAdvertisementStore.advertisement?.user.country_code}${customerAdvertisementStore.advertisement?.user.phone_number}`
                    )
                  "
                >
                  <i class="icon mgc_whatsapp_fill"></i>
                  {{ $t('WhatsApp') }}
                </v-button>
                <v-button
                  color="gray"
                  rounded="none"
                  custom-class="flex items-start justify-start py-3"
                  @click="
                    openPhoneCall(
                      `${customerAdvertisementStore.advertisement?.user.country_code}${customerAdvertisementStore.advertisement?.user.phone_number}`
                    )
                  "
                >
                  <i class="icon mgc_phone_call_fill"></i>
                  {{ $t('Call') }}
                </v-button>
                <v-button
                  color="gray"
                  rounded="none"
                  custom-class="flex items-start justify-start py-3"
                  @click="
                    router.push({
                      name: 'chat',
                      query: {
                        advertisement_id:
                          customerAdvertisementStore.advertisement?.id,
                        user_id:
                          customerAdvertisementStore.advertisement?.user?.id,
                      },
                    })
                  "
                >
                  <i class="icon mgc_chat_1_fill"></i>
                  {{ $t('Chat') }}
                </v-button>
              </template>
              <template v-else>
                <v-button
                  color="green"
                  rounded="none"
                  custom-class="flex items-start justify-start py-3"
                  @click="
                    router.push({
                      name: 'edit-advertisement',
                      params: {
                        id: customerAdvertisementStore.advertisement?.id,
                      },
                    })
                  "
                >
                  <i class="icon mgc_edit_fill"></i>
                  {{ $t('Edit') }}
                </v-button>
              </template>
            </div>
          </section>

          <!-- Map -->
          <section class="w-full">
            <GMapMap
              :center="{
                lat:
                  customerAdvertisementStore.advertisement?.latitude || 15.5527,
                lng:
                  customerAdvertisementStore.advertisement?.longitude ||
                  48.5164,
              }"
              :zoom="15"
              :options="mapOptions"
              class="w-full h-[500px]"
            >
              <GMapMarker
                :position="{
                  lat:
                    customerAdvertisementStore.advertisement?.latitude ||
                    15.5527,
                  lng:
                    customerAdvertisementStore.advertisement?.longitude ||
                    48.5164,
                }"
                :clickable="false"
                :draggable="false"
              />
            </GMapMap>
          </section>
        </template>

        <!-- Loading -->
        <v-skeleton :loop-number="29" v-else />
      </aside>

      <main class="flex-1 flex flex-col gap-10">
        <template v-if="!customerAdvertisementStore.isLoading">
          <!-- Advertisement Details -->
          <section class="bg-white rounded shadow">
            <header
              class="flex flex-col lg:flex-row justify-between items-start gap-2 lg:gap-5 p-4 border-b border-gray-200"
            >
              <div class="flex flex-col">
                <!-- Advertisement Name -->
                <h1
                  class="text-2xl font-semibold text-gray-800 dark:text-white"
                >
                  {{ customerAdvertisementStore.advertisement.name }}
                </h1>
                <div class="flex flex-col lg:flex-row gap-2 lg:items-center">
                  <!-- Advertisement Date -->
                  <time
                    class="text-gray-500 text-sm font-semibold flex items-center gap-1"
                  >
                    <i class="icon mgc_calendar_time_add_line text-xl"></i>
                    {{ customerAdvertisementStore.advertisement.created_at }}
                  </time>
                  <!-- Advertisement Country -->
                  <span
                    class="text-gray-500 text-sm font-semibold flex items-center gap-1"
                  >
                    <i class="icon mgc_location_line text-xl"></i>
                    {{ customerAdvertisementStore.advertisement.country?.name }}
                    <span
                      v-if="customerAdvertisementStore.advertisement.city?.id"
                      >/
                      {{
                        customerAdvertisementStore.advertisement.city.name
                      }}</span
                    >
                  </span>
                  <!-- Advertisement Views -->
                  <span
                    class="text-gray-500 text-sm font-semibold flex items-center gap-1"
                  >
                    <i class="icon mgc_eye_line text-xl"></i>
                    {{
                      $t('This advertisement has been viewed :count times', {
                        count:
                          customerAdvertisementStore.advertisement?.views?.toString() ||
                          '0',
                      })
                    }}
                  </span>
                </div>
              </div>

              <!-- Category -->
              <span class="flex items-center gap-2 text-base">
                <i class="icon mgc_sitemap_line text-xl"></i>
                <router-link
                  :to="{
                    name: 'customer.category.show',
                    params: {
                      slug: customerAdvertisementStore.advertisement.category
                        ?.slug,
                    },
                  }"
                >
                  {{ customerAdvertisementStore.advertisement.category?.name }}
                </router-link>

                <span
                  v-if="
                    customerAdvertisementStore.advertisement.sub_category?.id
                  "
                  class="mx-1"
                >
                  /
                  <router-link
                    :to="{
                      name: 'customer.category.show',
                      params: {
                        slug: customerAdvertisementStore.advertisement.category
                          ?.slug,
                        sub: customerAdvertisementStore.advertisement
                          .sub_category?.id,
                      },
                    }"
                  >
                    {{
                      customerAdvertisementStore.advertisement.sub_category.name
                    }}
                  </router-link>
                </span>
                <span
                  v-if="
                    customerAdvertisementStore.advertisement.sub_sub_category
                      ?.id
                  "
                  class="mx-1"
                >
                  /
                  <router-link
                    :to="{
                      name: 'customer.category.show',
                      params: {
                        slug: customerAdvertisementStore.advertisement.category
                          ?.slug,
                        sub: customerAdvertisementStore.advertisement
                          .sub_category?.id,
                        subsub:
                          customerAdvertisementStore.advertisement
                            .sub_sub_category?.id,
                      },
                    }"
                  >
                    {{
                      customerAdvertisementStore.advertisement.sub_sub_category
                        .name
                    }}
                  </router-link>
                </span>
              </span>
            </header>

            <!-- Carousel -->
            <div class="flex flex-col-reverse lg:flex-row gap-2 p-4">
              <div class="flex flex-wrap lg:flex-col gap-2">
                <button
                  v-for="media in customerAdvertisementStore.advertisement
                    .media"
                  :key="media.url_thumb"
                  @click="previewMedia = media"
                  class="h-20 w-20 overflow-hidden rounded-lg text-center"
                >
                  <img
                    class="h-full w-full object-cover"
                    :src="media.url"
                    :alt="customerAdvertisementStore.advertisement.name"
                    loading="lazy"
                  />
                </button>
              </div>
              <div class="flex-1">
                <img
                  class="max-h-[38rem] w-full object-cover rounded-lg"
                  :src="previewMedia?.url"
                  :alt="customerAdvertisementStore.advertisement.name"
                  loading="lazy"
                  @click="onOpenModalPreview"
                />
              </div>
            </div>

            <hr />

            <!-- Description -->
            <div class="p-4">
              <div
                class="text-gray-700 dark:text-gray-200 my-2"
                v-html="customerAdvertisementStore.advertisement.description"
              ></div>
            </div>
          </section>
        </template>

        <v-skeleton :loop-number="29" v-else />

        <!-- Comments -->
        <VComment :advertisement="customerAdvertisementStore.advertisement" />

        <!-- Similar advertisements -->
        <div
          v-if="
            !customerAdvertisementStore.isLoading &&
            customerAdvertisementStore.advertisements.length
          "
        >
          <div class="flex flex-col gap-5">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
              <i class="icon mgc_multiselect_fill"></i>
              {{ $t('Similar advertisements') }}
            </h2>
            <div
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 items-start gap-5"
            >
              <v-advertisement-card
                v-for="advertisement in customerAdvertisementStore.advertisements"
                :key="advertisement.id"
                :advertisement="advertisement"
              />
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal  preview -->
  <div
    class="bg-black bg-opacity-60 fixed top-0 left-0 w-full h-full flex items-center justify-center z-[60]"
    v-if="openModalPreview"
  >
    <img
      alt=""
      class="rounded-lg max-h-[90%] max-w-[90%]"
      :src="previewMedia?.url"
    />

    <!-- Close Button -->
    <button
      @click="openModalPreview = false"
      class="absolute top-5 right-5 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg"
    >
      <i class="icon mgc_close_fill text-lg"></i>
    </button>
  </div>
</template>

<script lang="ts" setup>
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import { useSettingsStore } from '@/stores/SettingsStore';
import { nextTick, onMounted, ref, watch } from 'vue';
import { useUserStore } from '@/stores/UserStore';
import { useRouter } from 'vue-router';

/**
 * Components
 */
import VAdvertisementCard from '@/views/components/advertisementCard.vue';
import VComment from './comments.vue';

/**
 * Variables
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
const customerAdvertisementStore = useCustomerAdvertisementStore();
const userStore = useUserStore();
const router = useRouter();
const previewMedia = ref<{ url?: string } | null>(null);
const openModalPreview = ref(false);
const wishIndexPreview = ref<number | undefined>(undefined);

/**
 * Open WhatsApp
 * @param phone_number
 */
const openWhatsApp = (phone_number: string) => {
  window.open(`https://wa.me/${phone_number}`, '_blank');
};

/**
 * Open Phone Call
 * @param phone_number
 */
const openPhoneCall = (phone_number: string) => {
  window.open(`tel:${phone_number}`, '_blank');
};

/**
 * Fetch the advertisement logic
 */
const fetchLogic = async () => {
  try {
    const id = parseInt(router.currentRoute.value.params.id as string);
    if (!id || isNaN(id)) {
      router.push({ name: '404' });
      return;
    }

    const ok = await customerAdvertisementStore.fetchOne(id);
    if (!ok) {
      router.push({ name: '404' });
      return;
    }

    previewMedia.value = customerAdvertisementStore.advertisement.main_media;
    await customerAdvertisementStore.saveToLocalStorage(id);

    // Reset filters for similar advertisements
    customerAdvertisementStore.filters = {};

    // Set filters for similar advertisements
    if (customerAdvertisementStore.advertisement.city?.id) {
      customerAdvertisementStore.filters.city_id =
        customerAdvertisementStore.advertisement.city.id;
    }
    if (customerAdvertisementStore.advertisement.country?.id) {
      customerAdvertisementStore.filters.country_id =
        customerAdvertisementStore.advertisement.country.id;
    }
    if (customerAdvertisementStore.advertisement.category?.id) {
      customerAdvertisementStore.filters.category =
        customerAdvertisementStore.advertisement.category.id;
    }
    if (customerAdvertisementStore.advertisement.sub_category?.id) {
      customerAdvertisementStore.filters.sub_category =
        customerAdvertisementStore.advertisement.sub_category.id;
    }

    customerAdvertisementStore.filters.except =
      customerAdvertisementStore.advertisement.id;

    await nextTick(async () => {
      await customerAdvertisementStore.fetch();
    });
  } catch (error) {
    console.error('Error fetching advertisement:', error);
    router.push({ name: '404' });
  }
};

/**
 * Open Modal Preview
 */
const onOpenModalPreview = () => {
  openModalPreview.value = true;
};

/**
 * Watch for changes in the route slug and fetch the category
 */
watch(
  () => router.currentRoute.value.params.id,
  async (newId) => {
    if (!newId || isNaN(parseInt(newId as string))) {
      router.push({ name: '404' });
      return;
    }
    await fetchLogic();
  }
);

/**
 * On mounted fetch the advertisement
 */
onMounted(async () => await fetchLogic());

function stripHtml(html) {
  const div = document.createElement('div');
  div.innerHTML = html;
  return div.textContent || div.innerText || '';
}

// When loading the advertisement for editing:
if (customerAdvertisementStore.advertisement.description) {
  customerAdvertisementStore.advertisement.description = stripHtml(
    customerAdvertisementStore.advertisement.description
  );
}
</script>
