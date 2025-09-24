<template>
  <div class="flex flex-col gap-4">
    <!-- Text and Number -->
    <div class="grid grid-cols-2 gap-2" v-if="typeFilter">
      <div>
        <v-input
            :placeholder="$t('Placeholder')"
            :value="options.nameField"
            @update:input="options.nameField = $event"
        />
        <p v-if="errors['options.nameField']" class="text-xs text-red-600">
          {{ errors['options.nameField'][0] }}
        </p>
      </div>

      <div>
        <v-input
            :placeholder="$t('Name')"
            :value="options.placeholder"
            @update:input="options.placeholder = $event"
        />
        <p v-if="errors['options.placeholder']" class="text-xs text-red-600">
          {{ errors['options.placeholder'][0] }}
        </p>
      </div>
    </div>
    <!-- End Text and Number -->

    <!-- Select, Radio and Checkbox -->
    <div v-if="typeFilter === 'select' || typeFilter === 'radio' || typeFilter === 'checkbox' || typeFilter === 'color'" class="flex flex-col gap-2">

      <div v-for="(option, index) in localData.options" :key="index" class="flex items-center gap-2">
        <div class="w-full">
          <v-input
            :placeholder="$t('Option')"
            :value="localData.options[index].name"
            @update:input="localData.options[index].name = $event"
            type="text"
          />
          <p v-if="errors[`options.options.${index}.name`]" class="text-xs text-red-600">
            {{ errors[`options.options.${index}.name`][0] }}
          </p>
        </div>

        <div class="w-full">
          <v-input
            :placeholder="$t('Value')"
            :value="localData.options[index].value"
            @update:input="localData.options[index].value = $event"
            type="text"
          />
          <p v-if="errors[`options.options.${index}.value`]" class="text-xs text-red-600">
            {{ errors[`options.options.${index}.name`][0] }}
          </p>
        </div>

        <button @click="removeOption(index)" class="text-red-500">
          <i class="icon mgc_delete_2_line"></i>
        </button>
      </div>

      <v-button color="blue" @click="addOption" class="w-fit place-self-end">
        <i class="icon mgc_add_line"></i>
        {{ $t('Add Option') }}
      </v-button>
    </div>
    <!-- End Select, Radio and Checkbox -->

    <!-- Range, Year -->
    <div v-if="typeFilter === 'year' || typeFilter === 'range'" class="flex gap-2">
      <div>
        <v-input
            :placeholder="$t('From')"
            :value="options.from"
            @update:input="options.from = $event"
            type="number"
        />
        <p v-if="errors['options.from']" class="text-xs text-red-600">
          {{ errors['options.from'][0] }}
        </p>
      </div>

      <div>
        <v-input
            :placeholder="$t('To')"
            :value="options.to"
            @update:input="options.to = $event"
            type="number"
        />
        <p v-if="errors['options.to']" class="text-xs text-red-600">
          {{ errors['options.to'][0] }}
        </p>
      </div>
    </div>
    <!-- End Range, Year -->
  </div>
</template>

<script lang="ts" setup>
import {onMounted, ref, watch} from 'vue';
import {OptionFilters} from "@/stores/FilterStore";

const props     = defineProps<{
  typeFilter: string;
  options: OptionFilters;
  errors: any;
}>();
const localData = ref<{
  nameField: string;
  placeholder: string;
  options: OptionFilters[];
  from: string;
  to: string;
}>({
  nameField: '',
  placeholder: '',
  options: [],
  from: '',
  to: ''
});
const emit      = defineEmits(['update:options']);

watch(() => localData.value, (value) => {
 emit('update:options', value);
}, {deep: true});

const addOption     = () => {
  localData.value.options.push({
    name: '',
    value: ''
  });
};
const removeOption  = (index: number) => {
  localData.value.options.splice(index, 1);
};

onMounted(() => {
  localData.value = props.options;
});
</script>
