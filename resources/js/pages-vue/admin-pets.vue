<template>
  <div class="space-y-6">
    <div class="flex gap-4">
      <input v-model="search" type="text" placeholder="Tìm kiếm thú cưng..." 
        class="flex-1 px-4 py-2 border rounded-lg">
      <select v-model="speciesFilter" class="px-4 py-2 border rounded-lg">
        <option value="">Tất cả loài</option>
        <option value="Dog">Chó</option>
        <option value="Cat">Mèo</option>
        <option value="Bird">Chim</option>
        <option value="Rabbit">Thỏ</option>
      </select>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left">Tên Thú Cưng</th>
            <th class="px-6 py-3 text-left">Chủ Nuôi</th>
            <th class="px-6 py-3 text-left">Loài</th>
            <th class="px-6 py-3 text-left">Giống</th>
            <th class="px-6 py-3 text-left">Năm Sinh</th>
            <th class="px-6 py-3 text-right">Cân Nặng</th>
            <th class="px-6 py-3 text-left">Hành Động</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="pet in filteredPets" :key="pet.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ pet.name }}</td>
            <td class="px-6 py-4">{{ pet.owner?.name || 'N/A' }}</td>
            <td class="px-6 py-4">{{ translateSpecies(pet.species?.name) }}</td>
            <td class="px-6 py-4">{{ pet.breed }}</td>
            <td class="px-6 py-4">{{ pet.birth_year }}</td>
            <td class="px-6 py-4 text-right">{{ pet.weight }}kg</td>
            <td class="px-6 py-4 space-x-3 text-sm flex items-center justify-end">
              <button @click="viewPet(pet)" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800">
                <Eye class="w-4 h-4" />
                <span>Xem</span>
              </button>
              <button @click="viewAppointments(pet)" class="inline-flex items-center text-green-600 hover:text-green-800" title="Xem lịch hẹn">
                <Calendar class="w-4 h-4" />
              </button>
              <button @click="viewHealth(pet)" class="inline-flex items-center text-purple-600 hover:text-purple-800" title="Xem hồ sơ sức khỏe">
                <Activity class="w-4 h-4" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pet Detail Modal -->
    <div v-if="showDetail" class="fixed inset-0 bg-black/50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-8 w-full max-w-2xl">
        <h3 class="text-2xl font-bold mb-4">{{ selectedPet?.name }}</h3>

        <div class="grid grid-cols-2 gap-4 mb-6">
          <div>
            <strong class="text-gray-600">Chủ Nuôi:</strong>
            <p>{{ selectedPet?.owner?.name }}</p>
          </div>
          <div>
            <strong class="text-gray-600">Loài:</strong>
            <p>{{ translateSpecies(selectedPet?.species?.name) }}</p>
          </div>
          <div>
            <strong class="text-gray-600">Giống:</strong>
            <p>{{ selectedPet?.breed }}</p>
          </div>
          <div>
            <strong class="text-gray-600">Cân Nặng:</strong>
            <p>{{ selectedPet?.weight }}kg</p>
          </div>
          <div>
            <strong class="text-gray-600">Năm Sinh:</strong>
            <p>{{ selectedPet?.birth_year }}</p>
          </div>
          <div>
            <strong class="text-gray-600">Giới Tính:</strong>
            <p>{{ selectedPet?.gender === 'male' ? 'Đực' : 'Cái' }}</p>
          </div>
        </div>

        <button @click="showDetail = false" class="w-full bg-gray-300 py-2 rounded hover:bg-gray-400">
          Đóng
        </button>
      </div>
    </div>

    <!-- Pet Appointments Modal -->
    <div v-if="showAppointments" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100]">
      <div class="bg-white rounded-lg p-8 w-full max-w-2xl max-h-[80vh] flex flex-col">
        <h3 class="text-2xl font-bold mb-4">Lịch Hẹn của {{ selectedPet?.name }}</h3>

        <div class="flex-1 overflow-y-auto mb-6">
          <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b">
              <tr>
                <th class="px-4 py-2 text-left">Ngày Giờ</th>
                <th class="px-4 py-2 text-left">Bác Sĩ</th>
                <th class="px-4 py-2 text-left">Dịch Vụ</th>
                <th class="px-4 py-2 text-left">Trạng Thái</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="apt in petAppointments" :key="apt.id" class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">{{ formatDateTime(apt.appointment_at) }}</td>
                <td class="px-4 py-3">{{ apt.doctor?.name || 'Chưa phân công' }}</td>
                <td class="px-4 py-3">{{ apt.service?.name || 'N/A' }}</td>
                <td class="px-4 py-3">
                  <span :class="[
                    'px-2 py-0.5 rounded text-xs font-semibold',
                    apt.status === 'completed' ? 'bg-green-100 text-green-800' :
                    apt.status === 'cancelled' ? 'bg-red-100 text-red-800' :
                    'bg-yellow-100 text-yellow-800'
                  ]">{{ translateStatus(apt.status) }}</span>
                </td>
              </tr>
              <tr v-if="petAppointments.length === 0">
                <td colspan="4" class="px-4 py-6 text-center text-gray-500">Chưa có lịch hẹn nào.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <button @click="showAppointments = false" class="w-full bg-gray-300 py-2 rounded hover:bg-gray-400 font-semibold">
          Đóng
        </button>
      </div>
    </div>

    <!-- Pet Health Records Modal -->
    <div v-if="showHealthRecords" class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100]">
      <div class="bg-white rounded-lg p-8 w-full max-w-3xl max-h-[80vh] flex flex-col">
        <h3 class="text-2xl font-bold mb-4">Hồ Sơ Sức Khỏe của {{ selectedPet?.name }}</h3>

        <div class="flex-1 overflow-y-auto mb-6 space-y-4">
          <div v-for="rec in petHealthRecords" :key="rec.id" class="border rounded-lg p-4 bg-gray-50 hover:shadow transition">
            <div class="flex justify-between items-start mb-2 border-b pb-2">
              <div>
                <span class="font-semibold text-blue-600">{{ formatDateTime(rec.record_date || rec.created_at) }}</span>
                <span class="text-gray-500 text-xs ml-2">| Bác sĩ: {{ rec.doctor?.name || 'N/A' }}</span>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm mt-2">
              <div>
                <strong class="text-gray-600">Triệu chứng:</strong>
                <p class="text-gray-800">{{ rec.symptoms || 'N/A' }}</p>
              </div>
              <div>
                <strong class="text-gray-600">Chẩn đoán:</strong>
                <p class="text-gray-800 font-semibold">{{ rec.diagnosis || 'N/A' }}</p>
              </div>
              <div class="col-span-2">
                <strong class="text-gray-600">Phác đồ điều trị:</strong>
                <p class="text-gray-800">{{ rec.treatment_plan || 'N/A' }}</p>
              </div>
              <div class="col-span-2" v-if="rec.notes">
                <strong class="text-gray-600">Ghi chú thêm:</strong>
                <p class="text-gray-800 text-xs italic">{{ rec.notes }}</p>
              </div>
            </div>
          </div>
          <div v-if="petHealthRecords.length === 0" class="text-center py-8 text-gray-500">
            Chưa có bệnh án nào.
          </div>
        </div>

        <button @click="showHealthRecords = false" class="w-full bg-gray-300 py-2 rounded hover:bg-gray-400 font-semibold">
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useNotification } from '../composables/useNotification';
import { Eye, Calendar, Activity } from '@lucide/vue';

const { notifyInfo, handleApiError } = useNotification();

const pets = ref([]);
const search = ref('');
const speciesFilter = ref('');
const showDetail = ref(false);
const selectedPet = ref(null);
const showAppointments = ref(false);
const petAppointments = ref([]);
const showHealthRecords = ref(false);
const petHealthRecords = ref([]);

const filteredPets = computed(() => {
  let result = pets.value;
  if (search.value) {
    const q = search.value.toLowerCase();
    result = result.filter(p => 
      p.name.toLowerCase().includes(q) ||
      p.owner?.name?.toLowerCase().includes(q)
    );
  }
  if (speciesFilter.value) {
    result = result.filter(p => p.species?.name === speciesFilter.value);
  }
  return result;
});

const isLoading = ref(true);

const fetchPets = async () => {
  isLoading.value = true;
  try {
    const res = await fetch('/api/admin/pets', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
    });
    if (!res.ok) {
        await handleApiError(null, res);
        return;
    }
    const data = await res.json();
    pets.value = data.data;
  } catch (err) {
    handleApiError(err);
  } finally {
    isLoading.value = false;
  }
};

const translateSpecies = (name) => {
  const translations = {
    'Dog': 'Chó',
    'Cat': 'Mèo',
    'Bird': 'Chim',
    'Rabbit': 'Thỏ'
  };
  return translations[name] || name || 'N/A';
};

const viewPet = (pet) => {
  selectedPet.value = pet;
  showDetail.value = true;
};

const viewAppointments = async (pet) => {
  selectedPet.value = pet;
  try {
    const res = await fetch(`/api/admin/pets/${pet.id}/appointments`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
    });
    if (res.ok) {
      const data = await res.json();
      petAppointments.value = data.data;
      showAppointments.value = true;
    } else {
      await handleApiError(null, res);
    }
  } catch (err) {
    handleApiError(err);
  }
};

const viewHealth = async (pet) => {
  selectedPet.value = pet;
  try {
    const res = await fetch(`/api/admin/pets/${pet.id}/health-records`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('petcare_sanctum_token')}` }
    });
    if (res.ok) {
      const data = await res.json();
      petHealthRecords.value = data.data;
      showHealthRecords.value = true;
    } else {
      await handleApiError(null, res);
    }
  } catch (err) {
    handleApiError(err);
  }
};

const formatDateTime = (val) => {
  if (!val) return 'N/A';
  return new Date(val).toLocaleString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const translateStatus = (val) => {
  const statuses = {
    'pending': 'Chờ xác nhận',
    'confirmed': 'Đã xác nhận',
    'completed': 'Đã hoàn thành',
    'cancelled': 'Đã hủy',
    'check_in': 'Đã check-in'
  };
  return statuses[val] || val;
};

onMounted(fetchPets);
</script>
