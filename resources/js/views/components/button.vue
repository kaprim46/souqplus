<template>
    <button
        :type="buttonType"
        :class="[baseClass, outlineClass, colorClass, customClass]"
        :disabled="isDisabled || loading"
    >
        <slot v-if="!loading">Button</slot>
        <template v-if="loading">
            <span class="animate-spin inline-block w-4 h-4 border-[3px] border-current border-t-transparent rounded-full" role="status" aria-label="loading"></span>
            {{ $t('Loading') }}
        </template>
    </button>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        outline: {
            type: Boolean as PropType<boolean>,
            default: false,
        },
        type: {
            type: String as PropType<'submit' | 'button'>,
            default: 'button',
        },
        color: {
            type: String as PropType<string>,
            default: 'red', // Default color
        },
        disabled: {
            type: Boolean as PropType<boolean>,
            default: false,
        },
        circle: {
            type: Boolean as PropType<boolean>,
            default: false,
        },
        size : {
            type: String as PropType<'sm' | 'md' | 'lg'>,
            default: 'md',
        },
        loading: {
            type: Boolean as PropType<boolean>,
            default: false,
        },
        rounded: {
            type: String as PropType<string>,
            default: 'md',
        },
        customClass: {
            type: String as PropType<string>,
            default: '',
        },
    },
    computed: {
        roundedClass(): string {
            switch (this.rounded) {
                case 'none':
                    return 'rounded-none';
                case 'sm':
                    return 'rounded-sm';
                case 'md':
                    return 'rounded-md';
                case 'lg':
                    return 'rounded-lg';
                case 'xl':
                    return 'rounded-xl';
                case '2xl':
                    return 'rounded-2xl';
                case '3xl':
                    return 'rounded-3xl';
                case 'full':
                    return 'rounded-full';
                default:
                    return '';
            }
        },
        sizeClass(): string {
            switch (this.size) {
                case 'sm':
                    return 'py-1 px-2 text-xs min-w-[100px] h-[40px]';
                case 'md':
                    return 'py-2 px-3 text-sm min-w-[120px] h-[50px]';
                case 'lg':
                    return 'py-3 px-4 text-base min-w-[140px] h-[60px]';
                default:
                    return '';
            }
        },
        baseClass(): string {
            return this.circle ?
                'rounded-full min-w-[35px] min-h-[35px] max-w-[35px] max-h-[35px] flex items-center justify-center'
                :
                `${this.roundedClass} py-2 px-2 gap-x-2 text-sm font-medium`;
        },
        outlineClass(): string {
            return this.outline ? 'border' : 'border-transparent';
        },
        colorClass(): string {
            switch (this.color) {
                case 'gray':
                    return `text-gray-600 bg-gray-100 hover:bg-gray-200`;
                case 'red':
                    return `text-white bg-red-600 hover:bg-red-700`;
                case 'green':
                    return `text-white bg-green-600 hover:bg-green-700`;
                case 'blue':
                    return `text-white bg-[#0C74BB] hover:bg-[#0C74CC]`;
                case 'yellow':
                    return `text-white bg-yellow-600 hover:bg-yellow-700`;
                case 'indigo':
                    return `text-white bg-indigo-600 hover:bg-indigo-700`;
                case 'purple':
                    return `text-white bg-purple-600 hover:bg-purple-700`;
                case 'pink':
                    return `text-white bg-pink-600 hover:bg-pink-700`;
                case 'greenTwo':
                    return `text-white bg-green-500 hover:bg-green-600`;
                case 'iceberg':
                    return `text-white bg-iceberg hover:bg-iceberg/80`;
                case 'froly':
                    return `text-white bg-froly hover:bg-froly/80`;
                case 'coralred':
                    return `text-white bg-coralred hover:bg-coralred/80`;
                case 'easternblue':
                    return `text-white bg-easternblue hover:bg-easternblue/80`;
                case 'prussianblue':
                    return `text-white bg-prussianblue hover:bg-prussianblue/80`;
                default:
                    return '';
            }
        },
        isDisabled(): boolean {
            return this.disabled;
        },
        buttonType(): 'submit' | 'button' {
            return this.type;
        },
    },
});
</script>

<style scoped>
/* Add your custom styles here */
</style>
