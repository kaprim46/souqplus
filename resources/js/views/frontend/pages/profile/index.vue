<template>
    <div class="block bg-white rounded shadow">
        <!-- Page header -->
        <div class="space-y-0.5 p-5">
            <h2 class="text-2xl font-bold tracking-tight">
                <i class="icon mgc_profile_fill"></i>
                {{ $t('Account') }}
            </h2>
            <p class="text-muted-foreground">
                {{ $t('Update your account settings.') }}
            </p>
        </div>
        <!-- End Page header -->
        <hr class="border-gray-200" />
        <!-- Content -->
        <div class="flex flex-col md:flex-row">
            <nav class="w-full md:w-[20rem] ltr:border-r rtl:border-l">
                <ul class="flex flex-col divide-y">
                    <li class="px-4 py-2" :class="{ 'bg-blue-50': wishTab === 'store' }" v-if="userStore.me.role === 'business'">
                        <a
                            href="#store"
                            class="text-[15.4px] flex items-center gap-1"
                            :class="{ 'text-blue-800': wishTab === 'store' }"
                            @click.prevent="wishTab = 'store'"
                        >
                            <i class="icon mgc_store_2_fill text-xl"></i>
                            {{ $t('Store') }}
                        </a>
                    </li>
                    <li class="px-4 py-2" :class="{ 'bg-blue-50': wishTab === 'profile' }">
                        <a
                            href="#profile"
                            class="text-[15.4px] flex items-center gap-1"
                            :class="{ 'text-blue-800': wishTab === 'profile' }"
                            @click.prevent="wishTab = 'profile'"
                        >
                            <i class="icon mgc_user_1_fill text-xl"></i>
                            {{ $t('Profile') }}
                        </a>
                    </li>
                    <li class="px-4 py-2" :class="{ 'bg-blue-50': wishTab === 'password' }">
                        <a
                            href="#password"
                            class="text-[15.4px] flex items-center gap-1"
                            :class="{ 'text-blue-800': wishTab === 'password' }"
                            @click.prevent="wishTab = 'password'"
                        >
                            <i class="icon mgc_user_security_line text-xl"></i>
                            {{ $t('Password') }}
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- View tab -->
            <div id="tab" class="flex-1 p-5">
                <v-profile v-if="wishTab === 'profile'" />
                <v-password v-if="wishTab === 'password'" />
                <v-store v-if="wishTab === 'store'" />
            </div>
            <!-- End View tab -->
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import VPassword from './compnents/password.vue'
import VProfile from './compnents/profile.vue'
import VStore from './compnents/store.vue'
import {useUserStore} from "@/stores/UserStore";

const wishTab = ref('profile')
const userStore = useUserStore()
</script>
