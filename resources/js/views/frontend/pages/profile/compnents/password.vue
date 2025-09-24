<template>
    <form class="grid grid-cols-1 gap-y-4" action="#" method="POST" onsubmit="event.preventDefault();" v-if="!userStore.isLoadingMe" id="password-form">
        <!-- Form Group -->
        <div>
            <label for="current-password" class="block text-sm mb-2 dark:text-white">
                {{ $t('Current Password') }}
            </label>
            <v-input
                type="password"
                id="current-password"
                :value="userStore.passwordForm.current_password"
                :placeholder="$t('Current Password')"
                @update:input="userStore.passwordForm.current_password = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="current-password-error" v-if="userStore.errors.current_password">
                @{{ userStore.errors.current_password[0] }}
            </p>
        </div>

        <!-- Form Group -->
        <div>
            <label for="password" class="block text-sm mb-2 dark:text-white">
                {{ $t('Password') }}
            </label>
            <v-input
                type="password"
                id="password"
                :value="userStore.passwordForm.password" :placeholder="$t('Password')"
                @update:input="userStore.passwordForm.password = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="password-error" v-if="userStore.errors.password">
                @{{ userStore.errors.password[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
            <label for="confirm-password" class="block text-sm mb-2 dark:text-white">
                {{ $t('Confirm Password') }}
            </label>
            <v-input
                type="password"
                id="confirm-password"
                :value="userStore.passwordForm.password_confirmation"
                :placeholder="$t('Confirm Password')"
                @update:input="userStore.passwordForm.password_confirmation = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="confirm-password-error" v-if="userStore.errors.password_confirmation">
                @{{ userStore.errors.password_confirmation[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div class="flex items-center justify-end">
            <v-button type="submit" color="green" :loading="userStore.isLoadingMe" :disabled="userStore.isLoadingMe" @click="userStore.updatePassword">
                <i class="icon mgc_save_line"></i>
                {{ $t('Save') }}
            </v-button>
        </div>
    </form>
    <div class="flex justify-center items-center" v-if="userStore.isLoadingMe">
        <v-loader color="blue" :size="8"></v-loader>
    </div>
</template>

<script lang="ts" setup>
import {useUserStore} from "@/stores/UserStore";

const userStore = useUserStore();
</script>
