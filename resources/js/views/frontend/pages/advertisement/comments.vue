<template>
    <section class="flex flex-col gap-3 bg-white rounded shadow">
      <!-- Title -->
      <h2 class="text-xl text-gray-800 font-bold sm:text-3xl dark:text-white flex items-center gap-2 py-2 px-5">
          <i class="icon mgc_comment_2_line"></i>
          {{ $t('Post a comment') }}
      </h2>

      <!-- Info Message -->
      <div class="bg-blue-50 text-blue-800 py-2 px-5 flex items-start gap-2" v-if="appSettings.appSettings?.warning_comments_message?.length">
        <i class="icon mgc_information_line text-3xl"></i>
        <p v-html="appSettings.appSettings?.warning_comments_message"></p>
      </div>

      <!-- Comments -->
      <div class="divide-y divide-gray-200 dark:divide-gray-700" v-if="!isLoadingList">
        <div
            class="flex gap-3 py-2 px-5"
            :class="advertisement.user.id === comment.user.id ? 'bg-gray-100 dark:bg-gray-800' : 'bg-white dark:bg-gray-900'"
            v-for="comment in comments"
            :key="comment.id"
        >
            <figure class="h-10 w-10 rounded-full relative overflow-hidden">
              <router-link
                  :to="{name: comment?.user?.is_business ? 'store.show' : 'profile_uid', params: {id: comment?.user?.id, slug: comment?.user?.slug}}"
              >
                <img class="object-cover absolute inset-0 h-full w-full" :src="comment.user?.avatar_url ?? `https://ui-avatars.com/api/?name=${comment.name}&color=333333&background=F4F4F4F4`" :alt="comment.user?.name?? comment.name" />
              </router-link>
            </figure>
            <div class="flex-1">
                <div class="font-medium text-gray-800 flex justify-between">
                    <router-link
                        v-if="comment.user?.id"
                        :to="{name: comment?.user?.is_business ? 'store.show' : 'profile_uid', params: {id: comment?.user?.id, slug: comment?.user?.slug}}"
                        class="flex items-center gap-2"
                    >
                      {{ comment.user?.id ? comment.user.name : comment.name }}
                      <span class="w-5 h-5 flex items-center justify-center bg-blue-600 text-white rounded-full">
                         <i class="icon mgc_check_fill text-sm" v-if="comment.user?.is_verified"></i>
                      </span>
                    </router-link>

                    <!-- More actions -->
                </div>
                <div class="text-gray-600 text-sm">
                    {{ comment.created_at }}
                </div>
                <div class="mt-1 text-base text-black">
                    {{ comment.content }}
                </div>
            </div>
        </div>
      </div>

      <v-skeleton class="p-5" :loop-number="10" v-else />

      <!-- Load More -->
      <v-button
        v-if="pagination.last_page > 1 && pagination.current_page < pagination.last_page"
        @click="fetchComments(pagination.current_page + 1)"
        :loading="isLoadingList"
        :disabled="isLoadingList"
        type="button"
        color="gray"
        class="w-fit place-self-end flex items-center gap-1 ltr:mr-2 rtl:ml-2"
    >
        <i class="icon mgc_down_fill text-xl"></i>

        {{ $t('Load more') }}
    </v-button>

      <!-- Form -->
      <form class="flex flex-col gap-5 py-2 px-5" @submit.prevent="submitComment">
        <div class="flex flex-col gap-2">
          <v-textarea input-style="two" :label="$t('Comment')" :id="$t('Comment')" :placeholder="$t('Your comment')"  @update:input="comment = $event" :value="comment" />
          <p v-if="errors.content" class="text-red-500 text-sm">{{ errors.content[0] }}</p>
        </div>

        <v-button
            :disabled="isLoading"
            :loading="isLoading"
            type="submit"
            color="blue"
            class="w-full"
        >
          {{ $t('Post') }}
        </v-button>
      </form>
    </section>
</template>

<script lang="ts" setup>
import {Advertisement} from "@/types/Advertisements";
import {onMounted, ref} from "vue";
import {useUserStore} from "@/stores/UserStore";
import {notify} from "notiwind";
import {wTrans} from "laravel-vue-i18n";
import {useSettingsStore} from "@/stores/SettingsStore";
import axios from "@/axios";

interface CommentInterFace {
    id: number;
    name: string;
    content: string;
    status: string;
    created_at: string;
    user: {
        id: number;
        name: string;
        avatar_url: string;
        is_verified: boolean;
        is_business: boolean;
        slug: string;
    }
}

const props             = defineProps<{
  advertisement: Advertisement;
}>();
const appSettings       = useSettingsStore();
const userStore         = useUserStore();
const comments          = ref<CommentInterFace[]>([]);
const pagination        = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});
const comment           = ref<string>('');
const name              = ref<string>(userStore.user?.name ?? '');
const isLoading         = ref<boolean>(false);
const isLoadingList     = ref<boolean>(false);
const errors            = ref({} as Record<string, string[]>);

const submitComment = async () => {
    if(!props.advertisement.id) return;

    isLoading.value = true;

    try {
        const { data } = await axios.post(`/advertisements/${props.advertisement.id}/comments`, {
            name: name.value,
            content: comment.value,
        });

        errors.value = {};

        if(data.ok) {
            comment.value = '';
            comments.value.unshift(data.comment);
        }

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? wTrans("Success") : wTrans("Error"),
            text: data.message
        }, 2000);

    } catch (e: any) {
        if(e.response.status === 422) {
            errors.value = e.response.data.errors;
        }
    } finally {
        isLoading.value = false;
    }
};
const fetchComments = async (page = 1) => {
    if(!props.advertisement.id) return;

    isLoadingList.value = true;

    try {
        const { data } = await axios.get(`/advertisements/${props.advertisement.id}/comments?page=${page}`);

        if(data.ok) {
            comments.value = [...comments.value, ...data.comments.data]
            pagination.value = {
                current_page: data.comments.current_page,
                last_page: data.comments.last_page,
                per_page: data.comments.per_page,
                total: data.comments.total,
            };
        }
    } catch (e) {
        console.error(e);
    } finally {
        isLoadingList.value = false;
    }
};

onMounted(async () => await fetchComments());
</script>
