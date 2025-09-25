import { defineStore } from 'pinia';
import axios from '@/axios';
import { ref } from 'vue';

export interface SubSubCategory {
    id?: number;
    name: string;
    status: 'active' | 'inactive';
    sub_category_id: number | null;
}

export interface ParentSubCategory {
    id: number;
    name: string;
}

export const useDashboardSubSubCategoryStore = defineStore('dashboardSubSubCategory', () => {
    const subSubCategories = ref<SubSubCategory[]>([]);
    const parentSubCategory = ref<ParentSubCategory | null>(null); 
    const pagination = ref({ current_page: 1, last_page: 1, total: 0 });
    const filters = ref({ search: '', pagination: 10 });
    const isLoading = ref(false);

    /**
     * Fetches a paginated list of sub-sub-categories for the dashboard.
     */
    async function fetch(subCategoryId: number, nextPage = false, prevPage = false) {
        isLoading.value = true;
        if (nextPage) pagination.value.current_page++;
        if (prevPage) pagination.value.current_page--;

        try {
            const { data } = await axios.get(`/dashboard/sub/categories/${subCategoryId}/sub-sub-categories`, {
                params: {
                    page: pagination.value.current_page,
                    ...filters.value,
                },
            });
            subSubCategories.value = data.data;
            pagination.value = data.meta;
            parentSubCategory.value = data.sub_category; 

        } catch (error) {
            console.error("Failed to fetch dashboard sub-sub-categories", error);
            parentSubCategory.value = null; // Reset on error
        } finally {
            isLoading.value = false;
        }
    }

    /**
     * Deletes a sub-sub-category and refreshes the list.
     */
    async function onDelete(id: number, subCategoryId: number) {
        if (!confirm('Are you sure you want to delete this item?')) {
            return;
        }
        
        try {
            await axios.delete(`/dashboard/sub-sub-categories/${id}`);
            await fetch(subCategoryId);
        } catch (error) {
            console.error("Failed to delete sub-sub-category", error);
        }
    }

    function $reset() {
        subSubCategories.value = [];
        parentSubCategory.value = null;
        pagination.value = { current_page: 1, last_page: 1, total: 0 };
    }


    return {
        subSubCategories,
        parentSubCategory, // Keep exposing this
        pagination,
        filters,
        isLoading,
        fetch,
        onDelete,
        $reset, // Expose the reset function
    };
});