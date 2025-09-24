import { defineStore } from 'pinia'
import {nextTick} from "vue";
import axios from "@/axios";
import {notify} from "notiwind";
import {trans} from "laravel-vue-i18n";

export interface OptionFilters {
    nameField: string,
    placeholder: string,
    options?: any[],
    from?: string,
    to?: string
}

export interface Filter {
    id?: number;
    name: string;
    type: string;
    options?: OptionFilters;
}

export const useFilterStore = defineStore({
    id: 'filter',
    state: () => ({
        data_filter: {
            name: '',
            type: '',
            options: {
                nameField: '',
                placeholder: '',
                options: [],
                from: '',
                to: ''
            }
        } as Filter,
        data_filters: [] as Filter[],
        data_filters_category: [] as Filter[],
        data_filters_sub_category: [] as Filter[],
        isLoadingAction: false,
        filters: {} as {
            search?: string,
            pagination?: number
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 1,
            total: 0,
        },
        isLoading: false,
        errors: {} as any,
        any_data: []
    }),
    getters: {
        setFilterType: (state) => (type: string) => {
           state.data_filter.type = type;
        },
        setData: (state) => (data: Filter) => {
            state.data_filter = data;
        }
    },
    actions: {
        async fetchFilters(isPagination = false, isLess = false)  {
            this.isLoading             = true;

            let params             = {} as {
                search?: string,
                pagination?: number,
                page?: number
            };

            if(this.filters.search) {
                params.search        = this.filters.search;
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
                const { data }          = await axios.get(`/dashboard/filters`, {
                    params: {
                        ...params
                    }
                });

                if(data.ok) {
                    if(this.filters.pagination) {
                        this.data_filters      = data.data_filters.data;
                        this.pagination     = {
                            current_page: data.data_filters.current_page,
                            last_page: data.data_filters.last_page,
                            per_page: data.data_filters.per_page,
                            total: data.data_filters.total,
                        }
                    } else {
                        this.data_filters      = data.data_filters;
                    }
                }

                await nextTick();

                this.isLoading          = false;
            } catch (error) {
                this.isLoading          = false;
            }
        },
        async storeFilter() {
            this.isLoadingAction = true;

            try {
                const { data }      = await axios.post(`/dashboard/filters`, this.data_filter);

                this.errors         = {};

                if(data.ok) {
                    this.data_filters.push(data.data);
                    this.data_filter = {
                        name: '',
                        type: '',
                        options: {
                            nameField: '',
                            placeholder: '',
                            options: [],
                            from: '',
                            to: ''
                        }
                    };
                }

                notify({
                    group: "dashboard",
                    ok: data.ok,
                    title: data.ok ? trans("Success") : trans("Error"),
                    text: data.message
                }, 2000);


                this.isLoadingAction = false;
            } catch (error: any) {
                if(error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }

                this.isLoadingAction = false;
            }
        },
        async updateFilter() {
            if(!this.data_filter.id) return;

            this.isLoadingAction = true;

            try {
                const { data }      = await axios.put(`/dashboard/filters/${this.data_filter.id}`, {
                    ...this.data_filter,
                    _method :'PUT'
                });

                this.errors         = {};

                notify({
                    group: "dashboard",
                    ok: data.ok,
                    title: data.ok ? trans("Success") : trans("Error"),
                    text: data.message
                }, 2000);


                this.isLoadingAction = false;
            } catch (error: any) {
                if(error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }

                this.isLoadingAction = false;
            }
        },
        async getFiltersByCategory(categoryID: number) {
            this.isLoading = true;

            try {
                const { data } = await axios.get(`/filters/category/${categoryID}`);

                if(data.ok) {
                    this.data_filters_category = data.filters;
                }
            } catch(e: any) {
                console.log(e);
            } finally {
                this.isLoading = false;
            }
        },
        async getFiltersBySubCategory(categoryID: number) {
            this.isLoading = true;

            try {
                const { data } = await axios.get(`/filters/sub/category/${categoryID}`);

                if(data.ok) {
                    this.data_filters_category = data.filters;
                }
            } catch(e: any) {
                console.log(e);
            } finally {
                this.isLoading = false;
            }
        },
        resetAll() {
            this.data_filter = {
                name: '',
                type: '',
                options: {
                    nameField: '',
                    placeholder: '',
                    options: [],
                    from: '',
                    to: ''
                }
            };
            this.errors = {};
        }
    }
});
