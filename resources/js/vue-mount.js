import { createApp } from 'vue';

export function mountRenderlessPage(component) {
    const mountEl = document.createElement('div');
    mountEl.style.display = 'none';
    document.body.appendChild(mountEl);
    createApp(component).mount(mountEl);
}
