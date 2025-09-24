<template>
    <!-- Background opacity -->
    <div class="fixed inset-0 bg-black opacity-50 z-50" v-show="isOptionOpen" @click="isOptionOpen = !isOptionOpen"></div>

    <!-- Content -->
    <div class="inline-block text-left">
        <button type="button"
                class="p-0"
                id="menu-button"
                aria-expanded="true"
                aria-haspopup="true"
                @click="isOptionOpen = !isOptionOpen"
        >
            <span class="w-8 h-8 flex items-center justify-center relative">
                <i class="icon mgc_bell_ringing_line text-2xl"></i>
                <span
                      class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs font-semibold rounded-full flex items-center justify-center">
                    {{ userStore.unread_count }}
                </span>
            </span>
        </button>

        <div
            class="absolute rtl:left-1 ltr:right-1 z-[51] mt-2 w-[25rem] origin-top-right bg-white overflow-hidden rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            :class="{'hidden': !isOptionOpen, '': isOptionOpen}"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="menu-button"
            tabindex="-1"
        >
            <template v-if="!userStore.isLoadingNotifications">
                <template v-if="userStore.notifications.length">
                    <div class="notification-body">
                      <article class="notification" v-for="notification in userStore.notifications" :key="notification.id" :class="{'bg-gray-100': !notification.is_read}">
                          <router-link :to="generateLink(notification)" class="notification-link">
                              <span class="min-w-[50px] min-h-[50px] max-w-[50px] max-h-[50px] bg-blue-50 rounded-full flex items-center justify-center">
                                  <i class="text-2xl" :class="wishIcon(notification.action)"></i>
                              </span>
                              <div class="notification-content">
                                      <span class="notification-text" v-html="notification.message"></span>
                                  <time class="font-bold whitespace-nowrap text-sm text-gray-500" :datetime="notification.date">
                                      {{ notification.date_ago }}
                                  </time>
                              </div>
                          </router-link>
                      </article>

                    <!-- Load more -->
                    <div class="flex justify-center" v-if="userStore.pagination.last_page > 1 && userStore.pagination.current_page < userStore.pagination.last_page">
                        <v-button type="button" :loading="userStore.isLoadingNotifications" color="blue" class="w-full" :disabled="userStore.isLoadingNotifications" @click="userStore.fetchNotifications(true)">
                            {{ $t('Load more') }}
                        </v-button>
                    </div>
                </div>
                </template>
                <template v-else>
                    <div class="w-full max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto flex flex-col gap-5">
                        <img :src="`${$assetUrl}/empty-trash.svg`" alt="No data" class="w-full h-[20rem] mx-auto">
                        <h2 class="text-2xl font-bold text-center text-gray-600">
                            {{ $t('No notifications') }}
                        </h2>
                    </div>
                </template>
            </template>
            <div class="flex items-center justify-center p-4" v-else>
                <v-loader :size="4" :dark="true" />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref as dbRef, query, orderByChild, set, child, onChildAdded, get } from 'firebase/database';
import {useUserStore} from "@/stores/UserStore";
import {onMounted, ref, watch} from "vue";
import { database } from "@/firebase";

const receivedNotificationIds = ref<Set<string>>(new Set());
const isOptionOpen            = ref(false);
const userStore               = useUserStore();

watch(isOptionOpen, async (value) => {
    if (value) {
       await userStore.markAsRead();
    }
});

const wishIcon = (action: string) => {
    switch (action) {
        case 'new_comment':
            return 'icon mgc_comment_2_line text-blue-500';
        case 'ad_approved':
            return 'icon mgc_checks_line text-green-500';
        case 'ad_rejected':
            return 'icon mgc_safe_alert_line text-red-500';
        case 'ad_pending':
            return 'icon mgc_alarm_1_line text-yellow-500';
        case 'add_your_ad_to_favorites':
            return 'icon mgc_love_line text-red-500';
        case 'new_follower':
            return 'icon mgc_user_follow_2_line text-green-700';
        case 'followed_user_ad':
            return 'icon mgc_announcement_line text-blue-700';
        case 'new_store_follower':
            return 'icon mgc_user_heart_line text-pink-500';
        case 'followed_business_ad':
            return 'icon mgc_store_2_line text-gray-950';
        case 'admin_notification':
            return 'icon mgc_bling_line text-yellow-500';
        default:
            return 'icon mgc_list_check_line text-gray-500';
    }
}

const generateLink = (notification: any) => {
    switch (notification.action) {
        case 'new_comment':
            return `/advertisement/${notification.ad.id}#comments`;
        case 'ad_approved':
          return `/advertisement/${notification.ad.id}`;
        case 'ad_rejected':
        case 'ad_pending':
            return `/advertisement/${notification.ad.id}`;
        case 'add_your_ad_to_favorites':
            return `/advertisement/${notification.ad.id}`;
        case 'new_follower':
            return `/profile/${notification.sender.id}`;
        case 'followed_user_ad':
            return `/advertisement/${notification.ad.id}`;
        case 'followed_business_ad':
          return `/advertisement/${notification.ad.id}`;
        case 'new_store_follower':
            return `/profile/${notification.sender.id}`;
        default:
            return '#';
    }
}

onMounted(async () => {
  await userStore.fetchNotifications();

  const notificationsRef                    = dbRef(database, 'notifications');
  const userProcessedNotificationsRef       = dbRef(database, `users/${userStore.me?.id}/processedNotifications`);
  const userProcessedNotificationsSnapshot  = await get(userProcessedNotificationsRef);

  if (userProcessedNotificationsSnapshot.exists()) {
    receivedNotificationIds.value = new Set(Object.keys(userProcessedNotificationsSnapshot.val()));
  }

  const initialNotificationsQuery    = query(notificationsRef, orderByChild('date_time'));
  const initialNotificationsSnapshot = await get(initialNotificationsQuery);


  initialNotificationsSnapshot.forEach((snapshot: any) => {
    const notification = snapshot.val();
    const id           = snapshot.key;

    if (!receivedNotificationIds.value.has(id)) {
      receivedNotificationIds.value.add(id);
      set(child(userProcessedNotificationsRef, id), true);

      if(!userStore.notifications.find((n) => n.sender_id === notification.sender_id) && notification.sender_id !== userStore.me.id) {
        userStore.fetchNotifications();
      }
    }
  });

  onChildAdded(notificationsRef, (snapshot) => {
    const newNotification  = snapshot.val();
    const notificationId   = snapshot.key;

    if (notificationId && !receivedNotificationIds.value.has(notificationId)) {
      receivedNotificationIds.value.add(notificationId);

      if(!userStore.notifications.find((n) => n.sender_id === notification.sender_id) && notification.sender_id !== userStore.me.id) {
        userStore.fetchNotifications();
      }
    }
  });
});
</script>


<style scoped>
.notification-body {
    @apply max-h-[27rem] overflow-y-auto flex flex-col divide-y divide-gray-200;
}

.notification {
    @apply flex items-center px-4 py-3 hover:bg-gray-100;
}

.notification-link {
    @apply flex gap-3 items-center w-full;
}

.notification-content {
    @apply flex-1 w-full flex justify-between gap-5 items-center;
}

.notification-text {
    @apply text-gray-600 text-[14px] ltr:text-left rtl:text-right;
}
</style>
