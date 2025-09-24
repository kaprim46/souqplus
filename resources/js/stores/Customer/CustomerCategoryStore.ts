import { defineStore } from 'pinia';
import { Category } from '@/types/Category';
import { nextTick } from 'vue';
import router from '@/routes';
import axios from '@/axios';

export const useCustomerCategoryStore = defineStore({
  id: 'customer-category',
  state: () => ({
    categories: [] as Category[],
    subCategories: [] as Category[],
    category: {} as Category,
    isLoading: true,
    filters: {} as {
      search?: string;
      order_by?: string;
    },
    subSubCategories: [] as Category[],
  }),
  getters: {},
  actions: {
    async fetch() {
      this.isLoading = true;
      let params = {} as {
        search?: string;
        order_by?: string;
      };

      if (this.filters.search) {
        params.search = this.filters.search;
      }

      if (this.filters.order_by) {
        params.order_by = this.filters.order_by;
      }
      try {
        const { data } = await axios.get(`/categories`, {
          params: {
            ...params,
          },
        });

        if (data.ok) {
          this.categories = data.categories;
        }

        await nextTick();
      } catch (error) {
      } finally {
        this.isLoading = false;
      }
    },
    async fetchSubCategories(id: number) {
      this.isLoading = true;

      try {
        const { data } = await axios.get(`/categories/${id}/sub/categories`);

        if (data.ok) {
          this.subCategories = data.categories;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoading = false;
      }
    },
    async fetchCategory(slug: string) {
      this.isLoading = true;

      try {
        const { data } = await axios.get(`/categories/${slug}`);

        if (data.ok) {
          this.category = data.category;
          return;
        }

        await router.push({ name: '404' });
      } catch (error) {
        await router.push({ name: '404' });
      } finally {
        this.isLoading = false;
      }
    },
    async fetchSubSubCategories(subCategoryId: number) {
      this.isLoading = true;
      try {
        const { data } = await axios.get(
          `/sub-categories/${subCategoryId}/sub-sub-categories`
        );
        if (data.ok) {
          this.subSubCategories = data.categories || [];
        } else {
          this.subSubCategories = [];
        }
      } catch (error) {
        this.subSubCategories = [];
      } finally {
        this.isLoading = false;
      }
    },
  },
});
