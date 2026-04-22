<x-layout.app
    title="Billing & Payments | {{ config('app.name', 'PetCare') }}"
    :vite="['resources/css/app.css', 'resources/js/app.js']"
    page="receptionist-billing"
    :showSidebar="true"
>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Billing & Payments</h1>
        <p class="text-sm text-gray-500">Process unpaid medical records and prescriptions.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <article class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm col-span-2">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Unpaid Bills</h2>
            <div id="unpaid-container" class="space-y-3">
                <div class="p-4 text-center text-sm text-gray-500 bg-gray-50 rounded-xl">Loading unpaid bills...</div>
            </div>
        </article>
    </div>
    
    <!-- Payment Modal (Hidden by default) -->
    <div id="payment-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
            <div class="inline-block transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle">
                <div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">Process Payment</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Record payment for medical services.</p>
                        </div>
                    </div>
                </div>
                <form id="payment-form" class="mt-5 sm:mt-6">
                    <input type="hidden" id="appointment_id" name="appointment_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Amount Paid ($)</label>
                        <input type="number" step="0.01" id="amount" name="amount" class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm border" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm border">
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="vnpay">VNPay</option>
                            <option value="momo">MoMo</option>
                        </select>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:col-start-2 sm:text-sm">
                            Confirm Payment
                        </button>
                        <button type="button" id="btn-close-modal" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>