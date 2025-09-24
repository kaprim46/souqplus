<template>
  <div class="flex flex-col lg:flex-row gap-5">
    <!-- Info -->
    <div class="w-full lg:w-1/4 flex flex-col gap-5">
      <!-- Avatar & name -->
      <figure v-if="!userStore.isLoadingUser" class="bg-white p-5 rounded-xl shadow flex flex-col items-center">
        <img class="w-20 h-20 rounded-full object-cover" :src="userStore.user.avatar_url" :alt="userStore.user.name" />
        <figcaption class="mt-2 text-lg font-semibold flex items-center gp-2">
          {{ userStore.user.name }}
          <span v-if="!userStore.user.is_verified" class="text-blue-400">
                <svg class="h-5 fill-blue-400" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M23,12L20.56,9.22L20.9,5.54L17.29,4.72L15.4,1.54L12,3L8.6,1.54L6.71,4.72L3.1,5.53L3.44,9.21L1,12L3.44,14.78L3.1,18.47L6.71,19.29L8.6,22.47L12,21L15.4,22.46L17.29,19.28L20.9,18.46L20.56,14.78L23,12M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z" />
                </svg>
            </span>
        </figcaption>

        <!-- Follow button -->
        <div class="flex items-center gap-2 mt-5" v-if="userStore.me?.id !== userStore.user.id">
          <v-followButton
              @on:response="onFollowResponse($event)"
              @on:loading="followLoading = $event"
              :is-following="userStore.user.is_following"
              :user-id="userStore.user.id"
              :followLoading="followLoading"
          >
            <template #button>
              <button
                    type="button"
                    class="bg-transparent border font-semibold px-5 py-2 rounded-full transition-colors flex items-center justify-center gap-2"
                    :class="userStore.user.is_following ? 'border-red-400 text-red-400  hover:bg-red-400 hover:text-white' : 'border-blue-400 text-blue-400 hover:bg-blue-400 hover:text-white'"
                    :disabled="followLoading"
              >
                  <i :class="userStore.user.is_following ? 'icon mgc_announcement_line text-lg' : 'icon mgc_check_line text-lg'"></i>
                  {{ userStore.user.is_following ? $t('Unfollow') : $t('Follow') }}
              </button>
            </template>
          </v-followButton>

          <router-link
              :to="{ name: 'chat', query: { user_id: userStore.user.id } }"
              class="bg-transparent border font-semibold px-5 py-2 rounded-full transition-colors flex items-center justify-center gap-4"
          >
            <i class="icon mgc_chat_2_line text-lg"></i>
            {{ $t('Send Message') }}
          </router-link>
        </div>
        <!-- End follow button -->

        <!-- Follower & Following -->
        <div class="flex gap-5 mt-5">
          <div class="flex flex-col items-center cursor-pointer group" @click="onClickWish('Follower')">
            <span class="font-semibold text-gray-700 group-hover:text-blue-500">{{ userStore.user.followers_count }}</span>
            <span class="text-gray-500">{{ $t('Followers') }}</span>
          </div>
          <div class="flex flex-col items-center cursor-pointer group" @click="onClickWish('Following')">
            <span class="font-semibold text-gray-700 group-hover:text-blue-500">{{ userStore.user.following_count }}</span>
            <span class="text-gray-500">{{ $t('Following') }}</span>
          </div>
        </div>
        <!-- End Follower & Following -->
      </figure>
      <!-- End Avatar & name -->

      <!-- Skeleton-->
      <v-skeleton :loop-number="9" v-else></v-skeleton>
      <!-- End Skeleton-->

      <!-- Badges -->
      <div v-if="!userStore.isLoadingUser" class="bg-white rounded-xl shadow flex flex-col divide-y divide-solid divide-gray-100">
        <div class="font-semibold text-[18px] text-gray-700 px-2 py-3">
          <i class="icon mgc_badge_fill text-xl"></i>
          {{ $t('Badges') }}
        </div>
        <div class="px-2 py-3 flex gap-5">
          <template v-if="userStore.user.badges.length === 0">
            <p class="text-gray-500 text-center w-full">{{ $t('No badges yet') }}</p>
          </template>
          <div class="tooltip inline-block" v-for="badge in userStore.user.badges" :key="badge.id">
            <button type="button" class="tooltip-toggle">
              <img class="w-24 h-24 rounded-full object-cover" :src="badge.icon_url" :alt="badge.name" />
              <span class="tooltip-content invisible" role="tooltip">
                    {{ badge.name }}
                  </span>
            </button>
          </div>
        </div>
      </div>
      <!-- End badges -->

      <!-- Skeleton-->
      <v-skeleton :loop-number="9" v-else></v-skeleton>
      <!-- End Skeleton-->
    </div>
    <!-- End Info -->

    <!-- Bio & Advertisements -->
    <div class="flex-1 flex flex-col gap-5">
      <!-- Bio -->
      <div v-if="!userStore.isLoadingUser" class="bg-white divide-y divide-solid divide-gray-100 rounded-xl shadow">
        <h2 class="text-lg font-semibold text-gray-700 px-2 py-3">
          <i class="icon mgc_pen_2_line text-xl"></i>
          {{ $t('Bio') }}
        </h2>
        <p class="mt-2 text-gray-500 px-2 py-3">
          {{ userStore.user.bio ?? "--" }}
        </p>
      </div>
      <!-- End bio -->

      <!-- Skeleton-->
      <v-skeleton :loop-number="9" v-if="userStore.isLoadingUser"></v-skeleton>
      <!-- End Skeleton-->

      <!-- Advertisements -->
      <div v-if="!advertisementStore.isLoading && advertisementStore.advertisements.length" class="bg-white divide-y divide-solid divide-gray-100 rounded-xl shadow">
        <h2 class="text-lg font-semibold text-gray-700 px-2 py-3">
          <i class="icon mgc_ad_2_line text-xl"></i>
          {{ $t('Advertisements') }}
        </h2>
        <div class="flex flex-col divide-y divide-gray-200 ads-list">
          <Advertisement v-for="advertisement in advertisementStore.advertisements" :key="advertisement.id" :advertisement="advertisement"/>
        </div>
        <!-- Footer -->
        <div
            v-if="!advertisementStore.isLoading"
            class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ advertisementStore.pagination.total }}
                                </span>
              {{ $t('Results') }}
            </p>
          </div>

          <div>
            <div class="inline-flex gap-x-2">
              <button
                  @click="advertisementStore.fetch(true, true)"
                  :disabled="advertisementStore.pagination.current_page === 1"
                  type="button"
                  class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                {{ $t('Previous') }}
              </button>

              <button
                  @click="advertisementStore.fetch(true)"
                  :disabled="advertisementStore.pagination.current_page === advertisementStore.pagination.last_page"
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
    <!-- End Bio & Advertisements -->
  </div>

  <!-- Modal -->
  <v-modal :is-open="isModalVisible" @update:closeModal="isModalVisible = false">
    <template #title>
      {{ wishAction === "Follower" ? $t('Followers') : $t('Following') }}
    </template>

    <template #content>
      <template v-if="userStore.isLoadingFollowings || userStore.isLoadingFollowers">
        <v-skeleton :loop-number="9"></v-skeleton>
      </template>
      <template v-else>
        <div class="flex flex-col gap-5">
          <div v-for="following in wishAction === 'Follower' ? userStore.followers : userStore.followings" :key="following.id" class="flex items-center gap-5">
            <img class="w-10 h-10 rounded-full object-cover" :src="following.avatar_url" :alt="following.name" />
            <div class="flex flex-col">
              <span class="font-semibold text-gray-700">{{ following.name }}</span>
              <span class="text-gray-500">{{ following.email }}</span>
            </div>
          </div>
        </div>
      </template>
    </template>
  </v-modal>
</template>

<script lang="ts" setup>
import {nextTick, onBeforeMount, ref} from "vue";
import { useRoute } from "vue-router";
import { useCustomerAdvertisementStore } from "@/stores/Customer/CustomerAdvertisementStore";
import {useUserStore } from "@/stores/UserStore";
import Advertisement from "@/views/components/advertisement.vue";

const advertisementStore    = useCustomerAdvertisementStore();
const routes                = useRoute();
const followLoading         = ref(false);
const userStore             = useUserStore();
const wishAction            = ref<string>(""); // Follower, Following
const isModalVisible        = ref(false);


const onClickWish = async (action: string) => {
  wishAction.value      = action;

  await nextTick(() => {
    isModalVisible.value  = true;

    if (action === "Follower") {
      userStore.fetchFollowers();
    } else {
      userStore.fetchFollowings();
    }
  })
};

const onFollowResponse = (isFollowing: any) => {
  userStore.user.is_following = isFollowing;

  isFollowing ? userStore.user.followers_count++ : userStore.user.followers_count--;
};

onBeforeMount(async () => await userStore.fetchUser(parseInt(routes.params.id as string)).then(async(ok) => {
  if(ok) {
    const resetAll = await advertisementStore.resetAll();

    if(resetAll) {
      advertisementStore.filters = {
        user_id: userStore.user.id,
        pagination: 1
      };

      await advertisementStore.fetch();
    }
  }
}));
</script>

<style lang="scss" scoped>
.tooltip {
  position: relative;
  display: inline-block;

  .tooltip-toggle {
    position: relative;
    display: inline-block;
    cursor: pointer;
    transition: transform 0.3s;

    &:hover {
      transform: scale(1.1);
    }

    img {
      border: 2px solid #fff;
    }

    .tooltip-content {
      position: absolute;
      top: 100%;
      left: 50%;
      transform: translateX(-50%);
      background-color: #333;
      color: #fff;
      padding: 5px 10px;
      border-radius: 5px;
      visibility: hidden;
      opacity: 0;
      transition: opacity 0.3s;
      z-index: 1;

      &::after {
        content: "";
        position: absolute;
        top: -5px;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: transparent #333  transparent transparent;
      }
    }

    &:hover .tooltip-content {
      visibility: visible;
      opacity: 1;
    }
  }
}
</style>
