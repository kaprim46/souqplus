<template>
    <input
        class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
        v-if="inputStyle === 'one'"
        :name="name"
        :id="id"
        :type="inputType"
        :value="value"
        :placeholder="placeholder"
        :autocomplete="autocomplete"
        :aria-describedby="ariaDescribedby"
        :step="inputType === 'number' ? step : null"
        @change="$emit('update:input', $event.target.value)"
        :disabled="disabled"
        :min="min"
        :max="max"
    />

    <div class="relative" v-if="inputStyle === 'two'">
        <input
            class="peer p-4 block w-full border-gray-200 rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600 focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2"
            :name="name"
            :id="id"
            :type="inputType"
            :value="value"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :aria-describedby="ariaDescribedby"
            :disabled="disabled"
            @change="$emit('update:input', $event.target.value)"
        />
        <label :for="id" class="absolute top-0 start-0 p-4 h-full text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent dark:text-white peer-disabled:opacity-50 peer-disabled:pointer-events-none
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

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        type: {
            type: String as PropType<'text' | 'password' | 'email' | 'number' | 'tel' | 'url'>,
            default: 'text',
        },
        placeholder: {
            type: String as PropType<string>,
            default: 'This is placeholder',
        },
        value: {
            type: [String, Number] as PropType<string | number>,
            default: '',
        },
        inputStyle: {
            type: String as PropType<string>,
            default: 'one',
        },
        label: {
            type: String as PropType<string>,
            default: '',
        },
        id: {
            type: String as PropType<string>,
            default: '',
        },
        autocomplete: {
            type: String as PropType<string>,
            default: 'off',
        },
        ariaDescribedby: {
            type: String as PropType<string>,
            default: '',
        },
        name: {
          type: String as PropType<string>,
          default: '',
        },
        min: {
          type: [String, Number] as PropType<string | number>,
          default: '',
        },
        max: {
          type: [String, Number] as PropType<string | number>,
          default: '',
        },
        step: {
            type: [String, Number] as PropType<string | number>,
            default: '',
        },
        disabled: {
          type: Boolean as PropType<boolean>,
          default: false,
        },
    },
    computed: {
        inputType(): 'text' | 'password' | 'email' | 'number' | 'tel' | 'url' {
            return this.type;
        },
    },
});
</script>

<style scoped>
/* Add your custom styles here */
</style>
