import { callApi } from '../auth/http';

interface QueueItem {
    id: number;
    queue_number: number;
    is_emergency: boolean;
    pet: { name: string; species: { name: string } };
    owner: { name: string; phone: string; email: string };
    doctor?: { user: { name: string } };
}

interface DoctorWorkload {
    id: number;
    user: { name: string };
    pending_appointments_count: number;
}

document.addEventListener('DOMContentLoaded', () => {
    fetchDashboardData();
    
    document.getElementById('btn-sync-queue')?.addEventListener('click', () => {
        fetchDashboardData();
    });
});

async function fetchDashboardData() {
    try {
        await Promise.all([
            fetchQueue(),
            fetchDoctors(),
            fetchUnpaidStats()
        ]);
    } catch (e) {
        console.error('Error fetching dashboard info', e);
    }
}

async function fetchQueue() {
    const queueData = await callApi<any>('/api/receptionist/queue', 'GET');
    const container = document.getElementById('queue-container');
    const statEl = document.getElementById('stat-queue');
    
    if (!container || !statEl) return;
    
    const items: QueueItem[] = queueData?.data || [];
    statEl.innerText = items.length.toString();
    
    if (items.length === 0) {
        container.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">Queue is currently empty.</div>';
        return;
    }
    
    container.innerHTML = items.map((app) => `
        <div class="flex items-center justify-between rounded-xl border ${app.is_emergency ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white'} p-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full ${app.is_emergency ? 'bg-red-200 text-red-700' : 'bg-blue-100 text-blue-700'} text-xl font-bold">
                    #${app.queue_number}
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">${app.pet?.name || 'Unknown Pet'} ${app.is_emergency ? '<span class="ml-2 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">EMERGENCY</span>' : ''}</h3>
                    <p class="text-xs text-gray-500">Owner: ${app.owner?.name}</p>
                    <p class="text-xs text-gray-500">Doctor: ${app.doctor?.user?.name || 'Pending Assignment'}</p>
                </div>
            </div>
            <div class="flex gap-2">
                ${!app.is_emergency ? `<button onclick="markEmergency(${app.id})" class="rounded-lg bg-orange-100 px-3 py-1.5 text-xs font-semibold text-orange-700 hover:bg-orange-200">Alert Emergency</button>` : ''}
            </div>
        </div>
    `).join('');
}

async function fetchDoctors() {
    const doctorsData = await callApi<any>('/api/receptionist/doctors/available', 'GET');   
    const container = document.getElementById('doctors-container');
    const statEl = document.getElementById('stat-doctors');
    
    if (!container || !statEl) return;
    
    const items: DoctorWorkload[] = doctorsData?.data || [];
    statEl.innerText = items.length.toString();
    
    if (items.length === 0) {
        container.innerHTML = '<div class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">No doctors on shift.</div>';
        return;
    }
    
    container.innerHTML = items.map((doc) => `
        <div class="flex items-center justify-between rounded-xl border border-gray-200 p-4">
            <span class="font-semibold text-gray-800">Dr. ${doc.user?.name}</span>
            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Pending: ${doc.pending_appointments_count || 0}</span>
        </div>
    `).join('');
}

async function fetchUnpaidStats() {
    const unpaidData = await callApi<any>('/api/receptionist/payments/unpaid', 'GET');      
    const statEl = document.getElementById('stat-unpaid');
    
    if (!statEl) return;
    
    const list = unpaidData?.data || [];
    statEl.innerText = list.length.toString();
}

(window as any).markEmergency = async function(appId: number) {
    if(!confirm("Are you sure you want to flag this as an emergency?")) return;
    
    try {
        await callApi<any>(`/api/receptionist/appointments/${appId}/emergency`, 'PATCH');
        showToast('Emergency alert triggered');
        fetchDashboardData();
    } catch (e) {
        alert('Failed to mark emergency.');
    }
};

function showToast(msg: string) {
    const el = document.getElementById('toast-message');
    if (!el) return;
    el.innerText = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3000);
}