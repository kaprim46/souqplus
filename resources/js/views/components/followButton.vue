<template>
  <div @click="onClick">
    <slot name="button">
      <button class="flex items-center justify-center w-24 h-10 bg-blue-500 text-white rounded-md">
        <template v-if="!followLoading">
          <template v-if="props.isFollowing">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ $t('Unfollow') }}
          </template>
          <template v-else>
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ $t('Follow') }}
          </template>
          </template>
        <template v-else>
          <v-loader :size="1" />
        </template>
      </button>
    </slot>
  </div>
</template>

<script lang="ts" setup>
import axios from "@/axios";
import {nextTick} from "vue";

const props = defineProps({
  isFollowing: {
    type: Boolean,
    required: true,
  },
  userId: {
    type: Number,
    required: true,
  },
  followLoading: {
    type: Boolean,
    required: true,
  },
  typeFollowing: {
    type: String,
    required: false,
    default: "customer",
  },
});

const emits = defineEmits(["on:response", "on:loading"]);


const onClick = async () => {
  emits('on:loading', true);

  try {
    const {data} = await axios.post(`/follow/${props.userId}`, {
      type: props.typeFollowing,
    });

    if(data.ok) {
      emits('on:response', data.isFollowing);

      await nextTick(() => {
        emits('on:loading', false);
      });
    }
  } catch (error) {
    console.error(error);
  }
};
</script>