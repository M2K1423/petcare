<template>
  <div class="flex flex-col gap-6">
    <!-- Header Page -->
    <div class="border-b border-slate-100 pb-4">
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-800 flex items-center gap-2">
        <FileText class="w-6 h-6 text-indigo-500" />
        Hồ sơ bệnh án & Tiêm phòng
      </h1>
      <p class="text-sm text-slate-400 mt-1">Xem chi tiết lịch sử khám bệnh, chỉ số sinh tồn, đơn thuốc và lịch tiêm phòng của các bé cưng.</p>
    </div>

    <!-- Main Content Grid -->
    <section class="grid gap-6 lg:grid-cols-4">
      <!-- Left Sidebar: Pets Selector List (1/4 cols) -->
      <aside class="lg:col-span-1 flex flex-col gap-4">
        <article class="rounded-3xl border border-slate-100 bg-white p-5 shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
          <h2 class="text-sm font-bold uppercase tracking-[0.12em] text-slate-400 mb-3 flex items-center gap-1.5">
            <PawPrint class="w-4 h-4 text-indigo-500" />
            Chọn thú cưng
          </h2>
          
          <div class="space-y-2">
            <button 
              v-for="p in petsList" 
              :key="p.id"
              @click="selectPet(p.id)"
              :class="[
                selectedPetId === p.id 
                  ? 'bg-indigo-50 text-indigo-700 border-indigo-200 ring-2 ring-indigo-500/10' 
                  : 'bg-slate-50/50 hover:bg-slate-50 text-slate-600 border-slate-100 hover:border-slate-200'
              ]"
              class="w-full text-left p-3.5 rounded-2xl border flex items-center justify-between transition duration-300 group"
            >
              <div class="flex items-center gap-3">
                <div 
                  :class="[
                    selectedPetId === p.id 
                      ? 'bg-indigo-600 text-white' 
                      : 'bg-slate-200 text-slate-500 group-hover:bg-slate-300'
                  ]"
                  class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-sm transition duration-300"
                >
                  {{ p.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <p class="text-sm font-extrabold truncate max-w-[120px]">{{ p.name }}</p>
                  <p class="text-xs text-slate-400 truncate max-w-[120px]">{{ translateSpecies(p.species?.name) }}</p>
                </div>
              </div>
              <ChevronRight :class="[selectedPetId === p.id ? 'text-indigo-500 translate-x-0.5' : 'text-slate-300']" class="w-4 h-4 transition duration-300" />
            </button>

            <div v-if="petsList.length === 0 && !isLoadingPets" class="text-center py-8">
              <p class="text-xs font-semibold text-slate-400">Bạn chưa có thú cưng nào.</p>
              <a href="/owner/pets" class="mt-2 text-xs font-bold text-indigo-500 hover:underline inline-block">Thêm thú cưng ngay</a>
            </div>
          </div>
        </article>
      </aside>

      <!-- Right Panel: Health Records Details (3/4 cols) -->
      <main class="lg:col-span-3 flex flex-col gap-6">
        <!-- Loading State -->
        <article v-if="isLoadingRecords" class="rounded-3xl border border-slate-100 bg-white p-20 shadow-[0_8px_30px_rgb(0,0,0,0.015)] flex flex-col items-center justify-center">
          <LoadingSpinner />
          <p class="mt-4 text-sm font-semibold text-slate-500">Đang tải hồ sơ bệnh án...</p>
        </article>

        <!-- Main Details (When loaded and pet selected) -->
        <div v-else-if="selectedPet" class="flex flex-col gap-6">
          <!-- Pet Info Banner Card -->
          <article class="rounded-3xl border border-slate-100 bg-white p-6 shadow-[0_8px_30px_rgb(0,0,0,0.015)] relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/[0.015] rounded-bl-full pointer-events-none"></div>
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl">
                <PawPrint class="w-7 h-7" />
              </div>
              <div>
                <h2 class="text-xl font-extrabold text-slate-800">{{ selectedPet.name }}</h2>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-slate-400">
                  <p><span class="font-semibold text-slate-500">Loài:</span> {{ translateSpecies(selectedPet.species?.name) }}</p>
                  <p v-if="selectedPet.breed"><span class="font-semibold text-slate-500">Giống:</span> {{ selectedPet.breed }}</p>
                  <p v-if="selectedPet.weight"><span class="font-semibold text-slate-500">Cân nặng:</span> {{ selectedPet.weight }} kg</p>
                  <p v-if="selectedPet.birth_date"><span class="font-semibold text-slate-500">Ngày sinh:</span> {{ formatDateOnly(selectedPet.birth_date) }}</p>
                </div>
              </div>
            </div>

            <!-- Tab view switcher -->
            <div class="flex rounded-xl bg-slate-100 p-1 self-start md:self-center">
              <button 
                @click="activeTab = 'records'"
                :class="[activeTab === 'records' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800']"
                class="rounded-lg px-4 py-1.5 text-xs font-bold transition duration-200"
              >
                Bệnh án ({{ medicalRecords.length }})
              </button>
              <button 
                @click="activeTab = 'vaccinations'"
                :class="[activeTab === 'vaccinations' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800']"
                class="rounded-lg px-4 py-1.5 text-xs font-bold transition duration-200"
              >
                Lịch tiêm phòng ({{ vaccinations.length }})
              </button>
            </div>
          </article>

          <!-- Tab 1: Medical Records list -->
          <div v-if="activeTab === 'records'" class="space-y-4">
            <div v-if="medicalRecords.length === 0" class="rounded-3xl border border-dashed border-slate-200 bg-white p-16 text-center shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
              <FileText class="w-12 h-12 text-slate-300 mx-auto mb-3" />
              <p class="text-sm font-bold text-slate-500">Chưa có hồ sơ bệnh án nào.</p>
              <p class="text-xs text-slate-400 mt-1">Các bệnh án sẽ tự động xuất hiện sau khi bé cưng hoàn thành lượt khám tại phòng khám.</p>
            </div>

            <article 
              v-for="record in medicalRecords" 
              :key="record.id" 
              class="rounded-3xl border border-slate-100 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.015)] overflow-hidden transition-all duration-300"
            >
              <!-- Accordion Header -->
              <div 
                @click="toggleRecordExpand(record.id)"
                class="p-5 flex flex-wrap items-center justify-between gap-4 cursor-pointer hover:bg-slate-50/40 transition duration-200"
              >
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-indigo-50/50 text-indigo-600 flex items-center justify-center">
                    <Activity class="w-5 h-5" />
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <p class="text-sm font-extrabold text-slate-800">{{ record.record_code || 'MR-' + record.id }}</p>
                      <span :class="getSeverityClass(record.severity_level)" class="text-[9px] uppercase tracking-wider font-extrabold px-2 py-0.5 rounded-full border">
                        {{ getSeverityLabel(record.severity_level) }}
                      </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Ngày khám: {{ formatDate(record.record_date) }}</p>
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <div class="text-right hidden sm:block">
                    <p class="text-xs text-slate-400">Bác sĩ khám</p>
                    <p class="text-sm font-bold text-slate-700">{{ record.doctor?.user?.name || 'Bác sĩ PetCare' }}</p>
                  </div>
                  <button class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-100 transition">
                    <ChevronDown v-if="!expandedRecordIds.includes(record.id)" class="w-4 h-4" />
                    <ChevronUp v-else class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <!-- Accordion Body (Only shown when expanded) -->
              <div v-show="expandedRecordIds.includes(record.id)" class="px-6 pb-6 border-t border-slate-50 pt-5 space-y-6">
                <!-- Row 1: Vitals Block -->
                <div class="grid gap-4 grid-cols-3 bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                  <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center">
                      <Thermometer class="w-4 h-4" />
                    </div>
                    <div>
                      <p class="text-[10px] uppercase font-bold text-slate-400">Nhiệt độ</p>
                      <p class="text-sm font-extrabold text-slate-700">{{ record.temperature_c ? record.temperature_c + ' °C' : '-' }}</p>
                    </div>
                  </div>

                  <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                      <Scale class="w-4 h-4" />
                    </div>
                    <div>
                      <p class="text-[10px] uppercase font-bold text-slate-400">Cân nặng</p>
                      <p class="text-sm font-extrabold text-slate-700">{{ record.weight_kg ? record.weight_kg + ' kg' : '-' }}</p>
                    </div>
                  </div>

                  <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center">
                      <Heart class="w-4 h-4" />
                    </div>
                    <div>
                      <p class="text-[10px] uppercase font-bold text-slate-400">Nhịp tim</p>
                      <p class="text-sm font-extrabold text-slate-700">{{ record.heart_rate_bpm ? record.heart_rate_bpm + ' bpm' : '-' }}</p>
                    </div>
                  </div>
                </div>

                <!-- Row 2: Symptoms & Diagnoses -->
                <div class="grid gap-6 md:grid-cols-2">
                  <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                      <AlertCircle class="w-3.5 h-3.5 text-indigo-500" />
                      Triệu chứng lâm sàng
                    </h4>
                    <div class="text-sm text-slate-600 bg-slate-50/30 p-3.5 rounded-2xl border border-slate-100 min-h-[70px]">
                      <p class="font-bold text-slate-800">Triệu chứng chính:</p>
                      <p class="mt-1">{{ record.symptoms || 'Không ghi nhận' }}</p>
                      
                      <p v-if="record.abnormal_signs" class="font-bold text-rose-500 mt-2">Dấu hiệu bất thường:</p>
                      <p v-if="record.abnormal_signs" class="mt-0.5 text-rose-600">{{ record.abnormal_signs }}</p>
                    </div>
                  </div>

                  <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                      <FileText class="w-3.5 h-3.5 text-indigo-500" />
                      Chẩn đoán bệnh lý
                    </h4>
                    <div class="text-sm text-slate-600 bg-slate-50/30 p-3.5 rounded-2xl border border-slate-100 min-h-[70px]">
                      <p class="font-bold text-slate-800">Chẩn đoán sơ bộ:</p>
                      <p class="mt-1">{{ record.preliminary_diagnosis || '-' }}</p>
                      
                      <p class="font-bold text-indigo-600 mt-2">Chẩn đoán cuối cùng:</p>
                      <p class="mt-0.5 text-indigo-700 font-semibold">{{ record.final_diagnosis || record.diagnosis }}</p>
                      
                      <p v-if="record.pathology" class="mt-2 text-xs font-bold text-slate-500">Bệnh lý chính: <span class="font-normal text-slate-600">{{ record.pathology }}</span></p>
                    </div>
                  </div>
                </div>

                <!-- Row 3: Prescriptions / Medicines -->
                <div class="space-y-3">
                  <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <Pill class="w-3.5 h-3.5 text-emerald-500" />
                    Đơn thuốc chỉ định
                  </h4>
                  
                  <div class="overflow-x-auto rounded-2xl border border-slate-100">
                    <table class="w-full text-left text-sm text-slate-500">
                      <thead class="bg-slate-50 text-xs font-bold uppercase text-slate-400">
                        <tr>
                          <th class="px-4 py-3">Tên thuốc</th>
                          <th class="px-4 py-3">Liều dùng</th>
                          <th class="px-4 py-3 text-center">Số ngày</th>
                          <th class="px-4 py-3">Hướng dẫn sử dụng</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-slate-100">
                        <tr v-for="(med, idx) in parseJson(record.prescriptions)" :key="idx" class="hover:bg-slate-50/30 transition">
                          <td class="px-4 py-3 font-bold text-slate-700">{{ med.medicine_name }}</td>
                          <td class="px-4 py-3 text-xs">{{ med.dosage || '-' }}</td>
                          <td class="px-4 py-3 text-xs text-center font-bold">{{ med.days ? med.days + ' ngày' : '-' }}</td>
                          <td class="px-4 py-3 text-xs text-slate-500 leading-relaxed">{{ med.instructions || '-' }}</td>
                        </tr>
                        <tr v-if="!record.prescriptions || parseJson(record.prescriptions).length === 0">
                          <td colspan="4" class="px-4 py-4 text-center text-xs text-slate-400 italic">Không có thuốc kê chi tiết hoặc thuốc bán lẻ tại quầy.</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div v-if="record.treatment" class="text-xs text-slate-500 bg-slate-50/50 p-3.5 rounded-2xl border border-slate-100 mt-2">
                    <p class="font-bold text-slate-700 mb-1">Hướng dẫn điều trị tóm tắt:</p>
                    <p class="leading-relaxed">{{ record.treatment }}</p>
                  </div>
                </div>

                <!-- Row 4: Service Orders (Tests) & Procedures -->
                <div class="grid gap-6 md:grid-cols-2" v-if="hasOrdersOrProcedures(record)">
                  <!-- Service orders -->
                  <div class="space-y-3" v-if="record.service_orders && parseJson(record.service_orders).length > 0">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                      <Activity class="w-3.5 h-3.5 text-sky-500" />
                      Chỉ định xét nghiệm & Dịch vụ
                    </h4>
                    <div class="space-y-2">
                      <div 
                        v-for="(order, idx) in parseJson(record.service_orders)" 
                        :key="idx" 
                        class="p-3 rounded-xl border border-slate-100 bg-slate-50/30 text-xs flex justify-between items-start"
                      >
                        <div>
                          <p class="font-bold text-slate-700">{{ order.name }}</p>
                          <p v-if="order.result" class="mt-1 text-slate-500 leading-relaxed"><span class="font-bold text-slate-600">Kết quả:</span> {{ order.result }}</p>
                        </div>
                        <span 
                          :class="[
                            order.status === 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100'
                          ]" 
                          class="px-2 py-0.5 rounded-full border font-bold text-[9px] uppercase"
                        >
                          {{ order.status === 'completed' ? 'Đã xong' : 'Chờ KQ' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Procedures -->
                  <div class="space-y-3" v-if="record.procedures && parseJson(record.procedures).length > 0">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                      <FileSpreadsheet class="w-3.5 h-3.5 text-indigo-500" />
                      Điều trị thủ thuật
                    </h4>
                    <div class="space-y-2">
                      <div 
                        v-for="(proc, idx) in parseJson(record.procedures)" 
                        :key="idx" 
                        class="p-3 rounded-xl border border-slate-100 bg-slate-50/30 text-xs flex justify-between items-start"
                      >
                        <div>
                          <p class="font-bold text-slate-700">{{ proc.name }}</p>
                          <p v-if="proc.notes" class="mt-1 text-slate-500 leading-relaxed">{{ proc.notes }}</p>
                        </div>
                        <span 
                          :class="[
                            proc.status === 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-100'
                          ]" 
                          class="px-2 py-0.5 rounded-full border font-bold text-[9px] uppercase"
                        >
                          {{ proc.status === 'completed' ? 'Đã xong' : proc.status || 'Chờ' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Row 5: Treatment Protocol, Follow up plan & Disease progress -->
                <div class="grid gap-6 md:grid-cols-2 border-t border-slate-50 pt-5 text-xs text-slate-600">
                  <div class="space-y-2" v-if="record.treatment_protocol || record.disease_progress">
                    <p v-if="record.treatment_protocol"><span class="font-bold text-slate-700">Phác đồ điều trị:</span> {{ record.treatment_protocol }}</p>
                    <p v-if="record.disease_progress"><span class="font-bold text-slate-700">Tiến triển bệnh:</span> {{ record.disease_progress }}</p>
                  </div>
                  
                  <div class="space-y-2">
                    <p v-if="record.follow_up_plan"><span class="font-bold text-indigo-600">Kế hoạch tái khám & tiêm phòng:</span> {{ record.follow_up_plan }}</p>
                    <p v-if="record.notes"><span class="font-bold text-slate-600">Ghi chú bác sĩ:</span> {{ record.notes }}</p>
                  </div>
                </div>

                <!-- Row 6: Signed off timestamp -->
                <div v-if="record.signed_off_at" class="flex justify-end items-center gap-1.5 text-xs text-emerald-600 font-bold border-t border-slate-50 pt-3">
                  <CheckCircle2 class="w-4 h-4" />
                  <span>Ký duyệt chuyên môn bởi {{ record.doctor?.user?.name }} lúc {{ formatDateTime(record.signed_off_at) }}</span>
                </div>
              </div>
            </article>
          </div>

          <!-- Tab 2: Vaccinations list -->
          <div v-if="activeTab === 'vaccinations'" class="space-y-4">
            <div v-if="vaccinations.length === 0" class="rounded-3xl border border-dashed border-slate-200 bg-white p-16 text-center shadow-[0_8px_30px_rgb(0,0,0,0.015)]">
              <Pill class="w-12 h-12 text-slate-300 mx-auto mb-3" />
              <p class="text-sm font-bold text-slate-500">Chưa có lịch sử tiêm phòng nào.</p>
              <p class="text-xs text-slate-400 mt-1">Lịch sử tiêm phòng của bé sẽ tự động cập nhật khi có mũi tiêm được ghi nhận.</p>
            </div>

            <div v-else class="grid gap-6 md:grid-cols-2">
              <article 
                v-for="item in vaccinations" 
                :key="item.id" 
                class="rounded-3xl border border-slate-100 bg-white p-5 shadow-[0_8px_30px_rgb(0,0,0,0.015)] relative overflow-hidden group hover:shadow-[0_16px_36px_rgba(0,0,0,0.03)] transition duration-300"
              >
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-500/[0.015] rounded-bl-full pointer-events-none"></div>
                
                <div class="flex items-start justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                      💉
                    </div>
                    <div>
                      <p class="text-sm font-extrabold text-slate-800">{{ item.vaccine_name }}</p>
                      <p class="text-xs text-slate-400 mt-0.5">Tiêm ngày: {{ formatDateOnly(item.vaccinated_on) }}</p>
                    </div>
                  </div>
                  
                  <span class="text-[10px] font-bold bg-slate-50 border border-slate-100 text-slate-500 px-2 py-0.5 rounded-full">
                    Mũi tiêm
                  </span>
                </div>

                <div class="mt-4 space-y-2 border-t border-slate-50 pt-3 text-xs text-slate-500">
                  <p class="flex justify-between">
                    <span class="font-semibold text-slate-400">Mũi tiếp theo:</span>
                    <span class="font-bold text-rose-500">{{ item.next_due_on ? formatDateOnly(item.next_due_on) : 'Không có lịch' }}</span>
                  </p>
                  <p v-if="item.batch_number" class="flex justify-between">
                    <span class="font-semibold text-slate-400">Số lô thuốc:</span>
                    <span class="font-bold text-slate-700">{{ item.batch_number }}</span>
                  </p>
                  <p v-if="item.notes" class="mt-2 text-slate-400 italic bg-slate-50/50 p-2.5 rounded-xl border border-slate-100">
                    {{ item.notes }}
                  </p>
                </div>
              </article>
            </div>
          </div>
        </div>

        <!-- No Pet Selected -->
        <article v-else class="rounded-3xl border border-slate-100 bg-white p-20 shadow-[0_8px_30px_rgb(0,0,0,0.015)] text-center">
          <PawPrint class="w-12 h-12 text-slate-300 mx-auto mb-3" />
          <p class="text-sm font-bold text-slate-500">Vui lòng chọn thú cưng để xem bệnh án.</p>
        </article>
      </main>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { FileText, PawPrint, Activity, ChevronRight, ChevronDown, ChevronUp, Thermometer, Scale, Heart, Pill, FileSpreadsheet, CheckCircle2, AlertCircle } from '@lucide/vue';
import { callApi } from '../auth/http';
import LoadingSpinner from '../components/LoadingSpinner.vue';
import { useNotification } from '../composables/useNotification';

const { handleApiError } = useNotification();

const petsList = ref([]);
const selectedPetId = ref(null);
const activeTab = ref('records');
const expandedRecordIds = ref([]);

const isLoadingPets = ref(true);
const isLoadingRecords = ref(false);

const pet = ref(null);
const medicalRecords = ref([]);
const vaccinations = ref([]);

const selectedPet = computed(() => {
  return petsList.value.find(p => p.id === selectedPetId.value) || null;
});

function translateSpecies(name) {
  if (!name) return 'Chưa rõ';
  const normalized = name.trim().toLowerCase();
  const mapping = {
    bird: 'Chim',
    cat: 'Mèo',
    dog: 'Chó',
    fish: 'Cá',
    rabbit: 'Thỏ',
    hamster: 'Chuột Hamster',
  };
  return mapping[normalized] || name;
}

function formatDate(input) {
  const date = new Date(input);
  if (Number.isNaN(date.getTime())) return input;
  return date.toLocaleDateString('vi-VN');
}

function formatDateOnly(value) {
  if (!value) return '';
  const [datePart] = value.split('T');
  const date = new Date(datePart);
  if (Number.isNaN(date.getTime())) return datePart;
  return date.toLocaleDateString('vi-VN');
}

function formatDateTime(value) {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleString('vi-VN');
}

function parseJson(val) {
  if (!val) return [];
  if (typeof val === 'object') return val;
  try {
    return JSON.parse(val);
  } catch (e) {
    return [];
  }
}

function getSeverityClass(severity) {
  switch (severity) {
    case 'mild': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
    case 'moderate': return 'bg-amber-50 text-amber-600 border-amber-100';
    case 'severe': return 'bg-orange-50 text-orange-600 border-orange-100';
    case 'critical': return 'bg-rose-50 text-rose-600 border-rose-100';
    default: return 'bg-slate-50 text-slate-500 border-slate-100';
  }
}

function getSeverityLabel(severity) {
  switch (severity) {
    case 'mild': return 'Nhẹ';
    case 'moderate': return 'Trung bình';
    case 'severe': return 'Nặng';
    case 'critical': return 'Nguy kịch';
    default: return severity || 'Thường';
  }
}

function hasOrdersOrProcedures(record) {
  const orders = parseJson(record.service_orders);
  const procs = parseJson(record.procedures);
  return orders.length > 0 || procs.length > 0;
}

function toggleRecordExpand(id) {
  const index = expandedRecordIds.value.indexOf(id);
  if (index === -1) {
    expandedRecordIds.value.push(id);
  } else {
    expandedRecordIds.value.splice(index, 1);
  }
}

async function loadRecords(petId) {
  if (!petId) return;
  isLoadingRecords.value = true;
  expandedRecordIds.value = [];
  try {
    const response = await callApi(`/api/owner/pets/${petId}/health-records`, 'GET');
    // The endpoint returns { data: { pet, medical_records, vaccinations } }
    medicalRecords.value = response.data.medical_records || [];
    vaccinations.value = response.data.vaccinations || [];
    
    // Automatically expand the first record by default if it exists
    if (medicalRecords.value.length > 0) {
      expandedRecordIds.value.push(medicalRecords.value[0].id);
    }
  } catch (error) {
    handleApiError(error);
  } finally {
    isLoadingRecords.value = false;
  }
}

async function selectPet(id) {
  selectedPetId.value = id;
  await loadRecords(id);
}

async function bootstrap() {
  isLoadingPets.value = true;
  try {
    const res = await callApi('/api/owner/pets', 'GET');
    petsList.value = res.data || [];
    
    // Check if query param exists
    const urlParams = new URLSearchParams(window.location.search);
    const paramPetId = Number(urlParams.get('pet_id'));
    
    if (paramPetId && petsList.value.some(p => p.id === paramPetId)) {
      selectedPetId.value = paramPetId;
    } else if (petsList.value.length > 0) {
      selectedPetId.value = petsList.value[0].id;
    }
    
    if (selectedPetId.value) {
      await loadRecords(selectedPetId.value);
    }
  } catch (error) {
    handleApiError(error);
  } finally {
    isLoadingPets.value = false;
  }
}

onMounted(() => {
  bootstrap();
});
</script>

<style scoped>
.transition-all {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
