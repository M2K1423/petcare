<x-layout.app
    title="Owner Appointments | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="owner-appointments"
    :showSidebar="true"
>
    <section class="grid gap-6 lg:grid-cols-5">
        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-2">
            <h2 class="text-lg font-bold text-[#333333]">Add a pet</h2>
            <p class="mt-2 text-sm text-[#4A4A4A]">Before creating an appointment, make sure your pet profile is available.</p>

            <div class="mt-4 rounded-2xl border border-dashed border-[#C7CDD5] bg-[#F8FAFC] p-4">
                <p class="text-sm text-[#4A4A4A]">Open the pet manager to add or update pet details first.</p>
                <a
                    href="{{ route('owner.pets') }}"
                    class="mt-3 inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]"
                >
                    Go to Add pet
                </a>
            </div>
        </article>

        <article class="rounded-3xl border border-[#DDE1E6] bg-[#FFFFFF] p-6 shadow-[0_16px_36px_rgba(0,0,0,0.05)] backdrop-blur lg:col-span-3">
            <h2 class="text-lg font-bold text-[#333333]">Create appointment</h2>
            <p id="owner-appointments-status" class="mt-2 text-sm text-[#4A4A4A]">Loading pets...</p>

            <form id="owner-appointment-form" class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Pet</label>
                    <select id="appointment-pet-select" name="pet_id" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                        <option>Select pet from your list</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Appointment date</label>
                    <input id="appointment-date" name="appointment_date" type="date" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" />
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Appointment time</label>
                    <div class="grid grid-cols-2 gap-2">
                        <select id="appointment-hour" name="appointment_hour" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                            <option value="08">08</option>
                            <option value="09" selected>09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                        </select>
                        <select id="appointment-minute" name="appointment_minute" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none">
                            <option value="00" selected>00</option>
                            <option value="15">15</option>
                            <option value="30">30</option>
                            <option value="45">45</option>
                        </select>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Reason</label>
                    <textarea id="appointment-reason" name="reason" rows="4" class="w-full rounded-xl border border-[#DDE1E6] bg-[#F1F3F5] px-3 py-2 text-sm text-[#333333] outline-none" placeholder="Describe your pet symptoms..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-[#2A6496] bg-[#2A6496] px-4 py-2.5 text-sm font-semibold text-[#FFFFFF] transition hover:bg-[#235780]">
                        Create appointment
                    </button>
                </div>
            </form>

            <div class="mt-6 rounded-2xl border border-[#DDE1E6] bg-[#F9FBFD] p-4">
                <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-[#4A4A4A]">Upcoming appointments</h3>
                <div id="owner-appointments-list" class="mt-2 space-y-2 text-sm text-[#4A4A4A]"></div>
            </div>
        </article>
    </section>
</x-layout.app>
