@props(['name' => 'photo'])

<div class="photo-upload-container">
    <div class="mb-4">
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">Фотография</label>
        <div class="mt-1 flex items-center">
            <div class="relative">
                <img id="preview" src="{{ asset('images/default-avatar.png') }}" 
                     class="h-32 w-24 object-cover rounded-lg border-2 border-gray-300"
                     alt="Preview">
                <div class="absolute inset-0 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                    <button type="button" 
                            onclick="document.getElementById('{{ $name }}').click()"
                            class="bg-white bg-opacity-75 rounded-full p-2 shadow-sm">
                        <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <input type="file" 
                   id="{{ $name }}" 
                   name="{{ $name }}" 
                   accept="image/*" 
                   class="hidden"
                   onchange="handlePhotoUpload(this)">
        </div>
    </div>
</div>

@push('scripts')
<script>
function handlePhotoUpload(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Открываем модальное окно для обрезки
            openCropperModal(e.target.result);
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function openCropperModal(imageUrl) {
    // Здесь будет код для открытия модального окна с кроппером
    // После обрезки будем обновлять превью и отправлять фото на сервер
}
</script>
@endpush 