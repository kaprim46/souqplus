<template>
    <select
        class="py-3 px-4 pe-9 w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
        :class="customClass"
        :id="id"
        :value="value"
        @change="$emit('update:value', $event.target.value)"
        v-if="inputStyle === 'one'"
    >
        <slot>
            <option value="" disabled selected>
                {{ $t('Select an option') }}
            </option>
            <option
                v-for="(option, index) in options"
                :key="index"
                :value="option.value"
            >{{ option.label }}</option>
        </slot>
    </select>
    <div class="relative" v-if="inputStyle === 'two'">
        <select
            class="peer p-4 pe-9 w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600 focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2"
            :class="customClass"
            :id="id"
            :value="value"
            @change="$emit('update:value', $event.target.value)"
        >
            <slot>
            <option value="" disabled selected>
                {{ $t('Select an option') }}
            </option>
            <option
                v-for="(option, index) in options"
                :key="index"
                :value="option.value"
            >{{ option.label }}</option>
            </slot>
        </select>
        <label class="absolute top-0 start-0 p-4 h-full truncate pointer-events-none transition ease-in-out duration-100 border border-transparent dark:text-white peer-disabled:opacity-50 peer-disabled:pointer-events-none
                                peer-focus:text-xs
                                peer-focus:-translate-y-1.5
                                peer-focus:text-gray-500
                                peer-[:not(:placeholder-shown)]:text-xs
                                peer-[:not(:placeholder-shown)]:-translate-y-1.5
                                peer-[:not(:placeholder-shown)]:text-gray-500">
            {{ label }}
        </label>
    </div>
</template>

<script lang="ts" setup>
const props = defineProps({
    value: {
        type: [String, Number] as PropType<string | number>,
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String as PropType<string>,
        default: '',
    },
    inputStyle: {
        type: String as PropType<string>,
        default: 'one',
    },
    id: {
        type: String as PropType<string>,
        default: '',
    },
    name: {
      type: String as PropType<string>,
      default: '',
    },
    customClass: {
      type: String as PropType<string>,
      default: '',
    },
});
</script>

<style scoped>
/* Add your custom styles here */
</style>
