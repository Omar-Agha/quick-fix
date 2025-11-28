<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { computed, ref, watch } from 'vue';

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
import FileUploaderInput from '@/components/dv-components/FileUploaderInput.vue';
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
    } else {
        form.resetForm();
    }
}, { immediate: true });



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
                    <FileUploaderInput v-bind="componentField" :existing-image-url="props.record?.image"
                        :max-size-m-b="5" accept="image/*" label="Service Image"
                        description="Upload a high-quality image for your service (Max 5MB)" :disabled="isSubmitting" />
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
