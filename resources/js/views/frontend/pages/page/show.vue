<template>
    <div class="px-4 py-5 mx-auto flex flex-col gap-5 bg-white rounded shadow" v-if="!pagesStore.isLoading">
        <h1 class="text-3xl font-semibold text-gray-800 dark:text-gray-100">
            {{ pagesStore.page.name }}
        </h1>

        <hr class="border-gray-200 dark:border-gray-700">

        <div class="prose dark:prose-dark max-w-none">
            <div v-html="pagesStore.page.content"></div>
        </div>
    </div>
    <div v-else class="flex justify-center items-center">
        <v-loader color="blue" :size="8"></v-loader>
    </div>
</template>

<script lang="ts" setup>

import {useCustomerPageStore} from "@/stores/Customer/CustomerPageStore";
import {onBeforeMount, watch} from "vue";
import {useRoute} from "vue-router";

const pagesStore            = useCustomerPageStore();
const route                 = useRoute();

watch(() => route.params.slug, async () => {
    if(!route.params.slug) return;

    await pagesStore.fetchPage(route.params.slug as string);
});

onBeforeMount(async () => {
    await pagesStore.fetchPage(route.params.slug as string);
});
</script>
