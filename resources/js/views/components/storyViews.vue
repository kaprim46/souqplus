<template>
  <div class="bg-black bg-opacity-80">
    <!-- Story Views Total & Close Button -->
    <div class="flex justify-between items-center p-2">
      <h1 class="text-white font-semibold">{{ $t(':views Views', {views: currentViews?.length ?? 0}) }}</h1>
      <button class="w-10 h-10 bg-white rounded-full flex items-center justify-center" @click="onCloseStoryModal">
        <i class="icon mgc_close_line text-2xl"></i>
      </button>
    </div>
  </div>
  <div class="bg-black bg-opacity-80 w-full max-h-[45%] overflow-y-auto">
    <div v-for="(view, index) in currentViews" :key="index" v-if="typeof currentViews === 'object'">
      <figure class="flex items-center gap-x-2 p-2">
        <img
            class="w-14 h-14 rounded-full object-cover"
            :src="view.avatar_url"
            :alt="view.name"/>
        <div class="flex flex-col">
          <h1 class="text-lg font-semibold text-white">{{ view.name }}</h1>
          <span class="text-white">{{ view.viewed_at }}</span>
        </div>
      </figure>
    </div>
    <div class="flex items-center justify-center p-4 text-white" v-if="!currentViews?.length">
      {{ $t('No views yet') }}
    </div>
  </div>
</template>


<script lang="ts" setup>
import {onMounted, ref} from 'vue';

const props = defineProps<{
  story: any;
  currentMedia: any;
  currentViews: any;
}>();

const story = ref(props.story);
const currentMedia = ref(props.currentMedia);
const currentViews = ref(props.currentViews);
const emit = defineEmits(['closeStoryModal']);

const onCloseStoryModal = () => {
  emit('closeStoryModal');
};

onMounted(() => {
  console.log(currentViews.value);
});
</script>