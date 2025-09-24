import { defineStore } from 'pinia'
import { nextTick } from 'vue'
import swal from 'sweetalert2'
import { trans } from 'laravel-vue-i18n'
import { notify } from 'notiwind'
import axios from '@/axios'

export interface Slider {
    id?: number
    title: string
    image: string|null|File
    image_url?: string
    description: string
    link: string
}

export const useSliderStore = defineStore({
    id: 'explore-slider',
    state: () => ({
        sliders: [] as Slider[],
        slider: {} as Slider,
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 1,
            total: 1,
        },
        isLoading: true,
        filters: {} as {
            search?: string
            order_by?: string;
            pagination?: number|boolean
        },
        isLoadingAction: false,
        errors: {} as { [key: string]: string[] }
    }),
    getters: {},
    actions: {
        async getSliders(isPagination = false, isLess = false) {
            this.isLoading = true
            let params = {} as {
                search?: string
                order_by?: string
                pagination?: number|boolean
                page?: number
            }

            if (this.filters.search) {
                params.search = this.filters.search
            }

            if (this.filters.order_by) {
                params.order_by = this.filters.order_by
            }

            if(this.filters.pagination) {
                params.pagination = this.filters.pagination


                if (isPagination) {
                    if (isLess) { this.pagination.current_page-- } else { this.pagination.current_page++ }
                }

                params.page = this.pagination.current_page
            }


            try {
                const { data } = await axios.get(`/explore-slider`, {
                    params: {
                        ...params
                    }
                })

                if (data.ok) {
                    this.sliders = this.filters.pagination ? data.exploreSliders.data : data.exploreSliders;

                    console.log(this.sliders)

                    if(this.filters.pagination) {
                        this.pagination = {
                            current_page: data.exploreSliders.current_page,
                            last_page: data.exploreSliders.last_page,
                            per_page: data.exploreSliders.per_page,
                            total: data.exploreSliders.total,
                        }
                    }
                }

                await nextTick()

                this.isLoading = false
            } catch (error) {
                this.isLoading = false
            }
        },
        async onDelete(id: number) {
            const { value } = await swal.fire({
                title: trans('Are you sure?'),
                text: trans('You will not be able to recover this slider!'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: trans('Yes, delete it!'),
                cancelButtonText: trans('No, keep it')
            })

            if (value) {
                try {
                    const { data } = await axios.delete(`/dashboard/explore-slider/${id}`)

                    if (data.ok) {
                        this.sliders = this.sliders.filter(slider => slider.id !== id)
                    }

                    await nextTick()

                    notify({
                        group: 'dashboard',
                        ok: data.ok,
                        title: data.ok ? trans('Success') : trans('Error'),
                        text: data.message
                    }, 2000)
                } catch (error) {
                    console.log(error)
                }
            }
        },
        async createSlider(slider: Slider) {
            this.isLoadingAction = true

            try {
                let form = new FormData();
                if(slider.title)
                    form.append('title', slider.title)

                if(slider.description)
                    form.append('description', slider.description)

                if(slider.link)
                    form.append('link', slider.link)

                form.append('image', slider.image as File)

                this.errors = {}

                const { data } = await axios.post('/dashboard/explore-slider', form)

                if (data.ok) {
                    this.sliders.unshift(data.slider)

                    //
                    notify({
                        group: 'dashboard',
                        ok: data.ok,
                        title: data.ok ? trans('Success') : trans('Error'),
                        text: data.message
                    }, 2000)
                }

                return data.ok;
            } catch (error: any) {
               if(error.response.status === 422) {
                    this.errors = error.response.data.errors
               }
            } finally {
                this.isLoadingAction = false
            }
        },
        async updateSlider(slider: Slider) {
            this.isLoadingAction = true

            try {
                let form = new FormData();
                if (slider.image && typeof slider.image === 'object') {
                    form.append('image', slider.image as File)
                }

                if(slider.title)
                    form.append('title', slider.title)

                if(slider.description)
                    form.append('description', slider.description)

                if(slider.link)
                    form.append('link', slider.link)

                form.append('_method', 'PUT')

                const { data } = await axios.post(`/dashboard/explore-slider/${slider.id}`, form)

                this.errors = {}

                if (data.ok) {
                    const index = this.sliders.findIndex(s => s.id === slider.id)
                    if (index !== -1) {
                        this.sliders[index] = data.slider
                    }

                    notify({
                        group: 'dashboard',
                        ok: data.ok,
                        title: data.ok ? trans('Success') : trans('Error'),
                        text: data.message
                    }, 2000)
                }

                return data.ok;
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoadingAction = false
            }
        }
    }
})
