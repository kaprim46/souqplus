import {guestMiddleware, authMiddleware} from "@/routes/middlewares/auth";

export default {
    path: '/',
    component: () => import('@/views/frontend/layouts/Main.vue'),
    children: [
        /**
         * Home Routes
         */
        {
            path: '/',
            name: 'home',
            component: () => import('@/views/frontend/pages/home/index.vue'),
        },
        {
            path: '/pages/:slug',
            name: 'customer.page.show',
            component: () => import('@/views/frontend/pages/page/show.vue'),
        },
        {
            path: '/categories/:slug/:sub?',
            name: 'customer.category.show',
            component: () => import('@/views/frontend/pages/category/show.vue'),
        },
        {
            path: '/contact-us',
            name: 'contact.us',
            component: () => import('@/views/frontend/pages/contact-us/index.vue'),
        },
        /**
         * Profile Settings
         */
        {
            path: '/profile',
            name: 'profile',
            component: () => import('@/views/frontend/pages/profile/index.vue'),
            beforeEnter: authMiddleware
        },
        {
            path: '/profile/:id/:slug?',
            name: 'profile_uid',
            component: () => import('@/views/frontend/pages/public_profile/index.vue'),
        },
        /**
         * My Advertisement
         */
        {
            path: '/my-advertisement',
            name: 'my-advertisement',
            component: () => import('@/views/frontend/pages/user/advertisement/index.vue'),
            beforeEnter: authMiddleware
        },
        {
            path: '/create-advertisement',
            name: 'create-advertisement',
            component: () => import('@/views/frontend/pages/advertisement/create.vue'),
            beforeEnter: authMiddleware
        },
        {
            path: '/edit-advertisement/:id',
            name: 'edit-advertisement',
            component: () => import('@/views/frontend/pages/advertisement/edit.vue'),
            beforeEnter: authMiddleware
        },
        {
            path: '/advertisement/:id/:slug?',
            name: 'show-advertisement',
            component: () => import('@/views/frontend/pages/advertisement/show.vue'),
        },
        {
            path: '/favorite-advertisement',
            name: 'favorite-advertisement',
            component: () => import('@/views/frontend/pages/favorite-advertisement/index.vue'),
            beforeEnter: authMiddleware
        },
        /**
         * Stores
         */
        {
            path: '/store/:id/:slug?',
            name: 'store.show',
            component: () => import('@/views/frontend/pages/store/show.vue'),
        },
        /**
         * 404 Page
         */
        {
            path: '/404',
            name: '404',
            component: () => import('@/views/frontend/pages/errors/404.vue')
        },
        /**
         * Chat
         */
        {
            path: '/chat/:slug?',
            name: 'chat',
            component: () => import('@/views/frontend/pages/chat/index.vue'),
            beforeEnter: authMiddleware
        },
        /**
         * Explore
         */
        {
            path: '/explore',
            name: 'explore',
            component: () => import('@/views/frontend/pages/explore/index.vue'),
        }
    ]
};
