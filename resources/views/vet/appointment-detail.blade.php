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
                <div>
                    <label for="record-date" class="text-sm font-semibold text-[#333333]">Record Date</label>
                    <input id="record-date" name="record_date" type="date" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]">
                </div>
                <div>
                    <label for="record-symptoms" class="text-sm font-semibold text-[#333333]">Symptoms</label>
                    <textarea id="record-symptoms" name="symptoms" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]"></textarea>
                </div>
                <div>
                    <label for="record-diagnosis" class="text-sm font-semibold text-[#333333]">Diagnosis</label>
                    <textarea id="record-diagnosis" name="diagnosis" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required></textarea>
                </div>
                <div>
                    <label for="record-treatment" class="text-sm font-semibold text-[#333333]">Treatment</label>
                    <textarea id="record-treatment" name="treatment" rows="4" class="mt-2 w-full rounded-2xl border border-[#C1C4C9] bg-white px-4 py-3 text-sm text-[#333333] outline-none transition focus:border-[#2A6496]" required></textarea>
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
