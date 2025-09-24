<template>
<div :dir="$t('dir')">
    <router-view />

    <v-notifications />
</div>
</template>

<script lang="ts" setup>
import {onBeforeMount, onMounted} from 'vue';

import { type IStaticMethods } from "preline/preline";
import {useSettingsStore} from "@/stores/SettingsStore";

const storeSettings = useSettingsStore();

declare global {
    interface Window {
        HSStaticMethods: IStaticMethods;
    }
}

onBeforeMount(async () => {
  await storeSettings.fetch();
})

onMounted(() => {
    setTimeout(() => {
        window.HSStaticMethods.autoInit();
    }, 100)
});
</script>
