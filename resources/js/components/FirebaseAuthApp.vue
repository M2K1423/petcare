<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { initializeApp, getApps } from 'firebase/app';
import {
    browserLocalPersistence,
    createUserWithEmailAndPassword,
    getAuth,
    onAuthStateChanged,
    setPersistence,
    signInWithEmailAndPassword,
    signOut,
    type User,
} from 'firebase/auth';

type StatusType = 'neutral' | 'success' | 'error';

const registerEmail = ref('');
const registerPassword = ref('');
const loginEmail = ref('');
const loginPassword = ref('');
const statusMessage = ref('Ready.');
const statusType = ref<StatusType>('neutral');
const flowLog = ref<string[]>([]);
const redirectUrl = ref('');
const currentUserEmail = ref('');

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
    appId: import.meta.env.VITE_FIREBASE_APP_ID,
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
};

const hasRequiredConfig = computed(() =>
    Boolean(
        firebaseConfig.apiKey &&
            firebaseConfig.authDomain &&
            firebaseConfig.projectId &&
            firebaseConfig.appId,
    ),
);

const isAuthenticated = computed(() => currentUserEmail.value !== '');
const statusClass = computed(() => ({
    'pc-status': true,
    'pc-status-neutral': statusType.value === 'neutral',
    'pc-status-success': statusType.value === 'success',
    'pc-status-error': statusType.value === 'error',
}));

function appendFlowLog(step: string) {
    flowLog.value.push(step);
}

function clearFlowLog() {
    flowLog.value = [];
}

function setStatus(message: string, type: StatusType = 'neutral') {
    statusMessage.value = message;
    statusType.value = type;
}

function getCsrfToken() {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    return tokenMeta?.getAttribute('content') ?? '';
}

async function syncLaravelSession(firebaseUser: User) {
    appendFlowLog('1) Firebase login completed. Requesting ID token...');

    const idToken = await firebaseUser.getIdToken(true);
    appendFlowLog('2) Firebase ID token received. Sending token to Laravel backend...');

    await fetch('/sanctum/csrf-cookie', {
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
        },
    });

    const response = await fetch('/auth/firebase/session-login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify({ idToken }),
        credentials: 'same-origin',
    });

    const payload = await response.json();

    if (!response.ok) {
        const backendMessage = payload?.message ?? 'Laravel session sync failed.';
        appendFlowLog(`3) Backend verify failed: ${backendMessage}`);
        throw new Error(backendMessage);
    }

    appendFlowLog('3) Laravel verified Firebase token and created session successfully.');
    appendFlowLog(`4) Role resolved: ${payload.role}`);
    appendFlowLog(`5) Redirect route resolved: ${payload.redirect_url}`);

    redirectUrl.value = payload.redirect_url;

    return payload;
}

function getAuthErrorMessage(error: unknown) {
    const fallback = 'Authentication request failed. Please try again.';

    if (!error || typeof error !== 'object') {
        return fallback;
    }

    const code = 'code' in error ? String((error as { code?: unknown }).code ?? '') : '';
    const message =
        'message' in error ? String((error as { message?: unknown }).message ?? fallback) : fallback;

    const friendlyByCode: Record<string, string> = {
        'auth/email-already-in-use': 'Email already exists. Try login instead of register.',
        'auth/invalid-email': 'Email format is invalid.',
        'auth/weak-password': 'Password is too weak. Use at least 6 characters.',
        'auth/invalid-credential': 'Email or password is incorrect.',
        'auth/too-many-requests': 'Too many attempts. Please wait and try again.',
        'auth/network-request-failed': 'Network error. Check your internet connection.',
        'auth/operation-not-allowed': 'Email/Password provider is not enabled in Firebase.',
        'auth/unauthorized-domain':
            'Current domain is not authorized in Firebase Auth. Add localhost and 127.0.0.1.',
    };

    if (code && friendlyByCode[code]) {
        return `${friendlyByCode[code]} (${code})`;
    }

    return code ? `${message} (${code})` : message;
}

onMounted(() => {
    if (!hasRequiredConfig.value) {
        setStatus('Missing Firebase config in .env. Fill VITE_FIREBASE_* and refresh.', 'error');
        return;
    }

    const app = getApps().length ? getApps()[0] : initializeApp(firebaseConfig);
    const auth = getAuth(app);

    setPersistence(auth, browserLocalPersistence).catch((error: Error) => {
        setStatus(error.message, 'error');
    });

    onAuthStateChanged(auth, (user) => {
        currentUserEmail.value = user?.email ?? '';

        if (user) {
            setStatus('Signed in successfully.', 'success');
        }
    });
});

async function register() {
    const app = getApps().length ? getApps()[0] : initializeApp(firebaseConfig);
    const auth = getAuth(app);

    try {
        clearFlowLog();
        appendFlowLog('Starting register flow...');

        const credential = await createUserWithEmailAndPassword(
            auth,
            registerEmail.value.trim(),
            registerPassword.value,
        );

        appendFlowLog('Firebase account created.');

        const sessionPayload = await syncLaravelSession(credential.user);
        setStatus('Register and Laravel session sync successful.', 'success');
        registerEmail.value = '';
        registerPassword.value = '';

        setTimeout(() => {
            window.location.href = sessionPayload.redirect_url;
        }, 800);
    } catch (error) {
        console.error('Firebase register error:', error);
        setStatus(getAuthErrorMessage(error), 'error');
    }
}

async function login() {
    const app = getApps().length ? getApps()[0] : initializeApp(firebaseConfig);
    const auth = getAuth(app);

    try {
        clearFlowLog();
        appendFlowLog('Starting login flow...');

        const credential = await signInWithEmailAndPassword(
            auth,
            loginEmail.value.trim(),
            loginPassword.value,
        );

        appendFlowLog('Firebase login successful.');

        const sessionPayload = await syncLaravelSession(credential.user);
        setStatus('Login and Laravel session sync successful.', 'success');
        loginEmail.value = '';
        loginPassword.value = '';

        setTimeout(() => {
            window.location.href = sessionPayload.redirect_url;
        }, 800);
    } catch (error) {
        console.error('Firebase login error:', error);
        setStatus(getAuthErrorMessage(error), 'error');
    }
}

async function logout() {
    const app = getApps().length ? getApps()[0] : initializeApp(firebaseConfig);
    const auth = getAuth(app);

    try {
        await fetch('/auth/logout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        });

        await signOut(auth);
        currentUserEmail.value = '';
        redirectUrl.value = '';
        clearFlowLog();
        appendFlowLog('Firebase and Laravel sessions logged out.');
        setStatus('Logged out.', 'neutral');
    } catch (error) {
        console.error('Firebase logout error:', error);
        setStatus(getAuthErrorMessage(error), 'error');
    }
}
</script>

<template>
    <section class="pc-card">
        <h1 class="pc-title">Firebase Authentication</h1>
        <p class="pc-subtitle">
            This page handles signup/login with Firebase Email + Password using Blade + VueJS.
        </p>

        <div v-if="!hasRequiredConfig" class="pc-alert-warning">
            Missing Firebase env values. Add VITE_FIREBASE_* in .env and restart Vite.
        </div>

        <p :class="statusClass">{{ statusMessage }}</p>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <form class="pc-form-card" @submit.prevent="register">
                <h2 class="text-lg font-medium">Register</h2>
                <label class="pc-label">Email</label>
                <input v-model="registerEmail" type="email" required class="pc-input" />

                <label class="pc-label">Password</label>
                <input v-model="registerPassword" type="password" required minlength="6" class="pc-input" />

                <button type="submit" class="pc-btn pc-btn-primary">Create account</button>
            </form>

            <form class="pc-form-card" @submit.prevent="login">
                <h2 class="text-lg font-medium">Login</h2>
                <label class="pc-label">Email</label>
                <input v-model="loginEmail" type="email" required class="pc-input" />

                <label class="pc-label">Password</label>
                <input v-model="loginPassword" type="password" required minlength="6" class="pc-input" />

                <button type="submit" class="pc-btn pc-btn-primary">Sign in</button>
            </form>
        </div>

        <div v-if="isAuthenticated" class="pc-alert-success">
            <p class="text-sm">Current user: <span class="font-medium">{{ currentUserEmail }}</span></p>
            <button type="button" class="pc-btn pc-btn-danger" @click="logout">Logout</button>
        </div>

        <div class="mt-6 pc-form-card">
            <h2 class="text-lg font-medium">Flow log</h2>
            <p class="pc-subtitle">Track each step: Firebase token -> Laravel verify -> role dashboard.</p>
            <ol class="mt-3 list-decimal space-y-1 pl-5 text-sm text-slate-700">
                <li v-for="(step, index) in flowLog" :key="`${index}-${step}`">{{ step }}</li>
            </ol>
            <a v-if="redirectUrl" :href="redirectUrl" class="mt-3 inline-block text-sm text-blue-700 underline">
                Open resolved dashboard
            </a>
        </div>
    </section>
</template>
