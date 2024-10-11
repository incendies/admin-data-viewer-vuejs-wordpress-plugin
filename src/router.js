import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        redirect: '/table'  // Redirect root path to /table
    },
    {
        path: '/table',
        name: 'Table',
        component: () => import('./components/Table.vue'),  // Lazy-loaded Table component
    },
    {
        path: '/graph',
        name: 'Graph',
        component: () => import('./components/Graph.vue'),  // Lazy-loaded Graph component
    },
    {
        path: '/settings',
        name: 'Settings',
        component: () => import('./components/Settings.vue'),  // Lazy-loaded Settings component
    },
    {
        path: '/:catchAll(.*)',  // Catch-all route for undefined paths
        redirect: '/table'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;