<template>
    <div class="grid gap-4 md:grid-cols-2">
        <!-- Form Group (ICON) -->
        <div>
            <label for="icon" class="block text-sm mb-2 dark:text-white">
                {{ $t('Icon') }}
            </label>

            <div class="flex flex-row gap-2">
                <img v-if="form.icon"
                     :src="typeof (form.icon) === 'object' ? createObjectURL(form.icon) : form.icon_url" class="h-[45px] w-[45px] rounded-lg"  alt="icon" />
                <div class="h-[45px] w-full rounded-lg bg-blue-600 font-semibold text-white text-[15.5px] flex items-center justify-center gap-2 relative">
                    <i class="icon mgc_upload_line"></i>
                    {{ $t('Upload icon') }}
                    <input
                        type="file"
                        id="icon"
                        name="icon"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        accept="image/*"
                        @change="form.icon = $event.target.files[0]"
                    />
                </div>
            </div>


            <p class="text-xs text-red-600 mt-2" id="icon-error" v-if="errors.icon">
                @{{ errors.icon[0] }}
            </p>
        </div>



        <!-- Form Group -->
        <div>
            <label for="name" class="block text-sm mb-2 dark:text-white">
                {{ $t('Name') }}
            </label>
            <v-input
                type="text"
                id="name"
                name="name"
                required
                aria-describedby="name-error"
                :value="form.name"
                :placeholder="$t('Name')"
                @update:input="form.name = $event"
            />
            <p class="text-xs text-red-600 mt-2" id="name-error" v-if="errors.name">
                @{{ errors.name[0] }}
            </p>
        </div>
        <!-- End Form Group -->


        <!-- Form Group (status (active, inactive) ) -->
        <div>
            <label for="status" class="block text-sm mb-2 dark:text-white">
                {{ $t('Status') }}
            </label>
            <v-select
                id="status"
                name="status"
                required
                aria-describedby="status-error"
                :value="form.status"
                :placeholder="$t('Status')"
                @update:value="form.status = $event"
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
        <div class="col-span-1 md:col-span-2">
            <label for="content" class="block text-sm mb-2 dark:text-white">
                {{ $t('Content') }}
            </label>
            <v-editor
                @update:input="form.content = $event"
                :value="form.content"
                :placeholder="$t('Content')"
            />
            <p class="text-xs text-red-600 mt-2" id="content-error" v-if="errors.content">
                @{{ errors.content[0] }}
            </p>
        </div>
        <!-- End Form Group -->

        <!-- Actions -->
        <div class="col-span-2">
            <div class="flex justify-end">
                <v-button
                    type="submit"
                    color="green"
                    :loading="loading"
                    @click="form.id ? onUpdate() : onCreate()"
                >
                    <i :class="form.id ? 'icon mgc_edit_line' : 'icon mgc_add_line'"></i>
                    {{ form.id ? $t('Update') : $t('Create') }}
                </v-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {inject, nextTick, ref} from "vue";
import {notify} from "notiwind";
import {wTrans} from "laravel-vue-i18n";
import Form from "@/views/dashboard/pages/customers/components/DataForm.vue";
import {statusEnum} from "@/types/Category";

const loading = ref(false);

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:form', 'update:errors']);

const createObjectURL = (file: File) => {
    return URL.createObjectURL(file);
}

const onCreate = async () => {
    loading.value = true;

    await nextTick();

    const dataForm = new FormData();
    dataForm.append('name', props.form.name);
    dataForm.append('status', props.form.status);
    dataForm.append('content', props.form.content);
    if(props.form.icon && typeof props.form.icon !== 'string') dataForm.append('icon', props.form.icon);

    try {
        const { data } = await axios.post('/dashboard/pages', dataForm);

        if(data.ok) {
            emit('update:form', {
                name: '',
                description: '',
                status: statusEnum.active,
                content: '',
                icon: null,
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
    if(!props.form.id) return;

    loading.value = true;

    await nextTick();

    const dataForm = new FormData();
    dataForm.append('_method', 'PUT');
    dataForm.append('name', props.form.name);
    dataForm.append('status', props.form.status);
    dataForm.append('content', props.form.content);
    if(props.form.icon && typeof props.form.icon !== 'string') dataForm.append('icon', props.form.icon);

    try {
        const { data } = await axios.post(`/dashboard/pages/${props.form.id}`, dataForm);

        if(data.ok) {
            emit('update:form', data.page);
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
</script>
