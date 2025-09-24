<template>
    <!-- Sidebar Toggle -->
    <div class="sticky top-0 inset-x-0 z-20 bg-white border-y px-4 sm:px-6 md:px-8 lg:hidden dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center py-4">
            <!-- Navigation Toggle -->
            <button type="button" class="text-gray-500 hover:text-gray-600" data-hs-overlay="#application-sidebar-dark" aria-controls="application-sidebar-dark" aria-label="Toggle navigation">
                <span class="sr-only">Toggle Navigation</span>
                <svg class="w-5 h-5" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </button>
            <!-- End Navigation Toggle -->

            <!-- Breadcrumb -->
            <ol class="ms-3 flex items-center whitespace-nowrap" aria-label="Breadcrumb">
                <li class="flex items-center text-sm text-gray-800 dark:text-gray-400">
                    <a href="#" class="hover:underline">
                        {{ $t('Home') }}
                    </a>
                    <svg class="flex-shrink-0 mx-3 overflow-visible h-2.5 w-2.5 text-gray-400 dark:text-gray-600" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </li>
                <li class="text-sm font-semibold text-gray-800 truncate dark:text-gray-400" aria-current="page">
                    {{ $t('Dashboard') }}
                </li>
            </ol>
            <!-- End Breadcrumb -->
        </div>
    </div>
    <!-- End Sidebar Toggle -->

    <!-- Sidebar -->
    <div id="application-sidebar-dark" class="
        hs-overlay
        hs-overlay-open:translate-x-0
        -translate-x-full
        transition-all
        duration-300
        transform
        hidden
        fixed
        top-0
        rtl:end-0
        ltr:start-0
        lg:rtl:end-auto
        lg:ltr:start-auto
        bottom-0
        z-[60]
        w-64
        bg-prussianblue
        border-e
        border-gray-800
        pt-7
        pb-10
        overflow-y-auto
        lg:block
        lg:translate-x-0
        lg:bottom-0
        [&::-webkit-scrollbar]:w-2
        [&::-webkit-scrollbar-thumb]:rounded-full
        [&::-webkit-scrollbar-track]:bg-gray-100
        [&::-webkit-scrollbar-thumb]:bg-gray-300
        dark:[&::-webkit-scrollbar-track]:bg-slate-700
        dark:[&::-webkit-scrollbar-thumb]:bg-slate-500
    ">
        <div class="px-6">
            <router-link :to="{name: 'dashboard', params: {}}" class="flex justify-center items-center" href="#" aria-label="Brand" v-if="!settingsStore.isLoading">
            <img class="h-[75px]" :src="getLogoSrc()" :alt="settingsStore.appSettings.app_name" />
        </router-link>
        </div>

        <nav class="hs-accordion-group p-6 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
            <ul class="space-y-1.5">
                <li v-for="(item, index) in listItems" :key="index">
                    <router-link
                        class="flex items-center gap-x-3 py-2 px-2.5 rounded-lg focus:outline-none"
                        :class="{'bg-easternblue': item.active}"
                        :to="{ name: item.link, params: {} }"
                    >
                        <i :class="item.icon" class="text-xl text-white"></i>
                        <span class="text-sm text-white">{{ item.title }}</span>
                    </router-link>
                </li>
            </ul>
        </nav>
    </div>
    <!-- End Sidebar -->
</template>

<script lang="ts" setup>
import { inject, ref, watch } from 'vue'
import { useRoute } from "vue-router";
import { wTrans } from 'laravel-vue-i18n';
import { useSettingsStore } from "@/stores/SettingsStore";

const route = useRoute();
const settingsStore = useSettingsStore();
const $assetUrl = inject('$assetUrl');

// Function to get logo source with proper URL handling
const getLogoSrc = () => {
  // Just return the URL from the store. 
  // We can add a cache-busting timestamp to force a reload after update.
  const timestamp = new Date().getTime();
  return `${settingsStore.appSettings.app_logo}?t=${timestamp}`;
};

const listItems = ref([
    {
        icon: 'icon mgc_dashboard_line',
        title: wTrans('Dashboard'),
        link: 'dashboard',
        active: route.name === 'dashboard'
    },
    {
        icon: 'icon mgc_settings_3_line',
        title: wTrans('Settings'),
        link: 'settings',
        active: route.name === 'settings'
    },
    {
        icon: 'icon mgc_cellphone_line',
        title: wTrans('Splash Screens'),
        link: 'splash-screens.index',
        active: route.name === 'splash-screens.index' || route.name === 'splash-screen.create' || route.name === 'splash-screen.edit'
    },
    {
      icon: 'icon mgc_photo_album_2_line',
      title: wTrans('Explore slider'),
      link: 'explore-sliders.index',
      active: route.name === 'explore-sliders.index'
    },
    {
        icon: 'icon mgc_map_pin_line',
        title: wTrans('Countries'),
        link: 'countries.index',
        active: route.name === 'countries.index' || route.name === 'country.create' || route.name === 'country.edit'
    },
    {
        icon: 'icon mgc_map_pin_line',
        title: wTrans('Cities'),
        link: 'cities.index',
        active: route.name === 'cities.index' || route.name === 'city.create' || route.name === 'city.edit'
    },
    {
        icon: 'icon mgc_group_line',
        title: wTrans('Customers'),
        link: 'customers.index',
        active: route.name === 'customers.index' || route.name === 'customer.create' || route.name === 'customer.edit'
    },
    {
          icon: 'icon mgc_filter_2_fill',
          title: wTrans('Filters'),
          link: 'filters.index',
          active: route.name === 'filters.index'
    },
    {
        icon: 'icon mgc_badge_fill',
        title: wTrans('Badges'),
        link: 'badges.index',
        active: route.name === 'badges.index'
    },
    {
        icon: 'icon mgc_sitemap_line',
        title: wTrans('Categories'),
        link: 'categories.index',
        active: route.name === 'categories.index' || route.name === 'category.create' || route.name === 'category.edit'
    },
    {
        icon: 'icon mgc_transfer_fill',
        title: wTrans('Sub Categories'),
        link: 'sub-categories.index',
        active: route.name === 'sub-categories.index' || route.name === 'sub-category.create' || route.name === 'sub-category.edit'
    },
    {
        icon: 'icon mgc_certificate_line',
        title: wTrans('Advertisements'),
        link: 'advertisements.index',
        active: route.name === 'advertisements.index' || route.name === 'advertisement.create' || route.name === 'advertisement.edit'
    },
    {
        icon: 'icon mgc_border_blank_line',
        title: wTrans('Pages'),
        link: 'pages.index',
        active: route.name === 'pages.index' || route.name === 'page.create' || route.name === 'page.edit'
    },
    {
        icon: 'icon mgc_messenger_line',
        title: wTrans('Contact Us'),
        link: 'contactus.index',
        active: route.name === 'contactus.index'
    }
]);

watch(() => route.name, (name) => {
    listItems.value.forEach((item) => {
        if ((['categories.index', 'category.create', 'category.edit'].includes(name) && item.link === 'categories.index') ||
            (['customers.index', 'customer.create', 'customer.edit'].includes(name) && item.link === 'customers.index') ||
            (['advertisements.index', 'advertisement.create', 'advertisement.edit'].includes(name) && item.link === 'advertisements.index') ||
            (['pages.index', 'page.create', 'page.edit'].includes(name) && item.link === 'pages.index') ||
            (['sub-categories.index', 'sub-category.create', 'sub-category.edit'].includes(name) && item.link === 'sub-categories.index')
        ) {
            item.active = true;
        } else {
            item.active = item.link === name;
        }
    });
});
</script>