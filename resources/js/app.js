import './bootstrap';
import './candidate-form';

import Alpine from 'alpinejs';

// Защита от повторной инициализации Alpine
if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
    console.log('Alpine.js initialized');
} else {
    console.log('Alpine.js already initialized, skipping...');
}

// For Livewire compatibility
document.addEventListener('livewire:navigated', () => {
    Alpine.start();
});
