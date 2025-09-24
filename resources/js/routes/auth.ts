import {guestMiddleware, authMiddleware} from "@/routes/middlewares/auth";

export default {
    path: '/authentication',
    component: () => import('@/views/auth/layouts/Auth.vue'),
    children: [
        {
            path: '/authentication/login',
            name: 'login',
            component: () => import('@/views/auth/Login.vue'),
            beforeEnter: guestMiddleware
        },
        {
            path: '/authentication/register',
            name: 'register',
            component: () => import('@/views/auth/Register.vue'),
            beforeEnter: guestMiddleware
        },
        {
            path: '/authentication/forgot-password',
            name: 'forgot-password',
            component: () => import('@/views/auth/ForgetPassword.vue'),
            beforeEnter: guestMiddleware
        }
    ]
};
