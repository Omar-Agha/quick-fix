<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Plus, Trash2 } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import z from 'zod';

import Button from '@/components/ui/button/Button.vue';
import { FormField } from '@/components/ui/form';
import FormControl from '@/components/ui/form/FormControl.vue';
import FormItem from '@/components/ui/form/FormItem.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormMessage from '@/components/ui/form/FormMessage.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { Example, exampleCreateSchema } from '../dtos/data';

interface Props {
    record: Example | null;
    onSuccess?: () => void;
    onCancel?: () => void;
}

const props = defineProps<Props>();

const isUpdating = computed(() => props.record !== null);
const isCreating = computed(() => props.record === null);
const updatedRecordId = computed(() => props.record?.id);

const form = useForm({
    validationSchema: exampleCreateSchema,
    initialValues: props.record,
});

// Watch for changes in exercises and update form values

const onSubmit = form.handleSubmit(async (values) => {


    try {
        router.post('/examples', {
            id: updatedRecordId.value,
            ...values,

        });


        var successMessage = isCreating.value ? 'Example created successfully!' : 'Example updated successfully!';
        toast.success(successMessage);
        props.onSuccess?.();
    } catch (error) {
        toast.error('Failed to create example');
    }
});
</script>

<template>
    <form @submit="onSubmit" class="space-y-4">
        <FormField name="name" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Name</FormLabel>
                <FormControl>
                    <Input type="text" placeholder="Enter example name" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="description" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Description</FormLabel>
                <FormControl>
                    <Textarea placeholder="Enter example description" v-bind="componentField" />
                </FormControl>
                <FormMessage />
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
