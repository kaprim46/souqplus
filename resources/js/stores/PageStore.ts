import { defineStore } from 'pinia'
import { Page} from "@/types/Page";
import {nextTick} from "vue";
import swal from "sweetalert2";
import {trans} from "laravel-vue-i18n";
import {notify} from "notiwind";
import axios from "@/axios";

export const usePageStore = defineStore({
    id: 'page',
    state: () => ({
        pages: [] as Page[],
        page: {} as Page,
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 1,
            total: 1,
        },
        isLoading: true,
        filters: {} as {
            search?: string,
            order_by?: string,
        }
    }),
    getters: {
    },
    actions: {
        async getPages(isPagination = false, isLess = false)  {
            this.isLoading             = true;
            let params             = {} as {
                search?: string,
                order_by?: string,
            };


            if(this.filters.search) {
                params.search        = this.filters.search;
            }

            if(this.filters.order_by) {
                params.order_by      = this.filters.order_by;
            }

            if(isPagination) {
                if(isLess) { this.pagination.current_page--; } else { this.pagination.current_page++; }
            }

            try {
                const { data }          = await axios.get(`/dashboard/pages?page=${this.pagination.current_page}`, {
                    params: {
                        ...params
                    }
                });

                if(data.ok) {
                    this.pages     = data.pages.data;

                    this.pagination    = {
                        current_page: data.pages.current_page,
                        last_page: data.pages.last_page,
                        per_page: data.pages.per_page,
                        total: data.pages.total,
                    }
                }

                await nextTick();

                this.isLoading          = false;
            } catch (error) {
                this.isLoading          = false;
            }
        },
        async onDelete (id: number) {
            /**
             * confirm sweet alert
             */
            const { value } = await swal.fire({
                title: trans('Are you sure?'),
                text: trans('You will not be able to recover this customer!'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: trans('Yes, delete it!'),
                cancelButtonText: trans('No, keep it')
            })

            if(value) {
                try {
                    const { data } = await axios.delete(`/dashboard/pages/${id}`);

                    if(data.ok) {
                        this.pages = this.pages.filter(page => page.id !== id);
                    }

                    await nextTick();

                    notify({
                        group: "dashboard",
                        ok: data.ok,
                        title: data.ok ? trans("Success") : trans("Error"),
                        text: data.message
                    }, 2000);
                } catch (error) {
                    console.log(error)
                }
            }
        },
        async fetchPage(id: number) {
            this.isLoading = true;

            try {
                const { data } = await axios.get(`/dashboard/pages/${id}`);

                if(data.ok) {
                    this.page = data.page;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false;
            }
        }
    }
});
