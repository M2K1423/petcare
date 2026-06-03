import { createApp } from 'vue';
import './owner-pay';
import './cart-store';
import './realtime';

const pageNode = document.querySelector('[data-page]:not([data-page=""])');
const pageKey = pageNode?.dataset?.page || '';

const pageLoaders = {
	'auth-sanctum': () => import('./pages-vue/sanctum-auth.vue'),
	'auth-sanctum-register': () => import('./pages-vue/sanctum-auth.vue'),

	'owner-overview': () => import('./pages-vue/owner-overview.vue'),
	'owner-profile': () => import('./pages-vue/owner-profile.vue'),
	'owner-pets': () => import('./pages-vue/owner-pets.vue'),
	'owner-pet-edit': () => import('./pages-vue/owner-pet-edit.vue'),
	'owner-pet-health-records': () => import('./pages-vue/owner-pet-health-records.vue'),
	'owner-appointments': () => import('./pages-vue/owner-appointments.vue'),
	'owner-shop': () => import('./pages-vue/owner-shop.vue'),
	'owner-medicine-detail': () => import('./pages-vue/owner-medicine-detail.vue'),
	'owner-orders': () => import('./pages-vue/owner-orders.vue'),
	'owner-cart': () => import('./pages-vue/owner-cart.vue'),
	'vet-appointments': () => import('./pages-vue/vet-appointments.vue'),
	'vet-appointments-week': () => import('./pages-vue/vet-appointments-week.vue'),
	'vet-appointment-detail': () => import('./pages-vue/vet-appointment-detail.vue'),
	'vet-workflow': () => import('./pages-vue/vet-workflow.vue'),

	'receptionist-dashboard': () => import('./pages-vue/receptionist-dashboard.vue'),
	'receptionist-appointments': () => import('./pages-vue/receptionist-appointments.vue'),
	'receptionist-appointment-detail': () => import('./pages-vue/receptionist-appointment-detail.vue'),
	'receptionist-shop': () => import('./pages-vue/receptionist-shop.vue'),
	'receptionist-billing': () => import('./pages-vue/receptionist-billing.vue'),
	'receptionist-walkins': () => import('./pages-vue/receptionist-walkins.vue'),

	// Admin Pages
	'admin-layout': () => import('./pages-vue/admin-layout.vue'),
	'admin-dashboard': () => import('./pages-vue/admin-dashboard.vue'),
	'admin-users': () => import('./pages-vue/admin-users.vue'),
	'admin-doctors': () => import('./pages-vue/admin-doctors.vue'),
	'admin-services': () => import('./pages-vue/admin-services.vue'),
	'admin-medicines': () => import('./pages-vue/admin-medicines.vue'),
	'admin-pets': () => import('./pages-vue/admin-pets.vue'),
	'admin-appointments': () => import('./pages-vue/admin-appointments.vue'),
	'admin-payments': () => import('./pages-vue/admin-payments.vue'),
	'admin-inventory': () => import('./pages-vue/admin-inventory.vue'),
	'admin-reports': () => import('./pages-vue/admin-reports.vue'),
	'admin-settings': () => import('./pages-vue/admin-settings.vue'),
	'admin-logs': () => import('./pages-vue/admin-logs.vue'),
};

import { useNotification } from './composables/useNotification';

async function boot() {
	const load = pageLoaders[pageKey];
	if (!load) return;

	const module = await load();
	if (module?.default) {
		const app = createApp(module.default);
		if (pageNode && pageNode !== document.body) {
			app.mount(pageNode);
		} else {
			const mountEl = document.createElement('div');
			document.body.appendChild(mountEl);
			app.mount(mountEl);
		}
	}
}

// Hàm mount riêng cho Chat Widget (Toàn cục)
async function mountChatWidget() {
	const root = document.getElementById('chat-widget-root');
	if (root) {
		const module = await import('./pages-vue/chat-widget.vue');
		if (module?.default) {
			const chatApp = createApp(module.default);
			chatApp.mount(root);
		}
	}
}

boot().then(() => {
	mountChatWidget();
	// Lắng nghe sự kiện realtime và hiển thị Toast
	const { notifyInfo } = useNotification();
	window.addEventListener('petcare-notification-received', (event) => {
		const data = event.detail;
		if (data) {
			const message = data.message || data.title || data.content || 'Bạn có một thông báo mới!';
			notifyInfo(`🔔 ${message}`);
		}
	});
});
