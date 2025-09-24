<template>
    <div class="relative">
        <input
            :type="inputType"
            :id="inputId"
            :name="inputName"
            :class="inputClass"
            :placeholder="placeholder"
            :value="value"
            @input="$emit('update:value', $event.target.value)"
        />
        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4">
            <span class="text-gray-500">{{ prefix }}</span>
        </div>
        <div class="absolute inset-y-0 end-0 flex items-center text-gray-500 pe-px">
            <label :for="selectId" class="sr-only">{{ selectLabel }}</label>
            <select :id="selectId" :name="selectName" :class="selectClass" @change="$emit('update:generic', $event.target.value)">
                <option v-for="option in options" :key="option.value" :value="option.value">{{ option.label }}</option>
            </select>
        </div>
    </div>
</template>

<script lang="ts" setup>
const props = defineProps({
    label: String,
    inputType: { type: String, default: "text" },
    inputId: String,
    inputName: String,
    inputClass: String,
    placeholder: String,
    prefix: String,
    selectId: String,
    selectName: String,
    selectClass: String,
    selectLabel: String,
    value: Number,
    options: { type: Array, default: () => [] },
});
</script>

<style scoped>
/* Add your component-specific styles here */
</style>
