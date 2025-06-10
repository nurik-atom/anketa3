<div id="cropperModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" x-data="{ show: false }">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Обрезка фотографии</h3>
                    <button type="button" onclick="closeCropperModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="cropper-container" style="height: 400px;">
                    <img id="cropperImage" src="" alt="Image to crop">
                </div>
                
                <div class="mt-4 flex justify-end space-x-3">
                    <button type="button" 
                            onclick="closeCropperModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Отмена
                    </button>
                    <button type="button"
                            onclick="cropAndSave()"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
let cropper = null;

function openCropperModal(imageUrl) {
    const modal = document.getElementById('cropperModal');
    const image = document.getElementById('cropperImage');
    
    image.src = imageUrl;
    modal.classList.remove('hidden');
    
    // Инициализация кроппера после загрузки изображения
    image.onload = function() {
        if (cropper) {
            cropper.destroy();
        }
        
        cropper = new Cropper(image, {
            aspectRatio: 3/4,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 1,
            restore: false,
            guides: true,
            center: true,
            highlight: false,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: false,
        });
    };
}

function closeCropperModal() {
    const modal = document.getElementById('cropperModal');
    modal.classList.add('hidden');
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

function cropAndSave() {
    if (!cropper) return;
    
    const canvas = cropper.getCroppedCanvas({
        width: 300,
        height: 400
    });
    
    // Конвертируем canvas в blob
    canvas.toBlob(function(blob) {
        const formData = new FormData();
        formData.append('photo', blob, 'photo.jpg');
        
        // Отправляем на сервер
        fetch('/api/upload-photo', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            // Обновляем превью
            document.getElementById('preview').src = data.url;
            closeCropperModal();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при загрузке фото');
        });
    }, 'image/jpeg', 0.9);
}
</script>
@endpush 