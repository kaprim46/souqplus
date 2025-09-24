import { createWebHistory, createRouter } from 'vue-router';

/**
 * Preline static methods
 */
import { type IStaticMethods } from "preline/preline";
declare global {
    interface Window {
        HSStaticMethods: IStaticMethods;
    }
}

/**
 * Routes
 */
import dashboardRoutes from './dashboard';
import frontendRoutes from './frontend';
import authRoutes from './auth';

const routes = [
    dashboardRoutes,
    frontendRoutes,
    authRoutes
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.afterEach((to, from, failure) => {
    window.scrollTo({ top: 0, behavior: 'smooth' });

    if (!failure) {
        setTimeout(() => {
            window.HSStaticMethods.autoInit();
        }, 100)
    }
});

router.beforeEach((to, from, next) => {
    next();
});

export default router;

