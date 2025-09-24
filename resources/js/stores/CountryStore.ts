import { defineStore } from 'pinia'
import {nextTick} from "vue";
import { trans } from "laravel-vue-i18n";
import { notify } from "notiwind";
import swal from "sweetalert2";
import axios from "@/axios";

interface baseInterface {
    id?: number,
    name: string,
    cities_count?: number,
    country?: object
}

export const useCountryStore = defineStore({
    id: 'country',
    state: () => ({
        countries: [] as baseInterface[],
        cities: [] as baseInterface[],
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 1,
            total: 0,
        },
        isLoading: true,
        isLoadingAction: false,
        filters: {} as {
            search?: string,
            pagination?: number
        },
        filtersCities: {} as {
            search?: string,
            pagination?: number
        },
        paginationCities: {
            current_page: 1,
            last_page: 1,
            per_page: 1,
            total: 0,
        }
    }),
    getters: {
    },
    actions: {
        async fetchCountries(isPagination = false, isLess = false)  {
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
                const { data }          = await axios.get(`/countries`, {
                    params: {
                        ...params
                    }
                });

                if(data.ok) {
                    if(this.filters.pagination) {
                        this.countries      = data.countries.data;
                        this.pagination     = {
                            current_page: data.countries.current_page,
                            last_page: data.countries.last_page,
                            per_page: data.countries.per_page,
                            total: data.countries.total,
                        }
                    } else {
                        this.countries      = data.countries;
                    }
                }

                await nextTick();

                this.isLoading          = false;
            } catch (error) {
                this.isLoading          = false;
            }
        },
        async onDeleteCountry (id: number) {
            /**
             * confirm sweet alert
             */
            const { value } = await swal.fire({
                title: trans('Are you sure?'),
                text: trans('You will not be able to recover this country!'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: trans('Yes, delete it!'),
                cancelButtonText: trans('No, keep it')
            })

            if(value) {
                try {
                    const { data } = await axios.delete(`/dashboard/countries/${id}`);

                    if(data.ok) {
                        this.countries = this.countries.filter((item) => item.id !== id);
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
        async onUpdateCountry(country: baseInterface) {
            if(!country.id) return;

            this.isLoadingAction = true;

           try {
               const { data } = await axios.put(`/dashboard/countries/${country.id}`, country);

               if(data.ok) {
                   this.countries = this.countries.map((item) => {
                       if(item.id === country.id) {
                           return country;
                       }

                       return item;
                   });
               }

               await nextTick();

               notify({
                   group: "dashboard",
                   ok: data.ok,
                   title: data.ok ? trans("Success") : trans("Error"),
                   text: data.message
                }, 2000);

                return data.ok;
              } catch (error) {
                console.log(error)
              } finally {
                this.isLoadingAction = false;
             }
        },
        async onCreateCountry(country: baseInterface) {

            this.isLoadingAction = true;

            try {
                const {data} = await axios.post(`/dashboard/countries`, country);

                if (data.ok) {
                    this.countries.push(data.country);
                }

                await nextTick();

                notify({
                    group: "dashboard",
                    ok: data.ok,
                    title: data.message,
                    text: data.message
                }, 2000);

                return data.ok;
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoadingAction = false;
            }
        },
        async fetchCities(isPagination = false, isLess = false, countryId?: number)  {
            this.isLoading             = true;
            let params             = {} as {
                search?: string,
                pagination?: number,
                page?: number
            };

            if(this.filtersCities.search) {
                params.search        = this.filtersCities.search;
            }

            if(this.filtersCities.pagination) {
                params.pagination    = 1;
            }

            if(isPagination && this.filtersCities.pagination) {
                if(isLess)
                    this.paginationCities.current_page--;
                else
                    this.paginationCities.current_page++;

                params.page          = this.paginationCities.current_page;
            }


            try {
                const { data }          = await axios.get(countryId ? `/cities/${countryId}` : `/cities` , {
                    params: {
                        ...params
                    }
                });

                if(data.ok) {

                    if(this.filtersCities.pagination) {
                        this.cities      = data.cities.data;
                        this.paginationCities     = {
                            current_page: data.cities.current_page,
                            last_page: data.cities.last_page,
                            per_page: data.cities.per_page,
                            total: data.cities.total,
                        }
                    } else {
                        this.cities      = data.cities;
                    }
                }

                await nextTick();

                this.isLoading          = false;
            } catch (error) {
                this.isLoading          = false;
            }
        },
        async onDeleteCity (id: number) {
            /**
             * confirm sweet alert
             */
            const { value } = await swal.fire({
                title: trans('Are you sure?'),
                text: trans('You will not be able to recover this country!'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: trans('Yes, delete it!'),
                cancelButtonText: trans('No, keep it')
            })

            if(value) {
                try {
                    const { data } = await axios.delete(`/dashboard/cities/${id}`);

                    if(data.ok) {
                        this.cities = this.cities.filter((item) => item.id !== id);
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
        async onUpdateCity(city: baseInterface) {
            if(!city.id) return;

            this.isLoadingAction = true;

            try {
                const { data } = await axios.put(`/dashboard/cities/${city.id}`, city);

                if(data.ok) {
                    this.cities = this.cities.map((item) => {
                        if(item.id === city.id) {
                            return city;
                        }

                        return item;
                    });
                }

                await nextTick();

                notify({
                    group: "dashboard",
                    ok: data.ok,
                    title: data.ok ? trans("Success") : trans("Error"),
                    text: data.message
                }, 2000);

                return data.ok;
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoadingAction = false;
            }
        },
        async onCreateCity(city: baseInterface) {

            this.isLoadingAction = true;

            try {
                const {data} = await axios.post(`/dashboard/cities`, city);

                if (data.ok) {
                    this.cities.push(data.city);
                }

                await nextTick();

                notify({
                    group: "dashboard",
                    ok: data.ok,
                    title: data.message,
                    text: data.message
                }, 2000);

                return data.ok;
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoadingAction = false;
            }
        },
    },
});
