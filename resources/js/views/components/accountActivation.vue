<template>
  <div>
    <template v-if="!isSentActivationEmail">
      <img :src="`${$assetUrl}/verification-user.png`" alt="Account activation" class="w-auto h-full max-h-[16rem] mx-auto">
      <v-button type="button" color="blue" @click="sendVerificationCode" class="mt-4 w-full" :loading="isLoading">
        {{ $t('Send activation email') }}
      </v-button>
      <!-- I'have the code -->
      <div class="text-center my-4 font-semibold text-gray-700">
        {{ $t('I have the code already') }} - <button type="button" class="text-[#0C74BB]" @click="isSentActivationEmail = true">{{ $t('Activate my account') }}</button>
      </div>
    </template>
    <template v-else>
      <img :src="`${$assetUrl}/verification-user.png`" alt="Email sent" class="w-auto h-full max-h-[16rem] mx-auto">
      <p class="text-center my-4 font-semibold text-gray-700">
        {{ $t('An activation email has been sent to your email address. Please check your email and click on the activation link.') }}
      </p>
      <!-- OTP input -->
      <v-otpInput :fields="6" @update:modelValue="onActiveAccount"></v-otpInput>
    </template>
  </div>
</template>


<script lang="ts" setup>
import {ref} from "vue";
import axios from "@/axios";
import {useUserStore} from "@/stores/UserStore";
import {notify} from "notiwind";
import {trans} from "laravel-vue-i18n";

const isSentActivationEmail = ref<boolean>(false);
const isLoading             = ref<boolean>(false);
const userStore             = useUserStore();
const emit                  = defineEmits(['close']);

const sendVerificationCode = async ()  => {
  isLoading.value = true;

  try {
    await userStore.sendVerification(userStore.me.email);
  } catch (e) {
    console.error(e);
  } finally {
    isLoading.value = false;
  }
}


const onActiveAccount = async (otp: number | null) => {
  if(otp?.toString().length=== 6) {
    try {
      const { data } = await axios.post('/verify', { otp_code: otp, email: userStore.me.email });

      if(data.ok) {
        isSentActivationEmail.value = false;
        isLoading.value             = false;

        userStore.updateUser(data.user);

        emit('close');
      }

      notify({
        group: "dashboard",
        ok: data.ok,
        title: data.ok ? trans("Success") : trans("Error"),
        text: data.message
      }, 2000);
    } catch (e) {
      console.error(e);
    }
  }
}
</script>