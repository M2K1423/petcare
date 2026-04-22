import { mountRenderlessPage } from './vue-mount';

const pageKey = document.body?.dataset?.page || '';

const pageLoaders = {
	'auth-sanctum': () => import('./pages-vue/sanctum-auth.vue'),
	'auth-sanctum-register': () => import('./pages-vue/sanctum-auth.vue'),

	'owner-overview': () => import('./pages-vue/owner-overview.vue'),
	'owner-profile': () => import('./pages-vue/owner-profile.vue'),
	'owner-pets': () => import('./pages-vue/owner-pets.vue'),
	'owner-pet-edit': () => import('./pages-vue/owner-pet-edit.vue'),
	'owner-pet-health-records': () => import('./pages-vue/owner-pet-health-records.vue'),
	'owner-appointments': () => import('./pages-vue/owner-appointments.vue'),

	'receptionist-dashboard': () => import('./pages-vue/receptionist-dashboard.vue'),
	'receptionist-appointments': () => import('./pages-vue/receptionist-appointments.vue'),
	'receptionist-appointment-detail': () => import('./pages-vue/receptionist-appointment-detail.vue'),
	'receptionist-billing': () => import('./pages-vue/receptionist-billing.vue'),
	'receptionist-walkins': () => import('./pages-vue/receptionist-appointments.vue'),
};

async function boot() {
	const load = pageLoaders[pageKey];
	if (!load) return;

	const module = await load();
	if (module?.default) {
		mountRenderlessPage(module.default);
	}
}

boot();
