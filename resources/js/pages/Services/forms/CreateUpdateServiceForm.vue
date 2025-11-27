<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Upload, X, Image as ImageIcon } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, h, ref, watch } from 'vue';

import Button from '@/components/ui/button/Button.vue';
import { FormField } from '@/components/ui/form';
import FormControl from '@/components/ui/form/FormControl.vue';
import FormItem from '@/components/ui/form/FormItem.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormMessage from '@/components/ui/form/FormMessage.vue';
import FormDescription from '@/components/ui/form/FormDescription.vue';
import Input from '@/components/ui/input/Input.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { Service, serviceCreateSchema, updateServiceSchema } from '../dtos/data';
import { toast } from 'vue-sonner';
import { objectToFormData, renderErrorList, saveRecord } from '@/lib/utils';

interface Props {
    record: Service | null;
    onSuccess?: () => void;
    onCancel?: () => void;
}

const props = defineProps<Props>();

const isUpdating = computed(() => props.record !== null);
const isCreating = computed(() => props.record === null);
const updatedRecordId = computed(() => props.record?.id);

const form = useForm({
    validationSchema: isUpdating.value ? updateServiceSchema : serviceCreateSchema,
    initialValues: {
        name: props.record?.name || '',
        description: props.record?.description || '',
        price: props.record?.price || 0,
        image: undefined as File | undefined,
        is_active: props.record?.is_active ?? true,
    },
});

const imagePreview = ref<string | null>(props.record?.image || null);
const imageFile = ref<File | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);
const isSubmitting = ref(false);

// Watch for record changes
watch(() => props.record, (newRecord) => {
    if (newRecord) {
        form.setValues({
            name: newRecord.name,
            description: newRecord.description || '',
            price: newRecord.price,
            image: undefined,
            is_active: newRecord.is_active,
        });
        imagePreview.value = newRecord.image || null;
        imageFile.value = null;
    } else {
        form.resetForm();
        imagePreview.value = null;
        imageFile.value = null;
    }
}, { immediate: true });

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            toast.error('Please select an image file');
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            toast.error('Image size must be less than 5MB');
            return;
        }

        imageFile.value = file;
        form.setFieldValue('image', file);

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
    imagePreview.value = props.record?.image || null;
    form.setFieldValue('image', undefined);
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};



const onSubmit = form.handleSubmit(async (values) => {

    saveRecord('/services', values, true, updatedRecordId.value,
        () => isSubmitting.value = true,
        () => isSubmitting.value = false,
        () => {
            toast.success('Service created successfully!');
            props.onSuccess?.();
        }, (ex) => {
        });


});
</script>

<template>
    <form @submit="onSubmit" class="space-y-6">
        <!-- Image Upload Section -->
        <FormField name="image" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Service Image</FormLabel>
                <FormDescription>
                    Upload a high-quality image for your service (Max 5MB)
                </FormDescription>
                <FormControl>
                    <div class="space-y-4">
                        <!-- Image Preview -->
                        <div v-if="imagePreview" class="relative inline-block">
                            <div
                                class="relative h-48 w-48 overflow-hidden rounded-lg border-2 border-dashed border-muted">
                                <img :src="imagePreview" alt="Service preview" class="h-full w-full object-cover" />
                                <button type="button" @click="removeImage"
                                    class="absolute right-2 top-2 rounded-full bg-destructive p-1.5 text-destructive-foreground shadow-sm transition-colors hover:bg-destructive/90">
                                    <X class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Upload Button -->
                        <div v-else class="flex items-center justify-center">
                            <div
                                class="flex h-48 w-full flex-col items-center justify-center rounded-lg border-2 border-dashed border-muted bg-muted/50 transition-colors hover:border-primary">
                                <ImageIcon class="mb-4 h-12 w-12 text-muted-foreground" />
                                <p class="mb-2 text-sm text-muted-foreground">
                                    Click to upload or drag and drop
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    PNG, JPG, GIF up to 5MB
                                </p>
                            </div>
                        </div>

                        <input ref="fileInputRef" type="file" accept="image/*" @change="handleImageChange"
                            class="hidden" id="image-upload" />
                        <Button type="button" variant="outline" @click="fileInputRef?.click()" class="w-full">
                            <Upload class="mr-2 h-4 w-4" />
                            {{ imagePreview ? 'Change Image' : 'Upload Image' }}
                        </Button>
                    </div>
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <!-- Name Field -->
        <FormField name="name" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Service Name</FormLabel>
                <FormDescription>
                    Enter a clear and descriptive name for your service
                </FormDescription>
                <FormControl>
                    <Input type="text" placeholder="e.g., Plumbing Service, Electrical Repair"
                        v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <!-- Description Field -->
        <FormField name="description" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Description</FormLabel>
                <FormDescription>
                    Provide detailed information about what this service includes
                </FormDescription>
                <FormControl>
                    <Textarea placeholder="Describe your service in detail..." rows="4" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <!-- Price Field -->
        <FormField name="price" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Price</FormLabel>
                <FormDescription>
                    Set the price for this service in dollars
                </FormDescription>
                <FormControl>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">$</span>
                        <Input type="number" step="0.01" min="0" placeholder="0.00" class="pl-7" v-bind="componentField"
                            @update:modelValue="(val) => form.setFieldValue('price', parseFloat(val as string) || 0)" />
                    </div>
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <!-- Active Status Toggle -->
        <FormField name="is_active" v-slot="{ componentField }">
            <FormItem class="flex flex-row items-center justify-between rounded-lg border p-4">
                <div class="space-y-0.5">
                    <FormLabel class="text-base">Active Status</FormLabel>
                    <FormDescription>
                        Toggle to enable or disable this service
                    </FormDescription>
                </div>
                <FormControl>
                    <!-- <Checkbox :checked="value" @update:checked="handleChange" /> -->
                    <!-- <input type="checkbox" v-bind="componentField" /> -->
                    <Checkbox :model-value="componentField.modelValue" @update:model-value="componentField.onChange"
                        @blur="componentField.onBlur" />
                </FormControl>
            </FormItem>
        </FormField>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 pt-4">
            <Button type="button" variant="outline" @click="props.onCancel">
                Cancel
            </Button>
            <Button type="submit" :disabled="isSubmitting">
                <span v-if="isSubmitting">Processing...</span>
                <span v-else>{{ isCreating ? 'Create Service' : 'Update Service' }}</span>
            </Button>
        </div>
    </form>
</template>
