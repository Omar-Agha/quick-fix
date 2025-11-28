<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Upload, X, Image as ImageIcon } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import { toast } from 'vue-sonner';

interface Props {
    modelValue?: File | string | null;
    existingImageUrl?: string | null;
    maxSizeMB?: number;
    accept?: string;
    previewHeight?: string;
    previewWidth?: string;
    label?: string;
    description?: string;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: undefined,
    existingImageUrl: null,
    maxSizeMB: 5,
    accept: 'image/*',
    previewHeight: 'h-48',
    previewWidth: 'w-48',
    label: 'Image',
    description: 'Upload a high-quality image (Max 5MB)',
    disabled: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: File | undefined): void;
}>();

const imagePreview = ref<string | null>(null);
const imageFile = ref<File | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

// Initialize preview from existing image URL or modelValue
watch(
    () => [props.modelValue, props.existingImageUrl],
    ([newValue, existingUrl]) => {
        if (newValue instanceof File) {
            imageFile.value = newValue;
            // Create preview from file
            const reader = new FileReader();
            reader.onload = (e) => {
                const result = e.target?.result;
                if (typeof result === 'string') {
                    imagePreview.value = result;
                }
            };
            reader.readAsDataURL(newValue);
        } else if (typeof newValue === 'string') {
            imagePreview.value = newValue;
        } else if (existingUrl && typeof existingUrl === 'string') {
            imagePreview.value = existingUrl;
        } else {
            imagePreview.value = null;
            imageFile.value = null;
        }
    },
    { immediate: true }
);

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            toast.error('Please select an image file');
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
            return;
        }

        // Validate file size
        const maxSizeBytes = props.maxSizeMB * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            toast.error(`Image size must be less than ${props.maxSizeMB}MB`);
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
            return;
        }

        imageFile.value = file;
        emit('update:modelValue', file);

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    imageFile.value = null;
    imagePreview.value = props.existingImageUrl || null;
    emit('update:modelValue', undefined);
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

const hasPreview = computed(() => !!imagePreview.value);
</script>

<template>
    <div class="space-y-4">
        <!-- Image Preview -->
        <div v-if="hasPreview" class="relative inline-block">
            <div :class="[
                'relative overflow-hidden rounded-lg border-2 border-dashed border-muted',
                previewHeight,
                previewWidth,
            ]">
                <img v-if="imagePreview" :src="imagePreview" :alt="label" class="h-full w-full object-cover" />
                <button v-if="!disabled" type="button" @click="removeImage"
                    class="absolute right-2 top-2 rounded-full bg-destructive p-1.5 text-destructive-foreground shadow-sm transition-colors hover:bg-destructive/90">
                    <X class="h-4 w-4" />
                </button>
            </div>
        </div>

        <!-- Upload Area -->
        <div v-else class="flex items-center justify-center">
            <div :class="[
                'flex w-full flex-col items-center justify-center rounded-lg border-2 border-dashed border-muted bg-muted/50 transition-colors',
                previewHeight,
                disabled ? '' : 'hover:border-primary',
            ]">
                <ImageIcon class="mb-4 h-12 w-12 text-muted-foreground" />
                <p class="mb-2 text-sm text-muted-foreground">
                    Click to upload or drag and drop
                </p>
                <p class="text-xs text-muted-foreground">
                    PNG, JPG, GIF up to {{ maxSizeMB }}MB
                </p>
            </div>
        </div>

        <!-- File Input -->
        <input ref="fileInputRef" type="file" :accept="accept" @change="handleImageChange" :disabled="disabled"
            class="hidden" :id="`file-upload-${Math.random().toString(36).substr(2, 9)}`" />
        <Button type="button" variant="outline" @click="fileInputRef?.click()" :disabled="disabled" class="w-full">
            <Upload class="mr-2 h-4 w-4" />
            {{ hasPreview ? 'Change Image' : 'Upload Image' }}
        </Button>
    </div>
</template>
