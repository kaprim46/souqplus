<template>
  <section>
    <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
      <h2
        class="mb-4 text-4xl tracking-tight font-extrabold text-center text-gray-900 dark:text-white"
      >
        {{ $t('Contact us') }}
      </h2>
      <p
        class="mb-8 lg:mb-16 font-light text-center text-gray-500 dark:text-gray-400 sm:text-xl"
      >
        {{
          $t(
            'We are here to help and answer any question you might have. We look forward to hearing from you'
          )
        }}
      </p>

      <form
        action="#"
        class="space-y-8"
        @submit.prevent="contactUsStore.sendMessage"
      >
        <div>
          <label
            for="name"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
          >
            {{ $t('Name') }}
          </label>
          <v-input
            type="text"
            id="name"
            :placeholder="$t('Enter your name')"
            @update:input="contactUsStore.message.name = $event"
            :value="contactUsStore.message.name"
          />
          <p
            class="mt-2 text-sm text-red-600 dark:text-red-400"
            v-if="contactUsStore.sendErrors.name"
          >
            {{ contactUsStore.sendErrors.name[0] }}
          </p>
        </div>
        <div>
          <label
            for="email"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
          >
            {{ $t('Email') }}
          </label>
          <v-input
            type="email"
            id="email"
            :placeholder="$t('Enter your email')"
            @update:input="contactUsStore.message.email = $event"
            :value="contactUsStore.message.email"
          />
          <p
            class="mt-2 text-sm text-red-600 dark:text-red-400"
            v-if="contactUsStore.sendErrors.email"
          >
            {{ contactUsStore.sendErrors.email[0] }}
          </p>
        </div>
        <div>
          <label
            for="subject"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
          >
            {{ $t('Subject') }}
          </label>
          <v-input
            type="text"
            id="subject"
            :placeholder="$t('Enter the subject')"
            @update:input="contactUsStore.message.subject = $event"
            :value="contactUsStore.message.subject"
          />
          <p
            class="mt-2 text-sm text-red-600 dark:text-red-400"
            v-if="contactUsStore.sendErrors.subject"
          >
            {{ contactUsStore.sendErrors.subject[0] }}
          </p>
        </div>
        <div class="sm:col-span-2">
          <label
            for="message"
            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
          >
            {{ $t('Message') }}
          </label>
          <v-textarea
            id="message"
            :placeholder="$t('Enter your message')"
            @update:input="contactUsStore.message.message = $event"
            :value="contactUsStore.message.message"
          />
          <p
            class="mt-2 text-sm text-red-600 dark:text-red-400"
            v-if="contactUsStore.sendErrors.message"
          >
            {{ contactUsStore.sendErrors.message[0] }}
          </p>
        </div>

        <v-button
          color="blue"
          type="submit"
          class="w-full"
          :loading="contactUsStore.isReplying"
          :disabled="contactUsStore.isReplying"
        >
          {{ $t('Send message') }}
        </v-button>
      </form>
    </div>

    <!-- Floating WhatsApp Button -->
    <a
      :href="whatsappLink"
      target="_blank"
      class="fixed bottom-6 right-6 bg-[#25D366] w-14 h-14 flex items-center justify-center rounded-full shadow-lg hover:bg-[#20ba59] transition-colors z-50"
      title="Chat on WhatsApp"
    >
      <svg
        class="w-8 h-8 text-white"
        fill="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
        />
      </svg>
    </a>
  </section>
</template>

<script lang="ts" setup>
import { useContactUsStore } from '@/stores/ContactUsStore';
import { useSettingsStore } from '../../../../stores/SettingsStore';
import { computed } from 'vue';

const contactUsStore = useContactUsStore();
const settingStore = useSettingsStore();

// WhatsApp configuration
const whatsappNumber = computed(() => {
  return settingStore.settings.app_whatsapp?.replace(/\s+/g, '') || '';
});
const defaultMessage = ''; // Empty default message

const whatsappLink = computed(() => {
  const encodedMessage = encodeURIComponent(defaultMessage);
  return `https://wa.me/${whatsappNumber.value}?text=${encodedMessage}`;
});
</script>

<style scoped>
/* Optional: Add a pulse animation to the WhatsApp button */
@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(37, 211, 102, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
  }
}

.fixed.bottom-6.right-6 {
  animation: pulse 2s infinite;
}
</style>
