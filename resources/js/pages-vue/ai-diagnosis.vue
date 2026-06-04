<template>
  <div class="min-h-[calc(100vh-6rem)] bg-[#F8FAFC] p-4 md:p-6 lg:p-8 font-sans">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-[#1E293B] flex items-center gap-2.5">
          <span>Trợ Lý Chẩn Đoán Y Khoa AI</span>
          <span class="text-xl md:text-2xl animate-pulse">🤖</span>
        </h1>
        <p class="text-sm text-slate-500 mt-1">
          Hỗ trợ chẩn đoán triệu chứng lâm sàng sơ bộ dựa trên cơ sở tri thức y khoa thú y chuẩn và Vector DB.
        </p>
      </div>
      <!-- Role Badge -->
      <div v-if="currentUser" class="shrink-0">
        <span 
          :class="[
            'px-4 py-1.5 rounded-full text-xs font-bold shadow-sm tracking-wide uppercase',
            currentUser.role === 'vet' ? 'bg-[#EEF2FF] text-[#4F46E5] border border-[#C7D2FE]' : 'bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]'
          ]"
        >
          {{ currentUser.role === 'vet' ? 'Bác sĩ Thú y' : 'Chủ Thú cưng' }}
        </span>
      </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
      <!-- Input Panel (Left) -->
      <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200/80 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] p-5 md:p-6 transition-all duration-300">
        <h2 class="text-lg font-bold text-[#1E293B] mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          <span>Nhập Thông Tin Bệnh Sử</span>
        </h2>

        <form @submit.prevent="startDiagnosis" class="space-y-5">
          <!-- Pet Type Selection -->
          <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2.5">Loài Thú Cưng</label>
            <div class="grid grid-cols-2 gap-3">
              <button 
                type="button" 
                @click="form.pet_type = 'Chó'"
                :class="[
                  'py-3 px-4 rounded-2xl border text-sm font-bold flex items-center justify-center gap-2.5 transition-all duration-200',
                  form.pet_type === 'Chó' 
                    ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-sm' 
                    : 'border-slate-200 bg-white hover:border-slate-350 text-slate-650'
                ]"
              >
                <span class="text-lg">🐶</span>
                <span>Chó</span>
              </button>
              <button 
                type="button" 
                @click="form.pet_type = 'Mèo'"
                :class="[
                  'py-3 px-4 rounded-2xl border text-sm font-bold flex items-center justify-center gap-2.5 transition-all duration-200',
                  form.pet_type === 'Mèo' 
                    ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-sm' 
                    : 'border-slate-200 bg-white hover:border-slate-350 text-slate-650'
                ]"
              >
                <span class="text-lg">🐱</span>
                <span>Mèo</span>
              </button>
            </div>
          </div>

          <!-- Pet Age and Vaccination Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Pet Age -->
            <div>
              <label for="pet-age" class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Độ Tuổi</label>
              <input 
                id="pet-age"
                type="text" 
                v-model="form.pet_age"
                placeholder="Ví dụ: 3 tháng, 2 tuổi"
                class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-[#1E293B] focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
              />
            </div>
            <!-- Vaccination Status -->
            <div>
              <label for="pet-vac" class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Tiêm Phòng</label>
              <select 
                id="pet-vac"
                v-model="form.vaccination"
                class="w-full bg-[#F8FAFC] border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-[#1E293B] focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition"
              >
                <option value="Chưa tiêm phòng">Chưa tiêm phòng</option>
                <option value="Đã tiêm 1 mũi">Đã tiêm 1 mũi</option>
                <option value="Đã tiêm 2 mũi">Đã tiêm 2 mũi</option>
                <option value="Đã tiêm 3 mũi">Đã tiêm đủ 3 mũi</option>
                <option value="Đã tiêm phòng hàng năm">Đã tiêm phòng hàng năm</option>
                <option value="Không rõ lịch trình">Không rõ lịch trình</option>
              </select>
            </div>
          </div>

          <!-- Symptom Description -->
          <div>
            <label for="pet-symptoms" class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Mô Tả Triệu Chứng</label>
            <textarea 
              id="pet-symptoms"
              v-model="form.message"
              rows="5"
              placeholder="Vui lòng mô tả chi tiết các triệu chứng của bé (Ví dụ: bé bỏ ăn 2 ngày nay, nôn ra dịch mật màu vàng, đi ngoài lỏng và người sờ thấy rất ấm/sốt)..."
              class="w-full bg-[#F8FAFC] border border-slate-200 rounded-2xl px-4 py-3 text-sm text-[#1E293B] focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition resize-none leading-relaxed"
              required
            ></textarea>
          </div>

          <!-- Quick Suggestion Pills -->
          <div>
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Gợi Ý Triệu Chứng Nhanh</label>
            <div class="flex flex-wrap gap-2">
              <button 
                v-for="pill in getSymptomPills()"
                :key="pill.text"
                type="button"
                @click="applyPill(pill)"
                class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs text-slate-650 hover:text-slate-800 transition duration-150 text-left border border-slate-200/50"
              >
                {{ pill.label }}
              </button>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button 
              type="submit"
              :disabled="isLoading || !form.message.trim()"
              class="w-full bg-indigo-600 text-white font-bold py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2.5 hover:bg-indigo-700 active:scale-[0.99] disabled:opacity-50 disabled:pointer-events-none transition-all shadow-[0_4px_12px_rgba(79,70,229,0.3)]"
            >
              <svg v-if="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
              <div v-else class="w-5 h-5 rounded-full border-2 border-white border-t-transparent animate-spin"></div>
              <span>{{ isLoading ? 'AI Đang Phân Tích Bệnh Sử...' : 'Bắt Đầu Chẩn Đoán' }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Result Panel (Right) -->
      <div class="lg:col-span-7 flex flex-col h-full self-stretch min-h-[500px]">
        <!-- Box Content -->
        <div class="flex-1 bg-white rounded-3xl border border-slate-200/80 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col">
          
          <!-- Box Header -->
          <div class="bg-slate-50/50 px-5 py-4 border-b border-slate-200/80 flex items-center justify-between shrink-0">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
              <span>Báo Cáo Phân Tích Y Khoa AI</span>
            </h3>
            <button 
              v-if="diagnosisResult && !isLoading" 
              @click="printReport" 
              class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition duration-150"
              title="In Báo Cáo"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2zm5-17V7a4 4 0 00-4-4H8a4 4 0 00-4 4v10"></path></svg>
            </button>
          </div>

          <!-- Empty State -->
          <div v-if="!diagnosisResult && !isLoading" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-50/30">
            <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl mb-4 shadow-inner">
              🔬
            </div>
            <h4 class="text-base font-bold text-slate-800">Chưa có kết quả chẩn đoán</h4>
            <p class="text-sm text-slate-400 max-w-sm mt-1 mx-auto">
              Vui lòng cung cấp chi tiết triệu chứng của bé ở cột bên trái và bấm nút "Bắt Đầu Chẩn Đoán" để bắt đầu quy trình phân tích RAG AI.
            </p>
          </div>

          <!-- Loading State (Scanner Animation) -->
          <div v-if="isLoading" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-slate-55/10 relative overflow-hidden">
            <!-- Scanning Light Bar -->
            <div class="absolute inset-x-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 animate-pulse shadow-md w-full" style="animation: scan 2s linear infinite"></div>

            <div class="relative w-28 h-28 mb-6">
              <!-- Double spinning rings -->
              <div class="absolute inset-0 rounded-full border-4 border-dashed border-indigo-200 animate-spin" style="animation-duration: 10s"></div>
              <div class="absolute inset-2 rounded-full border-4 border-dotted border-purple-300 animate-spin" style="animation-duration: 6s; animation-direction: reverse"></div>
              <div class="absolute inset-4 rounded-full bg-indigo-50/80 flex items-center justify-center text-4xl shadow-inner animate-bounce">
                🩺
              </div>
            </div>

            <h4 class="text-lg font-bold text-slate-800 transition duration-300">{{ loadingText }}</h4>
            <p class="text-xs text-slate-400 mt-2 max-w-xs mx-auto animate-pulse">
              AI đang tìm kiếm dữ liệu bệnh lý, tạo các kết nối triệu chứng lâm sàng và tạo đề xuất điều trị sơ cứu...
            </p>
          </div>

          <!-- Success Report Display (Markdown rendered) -->
          <div v-if="diagnosisResult && !isLoading" class="flex-1 overflow-y-auto p-6 md:p-8 bg-[#FAFAFA] border-t border-slate-100 print-content">
            <div class="prose max-w-none text-slate-850" v-html="renderedMarkdown"></div>
            
            <!-- Quick Disclaimer Note at bottom -->
            <div class="mt-8 pt-5 border-t border-slate-200/80 text-[11px] text-slate-400 leading-relaxed italic flex gap-2">
              <span>⚠️</span>
              <span><strong>Tuyên bố miễn trừ trách nhiệm y khoa:</strong> Các thông tin tư vấn chẩn đoán của trợ lý AI dựa hoàn toàn trên RAG cơ sở bệnh học thú y và triệu chứng bạn mô tả để hỗ trợ định hướng chăm sóc và sơ cứu y tế. Kết quả này hoàn toàn không thể thay thế cho việc đưa thú cưng tới bác sĩ khám lâm sàng, làm các xét nghiệm xét máu/test nhanh y tế thực tế. Vui lòng mang thú cưng đi khám nếu tình trạng xấu đi.</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { callApi } from '../auth/http';
import { useNotification } from '../composables/useNotification';

const http = {
  post: (url, body) => callApi(url, 'POST', body),
  get: (url) => callApi(url, 'GET'),
};

const { notifyError, notifySuccess } = useNotification();

// User state
const currentUser = ref(null);

// Form state
const form = ref({
  pet_type: 'Chó',
  pet_age: '',
  vaccination: 'Chưa tiêm phòng',
  message: '',
});

// UI states
const isLoading = ref(false);
const loadingText = ref('Đang phân tích triệu chứng...');
const diagnosisResult = ref('');

// Loading phrases to animate
const loadingPhrases = [
  'Đang khởi tạo kết nối an toàn...',
  'Đang thu thập các triệu chứng lâm sàng...',
  'Đang truy vấn cơ sở dữ liệu y khoa thú y...',
  'Đang đối chiếu dữ liệu vector học máy...',
  'Đang tổng hợp phương án sơ cứu và điều trị...',
  'Đang tạo báo cáo y khoa chi tiết...'
];

// Computed
const renderedMarkdown = computed(() => {
  return parseMarkdown(diagnosisResult.value);
});

// Methods
const fetchCurrentUser = async () => {
  try {
    const response = await http.get('/api/user');
    currentUser.value = response;
  } catch (error) {
    console.error('Lỗi lấy thông tin user:', error);
  }
};

const getSymptomPills = () => {
  if (form.value.pet_type === 'Chó') {
    return [
      { label: 'Nôn mửa tiêu chảy ra máu tươi (Parvo)', text: 'Chó con bị bỏ ăn lờ đờ, nôn mửa liên tục ra dịch bọt vàng, đi ngoài phân lỏng tóe nước có lẫn nhiều máu tươi kèm mùi hôi tanh rất nồng nặc.' },
      { label: 'Sốt cao, giật giật chân, chảy mũi xanh (Carré)', text: 'Chó bị sốt cao tái đi tái lại, chảy nhiều dịch mắt dịch mũi đặc màu xanh vàng, ho khò khè nhiều, dưới bụng có các mụn mủ nhỏ, thỉnh thoảng hai chân sau giật giật.' },
      { label: 'Nôn dịch vàng, đau bụng rên rỉ (Viêm dạ dày)', text: 'Bé cún nôn mửa ra thức ăn và dịch vàng liên tục, cứ uống nước vào lại nôn ra, nằm áp bụng sát đất rên rỉ vì đau bụng, đi phân loãng đen sệt.' },
      { label: 'Thay đổi tính tình dữ tợn, sùi bọt mép (Nghi Dại)', text: 'Chó tự nhiên trở nên hung dữ hoặc trốn góc tối sợ ánh sáng, hàm dưới xệ ra không khép được làm chảy nước dãi liên tục sùi bọt mép, đi lại lảo đảo liệt chân.' }
    ];
  } else {
    return [
      { label: 'Nôn dịch nhớt vàng, hạ thân nhiệt nằm gục (Care mèo)', text: 'Mèo con bị sốt cao xong giờ người lạnh ngắt hạ thân nhiệt, ngồi ủ rũ gục đầu cạnh bát nước mà không uống, đi ngoài tiêu chảy phân lỏng rất hôi tanh.' },
      { label: 'Loét lưỡi nướu chảy dãi, hắt hơi chảy mũi (Viêm hô hấp FCV)', text: 'Mèo bị loét miệng nướu đau đớn nên bỏ ăn, chảy nhiều nước dãi ròng ròng, mắt híp lại chảy dịch kèm theo hắt hơi liên tục có nước mũi.' },
      { label: 'Sợ nước, trốn góc tối chảy dãi (Nghi Dại ở mèo)', text: 'Mèo tự nhiên kêu khàn tiếng mất giọng, cắn xé đồ vật điên cuồng, sợ nước, chảy nhiều nước dãi miệng há hốc, chân đi lảo đảo bị liệt.' }
    ];
  }
};

const applyPill = (pill) => {
  form.value.message = pill.text;
};

// Cycle loading text
let loadInterval = null;
const startLoadingAnimation = () => {
  let index = 0;
  loadingText.value = loadingPhrases[0];
  loadInterval = setInterval(() => {
    index = (index + 1) % loadingPhrases.length;
    loadingText.value = loadingPhrases[index];
  }, 1800);
};

const stopLoadingAnimation = () => {
  if (loadInterval) {
    clearInterval(loadInterval);
    loadInterval = null;
  }
};

const startDiagnosis = async () => {
  if (!form.value.message.trim()) return;
  
  isLoading.value = true;
  diagnosisResult.value = '';
  startLoadingAnimation();

  try {
    const response = await http.post('/api/ai/diagnose', {
      message: form.value.message,
      pet_type: form.value.pet_type,
      pet_age: form.value.pet_age,
      vaccination: form.value.vaccination
    });
    
    if (response && response.message) {
      diagnosisResult.value = response.message;
      notifySuccess('Chẩn đoán y khoa AI hoàn tất thành công!');
    } else {
      throw new Error('Dữ liệu trả về không hợp lệ');
    }
  } catch (error) {
    notifyError(error, 'Lỗi chẩn đoán y khoa AI');
    diagnosisResult.value = `### ❌ Lỗi hệ thống chẩn đoán\n\nKhông thể kết nối hoặc nhận câu trả lời từ máy chủ AI. Vui lòng kiểm tra:\n- Dịch vụ AI Service đã được khởi động chưa.\n- API Key cấu hình trong .env có chính xác không.\n\nChi tiết lỗi: *${error.message || 'Lỗi kết nối mạng'}*`;
  } finally {
    isLoading.value = false;
    stopLoadingAnimation();
  }
};

const printReport = () => {
  window.print();
};

// Custom Markdown parser function (runs client-side)
const parseMarkdown = (text) => {
  if (!text) return '';
  let html = text;
  
  // HTML Escape first
  html = html
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
    
  // Headings
  html = html.replace(/^### (.*?)$/gm, '<h3 class="text-base md:text-lg font-bold text-slate-800 mt-4 mb-2 flex items-center gap-1.5 border-l-3 border-indigo-500 pl-2">$1</h3>');
  html = html.replace(/^## (.*?)$/gm, '<h2 class="text-lg md:text-xl font-bold text-slate-900 mt-6 mb-3 border-b border-slate-200 pb-1.5">$1</h2>');
  html = html.replace(/^# (.*?)$/gm, '<h1 class="text-xl md:text-2xl font-extrabold text-slate-900 mt-8 mb-4">$1</h1>');
  
  // Bold
  html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-indigo-700 bg-indigo-50/50 px-1 rounded">$1</strong>');
  
  // Italic
  html = html.replace(/\*(.*?)\*/g, '<em class="italic text-slate-600">$1</em>');
  
  // List items
  html = html.replace(/^\s*[-*]\s+(.*?)$/gm, '<div class="flex items-start gap-2.5 my-1.5 ml-2"><span class="text-indigo-500 text-sm mt-0.5">•</span><span class="text-slate-700 text-sm md:text-[14px]">$1</span></div>');
  
  // Blockquotes alert box (> [!NOTE])
  html = html.replace(/^&gt;\s*\[!(IMPORTANT|WARNING|CAUTION|NOTE|TIP)\](.*?)$/gim, (match, type, content) => {
    const colors = {
      'IMPORTANT': 'border-l-4 border-blue-500 bg-blue-50 text-blue-900',
      'WARNING': 'border-l-4 border-yellow-500 bg-yellow-50 text-yellow-900',
      'CAUTION': 'border-l-4 border-red-500 bg-red-50 text-red-900',
      'NOTE': 'border-l-4 border-slate-500 bg-slate-50 text-slate-900',
      'TIP': 'border-l-4 border-green-500 bg-green-50 text-green-900'
    };
    const classes = colors[type.toUpperCase()] || 'border-l-4 border-slate-300 bg-slate-50';
    return `<div class="p-4 my-4 rounded-r-2xl ${classes} text-sm font-semibold flex flex-col gap-1 shadow-sm"><span class="tracking-wider uppercase text-xs opacity-75">${type}:</span>`;
  });
  
  // Closing alerts div
  // A simple regex cleanup if there are multi-line blockquotes (we can just end it)
  // Our system outputs alert markdown on single lines or paragraphs
  
  // Handle regular text paragraphs
  html = html.split('\n').map(line => {
    const trimmed = line.trim();
    if (trimmed.startsWith('<h') || trimmed.startsWith('<div') || trimmed.startsWith('<span') || trimmed.startsWith('<p') || trimmed.startsWith('<ul') || trimmed.startsWith('<ol') || trimmed.startsWith('<li') || trimmed === '') {
      return line;
    }
    return `<p class="my-2.5 text-sm md:text-[14px] text-slate-650 leading-relaxed">${line}</p>`;
  }).join('\n');
  
  return html;
};

onMounted(() => {
  fetchCurrentUser();
});
</script>

<style scoped>
@keyframes scan {
  0% {
    top: 0%;
  }
  50% {
    top: 100%;
  }
  100% {
    top: 0%;
  }
}

/* Print layout optimization */
@media print {
  body * {
    visibility: hidden;
  }
  .print-content, .print-content * {
    visibility: visible;
  }
  .print-content {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    border: none !important;
    background: white !important;
    padding: 0 !important;
  }
}

/* Custom Scrollbars */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #E2E8F0;
  border-radius: 9999px;
}
::-webkit-scrollbar-thumb:hover {
  background: #CBD5E1;
}
</style>
