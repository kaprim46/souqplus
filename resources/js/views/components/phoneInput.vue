<template>
    <div class="relative" dir="ltr">
        <input
            type="text"
            class="py-3 px-4 ps-[8rem] block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600" :class="{'border border-red-500 dark:border-red-500': error || hasError}"
            :id="id"
            :placeholder="$t('Phone Number')"
            :disabled="disabled"
            :name="name"
            v-model="phone"
            @change="() => { $emit('update:phoneNumber', phone) }"
        />
        <div class="absolute inset-y-0 start-0 flex items-center text-gray-500 ps-px">
            <label for="country_code" class="sr-only">
                {{ $t("Country") }}
            </label>
            <select
                    @change="() => { $emit('update:countryCode', countryCode) }"
                    v-model="countryCode"
                    id="country_code"
                    name="country_code"
                    class="block w-full border-transparent rounded-lg appearance-none"
                    style="background-position: right center;"
            >
                <option disabled value="">{{ $t("#") }}</option>
                <option v-for="country in countries" :value="country.dial_code" :selected="country.dial_code === valueCountryCode">
                    {{ country.emoji }} {{ country.dial_code }}
                </option>
            </select>
        </div>
    </div>
</template>

<script lang="ts" setup>
/**
 * Open counters.json file in public folder and add your country code
 */
import {ref, watch, onBeforeMount, onMounted, nextTick} from "vue";
import { isValidNumber, format, parsePhoneNumber } from "libphonenumber-js";

/**
 * Get the country calling code for the selected country
 */
const file = "/countries.json";

/**
 * Props
 */
const props = defineProps({
    value: {
        type: String,
        default: "",
        required: false,
    },
    valueCountryCode: {
        type: String,
        default: "MA",
        required: false,
    },
    name: {
        type: String,
        default: "",
        required: false,
    },
    hasError: {
        type: Boolean,
        default: false,
        required: false,
    },
    disabled: {
        type: Boolean,
        default: false,
        required: false,
    },
    id: {
        type: String,
        default: "",
        required: false,
    },
});

const countryCode   = ref("");
const phone         = ref("");
const correctPhone  = ref("");
const countries     = ref<{ code: string; dial_code: string; emoji: string }[]>([]);
const error         = ref(false);
const emit          = defineEmits(["onPhoneUpdate", "update:phoneNumber", "update:countryCode"]);

/**
 * Watch for changes in selectedCountry and phone
 */
watch([countryCode, phone], ([countryCode, phone]) => {
    if (countryCode && phone) {
        const phoneNumberTerm = phone.replace(/\D/g, "");
        const countryCodeTerm = countryCode.replace(/\D/g, "");
        const parsedPhoneNumber = `+${countryCodeTerm}${phoneNumberTerm}`;
        error.value = !isValidNumber(parsedPhoneNumber);

        emit("onPhoneUpdate", parsedPhoneNumber);

        /**
         * Format the phone number
         */
        if (!error.value) {
            correctPhone.value = format(
                parsedPhoneNumber,
                "INTERNATIONAL"
            ).replace(/ /g, "");
        }
    }
});

onMounted(async () => {
    const response              = await fetch(file);
    const countriesResponse     = await response.json();

    if (countriesResponse.length) {
        countries.value = countriesResponse;
    }

    await nextTick();

    if (
        props.value &&
        props.valueCountryCode
    ) {
        const countryCodeInt  = parseInt(props.valueCountryCode);
        const dialCode        = countries.value.find((country) => country.dial_code === `+${countryCodeInt}`)?.code;

        if (!dialCode) return;

        const parsedPhoneNumber = parsePhoneNumber(props.value, dialCode);

        countryCode.value   = `+${parsedPhoneNumber.countryCallingCode}`;
        phone.value         = parsedPhoneNumber.nationalNumber;
        emit("onPhoneUpdate", `${parsedPhoneNumber.countryCallingCode}${parsedPhoneNumber.nationalNumber}`);
    }
});
</script>
