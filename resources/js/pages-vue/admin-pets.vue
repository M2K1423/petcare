<template>
  <div class="space-y-6">
    <div class="flex gap-4">
      <input v-model="search" type="text" placeholder="Tìm kiếm thú cưng..." 
        class="flex-1 px-4 py-2 border rounded-lg">
      <select v-model="speciesFilter" class="px-4 py-2 border rounded-lg">
        <option value="">Tất cả loài</option>
        <option value="Chó">Chó</option>
        <option value="Mèo">Mèo</option>
        <option value="Chim">Chim</option>
        <option value="Thỏ">Thỏ</option>
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
            <td class="px-6 py-4">{{ pet.species }}</td>
            <td class="px-6 py-4">{{ pet.breed }}</td>
            <td class="px-6 py-4">{{ pet.birth_year }}</td>
            <td class="px-6 py-4 text-right">{{ pet.weight }}kg</td>
            <td class="px-6 py-4 space-x-2 text-sm">
              <button @click="viewPet(pet)" class="text-blue-600 hover:text-blue-800">👁️ Xem</button>
              <button @click="viewAppointments(pet)" class="text-green-600 hover:text-green-800">📅</button>
              <button @click="viewHealth(pet)" class="text-purple-600 hover:text-purple-800">⚕️</button>
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
            <p>{{ selectedPet?.species }}</p>
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
            <p>{{ selectedPet?.gender === 'male' ? '🐕 Đực' : '🐈 Cái' }}</p>
          </div>
        </div>

        <button @click="showDetail = false" class="w-full bg-gray-300 py-2 rounded hover:bg-gray-400">
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const pets = ref([]);
const search = ref('');
const speciesFilter = ref('');
const showDetail = ref(false);
const selectedPet = ref(null);

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
    result = result.filter(p => p.species === speciesFilter.value);
  }
  return result;
});

const fetchPets = async () => {
  try {
    const res = await fetch('/api/admin/pets', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();
    pets.value = data.data;
  } catch (err) {
    console.error('Lỗi tải thú cưng:', err);
  }
};

const viewPet = (pet) => {
  selectedPet.value = pet;
  showDetail.value = true;
};

const viewAppointments = (pet) => {
  alert(`Lịch hẹn của ${pet.name}:\n(Sẽ hiển thị danh sách lịch hẹn)`);
};

const viewHealth = (pet) => {
  alert(`Hồ sơ sức khỏe của ${pet.name}:\n(Sẽ hiển thị hồ sơ y tế)`);
};

onMounted(fetchPets);
</script>
