import {adminMiddleware} from "@/routes/middlewares/auth";

export default {
    path: '/dashboard',
    component: () => import('@/views/dashboard/layout.vue'),
    beforeEnter: adminMiddleware,
    children: [
        /**
         * Dashboard
         */
        {
            path: '/dashboard',
            name: 'dashboard',
            component: () => import('@/views/dashboard/pages/home/index.vue'),
        },
        /**
         * Settings app
         */
        {
            path: '/dashboard/settings',
            name: 'settings',
            component: () => import('@/views/dashboard/pages/settings/index.vue'),
        },
        /**
         * Splash Screens
         */
        {
            path: '/dashboard/splash-screens',
            name: 'splash-screens.index',
            component: () => import('@/views/dashboard/pages/splash-screens/index.vue'),
        },
        {
            path: '/dashboard/splash-screens/:id/edit',
            name: 'splash-screen.edit',
            component: () => import('@/views/dashboard/pages/splash-screens/edit.vue'),
        },
        {
            path: '/dashboard/splash-screens/create',
            name: 'splash-screen.create',
            component: () => import('@/views/dashboard/pages/splash-screens/create.vue'),
        },
        /**
         * Countries & Cities
         */
        {
            path: '/dashboard/countries',
            name: 'countries.index',
            component: () => import('@/views/dashboard/pages/countries/index.vue'),
        },
        {
            path: '/dashboard/cities',
            name: 'cities.index',
            component: () => import('@/views/dashboard/pages/cities/index.vue'),
        },
        /**
         * explore-sliders
         */
        {
            path: '/dashboard/explore-sliders',
            name: 'explore-sliders.index',
            component: () => import('@/views/dashboard/pages/explore-sliders/index.vue'),
        },
        /**
         * Customers
         */
        {
            path: '/dashboard/customers',
            name: 'customers.index',
            component: () => import('@/views/dashboard/pages/customers/index.vue'),
        },
        {
            path: '/dashboard/customers/:id',
            name: 'customer.show',
            component: () => import('@/views/dashboard/pages/customers/show.vue'),
        },
        {
            path: '/dashboard/customers/:id/edit',
            name: 'customer.edit',
            component: () => import('@/views/dashboard/pages/customers/edit.vue'),
        },
        {
            path: '/dashboard/customers/create',
            name: 'customer.create',
            component: () => import('@/views/dashboard/pages/customers/create.vue'),
        },
        /**
         * Categories
         */
        {
            path: '/dashboard/categories',
            name: 'categories.index',
            component: () => import('@/views/dashboard/pages/categories/index.vue'),
        },
        {
            path: '/dashboard/categories/:id/edit',
            name: 'category.edit',
            component: () => import('@/views/dashboard/pages/categories/edit.vue'),
        },
        {
            path: '/dashboard/categories/create',
            name: 'category.create',
            component: () => import('@/views/dashboard/pages/categories/create.vue'),
        },
        /**
         * Sub Categories
         */
        {
            path: '/dashboard/sub-categories',
            name: 'sub-categories.index',
            component: () => import('@/views/dashboard/pages/sub-categories/index.vue'),
        },
        {
            path: '/dashboard/sub-categories/:id/edit',
            name: 'sub-category.edit',
            component: () => import('@/views/dashboard/pages/sub-categories/edit.vue'),
        },
        {
            path: '/dashboard/sub-categories/create',
            name: 'sub-category.create',
            component: () => import('@/views/dashboard/pages/sub-categories/create.vue'),
        },
        /**
         * Sub Sub Categories
         */
        {
            path: '/dashboard/sub-categories/:sub_category_id/sub-sub-categories',
            name: 'sub-sub-categories.index',
            component: () => import('@/views/dashboard/pages/sub-sub-categories/index.vue'),
        },
        {
            path: '/dashboard/sub-sub-categories/:sub_category_id/create',
            name: 'sub-sub-category.create',
            component: () => import('@/views/dashboard/pages/sub-sub-categories/create.vue'),
        },
        {
            path: '/dashboard/sub-sub-categories/edit/:id',
            name: 'sub-sub-category.edit',
            component: () => import('@/views/dashboard/pages/sub-sub-categories/edit.vue'),
        },
        {
            path: '/dashboard/sub-sub-categories/:sub_category_id/create',
            name: 'sub-sub-category.create',
            component: () => import('@/views/dashboard/pages/sub-sub-categories/create.vue'),
        },
        {
            path: '/dashboard/sub-sub-categories/edit/:id',
            name: 'sub-sub-category.edit',
            component: () => import('@/views/dashboard/pages/sub-sub-categories/edit.vue'),
        },
        
        /**
         * Advertisements
         */
        {
            path: '/dashboard/advertisements',
            name: 'advertisements.index',
            component: () => import('@/views/dashboard/pages/advertisements/index.vue'),
        },
        {
            path: '/dashboard/advertisements/:id/edit',
            name: 'advertisement.edit',
            component: () => import('@/views/dashboard/pages/advertisements/edit.vue'),
        },
        {
            path: '/dashboard/advertisements/create',
            name: 'advertisement.create',
            component: () => import('@/views/dashboard/pages/advertisements/create.vue'),
        },
        /**
         * Pages
         */
        {
            path: '/dashboard/pages',
            name: 'pages.index',
            component: () => import('@/views/dashboard/pages/pages/index.vue'),
        },
        {
            path: '/dashboard/pages/:id/edit',
            name: 'page.edit',
            component: () => import('@/views/dashboard/pages/pages/edit.vue'),
        },
        {
            path: '/dashboard/pages/create',
            name: 'page.create',
            component: () => import('@/views/dashboard/pages/pages/create.vue'),
        },
        /**
         * Contact us
         */
        {
            path: '/dashboard/contact-us',
            name: 'contactus.index',
            component: () => import('@/views/dashboard/pages/contactus/index.vue'),
        },
        {
            path: '/dashboard/contact-us/:id/view',
            name: 'contactus.view',
            component: () => import('@/views/dashboard/pages/contactus/view.vue'),
        },
        /**
         * Filters
         */
        {
            path: '/dashboard/filters',
            name: 'filters.index',
            component: () => import('@/views/dashboard/pages/filters/index.vue'),
        },
        /**
         * Badges
         */
        {
            path: '/dashboard/badges',
            name: 'badges.index',
            component: () => import('@/views/dashboard/pages/badges/index.vue'),
        }
    ]
};