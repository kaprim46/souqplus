import { ComponentCustomProperties } from 'vue';
import {statusEnum} from "@/types/Advertisements";

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $t: (key: string, params?: Record<string, any>) => string;
        $assetUrl: string;
        $defaultLang: string;
        $statusHexColor: (status: statusEnum) => statusEnum|string;
    }
}