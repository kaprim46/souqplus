<template>
    <form class="grid grid-cols-1 gap-y-4" action="#" method="POST" onsubmit="event.preventDefault();" v-if="!userStore.isLoadingMe" id="profile-form">
        <div class="grid gap-y-4">
            <!-- Form Group -->
            <div class="flex items-center gap-5">
                <label for="avatar" class="w-[85px] h-[85px] rounded-full overflow-hidden bg-gray-200 dark:bg-gray-800 flex items-center justify-center cursor-pointer">
                    <img :src="userStore.me.avatar_url" alt="avatar" class="w-full h-full object-cover" />
                    <input type="file" id="avatar" name="avatar" class="hidden" @change="userStore.uploadAvatar" />
                </label>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div>
                <label for="name" class="block text-sm mb-2 dark:text-white">
                    {{ $t('Full Name') }}
                </label>
                <v-input type="text" id="name" :value="userStore.me.name" :placeholder="$t('Full Name')" @update:input="userStore.me.name = $event" />
                <p class="text-xs text-red-600 mt-2" id="name-error" v-if="userStore.errors.name">@{{ userStore.errors.name[0] }}</p>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div>
                <label for="email" class="block text-sm mb-2 dark:text-white">
                    {{ $t('Email Address') }}
                </label>
                <v-input type="email" id="email" :value="userStore.me.email" :placeholder="$t('Email Address')" @update:input="userStore.me.email = $event" :disabled="true" />
                <p class="text-xs text-red-600 mt-2" id="email-error" v-if="userStore.errors.email">@{{ userStore.errors.email[0] }}</p>
            </div>
            <!-- End Form Group -->

            <!-- Form Group -->
            <div>
                <label for="phone" class="block text-sm mb-2 dark:text-white">
                    {{ $t('Phone Number') }}
                </label>
                <v-phoneInput @update:phoneNumber="userStore.me.phone_number = $event" @update:countryCode="userStore.me.country_code = $event" :value="userStore.me.phone_number" :valueCountryCode="userStore.me.country_code ?? '+212'" />
                <p class="text-xs text-red-600 mt-2" id="phone-error" v-if="userStore.errors.phone_number">@{{ userStore.errors.phone_number[0] }}</p>
            </div>
            <!-- End Form Group -->

          <!-- bio -->
            <div>
                <label for="bio" class="block text-sm mb-2 dark:text-white">
                    {{ $t('Bio') }}
                </label>
                <v-textarea id="bio" :value="userStore.me.bio" :placeholder="$t('Bio')" @update:input="userStore.me.bio = $event" />
                <p class="text-xs text-red-600 mt-2" id="bio-error" v-if="userStore.errors.bio">@{{ userStore.errors.bio[0] }}</p>
            </div>
            <!-- End Form Group -->

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-5">
              <!--
                <v-button type="button" color="red" :loading="userStore.isLoadingMe" :disabled="userStore.isLoadingMe" @click="onDeleteAccount">
                    <i class="icon mgc_user_remove_2_fill"></i>
                    {{ $t('Delete account') }}
                </v-button>
                -->
                <v-button type="submit" color="green" :loading="userStore.isLoadingMe" :disabled="userStore.isLoadingMe" @click="userStore.updateProfile">
                    <i class="icon mgc_save_line"></i>
                    {{ $t('Save') }}
                </v-button>
            </div>
        </div>
    </form>
    <div class="flex justify-center items-center" v-if="userStore.isLoadingMe">
        <v-loader color="blue" :size="8"></v-loader>
    </div>
</template>

<script lang="ts" setup>
import {inject, onBeforeMount, ref} from "vue";
import {useUserStore} from "@/stores/UserStore";
import {trans} from "laravel-vue-i18n";

const userStore         = useUserStore();
const sweetAlert        = inject('$swal');

/**
IS WORK
const onDeleteAccount   = async () => {
    const {value} = await sweetAlert.fire({
        title: trans('Are you sure?'),
        text: trans('You won\'t be able to revert this!'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: trans('Yes, delete it!'),
        cancelButtonText: trans('No, cancel!'),
        reverseButtons: true
    });

    if (value) {
       await userStore.deleteProfile();
    }
}
* Delete the user account
 */

onBeforeMount(async() => await userStore.fetchProfile());
</script>
