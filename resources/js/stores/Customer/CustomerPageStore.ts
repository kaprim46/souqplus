import { defineStore } from 'pinia'
import { Page} from "@/types/Page";
import router from "@/routes";
import axios from "@/axios";

export const useCustomerPageStore = defineStore({
    id: 'customer-page',
    state: () => ({
        pages: [] as Page[],
        page: {} as Page,
        isLoading: true,
    }),
    getters: {
    },
    actions: {
        async getPages()  {
            this.isLoading             = true;

            try {
                const { data }          = await axios.get(`/pages`);

                if(data.ok) {
                    this.pages     = data.pages;
                    return;
                }

                await router.push({ name: '404' });
            } catch (error) {
                this.isLoading          = false;
                await router.push({ name: '404' });
            } finally {
                this.isLoading          = false;
            }
        },
        async fetchPage(slug: string) {
            this.isLoading = true;

            try {
                const { data } = await axios.get(`/pages/${slug}`);

                if(data.ok) {
                    this.page = data.page;
                    return;
                }
                await router.push({ name: '404' });
            } catch (error) {
                await router.push({ name: '404' });
            } finally {
                this.isLoading = false;
            }
        }
    }
});
