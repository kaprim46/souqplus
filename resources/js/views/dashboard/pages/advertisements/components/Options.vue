<template>
<div class="flex flex-col gap-4">
  <template v-for="(option, index) in data" :key="index">
      <!-- Type : Number -->
      <div class="flex flex-col gap-1" v-if="option.type === 'number'">
        <label :for="`${option.id}-${option.name}`" class="block text-sm mb-2 dark:text-white">
          {{ option.name }} :
        </label>
       <v-input
           type="number"
           :min="option.options?.from"
           :max="option.options?.to"
           :placeholder="option.options?.placeholder"
           :value="selectData.find((input: any) => input.filter_id === option.id)?.value"
           @update:input="updateValue(option, $event)"
       />
      </div>
      <!-- End Type Number -->

      <!-- Type : Select -->
      <div class="flex flex-col gap-1" v-if="option.type === 'select'">
        <label :for="`${option.id}-${option.name}`" class="block text-sm mb-2 dark:text-white">
          {{ option.name }} :
        </label>
        <v-select
            :id="`${option.id}-${option.name}`"
            :value="selectData.find((input: any) => input.filter_id === option.id)?.value" @update:value="updateValue(option, $event)"
        >
          <option disabled selected>
            {{ option.options?.placeholder }}
          </option>
          <option v-for="(DataOption, index) in option.options?.options" :key="index" :value="DataOption.value">
           {{ DataOption.name }}
          </option>
        </v-select>
      </div>
      <!-- End Type Select -->

      <!-- Type : Checkbox -->
      <div class="flex flex-col gap-1" v-if="option.type === 'checkbox'">
        <span class="block text-sm mb-2 dark:text-white">
          {{ option.name }} :
        </span>

        <ul>
          <li v-for="(subOption, subIndex) in option.options?.options">
            <label  :for="`${subIndex}-${subOption.name}`" class="flex items-center gap-2">
              <span class="w-[15px] h-[15px] rounded-full" :style="`background: ${subOption.value};`"></span>
              <input
                  type="checkbox"
                  :checked="selectData[index] && selectData[index]?.value === subOption.value"
                  @change="updateValue(option, subOption.value)"
              />
              {{ subOption.name }}
            </label>
          </li>
        </ul>
      </div>
      <!-- End Type Checkbox -->

      <!-- Type : Range -->
      <div class="flex flex-col gap-1" v-if="option.type === 'range'">
        <label :for="`${option.id}-${option.name}`" class="block text-sm mb-2 dark:text-white">
          {{ option.name }} :
        </label>
        <input
               type="range"
               id="volume"
               name="volume"
               :min="option.options?.from"
               :max="option.options?.to"
               @input="updateValue(option, $event.target?.value ?? 0)"
               :value="selectData.find((input: any) => input.filter_id === option.id)?.value"
        />
        <span class="font-semibold text-sm">
            {{ selectData.find((input: any) => input.filter_id === option.id)?.value ?? 0 }}
        </span>
      </div>
      <!-- End Type Range -->

      <!-- Type : Text -->
      <div class="flex flex-col gap-1" v-if="option.type === 'text'">
        <label :for="`${option.id}-${option.name}`" class="block text-sm mb-2 dark:text-white">
          {{ option.name }} :
        </label>
        <v-input
            type="text"
            :placeholder="option.options?.placeholder"
            :value="selectData.find((input: any) => input.filter_id === option.id)?.value"
            @update:input="updateValue(option, $event)"
        />
      </div>
      <!-- End Type Text -->

      <!-- Type : Year -->
      <div class="flex flex-col gap-1" v-if="option.type === 'year'">
        <label :for="`${option.id}-${option.name}`" class="block text-sm mb-2 dark:text-white">
          {{ option.name }} :
        </label>
        <v-select
            :id="`${option.id}-${option.name}`"
            :value="selectData.find((input: any) => input.filter_id === option.id)?.value"
            @update:value="updateValue(option, $event)"
        >
          <option disabled selected>
            {{ option.options?.placeholder }}
          </option>
          <option
              v-for="year in getYears(option.options?.from ?? '1995', option.options?.to ?? new Date().getFullYear().toString())" :key="year"
              :value="year"
          >
            {{ year }}
          </option>
        </v-select>
      </div>
      <!-- End Type Year -->

      <!-- Type : Color, Radio -->
      <div class="flex flex-col gap-1" v-if="option.type === 'color' || option.type === 'radio'">
        <span class="block text-sm mb-2 dark:text-white">
          {{ option.name }} :
        </span>

        <ul>
          <li v-for="(subOption, subIndex) in option.options?.options">
            <label  :for="`${subIndex}-${subOption.name}`" class="flex items-center gap-2">
              <span class="w-[15px] h-[15px] rounded-full" :style="`background: ${subOption.value};`"></span>
              <input
                     type="radio"
                     :value="subOption.value"
                     :name="`${subIndex}-${subOption.name}`"
                     :checked="selectData.find((input: any) => input.filter_id === option.id) && selectData.find((input: any) => input.filter_id === option.id)?.value === subOption.value"
                     @input="updateValue(option, subOption.value)"
              />
              {{ subOption.name }}
            </label>
          </li>
        </ul>
      </div>
      <!-- End Type color, Radio -->
  </template>
</div>
</template>

<script lang="ts" setup>
import {onBeforeMount, ref} from "vue";
import {Filter} from "@/stores/FilterStore";


const props       = defineProps({
  data: {
    type: Array as () => Filter[],
    required: true
  },
  initialData: {
    type: null,
    default: null
  }
});

const selectData  = ref<{
  filter_id: number;
  value: any;
}[]|any>([]);
const emit        = defineEmits(['on:update']);

/**
 * Update value
 * @param id
 * @param type
 * @param value
 */
const updateValue   = ({id, type}: Filter, value: any) => {
  let index;

  if (type === 'checkbox') {
    index = selectData.value.findIndex((data: any) => data.filter_id === id && data.value === value);

    if (index !== -1) {
      selectData.value.splice(index, 1);
    } else {
      selectData.value.push({
        filter_id: id as number,
        value: value ?? null
      });
    }
  }
  else {
    index = selectData.value.findIndex((data: any) => data.filter_id === id);

    if (index === -1) {
      selectData.value.push({
        filter_id: id as number,
        value: value ?? null
      });
    } else {
      selectData.value[index] = {
        filter_id: id as number,
        value: value ?? null
      };
    }
  }

  emit('on:update', selectData.value);
};

/**
 * Get Years from - to
 * @param from
 * @param to
 */
const getYears      = (from: string, to: string): number[] => {
  const years = [];

  for (let year = parseInt(from); year <= parseInt(to); year++) {
    years.push(year);
  }

  return years;
}

onBeforeMount(() => {
  if (props.initialData)  selectData.value = props.initialData;
});
</script>