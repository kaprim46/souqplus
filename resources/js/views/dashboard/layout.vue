<template>
    <!-- Sidebar -->
    <v-sidebar />

    <main class="w-full py-5 px-4 sm:px-6 md:px-8 lg:ps-72 flex flex-col gap-y-10">
        <!-- Header -->
        <v-header />

        <!-- Content -->
        <div class="w-full">
            <router-view></router-view>
        </div>
        <!-- End Content -->
    </main>
</template>

<script lang="ts" setup>
import VSidebar from '@/views/dashboard/partials/sidebar.vue'
import VHeader from '@/views/dashboard/partials/header.vue'
</script>

<style>
body {
    @apply bg-blue-50;
}
</style>
