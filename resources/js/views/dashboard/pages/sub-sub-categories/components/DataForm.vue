<template>
    <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm mb-2 dark:text-white">{{ $t('Name') }}</label>
            <v-input
                type="text"
                id="name"
                :placeholder="$t('Name')"
                :value="data_form.name"
                @update:input="data_form.name = $event"
            />
            <p v-if="errors.name" class="text-xs text-red-600 mt-2">@{{ errors.name[0] }}</p>
        </div>

        <!-- Status -->
        <div>
            <label for="status" class="block text-sm mb-2 dark:text-white">{{ $t('Status') }}</label>
            <v-select id="status" :value="data_form.status" @update:value="data_form.status = $event">
                <option value="active">{{ $t('Active') }}</option>
                <option value="inactive">{{ $t('Inactive') }}</option>
            </v-select>
            <p v-if="errors.status" class="text-xs text-red-600 mt-2">@{{ errors.status[0] }}</p>
        </div>

        <!-- Actions -->
        <div class="col-span-1 md:col-span-2">
            <div class="flex justify-end">
                <v-button type="submit" color="green" :loading="loading" @click="data_form.id ? onUpdate() : onCreate()">
                    <i :class="data_form.id ? 'icon mgc_edit_line' : 'icon mgc_add_line'"></i>
                    {{ data_form.id ? $t('Update') : $t('Create') }}
                </v-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from "vue";
import { notify } from "notiwind";
import { wTrans } from "laravel-vue-i18n";
import axios from "@/axios";
import { SubSubCategory } from "@/types/Category";
import type { PropType } from 'vue'

const props = defineProps({
  formData: { type: Object as PropType<SubSubCategory>, required: true },
  errors: { type: Object, required: true },
});
const emit = defineEmits(['update:form', 'update:errors']);

const data_form = ref<SubSubCategory>(props.formData);
const loading = ref(false);

const onCreate = async () => {
    loading.value = true;
    try {
        const { data } = await axios.post('/dashboard/sub-sub-categories', data_form.value);
        if(data.ok) {
            emit('update:form', { name: '', status: 'active', sub_category_id: data_form.value.sub_category_id });
        }
        emit('update:errors', {});
        notify({ group: "dashboard", ok: data.ok, title: data.ok ? wTrans("Success") : wTrans("Error"), text: data.message }, 2000);
    } catch (e: any) {
        if(e.response.status === 422) emit('update:errors', e.response.data.errors);
    } finally {
        loading.value = false;
    }
}

const onUpdate = async () => {
    if(!data_form.value.id) return;
    loading.value = true;
    try {
        const { data } = await axios.put(`/dashboard/sub-sub-categories/${data_form.value.id}`, data_form.value);
        emit('update:errors', {});
        notify({ group: "dashboard", ok: data.ok, title: data.ok ? wTrans("Success") : wTrans("Error"), text: data.message }, 2000);
    } catch (e: any) {
        if(e.response.status === 422) emit('update:errors', e.response.data.errors);
    } finally {
        loading.value = false;
    }
}
</script>