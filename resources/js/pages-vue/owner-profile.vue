<template>
  <template v-if="!isLoading">
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] lg:col-span-2">
            <h1 class="text-2xl font-bold text-[#333333]">Hồ sơ</h1>
            <p class="mt-2 text-sm text-[#4A4A4A]">Cập nhật thông tin tài khoản dùng cho đặt lịch hẹn và liên hệ.</p>

            <p :class="statusClass">{{ statusMessage }}</p>

            <form @submit.prevent="updateProfile" class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Họ và tên</label>
                    <input v-model="form.name" required type="text" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" placeholder="Nhập họ và tên" />
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Email</label>
                    <input v-model="form.email" required type="email" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" placeholder="ban@example.com" />
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Số điện thoại</label>
                    <input v-model="form.phone" type="tel" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" placeholder="Nhập số điện thoại" />
                </div>

                <div class="md:col-span-2">
                    <button type="submit" :disabled="isSaving" class="inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780] disabled:opacity-50">
                        {{ isSaving ? 'Đang lưu...' : 'Lưu hồ sơ' }}
                    </button>
                </div>
            </form>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <h2 class="text-lg font-bold text-[#333333]">Thông tin tài khoản</h2>
            <dl class="mt-4 space-y-3 text-sm text-[#4A4A4A]">
                <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-2">
                    <dt class="text-xs uppercase tracking-[0.08em] text-[#6B7280]">Vai trò</dt>
                    <dd class="mt-1 font-semibold text-[#333333]">{{ user.role ? user.role.toUpperCase() : 'N/A' }}</dd>
                </div>
                <div class="rounded-xl border border-[#DDE1E6] bg-[#F9FBFD] px-3 py-2">
                    <dt class="text-xs uppercase tracking-[0.08em] text-[#6B7280]">Mã người dùng</dt>
                    <dd class="mt-1 font-semibold text-[#333333]">{{ user.id ?? '-' }}</dd>
                </div>
            </dl>

            <div class="mt-5 rounded-xl border border-dashed border-[#C7CDD5] bg-[#F8FAFC] p-3 text-sm text-[#4A4A4A]">
                Cần quản lý thú cưng hoặc lịch hẹn?
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="/owner/pets" class="rounded-lg border border-[#C1C4C9] bg-[#FFFFFF] px-3 py-1.5 text-xs font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Thú cưng</a>
                    <a href="/owner/appointments" class="rounded-lg border border-[#C1C4C9] bg-[#FFFFFF] px-3 py-1.5 text-xs font-semibold text-[#333333] transition hover:border-[#2A6496] hover:text-[#2A6496]">Lịch hẹn</a>
                </div>
            </div>
        </article>
    </section>
  </template>
  <LoadingSpinner v-else />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';

const isLoading = ref(true);
const isSaving = ref(false);

const statusMessage = ref('Đang tải hồ sơ của bạn...');
const statusClass = ref('mt-4 text-sm text-[#4A4A4A]');

const user = ref({});
const form = reactive({
    name: '',
    email: '',
    phone: ''
});

function setStatus(message, kind = 'neutral') {
    statusMessage.value = message;
    const classMap = {
        neutral: 'mt-4 text-sm text-[#4A4A4A]',
        success: 'mt-4 text-sm text-emerald-700',
        error: 'mt-4 text-sm text-rose-700',
    };
    statusClass.value = classMap[kind];
}

async function loadProfile() {
    isLoading.value = true;
    try {
        const response = await callApi('/api/auth/me', 'GET');
        
        if (!response.authenticated || !response.user) {
            throw new Error('Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.');
        }

        if (response.user.role !== 'owner') {
            throw new Error('Cần tài khoản chủ nuôi để dùng trang này.');
        }

        user.value = response.user;
        form.name = user.value.name ?? '';
        form.email = user.value.email ?? '';
        form.phone = user.value.phone ?? '';
        setStatus('Hồ sơ của bạn đã sẵn sàng để chỉnh sửa.');
    } catch (error) {
        setStatus(error.message, 'error');
    } finally {
        isLoading.value = false;
    }
}

async function updateProfile() {
    isSaving.value = true;
    try {
        const response = await callApi('/api/auth/profile', 'PUT', {
            name: form.name,
            email: form.email,
            phone: form.phone
        });

        if (response.user) {
            user.value = response.user;
            form.name = user.value.name ?? '';
            form.email = user.value.email ?? '';
            form.phone = user.value.phone ?? '';
        }

        setStatus(response.message ?? 'Đã cập nhật hồ sơ thành công.', 'success');
    } catch (error) {
        setStatus(error.message, 'error');
    } finally {
        isSaving.value = false;
    }
}

onMounted(() => {
    loadProfile();
});
</script>
