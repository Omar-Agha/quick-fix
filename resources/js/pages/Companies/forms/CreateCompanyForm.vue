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
import { Company, companyCreateSchema } from '../dtos/data';
import { saveRecord } from '@/lib/utils';
import FileUploaderInput from '@/components/dv-components/FileUploaderInput.vue';
const isSubmitting = ref(false);

interface Props {
    record: Company | null;
    onSuccess?: () => void;
    onCancel?: () => void;
}

const props = defineProps<Props>();

const isUpdating = computed(() => props.record !== null);
const isCreating = computed(() => props.record === null);
const updatedRecordId = computed(() => props.record?.id);

const form = useForm({
    validationSchema: companyCreateSchema,
    initialValues: {
        name: props.record?.name,
        logo: undefined,
        email: props.record?.user?.email,
        phone: props.record?.phone,
        address: props.record?.address,
        password: props.record?.user?.password,
    },
});

// Watch for changes in exercises and update form values

const onSubmit = form.handleSubmit(async (values) => {

    saveRecord('/companies', values, true, updatedRecordId.value,
        () => isSubmitting.value = true,
        () => isSubmitting.value = false,
        () => {
            toast.success('Company created successfully!');
            props.onSuccess?.();
        }, (ex) => {
        });
});
</script>

<template>
    <h2>record: {{ props.record?.user?.email }}</h2>
    <form @submit="onSubmit" class="space-y-4">
        <FormField name="name" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Name</FormLabel>
                <FormControl>
                    <Input type="text" placeholder="Enter company name" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="logo" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Logo</FormLabel>
                <FormControl>
                    <FileUploaderInput v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="email" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Email</FormLabel>
                <FormControl>
                    <Input type="email" placeholder="Enter company email" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="phone" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Phone</FormLabel>
                <FormControl>
                    <Input type="text" placeholder="Enter company phone" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="address" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Address</FormLabel>
                <FormControl>
                    <Input type="text" placeholder="Enter company address" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="password" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Password</FormLabel>
                <FormControl>
                    <Input type="password" placeholder="Enter company password" v-bind="componentField" />
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
