import './bootstrap';

import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Start Alpine when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

// For Livewire compatibility
document.addEventListener('livewire:navigated', () => {
    Alpine.start();
});
