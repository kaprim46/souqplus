import { defineStore } from 'pinia';
import axios from '@/axios';
import { SubSubCategory } from '@/types/Category';



interface State {
  subSubCategories: SubSubCategory[];
  isLoading: boolean;
}

export const useSubSubCategoryStore = defineStore('subSubCategory', {
  state: (): State => ({
    subSubCategories: [],
    isLoading: false,
  }),

  actions: {
    async fetchSubSubCategories(subCategoryId: number) {
      this.isLoading = true;
      try {
        const { data } = await axios.get(
          `/sub-categories/${subCategoryId}/sub-sub-categories`
        );
        if (data.ok) {
          this.subSubCategories = data.data;
        }
      } catch (error) {
        console.error('Error fetching sub-subcategories:', error);
      } finally {
        this.isLoading = false;
      }
    },
  },
});
