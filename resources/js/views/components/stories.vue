<template>
  <ul
    class="flex gap-5 overflow-y-hidden overflow-x-auto"
    v-if="!advertisementStore.isLoadingStories"
  >
    <li
      class="flex flex-col items-center space-y-1 relative"
      v-for="(story, index) in advertisementStore.stories"
      :key="index"
      @click="openStoryModal(index)"
    >
      <div
        class="bg-gradient-to-tr p-1 rounded-full"
        :class="
          story.is_viewed_all
            ? 'bg-gray-400'
            : 'bg-gradient-to-tr from-[#0C74CC] to-[#0C74BB]'
        "
      >
        <a
          class="bg-white block rounded-full p-1 hover:-rotate-6 transform transition"
          href="#"
        >
          <img
            class="min-h-24 min-w-24 max-h-24 max-w-24 rounded-full object-cover"
            :src="
              story.poster_url ? story.poster_url.replace('/thumbs', '') : ''
            "
            :alt="story.user.name"
          />
          <span
            class="absolute bottom-0 right-0 bg-[#0C74BB] text-white text-xs font-semibold px-1 rounded-lg"
            >{{ story.user.name }}</span
          >
        </a>
      </div>
    </li>
  </ul>

  <div v-else class="w-full h-24">
    <v-skeleton :loopNumber="2" />
  </div>

  <!-- Story Modal -->
  <div
    class="fixed inset-0 bg-black z-50 py-10"
    v-if="isStoryModalVisible && advertisementStore.stories[currentStoryIndex]"
  >
    <transition name="slide-fade" mode="out-in">
      <div
        class="h-full max-h-[1920px] rounded-lg overflow-hidden mx-auto w-[90%] max-w-[600px] flex flex-col bg-cover bg-no-repeat"
        :key="currentMedia?.url"
        :style="`background-image: url(${currentMedia?.url})`"
      >
        <template v-if="!isOpenViews">
          <!-- Stories dots -->
          <div class="flex justify-center gap-2 bg-black bg-opacity-25 p-2">
            <span
              v-for="(media, index) in advertisementStore.stories[
                currentStoryIndex
              ].stories[currentImageInStory].media"
              :key="index"
              @click="currentMediaInStory = index"
              class="w-2 h-2 rounded-full cursor-pointer"
              :class="
                index !== currentMediaInStory ? 'bg-white' : 'bg-[#0C74BB]'
              "
            >
            </span>
          </div>
          <!-- End of Stories dots -->

          <!-- Story Content -->
          <div class="flex-1 flex flex-col justify-between">
            <!-- Story Header -->
            <div
              class="flex justify-between items-center bg-black bg-opacity-25 p-3"
            >
              <figure class="flex items-center gap-x-2">
                <img
                  class="w-10 h-10 rounded-full object-cover"
                  :src="currentStory.user.avatar_url"
                  :alt="currentStory.user.name"
                />
                <h1 class="text-lg font-semibold text-white">
                  {{ currentStory.user.name }}
                </h1>
              </figure>
              <button
                class="w-10 h-10 bg-white rounded-full flex items-center justify-center"
                @click="onCloseStoryModal"
              >
                <i class="icon mgc_close_line text-2xl"></i>
              </button>
            </div>

            <!-- Story Controls -->
            <div
              class="flex rtl:flex-row-reverse justify-between items-center p-3"
            >
              <button
                @click="onPreviousImageInStory"
                class="bg-white p-2 rounded-full w-[50px] h-[50px] bg-opacity-50"
              >
                <i class="icon mgc_left_line text-xl"></i>
              </button>
              <button
                @click="onNextImageInStory"
                class="bg-white p-2 rounded-full w-[50px] h-[50px] bg-opacity-50"
              >
                <i class="icon mgc_right_line text-xl"></i>
              </button>
            </div>

            <!-- Story Footer -->
            <div
              class="w-full"
              v-if="currentStory.stories[currentImageInStory]?.advertisement"
            >
              <!-- Button for advertisement -->
              <button
                class="bg-[#0C74BB] hover:bg-[#0C74CC] text-white p-2 w-full h-[50px]"
                type="button"
                @click="onShowAdvertisement"
              >
                <i class="icon mgc_horn_line text-xl"></i>
                {{ $t('Show Advertisement') }}
              </button>

              <!--
                <button type="button" class="flex gap-2 items-center cursor-pointer p-0 m-0 bg-transparent border-0" v-if="advertisementStore.stories[currentStoryIndex].is_owner" @click="openViews(currentStoryIndex)">
                  <i class="icon mgc_eye_line text-white"></i>
                  <span class="text-white font-semibold">{{ currentStory.stories[currentImageInStory].views }}</span>
                </button>
                -->
            </div>
          </div>
          <!-- End of Story Content -->
        </template>

        <!--
          <div class="flex-1 flex-col flex justify-end w-full" v-else>
            <story-views :story="currentStory" :currentMedia="currentMedia" :currentViews="currentViews" @closeStoryModal="onCloseViewsModal" />
          </div>
          Views Components -->
      </div>
    </transition>
    <!-- Views Components -->
  </div>
</template>

<script lang="ts" setup>
import { useCustomerAdvertisementStore } from '@/stores/Customer/CustomerAdvertisementStore';
import StoryViews from '@/views/components/storyViews.vue';
import { onMounted, ref, computed, watch } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const advertisementStore = useCustomerAdvertisementStore();
const isStoryModalVisible = ref(false);
const currentStoryIndex = ref(0);
const currentImageInStory = ref(0);
const currentMediaInStory = ref(0);
const isOpenViews = ref(false);
const router = useRouter();

const currentStory = computed(
  () => advertisementStore.stories[currentStoryIndex.value]
);
const currentMedia = computed(
  () =>
    currentStory.value.stories[currentImageInStory.value].media[
      currentMediaInStory.value
    ]
);
const currentViews = computed(
  () => currentStory.value.stories[currentImageInStory.value].viewed_by
);
const storeIdsViews = new Set(<number[]>[]);

const storyInterval = ref<ReturnType<typeof setInterval> | null>(null);

const openStoryModal = (index: number) => {
  currentStoryIndex.value = index;
  isStoryModalVisible.value = true;
  addViewToStory();
  startStoryTimer();
};

const startStoryTimer = () => {
  if (storyInterval.value) clearInterval(storyInterval.value);
  storyInterval.value = setInterval(() => {
    onNextImageInStory();
  }, 5000); // Switch every 5 seconds
};

const stopStoryTimer = () => {
  if (storyInterval.value) clearInterval(storyInterval.value);
};

const onPreviousImageInStory = () => {
  if (currentMediaInStory.value > 0) {
    currentMediaInStory.value--;
  } else if (currentImageInStory.value > 0) {
    currentImageInStory.value--;
    currentMediaInStory.value =
      currentStory.value.stories[currentImageInStory.value].media.length - 1;
  } else if (currentStoryIndex.value > 0) {
    currentStoryIndex.value--;
    currentImageInStory.value = currentStory.value.stories.length - 1;
    currentMediaInStory.value =
      currentStory.value.stories[currentImageInStory.value].media.length - 1;
  }
};

const onNextImageInStory = () => {
  if (
    currentMediaInStory.value <
    currentStory.value.stories[currentImageInStory.value].media.length - 1
  ) {
    currentMediaInStory.value++;
  } else if (
    currentImageInStory.value <
    currentStory.value.stories.length - 1
  ) {
    currentImageInStory.value++;
    currentMediaInStory.value = 0;
  } else if (currentStoryIndex.value < advertisementStore.stories.length - 1) {
    currentStoryIndex.value++;
    currentImageInStory.value = 0;
    currentMediaInStory.value = 0;
  } else {
    stopStoryTimer();
  }
};

const onCloseStoryModal = () => {
  isStoryModalVisible.value = false;
  currentStoryIndex.value = 0;
  currentImageInStory.value = 0;
  currentMediaInStory.value = 0;
  stopStoryTimer();
};

const addViewToStory = async () => {
  try {
    const { data } = await axios.post(
      `/stories/${
        currentStory.value.stories[currentImageInStory.value].id
      }/view`
    );

    if (data.ok) {
      storeIdsViews.add(
        currentStory.value.stories[currentImageInStory.value].id
      );
    }
  } catch (error) {
    console.error(error);
  }
};

const openViews = (index: number) => {
  console.log('Open views');

  /**
   * Stop the story timer when the views modal is opened
   */
  stopStoryTimer();

  isOpenViews.value = true;
};

const onCloseViewsModal = () => {
  isOpenViews.value = false;
  startStoryTimer();
};

const onShowAdvertisement = () => {
  // Get the advertisement from the current story's stories array
  const adId =
    currentStory.value?.stories[currentImageInStory.value]?.advertisement?.id;
  const adSlug =
    currentStory.value?.stories[currentImageInStory.value]?.advertisement
      ?.slug || 'details';

  if (adId) {
    router.push({
      name: 'show-advertisement',
      params: {
        id: adId,
        slug: adSlug,
      },
    });
  }
};

onMounted(async () => {
  await advertisementStore.fetchStories();
});

watch(currentStoryIndex, () => {
  if (
    !storeIdsViews.has(currentStory.value.stories[currentImageInStory.value].id)
  )
    addViewToStory();
});

watch(isStoryModalVisible, (newVal) => {
  if (!newVal) {
    stopStoryTimer();
  }
});
</script>

<style>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: transform 0.5s ease, opacity 0.5s ease;
}
.slide-fade-enter,
.slide-fade-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
