import { defineStore } from 'pinia';
import { User } from '@/types/User';
import routes from '@/routes';
import { notify } from 'notiwind';
import { trans } from 'laravel-vue-i18n';
import axios from '@/axios';

export const useUserStore = defineStore({
  id: 'user',
  state: () => ({
    me: localStorage.getItem('user')
      ? (JSON.parse(localStorage.getItem('user')!) as User)
      : ({} as User),
    token: localStorage.getItem('token') || null,
    passwordForm: {
      current_password: '',
      password: '',
      password_confirmation: '',
    },
    isLoadingNotifications: true,
    pagination: {
      total: 0,
      count: 0,
      per_page: 0,
      last_page: 0,
      current_page: 0,
      total_pages: 0,
      next_page_url: null,
      prev_page_url: null,
    },
    notifications: [] as any[],
    isLoadingFollowings: true,
    isLoadingFollowers: true,
    followings: [] as any[],
    followers: [] as any[],
    isLoadingUser: true,
    isLoadingMe: true,
    errors: {} as any,
    unread_count: 0,
    user: {} as any,
  }),
  getters: {
    isLoggedIn: (state) => !!state.token,
  },
  actions: {
    /**
     * Async actions
     */
    async fetchUser(id: number) {
      this.isLoadingUser = true;

      try {
        const { data } = await axios.get(`/customers/${id}`);

        if (data.ok) {
          this.user = data.user;
        }

        return data.ok;
      } catch (e) {
        return false;
      } finally {
        this.isLoadingUser = false;
      }
    },
    async fetchProfile() {
      this.isLoadingMe = true;
      try {
        const { data } = await axios.get('/me');

        if (data.ok) {
          this.me = data.user;
        }
      } catch (e) {
        return false;
      } finally {
        this.isLoadingMe = false;
      }
    },
    async fetchNotifications(loadMore = false) {
      this.isLoadingNotifications = true;

      if (
        loadMore &&
        this.pagination.current_page === this.pagination.last_page
      ) {
        this.isLoadingNotifications = false;
        return;
      }

      try {
        const { data } = await axios.get('/me/notifications', {
          params: {
            page: loadMore ? this.pagination.current_page + 1 : 1,
          },
        });

        if (data.ok) {
          this.notifications = loadMore
            ? this.notifications.concat(data.notifications)
            : data.notifications;
          this.pagination = data.pagination;
          this.unread_count = data.unread;
        }

        return data.ok;
      } catch (e) {
        return false;
      } finally {
        this.isLoadingNotifications = false;
      }
    },
    async fetchFollowers() {
      this.isLoadingFollowers = true;

      try {
        const { data } = await axios.get(
          `/customers/${this.user.id}/followers`
        );

        if (data.ok) {
          this.followers = data.followers;
        }
      } catch (e) {
        return false;
      } finally {
        this.isLoadingFollowers = false;
      }
    },
    async fetchFollowings() {
      this.isLoadingFollowings = true;

      try {
        const { data } = await axios.get(
          `/customers/${this.user.id}/following`
        );

        if (data.ok) {
          this.followings = data.followings;
        }
      } catch (e) {
        return false;
      } finally {
        this.isLoadingFollowings = false;
      }
    },
    async markAsRead() {
      try {
        const { data } = await axios.put('/me/notifications');

        if (data.ok) {
          this.unread_count = 0;
        }
      } catch (e) {
        return false;
      }
    },
    async updatePassword() {
      this.isLoadingMe = true;

      try {
        const { data } = await axios.put('/me/password', this.passwordForm);

        if (data.ok) {
          this.passwordForm = {
            current_password: '',
            password: '',
            password_confirmation: '',
          };
        }

        this.errors = {};

        notify(
          {
            group: 'dashboard',
            ok: data.ok,
            title: data.ok ? trans('Success') : trans('Error'),
            text: data.message,
          },
          2000
        );
      } catch (e: any) {
        if (e.response.status === 422) {
          this.errors = e.response.data.errors;
        } else if (e.response.status === 401) {
          notify(
            {
              group: 'dashboard',
              ok: false,
              title: trans('Error'),
              text: e.response.data.message,
            },
            2000
          );
        }
      } finally {
        this.isLoadingMe = false;
      }
    },
    async updateProfile() {
      this.isLoadingMe = true;
      console.log('Starting profile update');

      try {
        let formData = new FormData();
        formData.append('name', this.me.name);
        formData.append('email', this.me.email);
        formData.append('phone_number', this.me.phone_number || '');
        formData.append('country_code', this.me.country_code || '');

        // Log if avatar is a File
        if (this.me.avatar instanceof File) {
          console.log('Avatar file details:', {
            name: this.me.avatar.name,
            size: this.me.avatar.size,
            type: this.me.avatar.type,
          });
          formData.append('avatar', this.me.avatar);
        } else {
          console.log('No avatar file or invalid file object:', this.me.avatar);
        }

        formData.append('bio', this.me.bio || '');
        formData.append('_method', 'PUT');

        console.log('Sending update request...');
        const { data } = await axios.post('/me/profile', formData);

        if (data.ok) {
          console.log('Profile update successful', data.user);
          this.me = data.user;

          localStorage.setItem(
            'user',
            JSON.stringify({
              id: data.user.id,
              uuid: data.user.uuid,
              name: data.user.name,
              email: data.user.email,
              role: data.user.role,
              avatar_url: data.user.avatar_url,
              bio: data.user.bio,
            })
          );
        }
        return data;
      } catch (e: any) {
        console.error('Profile update error:', e);
        if (e.response?.status === 422) {
          this.errors = e.response.data.errors;
        } else {
          throw e;
        }
      } finally {
        this.isLoadingMe = false;
      }
    },
    async deleteProfile() {
      this.isLoadingMe = true;

      try {
        const { data } = await axios.delete('/me');

        notify(
          {
            group: 'dashboard',
            ok: data.ok,
            title: data.ok ? trans('Success') : trans('Error'),
            message: data.message,
          },
          2000
        );

        if (data.ok) {
          await this.logout();
        }
      } catch (e: any) {
        notify(
          {
            group: 'dashboard',
            ok: false,
            message: e.response.data.message,
          },
          2000
        );
      } finally {
        this.isLoadingMe = false;
      }
    },
    async logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('user');

      await routes.push({ name: 'login' });

      return true;
    },
    async sendVerification(email: string) {
      try {
        const { data } = await axios.post('/send/verification', { email });

        notify(
          {
            group: 'dashboard',
            ok: data.ok,
            title: data.ok ? trans('Success') : trans('Error'),
            text: data.message,
          },
          2000
        );
      } catch (e: any) {
        console.log(e.response.data);
      }
    },
    /**
     * Sync actions
     */
    updateUser(user: User) {
      this.me = user;
      /**
       * Update local storage
       */
      localStorage.setItem('user', JSON.stringify(user));
    },
    addNotification(notification: any) {
      if (
        !this.notifications.find(
          (n) => n.sender_id === notification.sender_id
        ) &&
        notification.sender_id !== this.me.id
      ) {
        this.notifications.unshift(notification);

        this.unread_count++;
      }
    },
    login({ user, token }: { user: User; token: string | null }) {
      if (!user || !token) {
        return false;
      }

      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));

      return true;
    },
    uploadAvatar(event: Event) {
      const input = event.target as HTMLInputElement;
      const file = input.files?.[0];

      if (!file) return;

      // Check file type
      const validTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/svg+xml',
      ];
      if (!validTypes.includes(file.type)) {
        notify(
          {
            group: 'dashboard',
            ok: false,
            title: trans('Error'),
            text: 'Invalid file type. Please upload an image (JPEG, PNG, GIF, SVG)',
          },
          2000
        );
        return;
      }

      // Check file size (2MB max)
      if (file.size > 2 * 1024 * 1024) {
        notify(
          {
            group: 'dashboard',
            ok: false,
            title: trans('Error'),
            text: 'File is too large. Maximum size is 2MB',
          },
          2000
        );
        return;
      }

      const reader = new FileReader();

      reader.onload = (e) => {
        // Store the file object for upload
        this.me.avatar = file;
        // Set the preview URL
        this.me.avatar_url = e.target?.result as string;
      };

      reader.onerror = (error) => {
        console.error('Error reading file:', error);
        notify(
          {
            group: 'dashboard',
            ok: false,
            title: trans('Error'),
            text: 'Failed to read the file',
          },
          2000
        );
      };

      reader.readAsDataURL(file);
    },
    uploadStoreLogo(event: Event) {
      const file = (event.target as HTMLInputElement).files?.[0];
      const reader = new FileReader();

      if (file) {
        reader.readAsDataURL(file);
      }

      reader.onload = () => {
        if (this.me.business_info) {
          this.me.business_info.business_logo = file;
          this.me.business_info.business_logo_url = reader.result as string;
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
        if (this.me.business_info) {
          this.me.business_info.cover_image = file;
          this.me.business_info.cover_image_url = reader.result as string;
        }
      };

      reader.onerror = () => {
        console.log('Error: ', reader.error);
      };
    },
  },
});
