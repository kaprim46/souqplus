import { defineStore } from 'pinia'
import { Category } from '@/types/Category'
import {nextTick} from "vue";
import { trans } from "laravel-vue-i18n";
import { notify } from "notiwind";
import swal from "sweetalert2";
import axios from "@/axios";

export const useCategoryStore = defineStore({
    id: 'category',
    state: () => ({
        categories: [] as Category[],
        subCategories: [] as Category[],
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 1,
            total: 0,
        },
        isLoading: true,
        filters: {
            search: '',
            order_by: '',
            pagination: 1
        } as {
            search?: string,
            order_by?: string,
            pagination?: number

        }
    }),
    getters: {
    },
    actions: {
        async fetch(isPagination = false, isLess = false)  {
            this.isLoading             = true;
            let params             = {} as {
                search?: string,
                order_by?: string,
                pagination?: number,
                page?: number
            };


            if(this.filters.search) {
                params.search        = this.filters.search;
            }

            if(this.filters.order_by) {
                params.order_by      = this.filters.order_by;
            }

            if(this.filters.pagination) {
                params.pagination    = 1;
            }

            if(isPagination && this.filters.pagination) {
                if(isLess)
                    this.pagination.current_page--;
                else
                    this.pagination.current_page++;

                params.page          = this.pagination.current_page;
            }


            try {
                const { data }          = await axios.get(`/dashboard/categories`, {
                    params: {
                        ...params
                    }
                });

                if(data.ok) {
                    if(this.filters.pagination) {
                        this.categories     = data.categories.data;

                        this.pagination    = {
                            current_page: data.categories.current_page,
                            last_page: data.categories.last_page,
                            per_page: data.categories.per_page,
                            total: data.categories.total,
                        }
                    } else {
                        this.categories     = data.categories;
                    }
                }

                await nextTick();

                this.isLoading          = false;
            } catch (error) {
                this.isLoading          = false;
            }
        },
        async fetchSubCategories(id: number) {
            try {
                const { data }          = await axios.get(`/categories/${id}/sub/categories`);

                if(data.ok) {
                    this.subCategories  = data.categories;
                }

                return data;
            } catch (error) {
                console.log(error)
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
                    const { data } = await axios.delete(`/dashboard/categories/${id}`);

                    if(data.ok) {
                        this.categories = this.categories.filter(category => category.id !== id);
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
        }
    },
});
