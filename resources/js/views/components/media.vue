<template>
    <div class="border-2 border-dashed border-gray-300 rounded p-4 h-[150px] overflow-hidden overflow-y-auto flex flex-wrap gap-2">
        <label class="w-[85px] h-[85px] bg-prussianblue bg-opacity-20 rounded-lg flex items-center justify-center relative shadow overflow-hidden">
            <input type="file" class="absolute top-0 start-0 w-full h-full opacity-0 cursor-pointer" @change="onFileChange" :disabled="isLoading">
            <i class="icon mgc_upload_3_line text-3xl" v-if="!isLoading"></i>
            <i class="icon mgc_loading_2_fill text-3xl animate-spin" v-if="isLoading"></i>
        </label>

        <div class="w-[85px] h-[85px] rounded-lg relative shadow overflow-hidden group"
             :class="{ 'border-4 border-green-500': mediaFiles.find((mediaFile: Media) => mediaFile.id === item.id)?.is_main }"
             v-for="item in media">
            <img :src="item.url_thumb" alt="Placeholder" class="absolute top-0 start-0 w-full h-full object-cover">
            <div
                class="absolute top-0 start-0 w-full h-full bg-black bg-opacity-50 flex gap-1 items-center justify-center opacity-0 group-hover:opacity-100 transition-all"
                :class="mediaFiles.find((mediaFile: Media) => mediaFile.id === item.id) ? 'bg-blue-900 opacity-100 text-white' : 'text-white'">

                <div class="hs-tooltip">
                    <button type="button" class="hs-tooltip-toggle text-2xl" @click="deleteMedia(item)">
                        <i class="icon mgc_delete_2_line"></i>
                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-slate-700" role="tooltip">
                          {{ $t('Delete') }}
                        </span>
                    </button>
                </div>
                <div class="hs-tooltip">
                    <button type="button" class="hs-tooltip-toggle text-2xl" @click="mediaFiles.find((mediaFile: Media) => mediaFile.id === item.id) ? unSetMedia(item) : setMedia(item)">
                        <i class="icon" :class="mediaFiles.find((mediaFile: Media) => mediaFile.id === item.id) ? 'mgc_minus_circle_line' : 'mgc_check_2_fill'"></i>
                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-slate-700" role="tooltip">
                          {{ mediaFiles.find((mediaFile: Media) => mediaFile.id === item.id) ? 'Unset' : 'Set' }}
                        </span>
                    </button>
                </div>
                <!-- Set as main -->
                <div class="hs-tooltip" v-if="mediaFiles.find((mediaFile: Media) => mediaFile.id === item.id) && !mediaFiles.find((mediaFile: Media) => mediaFile.id === item.id)?.is_main">
                    <button type="button" class="hs-tooltip-toggle text-2xl" @click="setAsMainMedia(item)">
                        <i class="icon mgc_horn_2_line"></i>
                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-slate-700" role="tooltip">
                            {{ $t('Set as main') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import {onMounted, ref} from 'vue'

interface Media {
    mime_type: string
    size: string
    path: string
    file_name: string
    ext: string
    type: string
    url: string
    is_main?: boolean
}

const props = defineProps({
    mediaFiles: {
        type: <PropType<Media[]>>Array,
        default: () => []
    }
});

const isLoading     = ref(false)
const media         = ref<Media[]>([])
const emit          = defineEmits(['on:setMedia', 'on:unSetMedia', 'on:setAsMainMedia'])
const getMedia = async () => {
    try {
        const {data} = await axios.get('/media')

        if(data.ok) {
            media.value = data.media.data
        }
    } catch (error) {
        console.log(error)
    }
}

const onFileChange = async (e: any) => {
    isLoading.value = true
    const files = e.target.files || e.dataTransfer.files;

    if (!files.length)
        return;

    await createFile(files[0])
}

const createFile = async (file: any) => {
    try {
        const formData = new FormData()
        formData.append('file', file)
        formData.append('path', 'media')

        const {data} = await axios.post('/media', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        if(data.ok) {
            media.value.push(data.data)
        }
    } catch (error) {
        console.log(error)
    } finally {
        isLoading.value = false
    }
}

const deleteMedia = async (item: Media) => {
    try {
        const {data} = await axios.delete(`/media/${item.id}`)

        if(data.ok) {
            media.value = media.value.filter((media: Media) => media.id !== item.id)
        }
    } catch (error) {
        console.log(error)
    }
}

const setMedia = (item: Media) => {
    emit('on:setMedia', item)
}

const unSetMedia = (item: Media) => {
    emit('on:unSetMedia', item)
}

const setAsMainMedia = (item: Media) => {
    emit('on:setAsMainMedia', item)
}

onMounted(async () => {
    await getMedia()
});
</script>
