import { defineStore } from 'pinia';
import axios from '@/axios';
import { ref } from 'vue';

// Define a type for your SubSubCategory for the dashboard
// It's good practice to have it here, even if it's similar to another type
export interface SubSubCategory {
    id?: number;
    name: string;
    status: 'active' | 'inactive';
    sub_category_id: number | null;
}

export const useDashboardSubSubCategoryStore = defineStore('dashboardSubSubCategory', () => {
    // --- STATES ---
    const subSubCategories = ref<SubSubCategory[]>([]);
    const pagination = ref({ current_page: 1, last_page: 1, total: 0 });
    const filters = ref({ search: '', pagination: 10 });
    const isLoading = ref(false);

    // --- ACTIONS ---

    /**
     * Fetches a paginated list of sub-sub-categories for the dashboard.
     */
    async function fetch(subCategoryId: number, nextPage = false, prevPage = false) {
        isLoading.value = true;
        if (nextPage) pagination.value.current_page++;
        if (prevPage) pagination.value.current_page--;

        try {
            // Use the dashboard-specific API endpoint
            const { data } = await axios.get(`/dashboard/sub/categories/${subCategoryId}/sub-sub-categories`, {
                params: {
                    page: pagination.value.current_page,
                    ...filters.value,
                },
            });
            subSubCategories.value = data.data;
            pagination.value = data.meta;
        } catch (error) {
            console.error("Failed to fetch dashboard sub-sub-categories", error);
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
            // After deleting, refetch the data for the current sub-category
            await fetch(subCategoryId);
        } catch (error) {
            console.error("Failed to delete sub-sub-category", error);
        }
    }

    // --- RETURN ---
    return {
        subSubCategories,
        pagination,
        filters,
        isLoading,
        fetch,
        onDelete,
    };
});