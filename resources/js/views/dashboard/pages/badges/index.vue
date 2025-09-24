<template>
  <div class="flex flex-col">
    <div class="-m-1.5 overflow-x-auto">
      <div class="p-1.5 min-w-full">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
          <!-- Header -->
          <div class="px-6 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
            <div>
              <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                <i class="icon mgc_badge_line"></i>
                {{ $t('Badges') }}
              </h2>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('Manage badges') }}
              </p>
            </div>


            <div class="flex gap-x-2">
              <v-input :placeholder="$t('Search')"  :value="filters.search" @update:input="filters.search = $event" />
              <v-button color="blue" @click="resetData(); isModalOpen = true;" custom-class="whitespace-nowrap">
                <i class="icon mgc_plus_line"></i>
                {{ $t('Add badge') }}
              </v-button>
            </div>
          </div>
          <!-- End Header -->

          <!-- Table -->
          <div class="relative overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-slate-800 rtl:text-right ltr:text-left">
                <tr>
                  <th scope="col" class="px-6 py-2">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                      {{ $t('Name') }}
                    </span>
                  </th>

                  <th scope="col" class="px-6 py-2">
                      <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200 whitespace-nowrap">
                          {{ $t('Description') }}
                      </span>
                  </th>

                  <th scope="col" class="text-end"></th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-if="!isLoading" v-for="badge in badges" :key="badge.id">
                  <td class="px-6 py-3">
                    <div class="flex items-center gap-2">
                      <img :src="badge.icon_url" :alt="badge.name" class="h-[75px] object-cover" />
                      <span class="text-sm text-gray-800 dark:text-gray-200">
                          {{ badge.name }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-3">
                    {{ badge.description }}
                  </td>
                  <td class="px-6 py-3">
                    <div class="flex justify-end gap-x-2">
                      <v-button color="blue" class="whitespace-nowrap" @click="onEdit(badge)">
                        <i class="icon mgc_edit_line"></i>
                        {{ $t('Edit') }}
                      </v-button>

                      <v-button color="red" class="whitespace-nowrap" @click="onDelete(badge)">
                        <i class="icon mgc_delete_line"></i>
                        {{ $t('Delete') }}
                      </v-button>
                    </div>
                  </td>
                </tr>
                <!-- Loader -->
                <tr v-else>
                  <td colspan="6">
                    <div class="flex items-center justify-center p-4">
                      <v-loader :size="4" :dark="true" />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- End Table -->


          <!-- Footer -->
          <div
              v-if="!isLoading"
              class="w-full px-4 sm:px-6 lg:px-8 py-5 mx-auto grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-gray-700"
          >
            <div>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ pagination.total }}
                </span>
                {{ $t('Results') }}
              </p>
            </div>

            <div>
              <div class="inline-flex gap-x-2">
                <button
                    @click="fetchBadges(true, true)"
                    :disabled="pagination.current_page === 1"
                    type="button"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                  <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                  {{ $t('Previous') }}
                </button>

                <button
                    @click="fetchBadges(true)"
                    :disabled="pagination.current_page === pagination.last_page"
                    type="button"
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                  {{ $t('Next') }}
                  <svg class="flex-shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </button>
              </div>
            </div>

          </div>
          <!-- End Footer -->
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <v-modal :is-open="isModalOpen" @update:closeModal="isModalOpen = false; resetData();">
    <template #title>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
        <i class="icon" :class="currentBadge.id ? 'mgc_edit_line' : 'mgc_plus_line'"></i>
        {{ currentBadge.id ? $t('Edit badge') : $t('Add badge') }}
      </h2>
    </template>

    <template #content>
      <data-form :form-data="currentBadge" @update:form="onUpdateForm"></data-form>
    </template>
  </v-modal>
</template>

<script lang="ts" setup>
import {nextTick, onMounted, ref, watch} from "vue";
import DataForm, {Badge} from "@/views/dashboard/pages/badges/components/DataForm.vue";
import axios from "@/axios";
import {notify} from "notiwind";
import {trans} from "laravel-vue-i18n";

const isModalOpen       = ref(false);
const currentBadge      = ref<Badge>({condition: "", description: "", icon_url: "", name: "", condition_type: ""});
const pagination        = ref({
  current_page: 1,
  last_page: 1,
  per_page: 1,
  total: 0,
});
const isLoading         = ref<boolean>(false);
const filters           = ref<any>({
  pagination: 1,
  search: ""
})
const badges            = ref<Badge[]>([]);


const resetData = () => {
  currentBadge.value = {
    condition: "",
    description: "",
    icon_url: "",
    name: "",
    condition_type: ""
  };
}

const onEdit = async (badge: Badge) => {
  currentBadge.value = badge;

  await nextTick(() => {
    isModalOpen.value = true;
  })
}

const fetchBadges = async (isPagination = false, isLess = false) =>  {
  isLoading.value        = true;

  let params             = {} as {
    search?: string,
    pagination?: number,
    page?: number
  };

  if(filters.value.search) {
    params.search        = filters.value.search;
  }

  if(filters.value.pagination) {
    params.pagination    = 1;
  }

  if(isPagination && filters.value.pagination) {
    if(isLess)
      pagination.value.current_page--;
    else
      pagination.value.current_page++;

    params.page          = pagination.value.current_page;
  }


  try {
    const { data }          = await axios.get(`/dashboard/badges`, {
      params: {
        ...params
      }
    });

    if(data.ok) {
      console.log(data)

      if(filters.value.pagination) {
        badges.value      = data.badges.data;
        pagination.value     = {
          current_page: data.badges.current_page,
          last_page: data.badges.last_page,
          per_page: data.badges.per_page,
          total: data.badges.total,
        }
      } else {
        badges.value      = data.badges;
      }
    }

    await nextTick();

    isLoading.value        = false;
  } catch (error) {
    isLoading.value        = false;
  }
};

const onUpdateForm = (badge: Badge) => {
  if(badge.id) {
    const index = badges.value.findIndex(b => b.id === badge.id);

    if(index !== -1) {
      badges.value[index] = badge;
    }
  } else {
    fetchBadges();
  }

  isModalOpen.value = false;
}

const onDelete = async (badge: Badge) => {
  if(!badge.id) return;

  if(!confirm(trans('Are you sure you want to delete this badge?'))) return;

  try {
    const { data } = await axios.delete(`/dashboard/badges/${badge.id}`);

    if(data.ok) {
      const index = badges.value.findIndex(b => b.id === badge.id);

      if(index !== -1) {
        badges.value.splice(index, 1);
      }
    }

    notify({
      group: 'dashboard',
      ok: data.ok,
      type: data.ok ? 'success' : 'error',
      title: data.ok ? 'Success' : 'Error',
      text: data.message,
    }, 2000);
  } catch (error) {
    console.log(error)
  }
}

watch(filters, () => {
  fetchBadges();
}, {deep: true});

onMounted(async () => fetchBadges());
</script>
