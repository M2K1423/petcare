<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | {{ config('app.name', 'PetCare') }}</title>
    @vite(['resources/css/app.css', 'resources/js/pages/sanctum-auth.ts'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto flex min-h-screen w-full max-w-5xl items-center justify-center p-4">
        <section class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-xl md:p-8">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-sm font-bold text-white">PC</div>
                <div>
                    <p class="text-lg font-bold text-slate-900">PetCare Clinic</p>
                    <p class="text-xs text-slate-500">Clinic management portal</p>
                </div>
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight text-amber-900">Create account</h1>
            <p class="mt-2 text-sm text-amber-800/80">Register a new owner account to start using the system.</p>

            <p id="sanctum-status" class="mt-4 text-sm text-slate-600">Ready.</p>

            <form id="sanctum-register-form" class="mt-5 space-y-4">
                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-slate-800">Full name</label>
                    <input id="name" name="name" type="text" required placeholder="Pet owner name" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-800">Email</label>
                    <input id="email" name="email" type="email" required placeholder="owner@example.com" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-800">Password</label>
                    <input id="password" name="password" type="password" minlength="6" required placeholder="At least 6 characters" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">Register</button>
            </form>

            <p class="mt-6 text-sm text-slate-600">
                Already have an account?
                <a href="{{ route('sanctum.auth') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">Back to login</a>
            </p>
        </section>
    </main>
</body>
</html>
