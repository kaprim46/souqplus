<template>
  <div class="flex flex-col gap-4">
    <!-- Form Group for Name -->
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
      <p class="text-xs text-red-600 mt-2" v-if="errors.name">
        @{{ errors.name[0] }}
      </p>
    </div>

    <!-- Form Group for Description -->
    <div>
      <label for="description" class="block text-sm mb-2 dark:text-white">
        {{ $t('Description') }}
      </label>
      <v-textarea
          id="description"
          :value="data_form.description"
          @update:input="data_form.description = $event"
          :placeholder="$t('Description')"
      />
      <p class="text-xs text-red-600 mt-2" v-if="errors.description">
        @{{ errors.description[0] }}
      </p>
    </div>

    <!-- Form Group for Icon -->
    <div>
      <div id="icon-preview" class="w-full p-6 bg-white border-dashed border border-gray-400 rounded items-center mx-auto text-center cursor-pointer">
        <input id="upload-icon" type="file" class="hidden" accept="image/*" @change="onChangeIcon" />
        <label for="upload-icon" class="cursor-pointer flex flex-col gap-1">
          <img v-if="data_form.icon_url" :src="data_form.icon_url" id="imgIcon" alt="Icon" class="h-[100px] text-gray-700 mx-auto mb-4" />
          <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-700 mx-auto mb-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
          </svg>
          <span class="mb-2 text-xl font-bold tracking-tight text-gray-700">
                        {{ $t('Upload icon image') }}
                    </span>
          <span class="font-normal text-sm text-gray-400 md:px-6">
                        {{ $t('Choose photo size should be less than :size', {size: `2mb`}) }}
                    </span>
          <span class="font-normal text-sm text-gray-400 md:px-6">
                        {{ $t('and should be in :format format.', {format: `JPG, PNG, or GIF`}) }}
                    </span>
          <span id="filename" class="text-gray-500 bg-gray-200 z-50"></span>
        </label>
      </div>
      <p class="text-xs text-red-600 mt-2" id="image-error" v-if="errors.icon">
        @{{ errors.icon[0] }}
      </p>
    </div>

    <!-- Form Group for Condition Type -->
    <div>
      <label for="condition_type" class="block text-sm mb-2 dark:text-white">
        {{ $t('Condition Type') }}
      </label>
      <v-select
          id="condition_type"
          :value="data_form.condition_type"
          @update:value="data_form.condition_type = $event"
      >
        <option value="advertisement_count">{{ $t('Advertisement Count') }}</option>
        <option value="role">{{ $t('Role') }}</option>
        <option value="membership_duration">{{ $t('Membership Duration') }}</option>
      </v-select>
      <p class="text-xs text-red-600 mt-2" v-if="errors.condition_type">
        @{{ errors.condition_type[0] }}
      </p>
    </div>

    <!-- Form Group for Condition Value -->
    <div v-if="data_form.condition_type">
      <label for="condition_value" class="block text-sm mb-2 dark:text-white">
        {{ $t('Condition Value') }}
      </label>
      <v-select
          v-if="data_form.condition_type === 'role'"
          id="condition_value"
          :value="conditionValue"
          @update:value="conditionValue = $event"
      >
        <option value="admin">{{ $t('Admin') }}</option>
        <option value="business">{{ $t('Business') }}</option>
        <option value="customer">{{ $t('Customer') }}</option>
      </v-select>
      <v-input
          v-else
          type="number"
          id="condition_value"
          :value="conditionValue"
          :placeholder="$t('Enter Value')"
          @update:input="conditionValue = $event"
      />
      <p class="text-xs text-red-600 mt-2" v-if="errors.condition">
        @{{ errors.condition[0] }}
      </p>
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
import {onBeforeMount, onMounted, ref, watch} from 'vue';
import { notify } from 'notiwind';
import axios from '@/axios';

export interface Badge {
  id?: number;
  name: string;
  description: string;
  icon_url?: string;
  icon?: File;
  condition_type: string;
  condition: string;
}

const props           = defineProps({
  formData: {
    type: Object as PropType<Badge>,
    required: true,
  }
});
const errors          = ref<any>({});
const data_form       = ref<Badge>(props.formData);
const loading         = ref(false);
const conditionValue  = ref<string | number | null>(null);
const emit            = defineEmits(['update:form', 'update:errors']);

watch([() => data_form.value.condition_type, conditionValue], ([newConditionType, newConditionValue]) => {
  if (newConditionType && newConditionValue !== null) {
    data_form.value.condition = JSON.stringify({ [newConditionType]: newConditionValue });
  } else {
    data_form.value.condition = '';
  }
});

const onChangeIcon = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      data_form.value.icon = file;
      data_form.value.icon_url = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const onCreate = async () => {
  loading.value = true;

  const formData = new FormData();

  formData.append('name', data_form.value.name);

  formData.append('description', data_form.value.description);

  formData.append('condition_type', data_form.value.condition_type);

  if (data_form.value.icon)
    formData.append('icon', data_form.value.icon);

  if (data_form.value.condition)
    formData.append('condition', data_form.value.condition);

  try {
    const { data } = await axios.post('/dashboard/badges', formData);

    if (data.ok)
    {
      emit('update:form', {
        name: '',
        description: '',
        icon_url: '',
        condition_type: '',
        condition: '',
      });
    }

    errors.value = {};

    notify({
      group: 'dashboard',
      ok: data.ok,
      type: data.ok ? 'success' : 'error',
      title: data.ok ? 'Success' : 'Error',
      text: data.message,
    }, 2000);
  } catch (e: any)
  {
    if (e.response.status === 422) {
      errors.value = e.response.data.errors;
    }
  } finally
  {
    loading.value = false;
  }
};

const onUpdate = async () => {

  if (!data_form.value.id) return;

  loading.value = true;

  const formData = new FormData();

  formData.append('_method', 'PUT');

  formData.append('name', data_form.value.name);

  formData.append('description', data_form.value.description);

  formData.append('condition_type', data_form.value.condition_type);

  if (data_form.value.icon)
    formData.append('icon', data_form.value.icon);

  if (data_form.value.condition)
    formData.append('condition', data_form.value.condition);

  try {
    const { data } = await axios.post(`/dashboard/badges/${data_form.value.id}`, formData);

    console.log(data.ok);

    if (data.ok) {
      emit('update:form', data.badge);
    }

    errors.value = {};

    notify({
      group: 'dashboard',
      ok: data.ok,
      type: data.ok ? 'success' : 'error',
      title: data.ok ? 'Success' : 'Error',
      text: data.message,
    }, 2000);

  } catch (e: any) {
    if (e.response.status === 422)
    {
      errors.value = e.response.data.errors;
    }
  } finally
  {
    loading.value = false;
  }
};

onMounted(() => {
  if(props.formData?.id) {
    conditionValue.value = Object.values(JSON.parse(props.formData.condition))[0] as string | number ?? null;
  }
})
</script>
