import { Advertisement } from '@/types/Advertisements';
import { trans } from 'laravel-vue-i18n';
import { defineStore } from 'pinia';
import { notify } from 'notiwind';
import { nextTick } from 'vue';
import swal from 'sweetalert2';
import axios from '@/axios';

export const useAdvertisementStore = defineStore({
  id: 'advertisement',
  state: () => ({
    advertisements: [] as Advertisement[],
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 1,
      total: 0,
    },
    isLoading: true,
    filters: {} as {
      search?: string;
      order_by?: string;
      user_id?: number;
    },
  }),
  getters: {},
  actions: {
    async fetch(isPagination = false, isLess = false) {
      this.isLoading = true;
      let params = {
        pagination: 1,
      } as {
        search?: string;
        order_by?: string;
        pagination?: number;
        user_id?: number;
      };

      if (this.filters.search) {
        params.search = this.filters.search;
      }

      if (this.filters.order_by) {
        params.order_by = this.filters.order_by;
      }

      if (this.filters.user_id) {
        params.user_id = this.filters.user_id;
      }

      if (isPagination) {
        if (isLess) {
          this.pagination.current_page--;
        } else {
          this.pagination.current_page++;
        }
      }

      try {
        const { data } = await axios.get(
          `/dashboard/advertisements?page=${this.pagination.current_page}`,
          {
            params: {
              ...params,
            },
          }
        );

        if (data.ok) {
          this.advertisements = data.advertisements.data;

          this.pagination = {
            current_page: data.advertisements.current_page,
            last_page: data.advertisements.last_page,
            per_page: data.advertisements.per_page,
            total: data.advertisements.total,
          };
        }

        await nextTick();

        this.isLoading = false;
      } catch (error) {
        this.isLoading = false;
      }
    },
    async onDelete(id: number) {
      /**
       * confirm sweet alert
       */
      const { value } = await swal.fire({
        title: trans('Are you sure?'),
        text: trans('You can restore this advertisement later.'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: trans('Yes, delete it!'),
        cancelButtonText: trans('No, keep it'),
      });

      if (value) {
        try {
          const { data } = await axios.delete(
            `/dashboard/advertisements/${id}`
          );

          if (data.ok) {
            let ad = this.advertisements.find(
              (advertisement) => advertisement.id === id
            );
            if (ad) ad.deleted_at = new Date().toISOString();
          }

          await nextTick();

          notify(
            {
              group: 'dashboard',
              ok: data.ok,
              title: data.ok ? trans('Success') : trans('Error'),
              text: data.message,
            },
            2000
          );
        } catch (error) {
          console.log(error);
        }
      }
    },
    async onRestore(id: number) {
      try {
        const { data } = await axios.delete(`/dashboard/advertisements/${id}`);

        if (data.ok) {
          let ad = this.advertisements.find(
            (advertisement) => advertisement.id === id
          );
          if (ad) ad.deleted_at = null;
        }

        await nextTick();

        notify(
          {
            group: 'dashboard',
            ok: data.ok,
            title: data.ok ? trans('Success') : trans('Error'),
            text: data.message,
          },
          2000
        );
      } catch (error) {
        console.log(error);
      }
    },
  },
});
