<template>
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden dark:bg-slate-900 dark:border-gray-700">
                    <!-- Header -->
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                <i class="icon mgc_cellphone_line"></i>
                                {{ $t('Splash Screens') }}
                            </h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $t('Manage your splash screens') }}
                            </p>
                        </div>


                        <div class="flex flex-col md:flex-row gap-2">
                            <v-button color="green" class="whitespace-nowrap" @click="isModalOpen = true; selectScreen = { title: '', content: '' }">
                                <i class="icon mgc_add_circle_fill"></i>
                                {{ $t('Create new splash screen') }}
                            </v-button>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="relative overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-slate-800 rtl:text-right ltr:text-left">
                            <tr>
                                <th scope="col" class="px-6 py-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                      {{ $t('Name') }}
                                    </span>
                                </th>

                                <th scope="col" class="px-6 py-2"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-if="!isLoading" v-for="screen in screens" :key="screen.id">
                                <td class="h-px px-6 py-2">
                                    <span class="whitespace-nowrap">
                                        {{ screen.title }}
                                    </span>
                                </td>
                                <td class="h-px px-6 py-2 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <v-button color="blue" class="whitespace-nowrap" @click="isModalOpen = true; selectScreen = {
                                            id: screen.id,
                                            title: screen.title,
                                            content: screen.content,
                                            image_url: screen.image_url
                                        }">
                                            <i class="icon mgc_edit_line"></i>
                                            {{ $t('Edit') }}
                                        </v-button>
                                        <v-button color="red" class="whitespace-nowrap" @click="onDelete(screen.id as number)">
                                            <i class="icon mgc_trash_line"></i>
                                            {{ $t('Delete') }}
                                        </v-button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Loader -->
                            <tr v-else>
                                <td colspan="6">
                                    <div class="flex items-center justify-center p-4">
                                        <v-loader v-if="isLoading" :size="4" :dark="true" />
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <v-modal :is-open="isModalOpen" @update:closeModal="isModalOpen = false; selectScreen = {
        title: '',
        content: ''
    }">
        <template v-slot:title>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                {{ selectScreen?.id ? $t('Edit screen') : $t('Create screen') }}
            </h3>
        </template>

        <template v-slot:content>
            <form class="flex flex-col gap-y-4" @submit.prevent="onSave()">

                <!-- Form Group -->
                <div>
                    <label for="title" class="block text-sm mb-2 dark:text-white">
                        {{ $t('Title') }}
                    </label>
                    <v-input
                        type="text"
                        id="title"
                        :value="selectScreen.title"
                        :placeholder="$t('Name')"
                        @update:input="selectScreen.title = $event"
                    />
                    <p class="text-xs text-red-600 mt-2" id="title-error" v-if="errors.title">
                        @{{ errors.title[0] }}
                    </p>
                </div>
                <!-- End Form Group -->

                <!-- Form Group -->
                <div>
                    <label for="content" class="block text-sm mb-2 dark:text-white">
                        {{ $t('Content') }}
                    </label>
                    <v-textarea
                        id="content"
                        :value="selectScreen.content"
                        :placeholder="$t('Content')"
                        @update:input="selectScreen.content = $event"
                    />
                    <p class="text-xs text-red-600 mt-2" id="content-error" v-if="errors.content">
                        @{{ errors.content[0] }}
                    </p>
                </div>
                <!-- End Form Group -->

                <!-- Screen Image -->
                <div>
                    <div id="logo-preview" class="w-full p-6 bg-white border-dashed border border-gray-400 rounded items-center mx-auto text-center cursor-pointer">
                        <input id="upload-logo" type="file" class="hidden" accept="image/*" @change="onChangeScreen($event)" />
                        <label for="upload-logo" class="cursor-pointer flex flex-col gap-1">
                            <img v-if="selectScreen.image_url" :src="selectScreen.image_url" id="imgLogo" alt="Screen" class="h-[100px] text-gray-700 mx-auto mb-4" />
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-700 mx-auto mb-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <span class="mb-2 text-xl font-bold tracking-tight text-gray-700">
                                    {{ $t('Upload screen image') }}
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
                    <p class="text-xs text-red-600 mt-2" id="image-error" v-if="errors.image">
                        @{{ errors.image[0] }}
                    </p>
                </div>
                <!-- End Screen Image -->

                <v-button color="blue" type="submit" class="whitespace-nowrap"
                          :disabled="isLoadingAction"
                          :loading="isLoadingAction"
                >
                    <i class="icon mgc_save_line"></i>
                    {{ $t('Save') }}
                </v-button>
            </form>
        </template>
    </v-modal>
</template>

<script lang="ts" setup>
import { ref,  onMounted } from 'vue'
import {notify} from "notiwind";
import {trans} from "laravel-vue-i18n";
import axios from "@/axios";

interface Screen {
    id?: number
    title: string
    content: string
    image_url?: string
    image?: File
}

const screens           = ref<Screen[]>([])
const isModalOpen       = ref(false)
const isLoading         = ref(false)
const isLoadingAction   = ref(false)
const errors            = ref<any>({})
const selectScreen      = ref<Screen>({
    title: '',
    content: ''
})

const onChangeScreen = (event: any) => {
    const file      = event.target.files[0] as File
    const reader    = new FileReader()
    reader.onload = (e) => {
        selectScreen.value.image        = file
        selectScreen.value.image_url    = e.target?.result as string
    }
    reader.readAsDataURL(file)
}

const fetchScreens = async () => {
    isLoading.value = true
    try {
        const { data } = await axios.get('/settings/splash-screens')

        if(data.ok) {
            screens.value = data.data
        }
    } catch (error:any) {
        console.error(error)
    } finally {
        isLoading.value = false
    }
}

const onSave = async () => selectScreen.value.id ? await onUpdate() : await onCreate()

const onCreate = async () => {
    isLoadingAction.value   = true
    const formData          = new FormData()
    formData.append('title', selectScreen.value.title)
    formData.append('content', selectScreen.value.content)
    if (selectScreen.value.image)  formData.append('image', selectScreen.value.image)

    try {
        const { data } = await axios.post('/dashboard/splash-screens', formData)

        if(data.ok) {
            screens.value.unshift(data.data);
            isModalOpen.value = false
        }

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? trans("Success") : trans("Error"),
            text: data.message
        }, 2000);
        errors.value = {}
    } catch (error:any) {
        errors.value = error.response.data.errors
    } finally {
        isLoadingAction.value = false
    }
}

const onUpdate = async () => {
    isLoadingAction.value   = true
    const formData          = new FormData()
    formData.append('_method', 'PUT')
    formData.append('title', selectScreen.value.title)
    formData.append('content', selectScreen.value.content)
    if (selectScreen.value.image)  formData.append('image', selectScreen.value.image)

    try {
        const { data } = await axios.post(`/dashboard/splash-screens/${selectScreen.value.id}`, formData)

        if(data.ok) {
            screens.value.splice(screens.value.findIndex(screen => screen.id === selectScreen.value.id), 1, data.data);
            isModalOpen.value = false
        }

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? trans("Success") : trans("Error"),
            text: data.message
        }, 2000);

        errors.value = {}
    } catch (error:any) {
        errors.value = error.response.data.errors
    } finally {
        isLoadingAction.value = false
    }
}

const onDelete = async (id: number) => {
    try {
        const { data } = await axios.delete(`/dashboard/splash-screens/${id}`)

        if(data.ok) {
            screens.value = screens.value.filter(screen => screen.id !== id)
        }

        notify({
            group: "dashboard",
            ok: data.ok,
            title: data.ok ? trans("Success") : trans("Error"),
            text: data.message
        }, 2000);

    } catch (error:any) {
        console.error(error)
    }
}

onMounted(async () => {
    await fetchScreens()
})
</script>
