import './bootstrap';
import { createApp } from 'vue';
import FirebaseAuthApp from './components/FirebaseAuthApp.vue';

const firebaseAuthRoot = document.getElementById('firebase-auth-app');

if (firebaseAuthRoot) {
    createApp(FirebaseAuthApp).mount(firebaseAuthRoot);
}
