<template>
  <section class="flex flex-col gap-5">
    <h1
      class="text-2xl font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2"
    >
      <i class="icon mgc_world_2_line"></i> {{ $t('General') }}
    </h1>

    <form
      class="grid grid-cols-1 gap-5"
      @submit.prevent="settingsStore.submit()"
      v-if="!settingsStore.isLoading"
    >
      <!-- Important fields -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- App Name -->
        <v-input
          input-style="two"
          id="app_name"
          type="text"
          :label="$t('App Name')"
          :value="settingsStore.appSettings.app_name"
          @update:input="settingsStore.appSettings.app_name = $event"
        />
        <!-- End App Name -->

        <!-- App Email -->
        <v-input
          input-style="two"
          id="app_email"
          type="email"
          :label="$t('App Email')"
          :value="settingsStore.appSettings.app_email"
          @update:input="settingsStore.appSettings.app_email = $event"
        />
        <!-- End App Email -->

        <!-- App URL -->
        <v-input
          input-style="two"
          id="app_url"
          type="url"
          :label="$t('App URL')"
          :value="settingsStore.appSettings.app_url"
          @update:input="settingsStore.appSettings.app_url = $event"
        />
        <!-- End App URL -->

        <!-- App keywords -->
        <v-input
          input-style="two"
          id="app_keywords"
          type="text"
          :label="$t('App Keywords')"
          :value="settingsStore.appSettings.app_keywords"
          @update:input="settingsStore.appSettings.app_keywords = $event"
        />
        <!-- End App keywords -->

        <!-- App Timezone -->
        <v-select
          input-style="two"
          id="app_timezone"
          :label="$t('App Timezone')"
          :options="timezones"
          :value="settingsStore.appSettings.app_timezone"
          @update:value="settingsStore.appSettings.app_timezone = $event"
        />
        <!-- End App Timezone -->

        <!-- App Currency -->
        <v-select
          input-style="two"
          id="app_currency"
          :label="$t('App Currency')"
          :options="currencies"
          :value="settingsStore.appSettings.app_currency"
          @update:value="settingsStore.appSettings.app_currency = $event"
        />
        <!-- End App Currency -->

        <!-- App WhatsApp -->
        <v-input
          input-style="two"
          id="app_whatsapp"
          type="text"
          :label="$t('WhatsApp Number')"
          :value="settingsStore.appSettings.app_whatsapp"
          @update:input="settingsStore.appSettings.app_whatsapp = $event"
        />
        <!-- End App WhatsApp -->

        <!-- App Description -->
        <v-textarea
          input-style="two"
          id="app_description"
          :label="$t('App Description')"
          :value="settingsStore.appSettings.app_description"
          @update:input="settingsStore.appSettings.app_description = $event"
        />
        <!-- End App Description -->

        <!-- App Robots -->
        <v-select
          @update:value="settingsStore.appSettings.app_robots = $event"
          :value="settingsStore.appSettings.app_robots"
          :label="$t('App Robots')"
          input-style="two"
          id="app_robots"
        >
          <option value="index, follow">
            {{ $t('Index, Follow') }}
          </option>
          <option value="noindex, follow">
            {{ $t('Noindex, Follow') }}
          </option>
          <option value="index, nofollow">
            {{ $t('Index, Nofollow') }}
          </option>
          <option value="noindex, nofollow">
            {{ $t('Noindex, Nofollow') }}
          </option>
        </v-select>
        <!-- End App Robots -->

        <!-- warning_comments_message -->
        <div class="flex flex-col gap-1 col-span-2">
          <label
            for="warning_comments_message"
            class="block text-sm dark:text-white"
          >
            {{ $t('Warning Comments Message') }}
          </label>

          <div class="bg-white rounded-lg">
            <v-editor
              @update:input="
                settingsStore.appSettings.warning_comments_message = $event
              "
              :value="settingsStore.appSettings.warning_comments_message"
              :placeholder="$t('Description')"
            />
          </div>
        </div>
        <!-- End warning_comments_message -->
      </div>

      <!-- Asset (Logo & Favicon) -->
      <div class="flex flex-col gap-5">
        <h2
          class="text-2xl font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2"
        >
          <i class="icon mgc_photo_album_2_fill"></i>
          {{ $t('Asset (Logo & Favicon)') }}
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
          <!-- App Logo -->
          <div
            id="logo-preview"
            class="w-full p-6 mb-4 bg-white border-dashed border border-gray-400 rounded items-center mx-auto text-center cursor-pointer"
          >
            <input
              id="upload-logo"
              type="file"
              class="hidden"
              accept="image/*"
              @change="onChangeAsset($event, 'logo')"
            />
            <label for="upload-logo" class="cursor-pointer flex flex-col gap-1">
              <img
                :src="getImageSrc('logo')"
                id="imgLogo"
                alt="logo"
                class="h-[100px] text-gray-700 mx-auto mb-4"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-8 h-8 text-gray-700 mx-auto mb-4"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                />
              </svg>
              <span class="mb-2 text-xl font-bold tracking-tight text-gray-700">
                {{ $t('Upload Logo') }}
              </span>
              <span class="font-normal text-sm text-gray-400 md:px-6">
                {{
                  $t('Choose photo size should be less than :size', {
                    size: `10MB`,
                  })
                }}
              </span>
              <span class="font-normal text-sm text-gray-400 md:px-6">
                {{
                  $t('and should be in :format format.', {
                    format: `JPG, PNG, or GIF`,
                  })
                }}
              </span>
              <span id="filename" class="text-gray-500 bg-gray-200 z-50"></span>
            </label>
          </div>

          <!-- App Favicon -->
          <div
            id="favicon-preview"
            class="w-full p-6 mb-4 bg-white border-dashed border border-gray-400 rounded items-center mx-auto text-center cursor-pointer"
          >
            <input
              id="upload-favicon"
              type="file"
              class="hidden"
              accept="image/*"
              @change="onChangeAsset($event, 'fav')"
            />
            <label
              for="upload-favicon"
              class="cursor-pointer flex flex-col gap-1"
            >
              <img
                :src="getImageSrc('favicon')"
                id="imgFav"
                alt="favicon"
                class="h-[100px] text-gray-700 mx-auto mb-4"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-8 h-8 text-gray-700 mx-auto mb-4"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"
                />
              </svg>
              <span class="mb-2 text-xl font-bold tracking-tight text-gray-700">
                {{ $t('Upload Favicon') }}
              </span>
              <span class="font-normal text-sm text-gray-400 md:px-6">
                {{
                  $t('Choose photo size should be less than :size', {
                    size: `2MB`,
                  })
                }}
              </span>
              <span class="font-normal text-sm text-gray-400 md:px-6">
                {{
                  $t('and should be in :format format.', {
                    format: `JPG, PNG, or GIF`,
                  })
                }}
              </span>
              <span id="filename" class="text-gray-500 bg-gray-200 z-50"></span>
            </label>
          </div>
        </div>
      </div>

      <!-- Social Media -->
      <div class="flex flex-col gap-5">
        <h2
          class="text-2xl font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2"
        >
          <i class="icon mgc_social_x_fill"></i> {{ $t('Social Media') }}
        </h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
          <!-- Facebook -->
          <v-input
            input-style="two"
            id="hs-inline-add-on"
            type="text"
            :label="$t('Facebook')"
            :value="settingsStore.appSettings.app_social_media?.facebook"
            @update:input="
              settingsStore.appSettings.app_social_media.facebook = $event
            "
          />
          <!-- End Facebook -->

          <!-- Twitter -->
          <v-input
            input-style="two"
            id="hs-inline-add-on"
            type="text"
            :label="$t('Twitter')"
            :value="settingsStore.appSettings.app_social_media?.twitter"
            @update:input="
              settingsStore.appSettings.app_social_media.twitter = $event
            "
          />
          <!-- End Twitter -->

          <!-- Instagram -->
          <v-input
            input-style="two"
            id="hs-inline-add-on"
            type="text"
            :label="$t('Instagram')"
            :value="settingsStore.appSettings.app_social_media?.instagram"
            @update:input="
              settingsStore.appSettings.app_social_media.instagram = $event
            "
          />
          <!-- End Instagram -->

          <!-- Linkedin -->
          <v-input
            input-style="two"
            id="hs-inline-add-on"
            type="text"
            :label="$t('LinkedIn')"
            :value="settingsStore.appSettings.app_social_media?.linkedin"
            @update:input="
              settingsStore.appSettings.app_social_media.linkedin = $event
            "
          />
          <!-- End Linkedin -->

          <!-- Whatsapp -->
          <div>
            <v-input
              input-style="two"
              id="hs-inline-add-on"
              type="text"
              :label="$t('Whatsapp')"
              :value="settingsStore.appSettings.app_social_media?.whatsapp"
              @update:input="
                settingsStore.appSettings.app_social_media.whatsapp = $event
              "
            />
            <span class="text-sm text-gray-400 dark:text-gray-500">
              {{
                $t(
                  'Please enter your phone number with country code +1234567890'
                )
              }}
            </span>
          </div>
          <!-- End Whatsapp -->
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-2">
        <v-button
          color="prussianblue"
          type="submit"
          :loading="settingsStore.isSubmitting"
        >
          <i class="icon mgc_save_2_fill"></i> {{ $t('Save') }}
        </v-button>
      </div>
    </form>
    <div v-else class="flex items-center justify-center">
      <v-loader :size="5" />
    </div>
  </section>
</template>

<script setup lang="ts">
import { useSettingsStore } from '@/stores/SettingsStore';
import { computed, inject, nextTick, onBeforeMount, ref, watch } from 'vue';
import axios from 'axios';
import { notify } from 'notiwind';
import { wTrans } from 'laravel-vue-i18n';

const settingsStore = useSettingsStore();
const globalUrl = inject('$assetUrl');
let parsedCountries: any = ref([]);

// Store preview URLs for uploaded images
const logoPreviewUrl = ref('');
const faviconPreviewUrl = ref('');

// Clear preview URLs when submission completes
watch(() => settingsStore.isSubmitting, (newVal, oldVal) => {
  if (oldVal && !newVal) { // Was submitting, now not submitting (submission completed)
    logoPreviewUrl.value = '';
    faviconPreviewUrl.value = '';
  }
});

const currencies = computed(() => {
  return parsedCountries.value.map((country: any) => {
    return { label: country.currency, value: country.currency };
  });
});

const timezones = computed(() => {
  return parsedCountries.value.map((country: any) => {
    return { label: country.timezone, value: country.timezone };
  });
});

// Helper function to get the correct image source
const getImageSrc = (type: 'logo' | 'favicon') => {
  if (type === 'logo') {
    // 1. If a new file was just selected for preview, use its local URL.
    if (logoPreviewUrl.value) {
      return logoPreviewUrl.value;
    }
    // 2. Otherwise, just use the full URL provided by the backend.
    return settingsStore.appSettings.app_logo;
  } 
  
  if (type === 'favicon') {
    // 1. If a new file was just selected for preview, use its local URL.
    if (faviconPreviewUrl.value) {
      return faviconPreviewUrl.value;
    }
    // 2. Otherwise, just use the full URL provided by the backend.
    return settingsStore.appSettings.app_favicon;
  }
};

const onChangeAsset = async (event: any, type: string) => {
  const file: any = event.target.files[0];

  if (!file) return;

  // Check file size based on type
  const maxSize = type === 'logo' ? 10 * 1024 * 1024 : 2 * 1024 * 1024; // 10MB for logo, 2MB for favicon
  const maxSizeText = type === 'logo' ? '10MB' : '2MB';
  
  if (file.size > maxSize) {
    notify(
      {
        group: 'dashboard',
        ok: false,
        title: wTrans('Error'),
        text: wTrans('File size must be less than :size', { size: maxSizeText }),
      },
      2000
    );
    
    // Reset the input
    event.target.value = '';
    return;
  }

  // Check file type
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
  if (!allowedTypes.includes(file.type)) {
    notify(
      {
        group: 'dashboard',
        ok: false,
        title: wTrans('Error'),
        text: wTrans('Please upload only JPG, PNG, or GIF images'),
      },
      2000
    );
    
    // Reset the input
    event.target.value = '';
    return;
  }

  const reader = new FileReader();
  reader.readAsDataURL(file);

  reader.onload = async () => {
    if (type === 'logo') {
      settingsStore.appSettings.app_logo = file;
      logoPreviewUrl.value = reader.result as string;
    } else {
      settingsStore.appSettings.app_favicon = file;
      faviconPreviewUrl.value = reader.result as string;
    }
  };
};

onBeforeMount(async () => {
  await axios.get(`${globalUrl}/countries.json`).then(({ data }: any): void => {
    parsedCountries.value = data;
  });
});
</script>