<template>
  <div class="flex flex-col">
    <div class="-m-1.5 overflow-x-auto">
      <div class="p-1.5 min-w-full">
        <div
          class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700"
        >
          <!-- Header -->
          <div
            class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700"
          >
            <div>
              <h2
                class="text-xl font-semibold text-gray-800 dark:text-gray-200"
              >
                <i class="icon mgc_certificate_line"></i>
                {{ $t('Advertisements') }}
              </h2>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('Manage your advertisements') }}
              </p>
            </div>

            <div class="flex flex-col md:flex-row gap-2">
              <v-input
                :placeholder="$t('Search')"
                @update:input="advertisementStore.filters.search = $event"
              />

              <v-button
                color="green"
                class="whitespace-nowrap"
                @click="router.push({ name: 'advertisement.create' })"
              >
                <i class="icon mgc_add_circle_fill"></i>
                {{ $t('Create new advertisement') }}
              </v-button>
            </div>
          </div>
          <!-- End Header -->

          <!-- Table -->
          <div class="relative overflow-x-auto">
            <table
              class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
            >
              <thead
                class="bg-gray-50 dark:bg-slate-800 rtl:text-right ltr:text-left"
              >
                <tr>
                  <th scope="col" class="px-6 py-2">
                    <span
                      class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap"
                    >
                      {{ $t('Name') }}
                    </span>
                  </th>

                  <th scope="col" class="px-6 py-2">
                    <span
                      class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap"
                    >
                      {{ $t('Owner') }}
                    </span>
                  </th>

                  <th scope="col" class="px-6 py-2">
                    <span
                      class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap"
                    >
                      {{ $t('Category') }}
                    </span>
                  </th>

                  <th scope="col" class="px-6 py-2">
                    <span
                      class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap"
                    >
                      {{ $t('Total comments') }}
                    </span>
                  </th>

                  <th scope="col" class="px-6 py-2">
                    <span
                      class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap"
                    >
                      {{ $t('Status') }}
                    </span>
                  </th>
                  <th scope="col" class="px-6 py-2">
                    <span
                      class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-20 whitespace-nowrap"
                    >
                      {{ $t('Address') }}
                    </span>
                  </th>

                  <th scope="col" class="px-6 py-2"></th>
                  <th scope="col" class="px-6 py-2"></th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                  v-if="!advertisementStore.isLoading"
                  v-for="advertisement in advertisementStore.advertisements"
                  :key="advertisement.id"
                >
                  <td class="px-6 py-2">
                    <div class="flex items-center gap-2 whitespace-nowrap">
                      <img
                        v-if="advertisement.main_media?.url_thumb"
                        :src="advertisement.main_media.url_thumb"
                        :alt="advertisement.name"
                        class="inline-block h-[2.375rem] w-[2.375rem] rounded-full"
                      />
                      {{ advertisement.name }}
                    </div>
                  </td>
                  <td class="px-6 py-2">
                    <a class="block" href="#">
                      <div class="flex items-center gap-x-3">
                        <img
                          class="inline-block h-[2.375rem] w-[2.375rem] rounded-full"
                          :src="advertisement.user.avatar_url"
                          :alt="advertisement.user.name"
                        />
                        <div class="grow">
                          <span
                            class="block text-sm font-semibold text-gray-800 dark:text-gray-200"
                          >
                            {{ advertisement.user.name }}
                          </span>
                          <span class="block text-sm text-gray-500">
                            {{ advertisement.user.email }}
                          </span>
                          <p
                            class="block font-semibold text-[12.2px] text-red-700 whitespace-nowrap"
                            v-if="advertisement.user.deleted_at"
                          >
                            <i class="icon mgc_alert_diamond_fill"></i>
                            {{
                              $t('Account deleted at :date', {
                                date: advertisement.user.deleted_at,
                              })
                            }}
                          </p>
                        </div>
                      </div>
                    </a>
                  </td>
                  <td class="px-6 py-2">
                    <div class="flex flex-col items-center gap-1">
                      <v-badge
                        :color="'blue'"
                        :text="`${advertisement.category.name}`"
                      />
                      <template v-if="advertisement.sub_category">
                        <i class="icon mgc_down_fill"></i>
                        <v-badge
                          :color="'green'"
                          :text="`${advertisement.sub_category.name}`"
                        />
                      </template>
                    </div>
                  </td>
                  <td class="px-6 py-2">
                    <v-badge
                      color="blue"
                      :text="
                        $t('Total comments: :count', {
                          count: advertisement.comments_count ?? 0,
                        })
                      "
                    />
                  </td>
                  <td class="px-6 py-2">
                    <v-badge
                      v-if="advertisement.status"
                      :color="getStatusColor(advertisement.status)"
                      :text="getStatusText(advertisement.status)"
                    />
                  </td>
                  <td class="px-6 py-2">
                    <span
                      class="whitespace-nowrap text-sm text-gray-800 dark:text-gray-200 flex items-center gap-1"
                    >
                      {{ advertisement.country?.name ?? 'N/A' }}
                      <i
                        class="icon mgc_right_fill"
                        v-if="advertisement.city?.name"
                      ></i>
                      {{ advertisement.city?.name ?? 'N/A' }}
                    </span>
                  </td>
                  <td class="h-px px-6 py-2">
                    <v-badge
                      v-if="advertisement.deleted_at"
                      color="red"
                      :text="
                        $t('Ad deleted at :date', {
                          date: advertisement.deleted_at,
                        })
                      "
                    />
                  </td>
                  <td class="px-6 py-2">
                    <div class="flex justify-end gap-x-2">
                      <v-button
                        color="purple"
                        class="whitespace-nowrap"
                        v-if="advertisement.comments_count"
                        @click="
                          selectedAdvertisement = advertisement;
                          isOpen = true;
                        "
                      >
                        <i class="icon mgc_comment_2_line"></i>
                        {{ $t('Comments management') }}
                      </v-button>

                      <v-button
                        color="blue"
                        class="whitespace-nowrap"
                        @click="
                          router.push({
                            name: 'advertisement.edit',
                            params: { id: advertisement.id },
                          })
                        "
                      >
                        <i class="icon mgc_edit_line"></i>
                        {{ $t('Edit') }}
                      </v-button>

                      <v-button
                        :color="advertisement.deleted_at ? 'green' : 'red'"
                        class="whitespace-nowrap"
                        @click="
                          advertisement.deleted_at
                            ? advertisementStore.onRestore(advertisement.id)
                            : advertisementStore.onDelete(advertisement.id)
                        "
                      >
                        <template v-if="advertisement.deleted_at">
                          <i class="icon mgc_restore_line"></i>
                          {{ $t('Restore') }}
                        </template>
                        <template v-else>
                          <i class="icon mgc_delete_line"></i>
                          {{ $t('Delete') }}
                        </template>
                      </v-button>
                    </div>
                  </td>
                </tr>
                <!-- Loader -->
                <tr v-else>
                  <td colspan="6">
                    <div class="flex items-center justify-center p-4">
                      <v-loader
                        v-if="advertisementStore.isLoading"
                        :size="4"
                        :dark="true"
                      />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- End Table -->

          <!-- Footer -->
          <div
            v-if="!advertisementStore.isLoading"
            class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700"
          >
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-800 dark:text-gray-200">
                  {{ advertisementStore.pagination.total }}
                </span>
                {{ $t('Results') }}
              </p>
            </div>

            <div>
              <div class="inline-flex gap-x-2">
                <button
                  @click="advertisementStore.fetch(true, true)"
                  :disabled="advertisementStore.pagination.current_page === 1"
                  type="button"
                  class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                >
                  <svg
                    class="flex-shrink-0 w-4 h-4"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="m15 18-6-6 6-6" />
                  </svg>
                  {{ $t('Previous') }}
                </button>

                <button
                  @click="advertisementStore.fetch(true)"
                  :disabled="
                    advertisementStore.pagination.current_page ===
                    advertisementStore.pagination.last_page
                  "
                  type="button"
                  class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                >
                  {{ $t('Next') }}
                  <svg
                    class="flex-shrink-0 w-4 h-4"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="m9 18 6-6-6-6" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <!-- End Footer -->
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Manage Comments -->
  <v-modal
    :is-open="isOpen"
    @update:closeModal="
      isOpen = false;
      selectedAdvertisement = null;
    "
  >
    <template #title>
      <h2
        class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2"
      >
        <i class="icon mgc_comment_2_line"></i>
        {{ $t('Comments management') }}
      </h2>
    </template>

    <template #content>
      <v-comment :advertisement="selectedAdvertisement" />
    </template>
  </v-modal>
</template>

<script lang="ts" setup>
import { useAdvertisementStore } from '@/stores/AdvertisementStore';
import { onBeforeMount, onMounted, ref, watch } from 'vue';
import { Advertisement, statusEnum } from '@/types/Advertisements';
import { trans } from 'laravel-vue-i18n';
import router from '@/routes';
import _ from 'lodash';

/**
 * Components
 */
import VComment from './components/comments.vue';

/**
 * States
 */
const advertisementStore = useAdvertisementStore();
const isOpen = ref(false);
const selectedAdvertisement = ref<any>({} as Advertisement);

/**
 * Methods
 */
const getStatusColor = (status: statusEnum) => {
  switch (status) {
    case statusEnum.PENDING:
      return 'orange';
    case statusEnum.APPROVED:
      return 'blue';
    case statusEnum.REJECTED:
      return 'red';
    default:
      return 'grey';
  }
};

const getStatusText = (status: string) => {
  switch (status) {
    case 'active':
      return `<i class='icon mgc_check_fill text-xl'></i> ${trans('active')}`;
    case 'pending':
      return `<i class='icon mgc_clock_line text-xl'></i> ${trans('pending')}`;
    case 'approved':
      return `<i class='icon mgc_check_fill text-xl'></i> ${trans('approved')}`;
    case 'rejected':
      return `<i class='icon mgc_close_fill text-xl'></i> ${trans('rejected')}`;
    default:
      return `<i class='icon mgc_question_line text-xl'></i> ${trans(
        'Unknown'
      )}`;
  }
};

/**
 * Watchers
 */
watch(
  advertisementStore.filters,
  _.debounce(async () => {
    await advertisementStore.fetch();
  }, 500)
);

onBeforeMount(() => {
  if (router.currentRoute.value.query.user_id) {
    advertisementStore.filters.user_id = parseInt(
      router.currentRoute.value.query.user_id as string
    );
  }
});

onMounted(async () => {
  await advertisementStore.fetch();
});
</script>
