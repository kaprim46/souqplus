<template>
    <label class="w-full h-[50px] bg-n-green border-2 border-n-green-light border-dashed rounded-md relative flex justify-center items-center hover:border-gray-400 focus-within:outline-none focus-within:border-blue-500">
        <span class="text-n-green-darker font-bold flex items-center gap-1">
            <i class="icon mgc_upload_2_fill"></i>
            {{ $t('Upload a file') }}
        </span>
        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
    </label>
</template>

<script lang="ts" setup>
</script>
