<template>
    <metainfo>
        <template v-slot:title="{ content }"> {{ storeSettings.appSettings?.app_name }} - {{ content }} </template>
    </metainfo>

    <div class="flex flex-col gap-5 min-h-screen">
        <v-header />
        <div class="flex-1 p-5 container mx-auto">
            <router-view />
        </div>
        <v-footer />
    </div>
</template>

<script lang="ts" setup>
import {onBeforeMount} from "vue";
import {useCustomerPageStore} from "@/stores/Customer/CustomerPageStore";
import {useCustomerCategoryStore} from "@/stores/Customer/CustomerCategoryStore";

/**
 * Components
 */
import VHeader from '@/views/frontend/layouts/partials/header.vue'
import VFooter from '@/views/frontend/layouts/partials/footer.vue'


const pagesStore            = useCustomerPageStore();
const customerCategoryStore = useCustomerCategoryStore();

onBeforeMount(async () => {
  await pagesStore.getPages();
  await customerCategoryStore.fetch();
})
</script>


<style>
body {
    @apply bg-blue-50;
}
</style>
