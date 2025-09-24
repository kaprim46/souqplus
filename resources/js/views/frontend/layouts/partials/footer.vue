<template>
  <footer class="bg-prussianblue w-full" v-if="!settingsStore.isLoading">
    <div
      class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 lg:pt-20 mx-auto"
    >
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
        <div class="col-span-full lg:col-span-1">
          <a
            class="flex-none text-xl font-semibold text-white dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
            href="#"
            aria-label="Brand"
          >
            {{ settingsStore.appSettings.app_name }}
          </a>
        </div>

        <div
          class="col-span-1"
          v-if="!pagesStore.isLoading && pagesStore.pages.length"
        >
          <h4 class="font-semibold text-gray-100">
            {{ $t('Pages') }}
          </h4>

          <div class="mt-3 grid space-y-3">
            <router-link
              class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
              v-for="page in pagesStore.pages"
              :key="page.id"
              :to="{ name: 'customer.page.show', params: { slug: page.slug } }"
            >
              {{ page.name }}
            </router-link>
          </div>
        </div>

        <div
          class="col-span-1"
          v-if="
            !customerCategoryStore.isLoading &&
            customerCategoryStore.categories.length
          "
        >
          <h4 class="font-semibold text-gray-100">
            {{ $t('Categories') }}
          </h4>

          <div class="mt-3 grid space-y-3">
            <p
              v-for="category in customerCategoryStore.categories"
              :key="category.id"
            >
              <router-link
                v-if="category.slug"
                :to="{
                  name: 'customer.category.show',
                  params: { slug: category.slug },
                }"
                class="inline-flex gap-x-2 text-gray-400 hover:text-gray-200 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
              >
                {{ category.name }}
              </router-link>
              <span
                v-else
                class="inline-flex gap-x-2 text-gray-400 opacity-50 cursor-not-allowed"
              >
                {{ category.name }}
              </span>
            </p>
          </div>
        </div>
      </div>

      <div
        class="mt-5 sm:mt-12 grid gap-y-2 sm:gap-y-0 sm:flex sm:justify-between sm:items-center"
      >
        <div class="flex justify-between items-center">
          <p class="text-sm text-gray-400">
            &copy; {{ new Date().getFullYear() }}
            {{ settingsStore.appSettings.app_name }}.
            {{ $t('All rights reserved') }}.
          </p>
        </div>

        <div
          v-if="settingsStore.appSettings.app_social_media"
          class="flex gap-x-2"
        >
          <a
            v-if="settingsStore.appSettings.app_social_media.linkedin"
            :href="settingsStore.appSettings.app_social_media.linkedin"
            class="w-10 h-10 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-white hover:bg-white/10 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-1 focus:ring-gray-600"
          >
            <svg
              class="flex-shrink-0 w-4 h-4"
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M4.98 3.5c0 1.381-1.11 2.5-2.48 2.5s-2.48-1.119-2.48-2.5c0-1.38 1.11-2.5 2.48-2.5s2.48 1.12 2.48 2.5zm.02 4.5h-5v16h5v-16zm7.982 0h-4.968v16h4.969v-8.399c0-4.67 6.029-5.052 6.029 0v8.399h4.988v-10.131c0-7.88-8.922-7.593-11.018-3.714v-2.155z"
              />
            </svg>
          </a>
          <a
            v-if="settingsStore.appSettings.app_social_media.facebook"
            :href="settingsStore.appSettings.app_social_media.facebook"
            class="w-10 h-10 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-white hover:bg-white/10 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-1 focus:ring-gray-600"
          >
            <svg
              class="flex-shrink-0 w-4 h-4"
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              viewBox="0 0 16 16"
            >
              <path
                d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"
              />
            </svg>
          </a>
          <a
            v-if="settingsStore.appSettings.app_social_media.instagram"
            :href="settingsStore.appSettings.app_social_media.instagram"
            class="w-10 h-10 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-white hover:bg-white/10 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-1 focus:ring-gray-600"
          >
            <svg
              class="flex-shrink-0 w-4 h-4"
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
              />
            </svg>
          </a>
          <a
            v-if="settingsStore.appSettings.app_social_media.twitter"
            :href="settingsStore.appSettings.app_social_media.twitter"
            target="_blank"
            class="w-10 h-10 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-white hover:bg-white/10 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:ring-1 focus:ring-gray-600"
          >
            <svg
              class="flex-shrink-0 w-4 h-4"
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              viewBox="0 0 16 16"
            >
              <path
                d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"
              />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script lang="ts" setup>
import { useSettingsStore } from '@/stores/SettingsStore';
import { onMounted } from 'vue';
import { useCustomerPageStore } from '@/stores/Customer/CustomerPageStore';
import { useCustomerCategoryStore } from '@/stores/Customer/CustomerCategoryStore';

const settingsStore = useSettingsStore();
const pagesStore = useCustomerPageStore();
const customerCategoryStore = useCustomerCategoryStore();
</script>
