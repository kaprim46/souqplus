import axios, {  AxiosResponse, AxiosError } from 'axios';

import {nextTick} from "vue";
import router from "@/routes";

const language           = localStorage.getItem('lang') ?? 'en';
const token          = localStorage.getItem('token') ?? null;
axios.defaults.baseURL          = import.meta.env.VITE_APP_API_URL;
console.log('API URL:', import.meta.env.VITE_APP_API_URL);
axios.defaults.withCredentials  = true;
axios.defaults.withXSRFToken    = true;

if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

axios.defaults.headers.common['X-localization'] = language;

axios.interceptors.response.use(
    (response: AxiosResponse) => response,
    async (error: AxiosError) => {
        if (error.response && error.response.status === 401) {

        }else if(error.response && error.response.status === 403){
            await router.push({name: '404'});
        }else if(error.response && error.response.status === 404){
            await router.push({name: '404'});
        }

        return Promise.reject(error);
    }
);

export default axios;
