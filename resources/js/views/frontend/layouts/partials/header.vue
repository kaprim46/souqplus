<template>
  <div>
    <div class="h-[50px] bg-red-500 flex items-center gap-2 justify-center text-white" v-if="userStore.isLoggedIn && !userStore.me?.verified_at">
      <i class="icon mgc_information_fill text-2xl"></i>
      {{ $t('Please active your account') }}
      <!-- Active call modal -->
      <button
          type="button"
          class="flex items-center gap-x-2 text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:bg-blue-500 py-1 px-2 rounded-lg"
          @click="showActiveAccountModal = true"
      >
          {{ $t('Active now') }}
      </button>
    </div>
    <header class="flex flex-wrap sm:justify-start sm:flex-col z-50 w-full bg-white border-b-2 border-gray-200 text-sm pb-2 sm:pb-0 dark:bg-gray-800 dark:border-gray-700">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-end gap-x-5 w-full py-2 sm:pt-2 sm:pb-0">
                <div class="hs-dropdown relative inline-flex">
                    <button id="hs-dropdown-basic" type="button" class="hs-dropdown-toggle inline-flex justify-center items-center gap-2 font-medium text-slate-600 hover:text-slate-500 text-sm dark:text-slate-400 dark:hover:text-slate-300">
                        <svg class="flex-shrink-0 w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.54 15H17a2 2 0 0 0-2 2v4.54"/><path d="M7 3.34V5a3 3 0 0 0 3 3v0a2 2 0 0 1 2 2v0c0 1.1.9 2 2 2v0a2 2 0 0 0 2-2v0c0-1.1.9-2 2-2h3.17"/><path d="M11 21.95V18a2 2 0 0 0-2-2v0a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2H2.05"/><circle cx="12" cy="12" r="10"/></svg>
                        {{  $defaultLang === 'ar' ? $t('Arabic') : $t('English') }}
                    </button>

                    <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 w-56 hidden z-[99] mt-2 min-w-[15rem] bg-white shadow-md rounded-lg p-2 dark:bg-gray-800 dark:border dark:border-gray-700 dark:divide-gray-700" aria-labelledby="hs-dropdown-basic">
                        <button
                            @click="settingsStore.setLocale('ar')"
                            class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700">
                            {{ $t('Arabic') }}
                        </button>
                        <button
                            @click="settingsStore.setLocale('en')"
                            class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700">
                            {{ $t('English') }}
                        </button>
                    </div>
                </div>

                <router-link class="inline-flex justify-center items-center gap-2 font-medium text-slate-600 hover:text-slate-500 text-sm dark:text-slate-400 dark:hover:text-slate-300" :to="{ name: 'contact.us', params: {} }">
                    {{ $t('Contact us') }}
                </router-link>
                <router-link
                    :to="{ name: 'create-advertisement', params: {} }"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-[#0C74BB] text-[#0C74BB] hover:border-blue-500 hover:text-[#0C74BC] disabled:opacity-50 disabled:pointer-events-none"
                >
                   <i class="icon mgc_add_circle_fill"></i>
                  <span class="sr-only md:not-sr-only">
                    {{ $t('Add your ad') }}
                    </span>
                </router-link>
                <router-link
                    :to="{ name: 'login', params: {} }"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-[#0C74BB] text-[#0C74BB] hover:border-blue-500 hover:text-[#0C74BC] disabled:opacity-50 disabled:pointer-events-none"
                    v-if="!userStore.isLoggedIn"
                >
                    <i class="icon mgc_user_1_fill"></i>
                  <span class="sr-only md:not-sr-only">
                    {{ $t('Sign in') }}
                  </span>
                </router-link>
            </div>
        </div>
        <!-- End Top bar -->

        <nav class="relative container mx-auto px-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8" aria-label="Global">
            <div class="flex items-center justify-between">
                <router-link :to="{name: 'home', params: {}}" class="flex justify-center items-center" href="#" aria-label="Brand" v-if="!settingsStore.isLoading">
                    <img
                        class="h-[75px]"
                        :src="typeof settingsStore.appSettings.app_logo === 'string' && settingsStore.appSettings.app_logo.startsWith('http')
                                ? settingsStore.appSettings.app_logo
                                : `${$assetUrl}/logo/${typeof settingsStore.appSettings.app_logo === 'string' ? settingsStore.appSettings.app_logo : 'logo.svg'}`"
                        :alt="settingsStore.appSettings.app_name"
/>
                </router-link>
                <div class="sm:hidden">
                    <button type="button" class="hs-collapse-toggle w-9 h-9 flex justify-center items-center text-sm font-semibold rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" data-hs-collapse="#navbar-collapse-with-animation" aria-controls="navbar-collapse-with-animation" aria-label="Toggle navigation">
                        <svg class="hs-collapse-open:hidden flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
                        <svg class="hs-collapse-open:block hidden flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>
            </div>
            <div id="navbar-collapse-with-animation" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow sm:block">
                <div class="flex flex-col gap-y-4 gap-x-0 mt-5 sm:flex-row sm:items-center sm:justify-end sm:gap-y-0 sm:gap-x-7 sm:mt-0 sm:ps-7">
                    <router-link class="font-medium sm:py-6 text-[#0C74BB]" :to="{ name: 'home', params: {} }" aria-current="page">
                        {{ $t('Home') }}
                    </router-link>
                    <!-- Explore -->
                    <router-link class="py-2 px-3 inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg bg-orange-600 hover:bg-orange-600/50 text-white disabled:opacity-50 disabled:pointer-events-none" :to="{ name: 'explore', params: {} }" aria-current="page">
                      <i class="icon mgc_user_follow_line"></i>
                      {{ $t('Explore') }}
                    </router-link>

                    <template  v-if="userStore.isLoggedIn">
                        <!-- User Dropdown -->
                        <div class="hs-dropdown relative inline-flex z-50">
                            <button id="hs-dropdown-custom-trigger" type="button" class="hs-dropdown-toggle py-1 ps-1 pe-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-full border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                                <img class="w-8 h-8 object-cover rounded-full" :src="userStore.me?.avatar_url" :title="userStore.me?.name" :alt="userStore.me?.name">
                                <span class="text-gray-600 font-medium truncate max-w-[7.5rem] dark:text-gray-400">
                                    {{ userStore.me?.name }} <i class="icon mgc_check_2_fill text-blue-600" v-if="userStore.me?.is_verified"></i>
                                </span>
                                <svg class="hs-dropdown-open:rotate-180 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </button>

                            <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2 dark:bg-gray-800 dark:border dark:border-gray-700" aria-labelledby="hs-dropdown-custom-trigger">
                                <router-link :to="{name: 'profile', params: {}}"
                                             class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700" href="#">
                                    {{ $t('Account') }}
                                </router-link>
                                <router-link :to="{name: 'favorite-advertisement', params: {}}"
                                             class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700" href="#">
                                    {{ $t('Favorite ads') }}
                                </router-link>
                                <router-link :to="{name: 'my-advertisement', params: {}}"
                                             class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700" href="#">
                                    {{ $t('My ads') }}
                                </router-link>
                                <button
                                    type="button"
                                    @click="userStore.logout()"
                                    class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700"
                                >
                                    {{ $t('Sign out') }}
                                </button>
                            </div>
                        </div>
                        <!-- End User Dropdown -->

                      <div>
                        <!-- Notifications Dropdown -->
                        <user-notifications />
                        <user-unread-messages />
                        <!-- End Notifications Dropdown -->
                      </div>
                    </template>
                </div>
            </div>
        </nav>
    </header>
  </div>

  <!-- Account activation modal -->
  <v-modal :is-open="showActiveAccountModal" @update:closeModal="showActiveAccountModal = false">
    <template v-slot:title>
      <i class="icon mgc_checks_line text-2xl"></i>
      {{ $t('Active your account') }}
    </template>

    <template v-slot:content>
      <account-activation @close="showActiveAccountModal = false" />
    </template>
  </v-modal>
</template>

<script lang="ts" setup>
import {ref} from "vue";
import {useUserStore} from "@/stores/UserStore";
import {useSettingsStore} from "@/stores/SettingsStore";
import userUnreadMessages from "@/views/components/userUnreadMessages.vue";
import AccountActivation from "@/views/components/accountActivation.vue";
import userNotifications from "@/views/components/userNotifications.vue";

const userStore               = useUserStore();
const settingsStore           = useSettingsStore()
const showActiveAccountModal  = ref<boolean>(false);
</script>
