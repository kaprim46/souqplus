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
                <i class="icon mgc_message_3_line text-2xl"></i>
                <span
                      class="absolute top-0 right-0 w-4 h-4 bg-red-500 text-white text-xs font-semibold rounded-full flex items-center justify-center">
                    {{ chatStore.totalUnreadMessages }}
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
                <div v-if="chatStore.conversations.data.length" class="max-h-[27rem] overflow-y-auto flex flex-col">
                  <ul class="flex flex-col divide-y divide-gray-200 w-full flex-1">
                    <li
                        v-for="(item, i) in chatStore.conversations_by_date"
                        :key="i"
                    >
                      <router-link
                          class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 cursor-pointer"
                          :class="item.total_unread > 0 && 'bg-blue-50'"
                          :to="{ name: 'chat', query: { user_id: item.contact.id } }"
                          @click="isOptionOpen = false"
                      >
                        <figure class="w-[50px] h-[50px] rounded-full overflow-hidden">
                          <img :src="item.contact.avatar_url" :alt="item.contact.name" class="w-full h-full object-cover" />
                        </figure>
                        <div class="flex flex-col flex-1">
                          <h1 class="text-lg font-semibold rtl:text-right ltr:text-left">
                            {{ item.contact.name }}
                          </h1>
                          <div class="flex justify-between items-center gap-1 w-full">
                            <span class="text-sm text-gray-500">
                              {{ item.last_message ?? '--' }}
                            </span>
                            <time class="text-xs text-gray-500">
                              {{ item.last_message_created_at }}
                            </time>
                          </div>
                        </div>
                      </router-link>
                    </li>
                  </ul>
                </div>
                <template v-else>
                    <div class="w-full max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto flex flex-col gap-5">
                        <img :src="`${$assetUrl}/empty-trash.svg`" alt="No data" class="w-full h-[20rem] mx-auto">
                        <h2 class="text-2xl font-bold text-center text-gray-600">
                            {{ $t('No messages') }}
                        </h2>
                    </div>
                </template>
            </template>
            <div class="flex items-center justify-center p-4" v-else>
                <v-loader :size="4" :dark="true" />
            </div>
            <!-- Show All -->
            <div class="w-full">
              <button
                  class="w-full bg-[#0C74BB] text-white text-sm font-semibold py-2"
                  @click="$router.push({ name: 'chat' }); isOptionOpen = false;"
              >
                <i class="icon mgc_message_3_line text-base"></i>
                {{ $t('Go to chat') }}
              </button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {useUserStore} from "@/stores/UserStore";
import {onMounted, ref, watch} from "vue";
import {useCustomerChatStore} from "@/stores/Customer/CustomerChatStore";
import { database } from "@/firebase";
import { ref as dbRef, query, orderByChild, set, child, onChildAdded, get } from 'firebase/database';
import {useRoute} from "vue-router";

const chatStore                     = useCustomerChatStore();
const userStore                     = useUserStore();
const isOptionOpen                  = ref(false);
const receivedMessageIds            = ref<Set<string>>(new Set());
const $route                        = useRoute();
const { type }                      = $route.query;

watch(isOptionOpen, async (value) => {
    if (value && chatStore.conversations.data.length) {
      await chatStore.markAsRead();
    }
});


onMounted(async () => {
    await chatStore.fetchConversations({ type : type ?? null });

    const messagesRef                   = dbRef(database, 'messages');
    const userProcessedMessagesRef      = dbRef(database, `users/${userStore.me?.id}/processedMessages`);
    const userProcessedMessagesSnapshot = await get(userProcessedMessagesRef);

    if (userProcessedMessagesSnapshot.exists()) {
      receivedMessageIds.value = new Set(Object.keys(userProcessedMessagesSnapshot.val()));
    }

    const initialMessagesQuery    = query(messagesRef, orderByChild('date_time'));
    const initialMessagesSnapshot = await get(initialMessagesQuery);

    initialMessagesSnapshot.forEach((snapshot: any) => {
      const message = snapshot.val();
      const id      = snapshot.key;

      if (!receivedMessageIds.value.has(id)) {
        receivedMessageIds.value.add(id);
        set(child(userProcessedMessagesRef, id), true);
        chatStore.addMessage(message);
      }
    });


    onChildAdded(messagesRef, (snapshot) => {
      const newMessage  = snapshot.val();
      const messageId   = snapshot.key;

      if (messageId && !receivedMessageIds.value.has(messageId)) {
        receivedMessageIds.value.add(messageId);
        chatStore.addMessage(newMessage);
      }
    });
  });
</script>