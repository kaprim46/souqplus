import { Advertisement } from '@/types/Advertisements';
import { useUserStore } from '@/stores/UserStore';
import { trans, wTrans } from 'laravel-vue-i18n';
import { defineStore } from 'pinia';
import { nextTick } from 'vue';
import { notify } from 'notiwind';
import router from '@/routes';
import axios from '@/axios';
import swal from 'sweetalert2';

export const useCustomerAdvertisementStore = defineStore({
  id: 'customer-advertisement',
  state: () => ({
    advertisements: [] as Advertisement[],
    advertisement: {} as Advertisement,
    favorites: [] as Advertisement[],
    toggleFavoriteLoading: false,
    isLoading: true,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 1,
      total: 0,
    },
    filters: {
      pagination: 0,
      search: '',
      category: null,
      sub_category: null,
      sub_sub_category: null,
      sortBy: null,
      manufacturing_year: null,
    } as {
      is_random?: number;
      limit?: number;
      category?: number | null;
      sub_category?: number | null;
      sub_sub_category?: number | null;
      pagination?: number;
      search?: string | null;
      except?: number | null;
      user_id?: number | null;
      sortBy?: string | null;
      country_id?: number | null;
      city_id?: number | null;
      category_filter?: {
        value: string | number;
        filter_id: number;
      }[];
      manufacturing_year?: number | null;
    },
    isLoadingStories: true,
    stories: [] as any[],
  }),
  actions: {
    async fetch(isPagination = false, isLess = false) {
      this.isLoading = true;
      let params = {} as {
        is_random?: number;
        limit?: number;
        sort_by?: string | null;
        category?: number | null;
        sub_category?: number | null;
        sub_sub_category?: number | null;
        pagination?: number;
        search?: string | null;
        page?: number;
        except?: number | null;
        user_id?: number | null;
        country_id?: number | null;
        city_id?: number | null;
        category_filter?: {
          value: string | number;
          filter_id: number;
        }[];
        manufacturing_year?: number | null;
      };

      if (this.filters.is_random) {
        params.is_random = this.filters.is_random;
      }

      if (this.filters.limit) {
        params.limit = this.filters.limit;
      }

      if (this.filters.sortBy) {
        params.sort_by = this.filters.sortBy;
      }

      if (this.filters.category) {
        params.category = this.filters.category;
      }

      if (this.filters.sub_category) {
        params.sub_category = this.filters.sub_category;
      }

      if (this.filters.sub_sub_category) {
        params.sub_sub_category = this.filters.sub_sub_category;
      }

      if (this.filters.pagination) {
        params.pagination = this.filters.pagination;
      }

      if (this.filters.search) {
        params.search = this.filters.search;
      }

      if (this.filters.except) {
        params.except = this.filters.except;
      }

      if (this.filters.user_id) {
        params.user_id = this.filters.user_id;
      }

      if (this.filters.category_filter) {
        params.category_filter = this.filters.category_filter;
      }

      if (this.filters.country_id) {
        params.country_id = this.filters.country_id;
      }

      if (this.filters.city_id) {
        params.city_id = this.filters.city_id;
      }

      if (this.filters.manufacturing_year) {
        params.manufacturing_year = this.filters.manufacturing_year;
      }

      if (isPagination && this.filters.pagination) {
        if (isLess) {
          this.pagination.current_page--;
        } else {
          this.pagination.current_page++;
        }

        params.page = this.pagination.current_page;
      }

      try {
        const { data } = await axios.get(`/advertisements`, {
          params: {
            ...params,
          },
        });

        if (data.ok) {
          this.advertisements = this.filters.pagination
            ? data.advertisements.data
            : data.advertisements;

          /**
           * set pagination
           */
          if (this.filters.pagination) {
            this.pagination = {
              current_page: data.advertisements.current_page,
              last_page: data.advertisements.last_page,
              per_page: data.advertisements.per_page,
              total: data.advertisements.total,
            };
          }
        }

        await nextTick();

        this.isLoading = false;
      } catch (error) {
        this.isLoading = false;
      }
    },
    async fetchOne(id: number, withMedia = 1) {
      this.isLoading = true;

      try {
        const { data } = await axios.get(`/advertisements/${id}`, {
          params: {
            with_media: withMedia,
          },
        });

        if (data.ok) {
          this.advertisement = data.advertisement;

          return true;
        }

        await router.push({ name: '404' });
      } catch (error) {
        await router.push({ name: 'home' });
      } finally {
        this.isLoading = false;
      }
    },
    async fetchMyAdvertisements(isPagination = false, isLess = false) {
      this.isLoading = true;
      let params = {} as {
        is_random?: number;
        limit?: number;
        sort_by?: string | null;
        category?: number | null;
        pagination?: number;
        search?: string | null;
        page?: number;
      };

      if (isPagination && this.filters.pagination) {
        if (isLess) {
          this.pagination.current_page--;
        } else {
          this.pagination.current_page++;
        }
      }

      if (this.filters.is_random) {
        params.is_random = this.filters.is_random;
      }

      if (this.filters.limit) {
        params.limit = this.filters.limit;
      }

      if (this.filters.sortBy) {
        params.sort_by = this.filters.sortBy;
      }

      if (this.filters.category) {
        params.category = this.filters.category;
      }

      if (this.filters.pagination) {
        params.pagination = this.filters.pagination;
        params.page = this.pagination.current_page;
      }

      if (this.filters.search) {
        params.search = this.filters.search;
      }

      try {
        const { data } = await axios.get(`/my/advertisements`, {
          params: {
            ...params,
          },
        });

        if (data.ok) {
          this.advertisements = this.filters.pagination
            ? data.advertisements.data
            : data.advertisements;

          /**
           * set pagination
           */
          if (this.filters.pagination) {
            this.pagination = {
              current_page: data.advertisements.current_page,
              last_page: data.advertisements.last_page,
              per_page: data.advertisements.per_page,
              total: data.advertisements.total,
            };
          }
        }

        await nextTick();

        this.isLoading = false;
      } catch (error) {
        this.isLoading = false;
      }
    },
    async fetchFavoriteAdvertisements(isPagination = false, isLess = false) {
      this.isLoading = true;
      let params = {} as {
        pagination?: number;
        search?: string | null;
        page?: number;
      };

      if (isPagination && this.filters.pagination) {
        if (isLess) {
          this.pagination.current_page--;
        } else {
          this.pagination.current_page++;
        }
      }

      if (this.filters.pagination) {
        params.pagination = this.filters.pagination;
        params.page = this.pagination.current_page;
      }

      if (this.filters.search) {
        params.search = this.filters.search;
      }

      try {
        const { data } = await axios.get('/my/advertisements/favorites', {
          params: {
            ...params,
          },
        });

        if (data.ok) {
          this.favorites = this.filters.pagination
            ? data.favorites.data
            : data.favorites;

          if (this.filters.pagination) {
            this.pagination = {
              current_page: data.favorites.current_page,
              last_page: data.favorites.last_page,
              per_page: data.favorites.per_page,
              total: data.favorites.total,
            };
          }
        }

        await nextTick();

        this.isLoading = false;
      } catch (error) {
        this.isLoading = false;
      }
    },
    async toggleFavorite(
      advertisement: Advertisement,
      fromArray = false,
      isCurrent = false
    ) {
      /**
       * Call userStore and check isLogged
       */
      const userStore = useUserStore();

      if (!userStore.isLoggedIn) {
        await router.push({ name: 'login' });
        return;
      }

      this.toggleFavoriteLoading = true;

      try {
        const { data } = await axios.post(
          `/advertisements/${advertisement.id}/favorite`
        );

        if (data.ok) {
          if (isCurrent) {
            this.advertisement.is_favorite = !this.advertisement.is_favorite;
            return;
          }

          if (!fromArray) {
            this.favorites = this.favorites.filter(
              (item) => item.id !== advertisement.id
            );
            this.pagination.total--;
            return;
          }

          this.advertisements = this.advertisements.map((item) => {
            if (item.id === advertisement.id) {
              item.is_favorite = !item.is_favorite;
            }

            return item;
          });
        }
      } catch (error: any) {
        if (error.response.status === 401) {
          await router.push({ name: 'login' });
        }
      } finally {
        this.toggleFavoriteLoading = false;
      }
    },
    async deleteAdvertisement(advertisement: { id: number }) {
      if (!advertisement?.id) {
        return;
      }

      try {
        const { data } = await axios.delete(
          `/my/advertisements/${advertisement.id}`
        );

        if (data.ok) {
          this.advertisements = this.advertisements.filter(
            (item) => item.id !== advertisement.id
          );
        }

        notify(
          {
            group: 'dashboard',
            ok: data.ok,
            title: data.ok ? wTrans('Success') : wTrans('Error'),
            text: data.message,
          },
          2000
        );
      } catch (error) {
        console.log(error);
      }
    },
    async saveToLocalStorage(id: number) {
      let advertisements_visited = JSON.parse(
        localStorage.getItem('advertisements_visited') || '[]'
      );

      let index = advertisements_visited.findIndex(
        (advertisement: any) => advertisement.id === id
      );

      if (index === -1) {
        advertisements_visited.push({ id: id, date: new Date().toISOString() });
      } else {
        advertisements_visited[index].date = new Date().toISOString();
      }

      localStorage.setItem(
        'advertisements_visited',
        JSON.stringify(advertisements_visited)
      );
    },
    async postAsStore(advertisement: Advertisement) {
      try {
        const { data } = await axios.post(
          `/my/advertisements/${advertisement.id}/story`
        );

        if (data.ok) {
          let findAd = this.advertisements.find(
            (item) => item.id === advertisement.id
          );

          if (findAd) {
            findAd.post_as_story = true;
          }

          notify(
            {
              group: 'dashboard',
              ok: data.ok,
              title: data.ok ? wTrans('Success') : wTrans('Error'),
              text: data.message,
            },
            2000
          );
        }
      } catch (error) {
        console.log(error);
      }
    },
    async unpostAsStore(advertisement: Advertisement) {
      /**
       * confirm sweet alert
       */
      const { value } = await swal.fire({
        title: trans('Are you sure?'),
        text: trans(
          'Do you want to remove this advertisement from your story?'
        ),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: trans('Yes, delete it!'),
        cancelButtonText: trans('No, keep it'),
      });

      if (!value) return;

      try {
        const { data } = await axios.delete(
          `/my/advertisements/${advertisement.id}/story`
        );

        if (data.ok) {
          let findAd = this.advertisements.find(
            (item) => item.id === advertisement.id
          );

          if (findAd) {
            findAd.post_as_story = false;
          }

          notify(
            {
              group: 'dashboard',
              ok: data.ok,
              title: data.ok ? wTrans('Success') : wTrans('Error'),
              text: data.message,
            },
            2000
          );
        }
      } catch (error) {
        console.log(error);
      }
    },
    async fetchStories() {
      this.isLoadingStories = true;

      try {
        const { data } = await axios.get('/stories');

        if (data.ok) {
          this.stories = data.stories;
        }
      } catch (error) {
        console.log(error);
      } finally {
        this.isLoadingStories = false;
      }
    },
    async fetchExploreAdvertisements(isPagination = false, isLess = false) {
      this.isLoading = true;
      let params = {} as {
        is_random?: number;
        limit?: number;
        sort_by?: string | null;
        category?: number | null;
        sub_category?: number | null;
        sub_sub_category?: number | null;
        pagination?: number;
        search?: string | null;
        page?: number;
        except?: number | null;
        user_id?: number | null;
        country_id?: number | null;
        city_id?: number | null;
        category_filter?: {
          value: string | number;
          filter_id: number;
        }[];
        manufacturing_year?: number | null;
      };

      if (this.filters.is_random) {
        params.is_random = this.filters.is_random;
      }

      if (this.filters.limit) {
        params.limit = this.filters.limit;
      }

      if (this.filters.sortBy) {
        params.sort_by = this.filters.sortBy;
      }

      if (this.filters.category) {
        params.category = this.filters.category;
      }

      if (this.filters.sub_category) {
        params.sub_category = this.filters.sub_category;
      }

      if (this.filters.sub_sub_category) {
        params.sub_sub_category = this.filters.sub_sub_category;
      }

      if (this.filters.pagination) {
        params.pagination = this.filters.pagination;
      }

      if (this.filters.search) {
        params.search = this.filters.search;
      }

      if (this.filters.except) {
        params.except = this.filters.except;
      }

      if (this.filters.user_id) {
        params.user_id = this.filters.user_id;
      }

      if (this.filters.category_filter) {
        params.category_filter = this.filters.category_filter;
      }

      if (this.filters.country_id) {
        params.country_id = this.filters.country_id;
      }

      if (this.filters.city_id) {
        params.city_id = this.filters.city_id;
      }

      if (this.filters.manufacturing_year) {
        params.manufacturing_year = this.filters.manufacturing_year;
      }

      if (isPagination && this.filters.pagination) {
        if (isLess) {
          this.pagination.current_page--;
        } else {
          this.pagination.current_page++;
        }

        params.page = this.pagination.current_page;
      }

      try {
        const { data } = await axios.get(`/advertisements/explore`, {
          params: {
            ...params,
          },
        });

        if (data.ok) {
          this.advertisements = this.filters.pagination
            ? data.advertisements.data
            : data.advertisements;

          /**
           * set pagination
           */
          if (this.filters.pagination) {
            this.pagination = {
              current_page: data.advertisements.current_page,
              last_page: data.advertisements.last_page,
              per_page: data.advertisements.per_page,
              total: data.advertisements.total,
            };
          }
        }

        await nextTick();

        this.isLoading = false;
      } catch (error) {
        this.isLoading = false;
      }
    },
    idExistsInLocalStorage(id: number) {
      let advertisements_visited = JSON.parse(
        localStorage.getItem('advertisements_visited') || '[]'
      );

      let index = advertisements_visited.findIndex(
        (advertisement: any) => advertisement.id === id
      );

      return index !== -1;
    },
    resetAll(
      filters = {
        pagination: 1,
        search: '',
        category: null,
        sub_category: null,
        sub_sub_category: null,
        country_id: null,
        city_id: null,
        sortBy: 'latest' as const,
        manufacturing_year: null,
      }
    ) {
      this.isLoading = true;

      this.advertisements = [];

      this.pagination = {
        current_page: 1,
        last_page: 1,
        per_page: 1,
        total: 0,
      };

      this.filters = filters;

      return true;
    },
  },
});
