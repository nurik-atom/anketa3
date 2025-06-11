import IMask from 'imask';

// Инициализация маски телефона
export function initPhoneMask() {
    const phoneInput = document.getElementById('phone-input');
    if (phoneInput && typeof IMask !== 'undefined') {
        const phoneMask = IMask(phoneInput, {
            mask: '+7 (000) 000-00-00',
            lazy: false
        });

        // Синхронизация с Livewire
        phoneMask.on('accept', function() {
            phoneInput.dispatchEvent(new Event('input', { bubbles: true }));
        });

        if (phoneInput.value) {
            phoneMask.value = phoneInput.value;
        }

        // Обновляем маску при изменениях от Livewire
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('message.processed', () => {
                if (phoneInput.value && phoneMask) {
                    phoneMask.value = phoneInput.value;
                }
            });
        }

        console.log('Phone mask initialized');
        return phoneMask;
    }
    return null;
}

// Упрощенная функция получения Livewire компонента
export function getLivewireComponent() {
    if (typeof Livewire === 'undefined') {
        console.error('Livewire not available');
        return null;
    }

    // Простой поиск по wire:id
    const wireElements = document.querySelectorAll('[wire\\:id]');
    
    for (let element of wireElements) {
        const wireId = element.getAttribute('wire:id');
        if (wireId && Livewire.find) {
            const component = Livewire.find(wireId);
            if (component) {
                return component;
            }
        }
    }
    
    return null;
}

// Инициализация загрузки фото
export function initPhotoUpload() {
    const photoInput = document.getElementById('photo-input');
    const fallbackInput = document.getElementById('photo-livewire-fallback');
    
    if (!photoInput || !fallbackInput) {
        console.log('Photo inputs not found');
        return;
    }

    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        console.log('File selected:', file.name);

        // Проверки
        if (file.size > 2 * 1024 * 1024) {
            alert('Размер файла не должен превышать 2MB');
            photoInput.value = '';
            return;
        }

        if (!file.type.match(/image\/(jpeg|jpg|png)/)) {
            alert('Загружаемый файл должен быть изображением (JPG, JPEG, PNG)');
            photoInput.value = '';
            return;
        }

        // Простая загрузка через fallback input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fallbackInput.files = dataTransfer.files;
        fallbackInput.dispatchEvent(new Event('change', { bubbles: true }));

        console.log('Photo uploaded via fallback');
    });

    console.log('Photo upload initialized');
}

// Функция удаления фото
export function removePhoto() {
    const component = getLivewireComponent();
    if (component && component.call) {
        component.call('removePhoto');
    } else {
        // Fallback
        const removeBtn = document.getElementById('hidden-remove-photo-btn');
        if (removeBtn) {
            removeBtn.click();
        }
    }
}

// Глобальная функция для шаблонов
window.removePhoto = removePhoto;

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('Candidate form JavaScript loading...');
    
    // Ждем Livewire
    function initWhenReady() {
        if (typeof Livewire !== 'undefined') {
            initPhoneMask();
            initPhotoUpload();
            console.log('Candidate form initialized');
        } else {
            setTimeout(initWhenReady, 100);
        }
    }
    
    initWhenReady();
});

// Также инициализируем при событиях Livewire
document.addEventListener('livewire:init', () => {
    initPhoneMask();
    initPhotoUpload();
});

document.addEventListener('livewire:navigated', () => {
    initPhoneMask();
    initPhotoUpload();
}); 