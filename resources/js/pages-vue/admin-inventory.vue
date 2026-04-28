<template>
  <div class="space-y-6">
    <div class="flex gap-4">
      <button @click="importInventory" class="bg-green-600 text-white px-6 py-2 rounded-lg">
        📥 Nhập Từ File
      </button>
      <button @click="exportInventory" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
        📤 Xuất Ra File
      </button>
      <button @click="viewValue" class="bg-purple-600 text-white px-6 py-2 rounded-lg">
        💰 Giá Trị Kho
      </button>
    </div>

    <!-- Inventory Stats -->
    <div class="grid grid-cols-3 gap-4">
      <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-400">
        <div class="text-sm text-gray-600">Tổng Giá Trị Kho</div>
        <div class="text-3xl font-bold text-blue-600">{{ formatCurrency(totalValue) }}</div>
      </div>
      <div class="bg-red-50 p-4 rounded-lg border-l-4 border-red-400">
        <div class="text-sm text-gray-600">Hàng Sắp Hết</div>
        <div class="text-3xl font-bold text-red-600">{{ lowStockCount }}</div>
      </div>
      <div class="bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-400">
        <div class="text-sm text-gray-600">Sắp Hết Hạn</div>
        <div class="text-3xl font-bold text-yellow-600">{{ expiringCount }}</div>
      </div>
    </div>

    <!-- Inventory Report Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-100 border-b">
          <tr>
            <th class="px-6 py-3 text-left">Tên Thuốc</th>
            <th class="px-6 py-3 text-left">SKU</th>
            <th class="px-6 py-3 text-right">Số Lượng</th>
            <th class="px-6 py-3 text-right">Giá Đơn Vị</th>
            <th class="px-6 py-3 text-right">Tổng Giá Trị</th>
            <th class="px-6 py-3 text-left">Hạn Dùng</th>
            <th class="px-6 py-3 text-left">Trạng Thái</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="item in inventory" :key="item.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium">{{ item.name }}</td>
            <td class="px-6 py-4">{{ item.sku }}</td>
            <td class="px-6 py-4 text-right font-semibold" :class="{
              'text-red-600': item.stock_quantity === 0,
              'text-orange-600': item.stock_quantity < 10
            }">
              {{ item.stock_quantity }}
            </td>
            <td class="px-6 py-4 text-right">{{ formatCurrency(item.price) }}</td>
            <td class="px-6 py-4 text-right font-bold">{{ formatCurrency(item.stock_quantity * item.price) }}</td>
            <td class="px-6 py-4" :class="{
              'text-red-600 font-bold': isExpired(item.expiration_date),
              'text-orange-600': isExpiringSoon(item.expiration_date)
            }">
              {{ formatDate(item.expiration_date) }}
            </td>
            <td class="px-6 py-4">
              <span :class="[
                'px-2 py-1 rounded text-xs font-semibold',
                item.stock_quantity > 50 ? 'bg-green-100 text-green-800' :
                item.stock_quantity > 10 ? 'bg-blue-100 text-blue-800' :
                item.stock_quantity > 0 ? 'bg-yellow-100 text-yellow-800' :
                'bg-red-100 text-red-800'
              ]">
                {{ inventoryStatus(item) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const inventory = ref([]);

const formatCurrency = (value) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN');
};

const isExpired = (date) => {
  return date && new Date(date) < new Date();
};

const isExpiringSoon = (date) => {
  if (!date) return false;
  const days = (new Date(date) - new Date()) / (1000 * 60 * 60 * 24);
  return days > 0 && days <= 30;
};

const inventoryStatus = (item) => {
  if (item.stock_quantity === 0) return 'Hết Hàng';
  if (item.stock_quantity < 10) return 'Sắp Hết';
  if (item.stock_quantity > 50) return 'Dồi Dào';
  return 'Bình Thường';
};

const totalValue = computed(() =>
  inventory.value.reduce((sum, item) => sum + (item.stock_quantity * item.price), 0)
);

const lowStockCount = computed(() =>
  inventory.value.filter(item => item.stock_quantity < 10 && item.stock_quantity > 0).length
);

const expiringCount = computed(() =>
  inventory.value.filter(item => isExpiringSoon(item.expiration_date)).length
);

const fetchInventory = async () => {
  try {
    const res = await fetch('/api/admin/inventory', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    const data = await res.json();
    inventory.value = data.data;
  } catch (err) {
    console.error('Lỗi tải kho:', err);
  }
};

const importInventory = () => {
  alert('Chế độ nhập từ file (triển khai Upload CSV)');
};

const exportInventory = () => {
  const csv = ['Tên,SKU,Số Lượng,Giá,Hạn Dùng'];
  inventory.value.forEach(item => {
    csv.push(`"${item.name}","${item.sku}",${item.stock_quantity},${item.price},"${item.expiration_date}"`);
  });
  const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'inventory.csv';
  a.click();
};

const viewValue = () => {
  alert(`Tổng Giá Trị Kho: ${formatCurrency(totalValue.value)}`);
};

onMounted(fetchInventory);
</script>
