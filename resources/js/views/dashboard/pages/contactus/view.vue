<template>
    <div class="flex flex-col md:flex-row items-start gap-5" v-if="!contactUsStore.isLoading">
        <div class="w-full md:w-[15rem] bg-white shadow-md rounded-md">
            <div class="flex flex-col gap-2 divide-y">
                <!-- Name -->
                <div class="flex flex-col gap-1 p-2">
                    <span class="text-sm text-gray-500">
                        {{ $t('Name') }}
                    </span>
                    <span class="text-sm text-gray-800 font-semibold">
                        {{ contactUsStore.message?.name }}
                    </span>
                </div>
                <!-- Email -->
                <div class="flex flex-col gap-1 p-2">
                    <span class="text-sm text-gray-500">
                        {{ $t('Email') }}
                    </span>
                    <span class="text-sm text-gray-800 font-semibold">
                        {{ contactUsStore.message?.email }}
                    </span>
                </div>
                <!-- Date -->
                <div class="flex flex-col gap-1 p-2">
                    <span class="text-sm text-gray-500">
                        {{ $t('Date') }}
                    </span>
                    <span class="text-sm text-gray-800 font-semibold">
                        {{ contactUsStore.message?.created_at }}
                    </span>
                </div>
                <!-- Last reply -->
                <div class="flex flex-col gap-1 p-2">
                    <span class="text-sm text-gray-500">
                        {{ $t('Last reply') }}
                    </span>
                    <span class="text-sm text-gray-800 font-semibold">
                        {{ contactUsStore.message?.updated_at }}
                    </span>
                </div>
                <!-- Is replied -->
                <div class="flex flex-col gap-1 p-2">
                    <span class="text-sm text-gray-500">
                        {{ $t('Is replied') }}
                    </span>
                    <span class="text-sm text-gray-800 font-semibold">
                        <v-badge :color="contactUsStore.message?.is_replied ? 'green' : 'red'" :text="contactUsStore.message?.is_replied ? $t('Yes') : $t('No')" />
                    </span>
                </div>
            </div>
        </div>
        <!-- Reply -->
        <div class="flex-1 w-full bg-white shadow-md rounded-md flex flex-col gap-2 divide-y">
            <div class="flex flex-col gap-2 p-4">
                <span class="text-sm text-gray-500">{{ $t('Subject') }} :</span>
                <p class="text-sm text-gray-800 font-semibold">{{ contactUsStore.message?.subject }}</p>
            </div>

            <div class="flex flex-col gap-2 p-4">
                <span class="text-sm text-gray-500">{{ $t('Message') }} :</span>
                <p class="text-sm text-gray-800 font-semibold">{{ contactUsStore.message?.message }}</p>
            </div>

            <div class="flex flex-col gap-2 p-4">
                <span class="text-sm text-gray-500">{{ $t('Reply') }} :</span>
                <v-editor @update:input="contactUsStore.message.reply = $event" />
                <span class="text-sm text-red-500" v-if="contactUsStore.replayErrors.reply">@{{ contactUsStore.replayErrors.reply[0] }}</span>
            </div>

            <div class="flex justify-end p-4">
                <v-button color="green" :loading="contactUsStore.isReplying" @click="contactUsStore.replyMessage()">
                    <i class="icon mgc_send_fill"></i>
                    {{ $t('Reply') }}
                </v-button>
            </div>
        </div>
    </div>
    <div v-else class="bg-white shadow-md rounded-md p-4 flex items-center justify-center">
        <v-loader :size="4" :dark="true" />
    </div>
</template>

<script lang="ts" setup>
import {useContactUsStore} from "@/stores/ContactUsStore";
import {onMounted} from "vue";
import { useRoute, useRouter } from 'vue-router'

/**
 * States
 */
const $route = useRoute()
const $router = useRouter()
const contactUsStore = useContactUsStore()

onMounted(async () => {
    let id = parseInt(<string>$route.params.id)

    if(!id) {
        await $router.push({name: 'contactus.index'})
    }

    await contactUsStore.fetchMessage(id)
})
</script>
