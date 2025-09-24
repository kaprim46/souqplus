import { defineStore } from 'pinia';
import { BusinessInfo } from '@/types/User';
import routes from '@/routes';
import { notify } from 'notiwind';
import { trans, wTrans } from 'laravel-vue-i18n';
import axios from '@/axios';
import router from '@/routes';

export const useMainStore = defineStore({
  id: 'main',
  state: () => ({
    store: {} as BusinessInfo,
    errors: {} as any,
    isLoadingMe: false,
    isLoading: false,
  }),
  getters: {},
  actions: {
    uploadStoreLogo(event: Event) {
      const file = (event.target as HTMLInputElement).files?.[0];
      const reader = new FileReader();

      if (file) {
        reader.readAsDataURL(file);
      }

      reader.onload = () => {
        if (this.store) {
          this.store.business_logo = file;
          this.store.business_logo_url = reader.result as string;
        }
      };

      reader.onerror = () => {
        console.log('Error: ', reader.error);
      };
    },
    uploadCoverImage(event: Event) {
      const file = (event.target as HTMLInputElement).files?.[0];
      const reader = new FileReader();

      if (file) {
        reader.readAsDataURL(file);
      }

      reader.onload = () => {
        if (this.store) {
          this.store.cover_image = file;
          this.store.business_cover_url = reader.result as string;
        }
      };

      reader.onerror = () => {
        console.log('Error: ', reader.error);
      };
    },
    async fetchProfile() {
      this.isLoadingMe = true;
      try {
        const { data } = await axios.get('/me');

        if (data.ok) {
          this.store = data.user.business_info;
        }
      } catch (e) {
        return false;
      } finally {
        this.isLoadingMe = false;
      }
    },
    async onStoreUpdate() {
      this.isLoading = true;

      try {
        let dataForm = new FormData();

        dataForm.append('business_type', this.store?.business_type ?? '');
        dataForm.append('business_name', this.store?.business_name ?? '');
        dataForm.append(
          'business_district',
          this.store?.business_district ?? ''
        );
        dataForm.append('business_bio', this.store?.business_bio ?? '');
        dataForm.append('phone_number', this.store?.phone_number ?? '');
        dataForm.append('country_code', this.store?.country_code ?? '');
        dataForm.append('business_email', this.store?.business_email ?? '');
        dataForm.append('city_id', this.store?.city?.id?.toString() ?? '');
        dataForm.append(
          'country_id',
          this.store?.country?.id?.toString() ?? ''
        );

        if (this.store?.business_logo) {
          dataForm.append('business_logo', this.store?.business_logo);
        }

        if (this.store?.cover_image) {
          dataForm.append('cover_image', this.store?.cover_image);
        }

        dataForm.append(
          'business_location',
          JSON.stringify(this.store?.business_location)
        );

        dataForm.append('_method', 'PUT');

        const { data } = await axios.post(`/me/store`, dataForm);

        this.errors = {};

        notify(
          {
            group: 'dashboard',
            ok: data.ok,
            title: data.ok ? wTrans('Success') : wTrans('Error'),
            text: data.message,
          },
          2000
        );
      } catch (e: any) {
        if (e.response.status === 422) {
          this.errors = e.response.data.errors;
        }
      } finally {
        this.isLoading = false;
      }
    },
  },
});
