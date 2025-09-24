import {NavigationGuardNext, RouteLocationNormalized} from "vue-router";

const guestMiddleware = (to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) => {
    if (localStorage.getItem('token')) {
        return next({ name: 'home' });
    }

    return next();
};

const authMiddleware = (to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) => {
    if (!localStorage.getItem('token')) {
        return next({ name: 'login' });
    }

    return next();
};

const adminMiddleware = (to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext) => {
   const user = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')!) : null;

    if (user?.role !== 'admin') {
        return next('/');
    }

    return next();
};


export {
    guestMiddleware,
    authMiddleware,
    adminMiddleware,
};
