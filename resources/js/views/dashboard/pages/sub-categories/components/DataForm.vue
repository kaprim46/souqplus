<template>
    <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
        <!-- Form Group -->
        <div>
            <label for="name" class="block text-sm mb-2 dark:text-white">
                {{ $t('Name') }}
            </label>
            <v-input
                type="text"
                id="name"
                :value="data_form.name"
                :placeholder="$t('Name')"
                @update:input="data_form.name = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="name-error" v-if="errors.name">
                @{{ errors.name[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Category_id -->
        <div>
            <label for="category_id" class="block text-sm mb-2 dark:text-white">
                {{ $t('Category') }}
            </label>
            <v-select
                id="category_id"
                :value="data_form.category_id"
                @update:value="data_form.category_id = $event"
            >
                <option v-for="category in categoryStore.categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </v-select>
            <p class="text-xs text-red-600 mt-2" id="category_id-error" v-if="errors.category_id">
                @{{ errors.category_id[0] }}
            </p>
        </div>
        <!-- End Category_id -->


        <!-- Form Group (status (active, inactive) ) -->
        <div>
            <label for="status" class="block text-sm mb-2 dark:text-white">
                {{ $t('Status') }}
            </label>
            <v-select
                id="status"
                :value="data_form.status"
                @update:value="data_form.status = $event"
            >
                <option value="active">{{ $t('Active') }}</option>
                <option value="inactive">{{ $t('Inactive') }}</option>
            </v-select>
            <p class="text-xs text-red-600 mt-2" id="status-error" v-if="errors.status">
                @{{ errors.status[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Form Group -->
        <div>
            <label for="description" class="block text-sm mb-2 dark:text-white">
                {{ $t('Description') }}
            </label>
            <v-textarea
                id="description"
                :value="data_form.description"
                :placeholder="$t('Description')"
                @update:input="data_form.description = $event"

            />
            <p class="text-xs text-red-600 mt-2" id="description-error" v-if="errors.description">
                @{{ errors.description[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <hr class="col-span-1 md:col-span-2">

        <h3 class="text-2xl font-bold dark:text-white col-span-1 md:col-span-2">
            <i class="icon mgc_layout_top_fill"></i>
            {{ $t('SEO') }}
        </h3>

        <!-- Form Group(meta_title, meta_description, meta_keywords, meta_robots) -->
        <div>
            <label for="meta_title" class="block text-sm mb-2 dark:text-white">
                {{ $t('Meta Title') }}
            </label>
            <v-input
                type="text"
                id="meta_title"
                :value="data_form.meta_title"
                :placeholder="$t('Meta Title')"
                @update:input="data_form.meta_title = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="meta_title-error" v-if="errors.meta_title">
                @{{ errors.meta_title[0] }}
            </p>
        </div>

        <div>
            <label for="meta_robots" class="block text-sm mb-2 dark:text-white">
                {{ $t('Meta Robots') }}
            </label>
            <v-select
                id="meta_robots"
                :value="data_form.meta_robots"
                @update:value="data_form.meta_robots = $event"
            >
                <option value="index, follow">{{ $t('Index, Follow') }}</option>
                <option value="noindex, follow">{{ $t('Noindex, Follow') }}</option>
                <option value="index, nofollow">{{ $t('Index, Nofollow') }}</option>
                <option value="noindex, nofollow">{{ $t('Noindex, Nofollow') }}</option>
            </v-select>
            <p class="text-xs text-red-600 mt-2" id="meta_robots-error" v-if="errors.meta_robots">
                @{{ errors.meta_robots[0] }}
            </p>
        </div>

        <div>
            <label for="meta_description" class="block text-sm mb-2 dark:text-white">
                {{ $t('Meta Description') }}
            </label>
            <v-textarea
                id="meta_description"
                :value="data_form.meta_description"
                :placeholder="$t('Meta Description')"
                @update:input="data_form.meta_description = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="meta_description-error" v-if="errors.meta_description">
                @{{ errors.meta_description[0] }}
            </p>

        </div>

        <div>
            <label for="meta_keywords" class="block text-sm mb-2 dark:text-white">
                {{ $t('Meta Keywords') }}
            </label>
            <v-textarea
                id="meta_keywords"
                :value="data_form.meta_keywords"
                :placeholder="$t('Meta Keywords')"
                @update:input="data_form.meta_keywords = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="meta_keywords-error" v-if="errors.meta_keywords">
                @{{ errors.meta_keywords[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <hr class="col-span-1 md:col-span-2">

        <h3 class="text-2xl font-bold dark:text-white col-span-1 md:col-span-2">
          <i class="icon mgc_filter_2_fill"></i>
          {{ $t('Filters') }}
        </h3>

        <div class="flex flex-wrap gap-4">
          <label v-for="(filter, index) in filtersStore.data_filters" :key="index" class="flex items-center gap-1">
            <input type="checkbox" class="form-checkbox" @change="onFilterChange(filter)" :checked="data_form.filters?.findIndex((f: number) => f === filter.id) !== -1" />
            <span class="font-semibold text-base">
                {{  filter.name }}
              </span>
          </label>
        </div>

        <!-- Actions -->
        <div class="col-span-1 md:col-span-2">
            <div class="flex justify-end">
                <v-button
                    type="submit"
                    color="green"
                    :loading="loading"
                    @click="data_form.id ? onUpdate() : onCreate()"
                >
                    <i :class="data_form.id ? 'icon mgc_edit_line' : 'icon mgc_add_line'"></i>
                    {{ data_form.id ? $t('Update') : $t('Create') }}
                </v-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {inject, nextTick, onBeforeMount, onMounted, ref} from "vue";
import {notify} from "notiwind";
import {wTrans} from "laravel-vue-i18n";
import Form from "@/views/dashboard/pages/customers/components/DataForm.vue";
import {Category, statusEnum} from "@/types/Category";
import {useCategoryStore} from "@/stores/CategoryStore";
import axios from "@/axios";
import {useFilterStore} from "@/stores/FilterStore";

const props         = defineProps({
  formData: {
    type: Object as PropType<Category>,
    required: true,
  },
  errors: {
    type: Object,
    required: true,
  },
});
const data_form     = ref<Category>(props.formData);
const loading       = ref(false);
const categoryStore = useCategoryStore();
const emit          = defineEmits(['update:form', 'update:errors']);
const filtersStore  = useFilterStore();


const onFilterChange = (fi: Category) => {
  const filterIndex = data_form.value.filters?.findIndex((f: number) => f === fi.id);

  if(filterIndex !== -1) {
    return data_form.value.filters?.splice(filterIndex, 1);
  }

  data_form.value.filters?.push(fi.id as number);
}

const onCreate = async () => {
    loading.value = true;

    await nextTick();

    try {
        const { data } = await axios.post('/dashboard/sub/categories', data_form.value);

        if(data.ok) {
            emit('update:form', {
                name: '',
                description: '',
                status: statusEnum.active,
                meta_title: '',
                meta_description: '',
                meta_keywords: '',
                meta_robots: '',
                category_id: null,
            });
        }

        emit('update:errors', {});

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? wTrans("Success") : wTrans("Error"),
            text: data.message
        }, 2000);

    } catch (e: any) {
        if(e.response.status === 422) {
            emit('update:errors', e.response.data.errors);
        }
    } finally {
        loading.value = false;
    }
}

const onUpdate =async () => {
    if(!data_form.value.id) return;

    loading.value = true;

    await nextTick();

    try {
        const { data } = await axios.put(`/dashboard/sub/categories/${data_form.value.id}`, data_form.value);

        if(data.ok) {
            emit('update:form', data.category);
        }

        emit('update:errors', {});

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? wTrans("Success") : wTrans("Error"),
            text: data.message
        }, 2000)
    } catch (e: any) {
        if(e.response.status === 422) {
            emit('update:errors', e.response.data.errors);
        }
    } finally {
        loading.value = false;
    }
}

onBeforeMount(async() => {
  filtersStore.filters.pagination = 0;

  await nextTick(async () => filtersStore.fetchFilters());
});

onMounted(async () => {
    categoryStore.filters.pagination = 0 as number;

    await nextTick();

    await categoryStore.fetch()
});
</script>
