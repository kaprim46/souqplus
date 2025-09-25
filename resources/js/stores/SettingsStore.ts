import { defineStore } from 'pinia';
import { notify } from 'notiwind';
import { wTrans } from 'laravel-vue-i18n';
import { nextTick } from 'vue';
import router from '@/routes';
import axios from '@/axios';

export const useSettingsStore = defineStore({
  id: 'settings',
  state: () => ({
    appSettings: {
      app_name: '',
      app_description: '',
      app_keywords: '',
      app_email: '',
      app_robots: '',
      app_url: '',
      app_timezone: '',
      app_currency: '',
      app_logo: '',
      app_favicon: '',
      warning_comments_message: '',
      app_social_media: {
        facebook: '',
        twitter: '',
        instagram: '',
        linkedin: '',
      },
      app_whatsapp: '',
    } as AppSettings,
    settings: {} as AppSettings,
    isLoading: true,
    isSubmitting: false,
  }),
  getters: {},
  actions: {
    async setLocale(locale: string) {
      try {
        const { data } = await axios.post<{
          ok: boolean;
          message: string;
          lang?: string;
        }>(`/locale/${locale}`);

        if (data.ok) {
          localStorage.setItem('lang', locale);

          await nextTick();

          router.go(0);

          return;
        }

        notify(
          {
            group: 'dashboard',
            ok: data.ok,
            title: data.ok ? wTrans('Success') : wTrans('Error'),
            text: data.message,
          },
          2000
        ); // 2s
      } catch (error) {
        console.log(error);
      }
    },
    async fetch() {
      this.isLoading = true;
      try {
        const { data } = await axios.get<{
          ok: boolean;
          settings: AppSettings;
        }>('/settings');

        if (data.ok) {
          this.settings = data.settings;
          for (const key in data.settings) {
            if (key === 'app_social_media') {
              this.appSettings[key] = JSON.parse(data.settings[key]);
            } else if (key === 'app_logo' || key === 'app_favicon') {
              // Construct full URL for logo and favicon if they don't already have a full URL
              const value = data.settings[key];
              if (value && !value.startsWith('http')) {
                const baseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin;
                const folder = key === 'app_logo' ? 'logo' : 'favicon';
                // Only assign string to keys that are of type string
                (this.appSettings as any)[key] = `${baseUrl}/storage/${folder}/${value}`;
              } else {
                (this.appSettings as any)[key] = value;
              }
            } else {
              this.appSettings[key as keyof AppSettings] = data.settings[key];
            }
          }
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoading = false;
      }
    },
    async submit() {
      this.isSubmitting = true;
      const dataForm = new FormData();

      dataForm.append('app_name', this.appSettings.app_name);
      dataForm.append('app_description', this.appSettings.app_description);
      dataForm.append('app_keywords', this.appSettings.app_keywords);
      dataForm.append('app_email', this.appSettings.app_email);
      dataForm.append('app_robots', this.appSettings.app_robots);
      dataForm.append('app_url', this.appSettings.app_url);
      dataForm.append('app_timezone', this.appSettings.app_timezone);
      dataForm.append('app_currency', this.appSettings.app_currency);
      
      // Only append files if they are File objects (newly uploaded)
      if ((this.appSettings.app_logo as any) instanceof File) {
        dataForm.append('app_logo', this.appSettings.app_logo);
      }
      if ((this.appSettings.app_favicon as any) instanceof File) {
        dataForm.append('app_favicon', this.appSettings.app_favicon);
      }
      
      dataForm.append(
        'app_social_media',
        JSON.stringify(this.appSettings.app_social_media)
      );
      dataForm.append('app_whatsapp', this.appSettings.app_whatsapp);
      dataForm.append(
        'warning_comments_message',
        this.appSettings.warning_comments_message
      );
      dataForm.append('_method', 'PUT');

      try {
        const { data } = await axios.post<{
          ok: boolean;
          settings: AppSettings;
          message: string;
        }>('/dashboard/settings', dataForm, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });

        if (data.ok) {
          // Update the settings with the response
          for (const key in data.settings) {
            if (key === 'app_social_media') {
              this.appSettings[key] = typeof data.settings[key] === 'string' 
                ? JSON.parse(data.settings[key]) 
                : data.settings[key];
            } else if (key === 'app_logo' || key === 'app_favicon') {
              // Construct full URL for newly uploaded images
              const value = data.settings[key];
              if (value && !value.startsWith('http')) {
                const baseUrl = import.meta.env.VITE_API_BASE_URL || window.location.origin;
                const folder = key === 'app_logo' ? 'logo' : 'favicon';
                (this.appSettings as any)[key] = `${baseUrl}/storage/${folder}/${value}`;
              } else {
                this.appSettings[key as keyof AppSettings] = value;
              }
            } else {
              this.appSettings[key as keyof AppSettings] = data.settings[key];
            }
          }
        }

        notify(
          {
            group: 'dashboard',
            ok: data.ok,
            title: data.ok ? wTrans('Success') : wTrans('Error'),
            text: data.message,
          },
          2000
        ); // 2s
      } catch (error) {
        console.log(error);
        
        notify(
          {
            group: 'dashboard',
            ok: false,
            title: wTrans('Error'),
            text: wTrans('Something went wrong, please try again later'),
          },
          2000
        ); // 2s
      } finally {
        this.isSubmitting = false;
      }
    },
  },
});