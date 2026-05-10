@props(['property' => 'picture', 'label' => 'Choose Image'])

<div x-data="{
    showCropper: false,
    cropper: null,
    filename: 'No file selected',
    isUploading: false,
    
    onFileSelect(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        this.filename = file.name;
        
        const reader = new FileReader();
        reader.onload = (event) => {
            this.showCropper = true;
            
            this.$nextTick(() => {
                this.$refs.imageElement.onload = () => {
                    setTimeout(() => {
                        if (this.cropper) {
                            this.cropper.destroy();
                        }
                        this.cropper = new Cropper(this.$refs.imageElement, {
                            aspectRatio: 1,
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
                    }, 100);
                };
                
                this.$refs.imageElement.src = event.target.result;
            });
        };
        reader.readAsDataURL(file);
    },
    
    cancelCrop() {
        this.showCropper = false;
        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }
        this.$refs.fileInput.value = '';
        this.filename = 'No file selected';
    },
    
    applyCrop() {
        if (!this.cropper) return;
        
        this.isUploading = true;
        
        this.cropper.getCroppedCanvas({
            width: 1200,
            height: 1200,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        }).toBlob((blob) => {
            const file = new File([blob], this.filename, { type: 'image/jpeg' });
            
            $wire.upload('{{ $property }}', file, () => {
                this.isUploading = false;
                this.showCropper = false;
                if (this.cropper) {
                    this.cropper.destroy();
                    this.cropper = null;
                }
            }, () => {
                this.isUploading = false;
                alert('Upload failed. Please try again.');
            }, (event) => {
                // Progress
            });
        }, 'image/jpeg', 0.9);
    }
}">
    
    <div class="custom-upload-wrapper">
        <label class="custom-upload-button" style="cursor: pointer;">
            <input type="file" x-ref="fileInput" @change="onFileSelect" accept="image/*" hidden>
            <span><i class="bi bi-upload me-1"></i> {{ $label }}</span>
        </label>
        
        <span class="custom-upload-filename" x-text="filename">
            No file selected
        </span>
    </div>

    <!-- Cropper Modal Overlay -->
    <div x-show="showCropper" x-cloak style="display: none; background: rgba(0,0,0,0.8); z-index: 9999;" class="modal" :class="{ 'd-block': showCropper }" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content claim-modal-content rounded-5 border-0">
                <div class="modal-header border-0 pb-2">
                    <h5 class="modal-title claim-modal-title fw-bold">Crop Image</h5>
                    <button type="button" class="btn-close" @click="cancelCrop" :disabled="isUploading"></button>
                </div>
                
                <div class="modal-body p-3">
                    <div wire:ignore class="w-100 rounded-4 overflow-hidden" style="max-height: 60vh; min-height: 300px; background: #e0e0e0;">
                        <img x-ref="imageElement" style="display: block; max-width: 100%; max-height: 100%;">
                    </div>
                </div>
                
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn claim-cancel-button rounded-pill" @click="cancelCrop" :disabled="isUploading">
                        Cancel
                    </button>
                    <button type="button" class="btn blue-button rounded-pill px-4" @click="applyCrop" :disabled="isUploading">
                        <span x-show="!isUploading">Crop & Save</span>
                        <span x-show="isUploading">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Uploading...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
