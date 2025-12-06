<script setup lang="ts">
import { computed, ref } from 'vue';
import { X, Upload, Image as ImageIcon, Trash2 } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import { toast } from 'vue-sonner';
import { router } from '@inertiajs/vue3';

interface ImageDto {
    id: number;
    path: string;
    url: string;
}

interface Props {
    images?: ImageDto[];
    deleteEndpoint?: string;
    uploadEndpoint?: string;
    maxSizeMB?: number;
    accept?: string;
    maxImages?: number;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    images: () => [],
    deleteEndpoint: undefined,
    uploadEndpoint: undefined,
    maxSizeMB: 5,
    accept: 'image/*',
    maxImages: undefined,
    disabled: false,
});

const emit = defineEmits<{
    (e: 'delete', image: ImageDto, imageData?: any): void;
    (e: 'upload', files: File[]): void;
    (e: 'uploadComplete'): void;
    (e: 'saveComplete'): void;
}>();

const fileInputRef = ref<HTMLInputElement | null>(null);
const newImages = ref<Array<{ file: File; preview: string }>>([]);
const isUploading = ref(false);
const isDeleting = ref<string | null>(null);

const canAddMore = computed(() => {
    if (props.maxImages === undefined) return true;
    const totalImages = props.images.length + newImages.value.length;
    return totalImages < props.maxImages;
});

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = Array.from(target.files || []);

    if (files.length === 0) return;

    // Check max images limit
    if (props.maxImages && props.images.length + newImages.value.length + files.length > props.maxImages) {
        toast.error(`Maximum ${props.maxImages} images allowed`);
        if (fileInputRef.value) {
            fileInputRef.value.value = '';
        }
        return;
    }

    // Validate and process each file
    files.forEach((file) => {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            toast.error(`${file.name} is not an image file`);
            return;
        }

        // Validate file size
        const maxSizeBytes = props.maxSizeMB * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            toast.error(`${file.name} exceeds ${props.maxSizeMB}MB limit`);
            return;
        }

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            const result = e.target?.result;
            if (typeof result === 'string') {
                newImages.value.push({
                    file,
                    preview: result,
                });
            }
        };
        reader.readAsDataURL(file);
    });

    // Reset input
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const removeNewImage = (index: number) => {
    newImages.value.splice(index, 1);
};

const handleDelete = async (image: ImageDto) => {
    if (props.disabled || isDeleting.value) return;

    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    isDeleting.value = image.path;

    try {
        if (props.deleteEndpoint) {

            let deleteUrl = props.deleteEndpoint;


            const imageId = image.id;


            if (imageId) {
                deleteUrl = `${props.deleteEndpoint}/${imageId}`;
            }


            await router.delete(deleteUrl, {
                onSuccess: () => {
                    toast.success('Image deleted successfully');
                    emit('delete', image);
                    isDeleting.value = null;
                    emit('saveComplete');

                },
                onError: () => {
                    toast.error('Failed to delete image');
                    isDeleting.value = null;
                },
            });
        } else {
            // Just emit the event if no endpoint is provided
            emit('delete', image);
            isDeleting.value = null;
        }
    } catch (error) {
        toast.error('Failed to delete image');
        isDeleting.value = null;
    }
};

const handleUpload = async () => {
    if (newImages.value.length === 0) {
        toast.error('No new images to upload');
        return;
    }

    if (props.disabled || isUploading.value) return;

    isUploading.value = true;

    try {
        const files = newImages.value.map((img) => img.file);

        if (props.uploadEndpoint) {
            const formData = new FormData();
            files.forEach((file, index) => {
                formData.append(`images[${index}]`, file);
            });

            await router.post(props.uploadEndpoint, formData, {
                forceFormData: true,
                onSuccess: () => {
                    toast.success(`${files.length} image(s) uploaded successfully`);
                    newImages.value = [];
                    emit('upload', files);
                    emit('uploadComplete');
                    emit('saveComplete');

                    isUploading.value = false;
                },
                onError: () => {
                    toast.error('Failed to upload images');
                    isUploading.value = false;
                },
            });
        } else {
            // Just emit the event if no endpoint is provided
            emit('upload', files);
            emit('uploadComplete');
            newImages.value = [];
            isUploading.value = false;
        }
    } catch (error) {
        toast.error('Failed to upload images');
        isUploading.value = false;
    }
};

const hasImages = computed(() => props.images.length > 0 || newImages.value.length > 0);
</script>

<template>
    <div class="space-y-4">
        <!-- Existing Images Grid -->
        <div v-if="props.images.length > 0" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
            <div v-for="(image, index) in props.images" :key="index"
                class="group relative aspect-square overflow-hidden rounded-lg border-2 border-muted bg-muted">
                <img :src="image.url" :alt="`Image ${index + 1}`"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110" />
                <button v-if="!disabled && isDeleting !== image.path" type="button" @click="handleDelete(image)"
                    class="absolute right-2 top-2 rounded-full bg-destructive p-1.5 text-destructive-foreground shadow-lg opacity-0 transition-opacity group-hover:opacity-100 hover:bg-destructive/90"
                    :disabled="isDeleting !== null">
                    <Trash2 class="h-4 w-4" />
                </button>
                <div v-if="isDeleting === image.path"
                    class="absolute inset-0 flex items-center justify-center bg-background/80 backdrop-blur-sm">
                    <div class="text-sm text-muted-foreground">Deleting...</div>
                </div>
            </div>
        </div>

        <!-- New Images Preview Grid -->
        <div v-if="newImages.length > 0" class="space-y-2">
            <div class="text-sm font-medium text-muted-foreground">New Images (Pending Upload)</div>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                <div v-for="(newImage, index) in newImages" :key="index"
                    class="group relative aspect-square overflow-hidden rounded-lg border-2 border-dashed border-primary bg-muted/50">
                    <img :src="newImage.preview" :alt="`New image ${index + 1}`" class="h-full w-full object-cover" />
                    <button v-if="!disabled" type="button" @click="removeNewImage(index)"
                        class="absolute right-2 top-2 rounded-full bg-destructive p-1.5 text-destructive-foreground shadow-lg opacity-0 transition-opacity group-hover:opacity-100 hover:bg-destructive/90">
                        <X class="h-4 w-4" />
                    </button>
                    <div
                        class="absolute bottom-0 left-0 right-0 bg-primary/80 px-2 py-1 text-center text-xs font-medium text-primary-foreground">
                        New
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!hasImages"
            class="flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-muted bg-muted/50 p-8">
            <ImageIcon class="mb-4 h-12 w-12 text-muted-foreground" />
            <p class="mb-2 text-sm font-medium text-muted-foreground">No images yet</p>
            <p class="text-xs text-muted-foreground">Upload your first image to get started</p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <!-- Add Image Button -->
            <div class="flex items-center gap-2">
                <input ref="fileInputRef" type="file" :accept="accept" :multiple="true" @change="handleFileSelect"
                    :disabled="disabled || !canAddMore || isUploading" class="hidden"
                    :id="`image-list-upload-${Math.random().toString(36).substr(2, 9)}`" />
                <Button type="button" variant="outline" @click="fileInputRef?.click()"
                    :disabled="disabled || !canAddMore || isUploading">
                    <Upload class="mr-2 h-4 w-4" />
                    Add Images
                </Button>
                <span v-if="maxImages" class="text-xs text-muted-foreground">
                    {{ images.length + newImages.length }} / {{ maxImages }}
                </span>
            </div>

            <!-- Upload Button -->
            <Button v-if="newImages.length > 0" type="button" @click="handleUpload" :disabled="disabled || isUploading">
                <Upload class="mr-2 h-4 w-4" />
                <span v-if="isUploading">Uploading...</span>
                <span v-else>Upload {{ newImages.length }} Image(s)</span>
            </Button>
        </div>
    </div>
</template>
