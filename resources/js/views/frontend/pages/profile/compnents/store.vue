<template>
  <form v-if="!main_store.isLoadingMe" @submit.prevent="main_store.onStoreUpdate" class="flex flex-col gap-5">
    <!-- Cover Image & Store Images -->
    <div class="relative h-[300px] mb-16">
      <div id="cover" class="relative h-[300px] overflow-hidden rounded-lg group">
        <img :src="main_store.store.business_cover_url" alt="cover" class="w-full h-full object-cover" />
        <input type="file" id="cover-image" name="cover-image" class="hidden" @change="main_store.uploadCoverImage" />
        <transition name="fade" mode="out-in">
        <div class="absolute gap-2 p-2 bg-black inset-0 hidden group-hover:flex items-center justify-center group-hover:bg-opacity-50 transition-all duration-300">
          <label for="cover-image" class="cursor-pointer">
            <i class="icon mgc_upload_2_fill text-6xl text-white"></i>
          </label>
        </div>
        </transition>
      </div>
      <div class="w-[100px] h-[100px] rounded-full overflow-hidden absolute bottom-0 transform translate-y-1/2 left-1/2 -translate-x-1/2 ring-4 ring-white group">
        <img :src="main_store.store.business_logo_url" alt="avatar" class="w-full h-full object-cover" />
        <input type="file" id="avatar" name="avatar" class="hidden" @change="main_store.uploadStoreLogo" />
        <div class="absolute inset-0 gap-2 p-2 bg-black bg-opacity-50 hidden group-hover:flex items-center justify-center transition-all duration-300">
          <label for="avatar" class="cursor-pointer">
            <i class="icon mgc_upload_2_fill text-4xl text-white"></i>
          </label>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <!-- Form Group business_type -->
      <div>
        <label for="type" class="block text-sm mb-2 dark:text-white">
          {{ $t('Type of Business') }}
        </label>
        <v-input
            type="text"
            id="type"
            name="type"
            :value="main_store.store.business_type"
            :placeholder="$t('Type of Business')"
            @update:input="onUpdate('business_type', $event)"
        />
        <p class="text-xs text-red-600 mt-2" id="type-error" v-if="main_store.errors['business_type']">
          @{{ main_store.errors['business_type'][0] }}
        </p>
      </div>

      <!-- Form Group business_name-->
      <div>
        <label for="business-name" class="block text-sm mb-2 dark:text-white">
          {{ $t('Business Name') }}
        </label>
        <v-input
            type="text"
            id="business-name"
            name="business-name"
            :value="main_store.store.business_name"
            :placeholder="$t('Business Name')"
            @update:input="onUpdate('business_name', $event)"
        />
        <p class="text-xs text-red-600 mt-2" id="business-name-error" v-if="main_store.errors['business_name']">
          @{{ main_store.errors['business_name'][0] }}
        </p>
      </div>

      <!-- Form Group business_phone-->
      <div>
        <label for="phone" class="block text-sm mb-2 dark:text-white">
          {{ $t('Phone Number') }}
        </label>
        <v-phoneInput @update:phoneNumber="main_store.store.phone_number = $event" @update:countryCode="main_store.store.country_code = $event" :value="main_store.store.phone_number" :valueCountryCode="main_store.store.country_code ?? '+212'" />
        <p class="text-xs text-red-600 mt-2" id="phone-error" v-if="main_store.errors['phone_number']">
          @{{ main_store.errors['phone_number'][0] }}
        </p>
      </div>

      <!-- Form Group business_email-->
      <div>
        <label for="email" class="block text-sm mb-2 dark:text-white">
          {{ $t('Email') }}
        </label>
        <v-input
            type="text"
            id="email"
            name="email"
            :value="main_store.store.business_email"
            :placeholder="$t('Email')"
            @update:input="onUpdate('business_email', $event)"
        />
        <p class="text-xs text-red-600 mt-2" id="email-error" v-if="main_store.errors['business_email']">
          @{{ main_store.errors['business_email'][0] }}
        </p>
      </div>

      <!-- Form Group business_bio-->
      <div class="col-span-1 lg:col-span-2">
        <label for="content" class="block text-sm mb-2 dark:text-white">
          {{ $t('Bio') }}
        </label>
        <v-textarea id="bio" :value="main_store.store.business_bio" :placeholder="$t('Bio')" @update:input="onUpdate('business_bio', $event)" />
        <p class="text-xs text-red-600 mt-2" id="bio-error" v-if="main_store.errors['business_bio']">
          @{{ main_store.errors['business_bio'][0] }}
        </p>
      </div>

      <!-- Form Group country -->
      <div>
        <label for="country_id" class="block text-sm mb-2 dark:text-white">
          {{ $t('Country') }}
        </label>
        <v-select
            id="country_id"
            name="country_id"
            :value="main_store.store.country?.id"
            @update:value="main_store.store.country.id = $event"
        >
          <option value="0">{{ $t('Select Country') }}</option>
          <option v-for="country in countryStore.countries" :key="country.id" :value="country.id">
            @{{ country.name }}
          </option>
        </v-select>
        <p class="text-xs text-red-600 mt-2" id="country_id-error" v-if="main_store.errors['country_id']">
          @{{ main_store.errors['country_id'][0] }}
        </p>
      </div>

      <!-- Form Group city -->
      <div v-if="!countryStore.isLoadingAction && main_store.store.country?.id">
        <label for="city" class="block text-sm mb-2 dark:text-white">
          {{ $t('City') }}
        </label>
        <v-select
            id="city_id"
            name="city_id"
            :value="main_store.store.city?.id"
            @update:value="main_store.store.city.id = $event"
        >
          <option value="0">{{ $t('Select City') }}</option>
          <option v-for="city in countryStore.cities" :key="city.id" :value="city.id">
            @{{ city.name }}
          </option>
        </v-select>
        <p class="text-xs text-red-600 mt-2" id="city-error" v-if="main_store.errors['city_id']">
          @{{ main_store.errors['city_id'][0] }}
        </p>
      </div>
      <!-- Form Group address-->

      <!-- Form Group business_district-->
      <div>
        <label for="district" class="block text-sm mb-2 dark:text-white">
          {{ $t('District') }}
        </label>
        <v-input
            type="text"
            id="district"
            name="district"
            :value="main_store.store.business_district"
            :placeholder="$t('District')"
            @update:input="onUpdate('business_district', $event)"
        />
        <p class="text-xs text-red-600 mt-2" id="district-error" v-if="main_store.errors['business_district']">
          @{{ main_store.errors['business_district'][0] }}
        </p>
      </div>

      <!-- Form Group business_location-->
      <div>
        <label for="location" class="block text-sm mb-2 dark:text-white">
          {{ $t('Location') }}
        </label>
        <GMapMap
            :center="{lat: main_store.store.business_location?.lat ?? 0, lng: main_store.store.business_location?.lng ?? 0}"
            :zoom="15"
            @update:center="onUpdate('business_location', $event)"
            class="w-full h-[300px] rounded-lg overflow-hidden"
        >
          <GMapMarker
              :position="{lat: main_store.store.business_location?.lat ?? 0, lng: main_store.store.business_location?.lng ?? 0}"
              :clickable="true"
              :draggable="true"
              @click="onUpdate('business_location', $event.latLng)"
              @dragend="onUpdate('business_location', $event.latLng)"
          />
        </GMapMap>
        <p class="text-xs text-red-600 mt-2" id="location-error" v-if="main_store.errors['business_location']">
          @{{ main_store.errors['business_location'][0] }}
        </p>
      </div>


    </div>

    <!--  Save -->
    <div class="flex items-center justify-end">
      <v-button type="submit" color="green" :loading="main_store.isLoading">
        <i class="icon mgc_save_line"></i>
        {{ $t('Save') }}
      </v-button>
    </div>
  </form>
</template>

<script lang="ts" setup>
import {useMainStore} from "@/stores/Store/MainStore";
import {onBeforeMount, watch} from "vue";
import {useCountryStore} from "@/stores/CountryStore";

const countryStore  = useCountryStore();
const main_store         = useMainStore();

watch(() => main_store.store.country?.id, async(value) => {
  if(value) {
    await countryStore.fetchCities(false,false, value);
  }
},  {immediate: true, deep: true});


const onUpdate = (key: string, value: string) => {
  if(key === 'business_location') {
    main_store.store[key] = {lat: value.lat(), lng: value.lng()};
    return;
  }

  main_store.store[key] = value;
}

onBeforeMount(async() => {
  await countryStore.fetchCountries();
  await main_store.fetchProfile()
});
</script>