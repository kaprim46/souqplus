<template>
  <section class="messenger_section">
    <div class="messenger">
      <!-- LEFT SIDE -->
      <div class="messenger__left">

        <!--LIST CONVERSATIONS-->
        <ul class="flex flex-col divide-y divide-gray-200 w-full flex-1">
          <li
              v-for="(item, i) in chatStore.conversations_by_date"
              :key="i"
              @click="chatStore.openConversation({ owner: item.contact.id })"
              class="cursor-pointer"
              :class="item.total_unread > 0 && 'bg-blue-50'"
          >
            <div class="flex items-center gap-2 p-2 rounded cursor-pointer hover:bg-gray-100 overflow-hidden">
              <figure class="min-w-[40px] min-h-[40px] max-w-[40px] max-h-[40px] rounded-full overflow-hidden relative">
                <img :src="item.contact.avatar_url" :alt="item.contact.name" class="absolute inset-0 w-full h-full object-cover" />
              </figure>
              <div class="flex flex-col flex-1">
                <h1 class="text-lg font-semibold">
                  {{ item.contact.name }}
                </h1>
                <div class="flex justify-between items-center gap-1 w-full relative">
                  <span class="text-sm text-gray-500 whitespace-pre-line break-all">
                    {{ item.last_message ?? '--' }}
                  </span>
                  <time class="text-xs text-gray-500">
                    {{ item.last_message_created_at }}
                  </time>

                  <span v-if="item.has_messages && item.total_unread > 0" class="w-[15px] h-[15px] bg-red-500 text-white text-xs font-semibold rounded-full flex items-center justify-center absolute top-[-1rem] rtl:left-0 ltr:right-0">
                    {{ item.total_unread }}
                  </span>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <!-- RIGHT SIDE -->
      <div class="messenger__right">
          <template v-if="chatStore.isChatOpen">
            <div class="messenger__right__content">
              <template  v-if="!chatStore.isLoadingMessages && chatStore.currentConversation">
                <!-- HEADER INFO -->
                <div class="flex items-center justify-between gap-5 p-2 border-b">
                  <router-link class="flex items-center gap-2" :to="{name: chatStore.currentConversation.owner.is_business ? 'store.show' : 'profile_uid', params: {id: chatStore.currentConversation.owner.id, slug: chatStore.currentConversation.owner.slug}}">
                    <img :src="chatStore.currentConversation.owner?.avatar_url" class="w-[50px] h-[50px] rounded-full object-cover" :alt="chatStore.currentConversation.owner?.name" />
                    <h3 class="text-lg font-semibold text-gray-900">
                      {{  chatStore.currentConversation.owner?.name }}
                    </h3>
                  </router-link>

                  <!-- Advertisement info -->
                  <div class="flex gap-2 items-center relative" v-if="chatStore.currentConversation.advertisement?.id">
                    <router-link :to="{name: 'show-advertisement', params: {id: chatStore.currentConversation.advertisement?.id, slug: chatStore.currentConversation.advertisement?.slug}}">
                      <figure class="w-[40px] h-[40px] overflow-hidden rounded-full">
                        <img :src="chatStore.currentConversation.advertisement?.main_media" :alt="chatStore.currentConversation.advertisement?.name" class="w-full h-full object-cover" />
                      </figure>
                    </router-link>


                    <router-link :to="{name: 'show-advertisement', params: {id: chatStore.currentConversation.advertisement?.id, slug: chatStore.currentConversation.advertisement?.slug}}">
                      <h1 class="text-base font-bold text-gray-800">
                        {{ chatStore.currentConversation.advertisement?.name }}
                      </h1>
                    </router-link>

                    <!-- Dots absolute -->
                    <span class="w-[5px] h-[5px] bg-blue-600 rounded-full absolute right-[-2.5px] top-[-2.5px] animate-ping"></span>
                  </div>
                  <!-- End Advertisement info -->
                </div>
                <!-- END HEADER INFO -->

                <!-- CHAT CONTENT -->
                <div class="messenger__right__content__body">
                 <ul class="messenger__right__content__body__messages">
                   <li
                       class="relative"
                       :class="item.is_sender && 'place-self-end'"
                       v-for="(item, index) in chatStore.messages.data"
                   >
                     <div
                         class="rounded w-[19rem] p-3 flex flex-col gap-1 relative z-10"
                         :class="item.is_sender ? 'bg-blue-50 text-black place-self-end' : 'bg-blue-600 text-white'"
                     >
                       <span class="text-[14.3px] break-words">
                         {{ item.body }}
                       </span>

                      <time class="text-xs place-self-end" :class="item.is_sender ? 'text-gray-500' : 'text-gray-300'">
                        {{ item.created_at }}
                      </time>
                     </div>


                     <span class="w-[15px] h-[15px] rotate-[45deg] absolute rtl:right-[-4px] rtl:bottom-[15px] ltr:left-[-4px] ltr:bottom-[15px]" :class="item.is_sender ? 'bg-blue-50' : 'bg-blue-600'"></span>
                   </li>
                 </ul>
                  <infinite-loading
                      @infinite="onInfinite"
                      class="flex items-center justify-center h-[3rem] mx-auto"
                  >
                    <template #error="{ retry }">
                      <button @click="retry">
                        {{ $t('Retry') }}
                      </button>
                    </template>

                    <template v-if="chatStore.conversations.current_page === chatStore.conversations.last_page" #complete>
                          <span class="text-[#333] dark:text-white font-bold text-[12.2px] flex items-center gap-2">
                              <i class="icon mgc_check_line text-2xl"></i>
                              {{ $t('No more messages') }}
                          </span>
                    </template>

                    <template #spinner>
                      <v-loader :size="2" color="blue" />
                    </template>
                  </infinite-loading>
                </div>
                <!-- END CHAT CONTENT -->

                <!-- CHAT INPUT -->
                <form class="bg-gray-500 relative overflow-hidden h-[5rem]" @submit.prevent="chatStore.sendMessage">
                  <textarea
                            :placeholder="$t('Entre message ...')"
                            class="absolute inset-0 w-full h-full border-0 ring-0 resize-none text-[14.3px] p-1 rtl:pl-[6rem] ltr:pr-[6rem] "
                            v-model="chatStore.message"
                            @keydown.enter="chatStore.sendMessage"
                  ></textarea>
                  <!-- Send button -->
                  <button type="submit" class="absolute top-0 h-full w-[5rem] bg-blue-600 text-white flex items-center justify-center rtl:left-0 ltr:right-0">
                    <i class="icon mgc_send_line text-2xl"></i>
                  </button>
                </form>
                <!-- END CHAT INPUT -->
              </template>

              <div class="flex items-center justify-center flex-1" v-else>
                <v-loader color="blue" :size="8"></v-loader>
              </div>
            </div>


          </template>
          <template v-else>
            <div class="flex items-center justify-center h-full" v-if="!chatStore.isChatOpen">
              <h1 class="text-2xl font-semibold text-gray-500 flex items-center gap-1">
                <i class="icon mgc_chat_1_line text-4xl"></i>
                {{ $t('Select a conversation') }}
              </h1>
            </div>
          </template>
      </div>
    </div>
  </section>
</template>

<style lang="scss">
.messenger_section {
  @apply flex flex-col;

  .messenger {
    @apply min-h-[40rem] lg:max-h-[40rem] w-full flex flex-col lg:flex-row overflow-hidden bg-gray-50 rounded-lg shadow-blue-100 shadow-lg relative;

    &__left {
      @apply flex flex-col transition-all duration-200 ease-in-out min-w-[18.33rem] max-w-full lg:max-w-[18.33rem] rtl:lg:border-l ltr:lg:border-r border-b lg:border-b-0 border-gray-200;

      &__title {
        @apply text-3xl font-bold px-5;
      }
    }

    &__right {
      @apply flex-1;

      &__content {
        @apply h-full flex flex-col;

        header {
          @apply flex flex-row space-x-2 items-center justify-between px-5 py-5;
        }

        &__body {
          @apply flex-1 overflow-y-scroll flex flex-col-reverse gap-y-5 py-5 px-5 relative overflow-hidden;

          &__messages {
            @apply flex flex-col-reverse gap-y-5;
          }

          /**
          Scrollbar
           */
          &::-webkit-scrollbar {
            @apply w-1;
          }

          &::-webkit-scrollbar-track {
            @apply bg-transparent;
          }

          &::-webkit-scrollbar-thumb {
            @apply bg-gray-300;
            border-radius: 20px;
          }

          &::-webkit-scrollbar-thumb:hover {
            @apply bg-gray-300;
          }
        }
      }
    }
  }
}
</style>

<script lang="ts" setup>
import { onBeforeMount, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { useUserStore } from "@/stores/UserStore";
import { useCustomerChatStore } from "@/stores/Customer/CustomerChatStore";

import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";


const $route                        = useRoute();
const chatStore                     = useCustomerChatStore();
const { user_id, advertisement_id, type } = $route.query;

const onInfinite = async ($state: any) => {
  if(chatStore.messages.current_page < chatStore.messages.last_page) {
    $state.loading();

    await chatStore.fetchMessages({
      page: chatStore.messages.current_page + 1,
      conversationId: chatStore.currentConversation?.id
    }).then((ok) => {
      if(ok) {
        setTimeout(async () => $state.loaded(), 100);
      }
    }).catch(() => {
      $state.error();
    });

    return;
  }

  $state.complete();
};


onBeforeMount(async () => {
  if(user_id) {
    await chatStore.openConversation({ owner: user_id, advertisement_id: advertisement_id ?? null, type: type ?? null });
  }
});

onMounted(async () => {
  await chatStore.markAsRead();
});
</script>


