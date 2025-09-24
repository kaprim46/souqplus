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
                {{ $t('Explore slider') }}
              </h2>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('Manage your explore slider') }}
              </p>
            </div>


            <div class="flex gap-x-2">
              <v-input :placeholder="$t('Search')"  />
              <v-button color="blue" @click="isModalOpen = true" custom-class="whitespace-nowrap">
                <i class="icon mgc_plus_line"></i>
                {{ $t('Add slider') }}
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
                    {{ $t('Content') }}
                  </span>
                </th>

                <th scope="col" class="text-end"></th>
              </tr>
              </thead>

              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-if="!sliderStore.isLoading" v-for="slider in sliderStore.sliders" :key="slider.id">
                <td class="px-6 py-3">
                  <figure class="flex items-center gap-x-2">
                    <img :src="slider.image_url" alt="Slider" class="min-w-20 min-h-20 max-w-20 max-h-20 object-cover rounded-lg" />
                    <span class="text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">
                      {{ slider.title ?? '--' }}
                    </span>
                  </figure>
                </td>
                <td class="px-6 py-3 w-full">
                  <span class="text-sm text-gray-800 dark:text-gray-200">
                    {{ slider.description ?? '--' }}
                  </span>
                </td>
                <td class="px-6 py-3 text-end">
                  <div class="flex gap-x-2">
                    <v-button color="blue" @click="selectedSlider = slider; isModalOpen = true" custom-class="whitespace-nowrap">
                      <i class="icon mgc_edit_line"></i>
                      {{ $t('Edit') }}
                    </v-button>
                    <v-button color="red" @click="sliderStore.onDelete(slider.id)"  custom-class="whitespace-nowrap" v-if="slider.id">
                      <i class="icon mgc_trash_line"></i>
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
              v-if="!sliderStore.isLoading"
              class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">

            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ sliderStore.pagination.total }}
                </span>
                {{ $t('Results') }}
              </p>
            </div>

            <div>
              <div class="inline-flex gap-x-2">
                <button
                    @click="sliderStore.getSliders(true, true)"
                    :disabled="sliderStore.pagination.current_page === 1"
                    type="button"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                  <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                  {{ $t('Previous') }}
                </button>

                <button
                    @click="sliderStore.getSliders(true)"
                    :disabled="sliderStore.pagination.current_page === sliderStore.pagination.last_page"
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
  <v-modal :is-open="isModalOpen" @update:closeModal="isModalOpen = false; selectedSlider = {
        title: '', description: '', image: '', link: ''
    }">
    <template v-slot:title>
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
        {{ selectedSlider?.id ? $t('Edit Slider') : $t('Add Slider') }}
      </h3>
    </template>

    <template v-slot:content>
      <form class="flex flex-col gap-y-4" @submit.prevent="onSubmit">

        <!-- Form Group -->
        <div>
          <label for="title" class="block text-sm mb-2 dark:text-white">
            {{ $t('Title') }}
          </label>
          <v-input
              type="text"
              id="title"
              :value="selectedSlider.title"
              :placeholder="$t('Name')"
              @update:input="selectedSlider.title = $event"
          />
          <p class="text-xs text-red-600 mt-2" id="title-error" v-if="sliderStore.errors.title">
            @{{ sliderStore.errors.title[0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
          <label for="content" class="block text-sm mb-2 dark:text-white">
            {{ $t('Content') }}
          </label>
          <v-textarea
              :value="selectedSlider.description"
              :placeholder="$t('Content')"
              @update:input="selectedSlider.description = $event"
          />
          <p class="text-xs text-red-600 mt-2" id="content-error" v-if="sliderStore.errors.content">
            @{{ sliderStore.errors.content[0] }}
          </p>
        </div>
        <!-- End Form Group -->

        <!-- Link -->
        <div>
          <label for="link" class="block text-sm mb-2 dark:text-white">
            {{ $t('Link') }}
          </label>
          <v-input
              type="text"
              id="link"
              :value="selectedSlider.link"
              :placeholder="$t('Link')"
              @update:input="selectedSlider.link = $event"
          />
          <p class="text-xs text-red-600 mt-2" id="link-error" v-if="sliderStore.errors.link">
            @{{ sliderStore.errors.link[0] }}
          </p>
        </div>

        <!-- Screen Image -->
        <div>
          <div id="logo-preview" class="w-full p-6 bg-white border-dashed border border-gray-400 rounded items-center mx-auto text-center cursor-pointer">
            <input id="upload-logo" type="file" class="hidden" accept="image/*" @change="onChangeScreen($event)" />
            <label for="upload-logo" class="cursor-pointer flex flex-col gap-1">
              <img v-if="selectedSlider.image_url" :src="selectedSlider.image_url" id="imgLogo" alt="Screen" class="h-[100px] text-gray-700 mx-auto mb-4" />
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-700 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
              </svg>
              <span class="mb-2 text-xl font-bold tracking-tight text-gray-700">
                {{ $t('Upload Image') }}
              </span>
              <span class="font-normal text-sm text-gray-400 md:px-6">
                  {{ $t('Choose photo size should be less than :size', {size: `2mb`}) }}
              </span>
              <span class="font-normal text-sm text-gray-400 md:px-6">
                    {{ $t('and should be in :format format.', {format: `JPG, PNG, or GIF`}) }}
                </span>
              <span id="filename" class="text-gray-500 bg-gray-200 z-50"></span>
            </label>
          </div>
          <p class="text-xs text-red-600 mt-2" id="image-error" v-if="sliderStore.errors.image">
            @{{ sliderStore.errors.image[0] }}
          </p>
        </div>
        <!-- End Screen Image -->

        <v-button color="blue" type="submit" class="whitespace-nowrap" :disabled="sliderStore.isLoadingAction" :loading="sliderStore.isLoadingAction">
          <i class="icon mgc_save_line"></i>
          {{ $t('Save') }}
        </v-button>
      </form>
    </template>
  </v-modal>
</template>

<script lang="ts" setup>
import {nextTick, onMounted, ref} from "vue";
import {Slider, useSliderStore} from "@/stores/SliderStore";

const sliderStore       = useSliderStore();
const isModalOpen       = ref(false);
const selectedSlider    = ref<Slider>({
  description: "", image: "", link: "", title: ""
});

const onChangeScreen = (event: any) => {
  const file      = event.target.files[0] as File
  const reader    = new FileReader()
  reader.onload = (e) => {
    selectedSlider.value.image        = file
    selectedSlider.value.image_url    = e.target?.result as string
  }
  reader.readAsDataURL(file)
}

const onSubmit = async () => {
  selectedSlider.value?.id ? await sliderStore.updateSlider(selectedSlider.value).then((ok: any) => {
    if (ok) {
      isModalOpen.value = false;
      selectedSlider.value = {
        title: '', description: '', image: '', link: ''
      }
    }
  }) : await sliderStore.createSlider(selectedSlider.value).then((ok: any) => {
    if (ok) {
      isModalOpen.value = false;
      selectedSlider.value = {
        title: '', description: '', image: '', link: ''
      }
    }
  });
}



onMounted(async () => {
  sliderStore.filters.pagination = 1;

  await nextTick(async () => {
    await sliderStore.getSliders();
  });
});
</script>
