<template>
    <header class="flex flex-wrap sm:justify-start sm:flex-nowrap z-50 w-full bg-white text-sm py-3 sm:py-0 dark:bg-slate-900 shadow rounded-lg">
        <nav class="relative w-full mx-auto px-4 sm:flex sm:items-center sm:justify-between sm:px-6 lg:px-8" aria-label="Global">
            <div class="flex items-center justify-between">
                <a class="flex-none text-xl font-semibold text-easternblue dark:text-white" href="#" aria-label="Brand">
                    {{ settingsStore.appSettings.app_name ?? 'SOOQ' }}
                </a>
                <div class="sm:hidden">
                    <button type="button" class="hs-collapse-toggle w-9 h-9 flex justify-center items-center text-sm font-semibold rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" data-hs-collapse="#navbar-collapse-with-animation" aria-controls="navbar-collapse-with-animation" aria-label="Toggle navigation">
                        <svg class="hs-collapse-open:hidden flex-shrink-0 w-4 h-4" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                        <svg class="hs-collapse-open:block flex-shrink-0 hidden w-4 h-4" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="navbar-collapse-with-animation" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow sm:block">
                <div class="py-2 flex flex-col gap-y-4 gap-x-0 mt-5 sm:flex-row sm:items-center sm:justify-end sm:gap-y-0 sm:gap-x-7 sm:mt-0 sm:ps-7">
                    <!-- Language Dropdown -->
                    <div class="hs-dropdown relative inline-flex">
                        <button id="hs-dropdown-basic" type="button" class="hs-dropdown-toggle py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                            {{ $t('Language') }}
                            <svg class="hs-dropdown-open:rotate-180 w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 w-56 hidden z-10 mt-2 min-w-[15rem] bg-white shadow-md rounded-lg p-2 dark:bg-gray-800 dark:border dark:border-gray-700 dark:divide-gray-700" aria-labelledby="hs-dropdown-basic">
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

                    <!-- User Dropdown -->
                    <div class="hs-dropdown relative inline-flex">
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
                            <button
                                type="button"
                                @click="userStore.logout()"
                                class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700"
                            >
                                {{ $t('Sign out') }}
                            </button>
                        </div>
                    </div>

                    <!-- Send global notifications -->
                  <v-button
                      class="py-3"
                      color="blue"
                      type="button"
                      @click="modalOpen = true"
                  >
                    <i class="icon mgc_notification_fill"></i>
                    {{ $t('Send Notification') }}
                  </v-button>
                </div>
            </div>
        </nav>
    </header>

  <!-- Modal -->
  <v-modal :is-open="modalOpen" @update:closeModal="modalOpen = false">
    <template #title>
      <i class="icon mgc_notification_fill"></i>
      {{ $t('Send Notification') }}
    </template>

    <template #content>
      <!-- Title -->
      <div class="mb-3">
        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">
          {{ $t('Title') }}
        </label>
        <v-input
            id="title"
            type="text"
            :placeholder="$t('Enter notification title')"
            @update:input="title = $event"
            :value="title"
        />
        <p class="mt-2 text-sm text-red-600 dark:text-red-400" v-if="errors.title">{{ errors.title[0] }}</p>
      </div>

      <!--Message-->
      <div class="mb-3">
        <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">
          {{ $t('Message') }}
        </label>
        <v-textarea
            id="message"
            :placeholder="$t('Enter your message')"
            @update:input="body = $event"
            :value="body"
        />
        <p class="mt-2 text-sm text-red-600 dark:text-red-400" v-if="errors.body">{{ errors.body[0] }}</p>
      </div>

      <!-- Role -->
      <div>
        <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">
          {{ $t('Send to') }}
        </label>
        <v-select
            :value="role"
            id="role"
            @update:value="role = $event"
        >
          <option :value="status.value" v-for="status in [{ label: $t('All'), value: 'all' }, { label: $t('Admin'), value: 'admin' }, { label: $t('Business'), value: 'business' }, { label: $t('Customer'), value: 'customer' }]" :key="status.value">
            {{ status.label }}
          </option>
        </v-select>
        <p class="mt-2 text-sm text-red-600 dark:text-red-400" v-if="errors.role">{{ errors.role[0] }}</p>
      </div>
    </template>
    <template #footer>
      <v-button color="blue" @click="sendNotification" :loading="isLoading">
        {{ $t('Send') }}
      </v-button>
    </template>
  </v-modal>
</template>

<script lang="ts" setup>
import {useUserStore} from "@/stores/UserStore";
import {useSettingsStore} from "@/stores/SettingsStore";
import {ref} from "vue";
import axios from "axios";
import {notify} from "notiwind";
import {wTrans} from "laravel-vue-i18n";

const userStore       = useUserStore();
const settingsStore   = useSettingsStore()
const modalOpen       = ref<boolean>(false);
const title           = ref<string>('');
const body            = ref<string>('');
const role            = ref<string>('all');
const errors          = ref<any>({});
const isLoading       = ref<boolean>(false);

const sendNotification = async () => {
  isLoading.value = true;
  errors.value = {};

  try {
    const { data } = await axios.post('/dashboard/notifications/send', {
      title: title.value,
      body: body.value,
      role: role.value
    });

    if(data.ok) {
      modalOpen.value = false;
      title.value = '';
      body.value = '';
      role.value = 'all';
    }

    notify({
      group: "dashboard",
      ok: data.ok,
      title: data.ok ? wTrans("Success") : wTrans("Error"),
      text: data.message
    }, 2000);
  } catch (e:any) {
    if(e.response.status === 422) {
      errors.value = e.response.data.errors;
    }
  } finally {
    isLoading.value = false;
  }
}
</script>
