<x-layout.app
    title="Vet Appointment Detail | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="vet-appointment-detail"
    :showSidebar="true"
>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#333333]">Examination & Medical Record</h1>
            <p class="text-sm text-[#4A4A4A]">Review the case and save the medical record after examination.</p>
        </div>
        <a href="{{ route('vet.appointments') }}" class="inline-flex items-center rounded-xl border border-[#C1C4C9] bg-white px-4 py-2 text-sm font-semibold text-[#333333] shadow-sm hover:border-[#2A6496] hover:text-[#2A6496]">
            Back to Appointments
        </a>
    </div>

    <div id="vet-appointment-root" data-appointment-id="{{ $appointmentId }}" class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <div id="vet-appointment-content" class="space-y-4 text-sm text-[#4A4A4A]">
                <div class="rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-5">Loading appointment details...</div>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)]">
            <div id="vet-record-status" class="hidden rounded-2xl px-4 py-3 text-sm"></div>
            <h2 class="text-lg font-bold text-[#333333]">Save Medical Record</h2>
            <form id="vet-medical-record-form" class="mt-5 space-y-4">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="record-temperature" class="text-sm font-semibold text-[#333333]">Nhiet do (C)</label>
                        <input id="record-temperature" name="temperature_c" type="number" step="0.1" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                    <div>
                        <label for="record-weight" class="text-sm font-semibold text-[#333333]">Can nang (kg)</label>
                        <input id="record-weight" name="weight_kg" type="number" step="0.01" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                    <div>
                        <label for="record-heart-rate" class="text-sm font-semibold text-[#333333]">Nhip tim (bpm)</label>
                        <input id="record-heart-rate" name="heart_rate_bpm" type="number" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                </div>
                <div>
                    <label for="record-date" class="text-sm font-semibold text-[#333333]">Record Date</label>
                    <input id="record-date" name="record_date" type="date" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                </div>
                <div>
                    <label for="record-symptoms" class="text-sm font-semibold text-[#333333]">Symptoms</label>
                    <textarea id="record-symptoms" name="symptoms" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-abnormal-signs" class="text-sm font-semibold text-[#333333]">Dau hieu bat thuong</label>
                    <textarea id="record-abnormal-signs" name="abnormal_signs" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-preliminary-diagnosis" class="text-sm font-semibold text-[#333333]">Chan doan so bo</label>
                    <textarea id="record-preliminary-diagnosis" name="preliminary_diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-diagnosis" class="text-sm font-semibold text-[#333333]">Diagnosis</label>
                    <textarea id="record-diagnosis" name="diagnosis" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required></textarea>
                </div>
                <div>
                    <label for="record-final-diagnosis" class="text-sm font-semibold text-[#333333]">Chan doan cuoi cung</label>
                    <textarea id="record-final-diagnosis" name="final_diagnosis" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="record-pathology" class="text-sm font-semibold text-[#333333]">Benh ly</label>
                        <input id="record-pathology" name="pathology" type="text" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder="Viem da, Parvo, Nhiem khuan...">
                    </div>
                    <div>
                        <label for="record-severity" class="text-sm font-semibold text-[#333333]">Muc do benh</label>
                        <select id="record-severity" name="severity_level" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                            <option value="">Chon muc do</option>
                            <option value="mild">Nhe</option>
                            <option value="moderate">Trung binh</option>
                            <option value="severe">Nang</option>
                            <option value="critical">Nguy kich</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="record-prescription" class="text-sm font-semibold text-[#333333]">Prescription</label>
                    <textarea id="record-prescription" name="prescription" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required></textarea>
                </div>
                <div>
                    <label for="record-treatment-protocol" class="text-sm font-semibold text-[#333333]">Phac do dieu tri</label>
                    <textarea id="record-treatment-protocol" name="treatment_protocol" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-disease-progress" class="text-sm font-semibold text-[#333333]">Tien trien benh</label>
                    <textarea id="record-disease-progress" name="disease_progress" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-follow-up-plan" class="text-sm font-semibold text-[#333333]">Ke hoach tai kham / theo doi</label>
                    <textarea id="record-follow-up-plan" name="follow_up_plan" rows="3" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="record-workflow-status" class="text-sm font-semibold text-[#333333]">Trang thai ca kham</label>
                        <select id="record-workflow-status" name="workflow_status" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                            <option value="awaiting_exam">Cho kham</option>
                            <option value="examining">Dang kham</option>
                            <option value="awaiting_lab">Cho xet nghiem</option>
                            <option value="treating">Dang dieu tri</option>
                            <option value="completed">Hoan thanh</option>
                            <option value="follow_up">Tai kham</option>
                        </select>
                    </div>
                    <div>
                        <label for="record-follow-up-at" class="text-sm font-semibold text-[#333333]">Ngay tai kham</label>
                        <input id="record-follow-up-at" name="follow_up_at" type="date" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                    </div>
                </div>
                <div>
                    <label for="record-service-orders" class="text-sm font-semibold text-[#333333]">Chi dinh dich vu / xet nghiem (JSON)</label>
                    <textarea id="record-service-orders" name="service_orders" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder='[{"name":"Xet nghiem mau","status":"ordered","result":null}]'></textarea>
                </div>
                <div>
                    <label for="record-prescriptions-list" class="text-sm font-semibold text-[#333333]">Don thuoc chi tiet (JSON)</label>
                    <textarea id="record-prescriptions-list" name="prescriptions" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder='[{"medicine_name":"Amoxicillin","dosage":"2 lan/ngay","days":5,"instructions":"Sau an"}]'></textarea>
                </div>
                <div>
                    <label for="record-procedures" class="text-sm font-semibold text-[#333333]">Thu thuat / dieu tri (JSON)</label>
                    <textarea id="record-procedures" name="procedures" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder='[{"name":"Truyen dich","status":"done","notes":"500ml"}]'></textarea>
                </div>
                <div>
                    <label for="record-progress-logs" class="text-sm font-semibold text-[#333333]">Theo doi noi tru / tien trien (JSON)</label>
                    <textarea id="record-progress-logs" name="progress_logs" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 font-mono text-xs text-[#333333] outline-none transition focus:border-[#2A6496]" placeholder='[{"noted_at":"2026-04-27 09:00:00","note":"On dinh","vitals":"T=38.5"}]'></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input id="record-sign-off" name="sign_off" type="checkbox" value="1" class="h-4 w-4 rounded border-[#C1C4C9] text-[#2A6496] focus:ring-[#2A6496]">
                    <label for="record-sign-off" class="text-sm font-semibold text-[#333333]">Ky xac nhan chuyen mon</label>
                </div>
                <div>
                    <label for="record-notes" class="text-sm font-semibold text-[#333333]">Notes</label>
                    <textarea id="record-notes" name="notes" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <button id="vet-record-submit" type="submit" class="inline-flex w-full items-center justify-center rounded-2xl border border-[#2A6496] bg-[#2A6496] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#235780]">
                    Save Medical Record
                </button>
            </form>
        </article>
    </div>
</x-layout.app>
