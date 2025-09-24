import { defineStore } from 'pinia'
import { nextTick } from "vue";
import swal from "sweetalert2";
import {trans, wTrans} from "laravel-vue-i18n";
import {notify} from "notiwind";
import {MessageInterFace} from "@/types/ContactUs";
import axios from "@/axios";

export const useContactUsStore = defineStore({
    id: 'contact-us',
    state: () => ({
        messages: [] as MessageInterFace[],
        message: {} as MessageInterFace,
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 1,
            total: 0,
        },
        isLoading: true,
        isReplying: false,
        replayErrors: {} as { [key: string]: string[] },
        sendErrors: {} as { [key: string]: string[] },
        filters: {} as {
            search?: string,
            order_by?: string,
        }
    }),
    getters: {
    },
    actions: {
        async getMessages(isPagination = false, isLess = false)  {
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
                const { data }          = await axios.get(`/dashboard/contact-us?page=${this.pagination.current_page}`, {
                    params: {
                        ...params
                    }
                });

                if(data.ok) {
                    this.messages     = data.messages.data;

                    this.pagination    = {
                        current_page: data.messages.current_page,
                        last_page: data.messages.last_page,
                        per_page: data.messages.per_page,
                        total: data.messages.total,
                    }
                }

                await nextTick();

                this.isLoading          = false;
            } catch (error) {
                this.isLoading          = false;
            }
        },
        async deleteMessage (id: number) {
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
                    const { data } = await axios.delete(`/dashboard/contact-us/${id}`);

                    if(data.ok) {
                        this.messages = this.messages.filter(msg => msg.id !== id);
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
        async fetchMessage(id: number) {
            this.isLoading = true;

            try {
                const { data } = await axios.get(`/dashboard/contact-us/${id}`);

                if(data.ok) {
                    this.message = data.contactUs;
                }
            } catch (error) {
                console.log(error)
            } finally {
                this.isLoading = false;
            }
        },
        async replyMessage(){
            this.isReplying = true;

            try {
                const { data } = await axios.post(`/dashboard/contact-us/${this.message.id}/reply`, {
                    reply: this.message.reply
                });

                if(data.ok) {
                    this.message = data.contactUs;
                }


                notify({
                    group: "dashboard",
                    ok: data.ok,
                    title: data.ok ? trans("Success") : trans("Error"),
                    text: data.message
                }, 2000);
            } catch (error: any) {
                if(error.response.status === 422) {
                    this.replayErrors = error.response.data.errors;
                }
            } finally {
                this.isReplying = false;
            }
        },
        async sendMessage() {
            this.isReplying = true;

            try {
                const { data } = await axios.post(`/contact-us`, {
                    name: this.message.name,
                    email: this.message.email,
                    subject: this.message.subject,
                    message: this.message.message,
                });

                this.sendErrors = {};

                if(data.ok) {
                    this.message = {} as MessageInterFace;
                }

                notify({
                    group: "dashboard",
                    ok: data.ok,
                    title: data.ok ? trans("Success") : trans("Error"),
                    text: data.message
                }, 2000);
        } catch (error: any) {
            if (error.response.status === 422) {
                this.sendErrors = error.response.data.errors;
            }
        } finally {
            this.isReplying = false;
        }
    }
    }
});
