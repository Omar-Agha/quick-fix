<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Plus, Trash2 } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { renderErrorList } from '@/lib/utils';

import Button from '@/components/ui/button/Button.vue';
import { FormField } from '@/components/ui/form';
import FormControl from '@/components/ui/form/FormControl.vue';
import FormItem from '@/components/ui/form/FormItem.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormMessage from '@/components/ui/form/FormMessage.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { Article, articleCreateSchema } from '../dtos/data';
import { saveRecord } from '@/lib/utils';
import FileUploaderInput from '@/components/dv-components/FileUploaderInput.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
const isSubmitting = ref(false);

interface Props {
    record: Article | null;
    onSuccess?: () => void;
    onCancel?: () => void;
}

const props = defineProps<Props>();

const isUpdating = computed(() => props.record !== null);
const isCreating = computed(() => props.record === null);
const updatedRecordId = computed(() => props.record?.id);

const form = useForm({
    validationSchema: articleCreateSchema,
    initialValues: {
        title: props.record?.title || '',
        content: props.record?.content || '',
        image: undefined as File | undefined,
        is_active: props.record?.is_active ?? true,

    },
});

// Watch for changes in exercises and update form values

const onSubmit = form.handleSubmit(async (values) => {

    saveRecord('/articles', values, true, updatedRecordId.value,
        () => isSubmitting.value = true,
        () => isSubmitting.value = false,
        () => {
            toast.success('Article created successfully!');
            props.onSuccess?.();
        }, (error) => {

            console.error(error);
        });
});
</script>

<template>
    <form @submit="onSubmit" class="space-y-4">
        <FormField name="title" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Title</FormLabel>
                <FormControl>
                    <Input type="text" placeholder="Enter Article title" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>


        <FormField name="content" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Content</FormLabel>
                <FormControl>
                    <Textarea placeholder="Enter Article content" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="image" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Image</FormLabel>
                <FormControl>
                    <FileUploaderInput v-bind="componentField" :existing-image-url="props.record?.image"
                        :max-size-m-b="5" accept="image/*" label="Article Image"
                        description="Upload a high-quality image for your article (Max 5MB)" :disabled="isSubmitting" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="is_active" v-slot="{ componentField }">
            <FormItem class="flex flex-row items-center justify-between rounded-lg border p-4">
                <div class="space-y-0.5">
                    <FormLabel class="text-base">Active Status</FormLabel>
                    <FormDescription>
                        Toggle to enable or disable this article
                    </FormDescription>
                </div>
                <FormControl>

                    <Checkbox :model-value="componentField.modelValue" @update:model-value="componentField.onChange"
                        @blur="componentField.onBlur" />
                </FormControl>
            </FormItem>
        </FormField>



        <div class="flex justify-end space-x-2">
            <Button type="button" variant="outline" @click="props.onCancel">
                Cancel
            </Button>
            <Button type="submit">
                {{ isCreating ? 'Create' : 'Update' }}
            </Button>
        </div>
    </form>
</template>
