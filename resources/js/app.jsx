import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'LSPro BBPM SDLP';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
        
        // Hide global loader after initial render
        setTimeout(() => {
            document.body.classList.add('loaded');
        }, 150);

        // Show/hide loader on page transitions
        router.on('start', () => {
            document.body.classList.remove('loaded');
        });
        router.on('finish', () => {
            setTimeout(() => {
                document.body.classList.add('loaded');
            }, 100); // slight delay to ensure UI is ready
        });
    },
    progress: {
        color: '#4B5563', // Professional gray for progress bar
    },
});
