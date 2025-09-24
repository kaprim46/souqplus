<template>
    <section id="home" class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <h1 class="text-2xl font-bold flex items-center gap-1 col-span-2">
            <i class="icon mgc_dashboard_line"></i>
            {{ $t('Dashboard') }}
        </h1>

        <!-- Online Sessions -->
        <div class="bg-white rounded-lg shadow-lg p-5">
            <v-skeleton v-if="!isLoaded" />
            <template v-else>
                <div class="flex justify-between gap-5">
                    <div class="flex flex-col">
                        <span class="text-gray-400">
                            {{ $t('Online Sessions') }}
                        </span>
                        <span class="text-2xl font-bold">
                            {{ totalSessions }}
                        </span>
                    </div>
                </div>
                <span class="text-gray-400">
                    {{ $t('Sessions over time') }}
                </span>

                <apexchart type="area" :options="optionsSessions" :series="seriesSessions"></apexchart>
            </template>
        </div>
        <!--
        <div class="bg-white rounded-lg shadow-lg p-5">
            <div class="flex justify-between gap-5">
                <div class="flex flex-col">
                    <span class="text-gray-400">
                        {{ $t('Advertisments') }}
                    </span>
                    <span class="text-2xl font-bold">1,200</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-bold">1,200</span>
                </div>
            </div>
            <span class="text-gray-400">
                {{ $t('Advertisments over years') }}
            </span>
            <apexchart  type="bar" :options="options" :series="series"></apexchart>
        </div>
        -->
    </section>
</template>

<script lang="ts" setup>
import {onMounted, ref} from 'vue';


const isLoaded = ref(false);

const optionsSessions = ref({});

const seriesSessions = ref([
    {
        name: 'series-1',
        data: [30, 40, 45, 50, 49, 60, 70, 91],
    },
]);

const totalSessions = ref(0);

const getOnlineSessions = async () => {
    isLoaded.value = false;

    try {
        const { data } = await axios.get('/dashboard/analytics');

        if(data.ok) {
            optionsSessions.value   = data.options;
            seriesSessions.value    = data.series;
            totalSessions.value     = data.total;
        }
    } catch (error) {
        console.log(error);
    } finally {
        isLoaded.value = true;
    }
};

onMounted(async () => {
    await getOnlineSessions();
});
</script>
