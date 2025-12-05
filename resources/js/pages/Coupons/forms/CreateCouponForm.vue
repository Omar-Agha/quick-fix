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
import { Coupon, createCouponSchema } from '../dtos/data';
import { saveRecord } from '@/lib/utils';
const isSubmitting = ref(false);

interface Props {
    record: Coupon | null;
    onSuccess?: () => void;
    onCancel?: () => void;
}

const props = defineProps<Props>();

const isUpdating = computed(() => props.record !== null);
const isCreating = computed(() => props.record === null);
const updatedRecordId = computed(() => props.record?.id);

const form = useForm({
    validationSchema: createCouponSchema,
    initialValues: props.record,
});

// Watch for changes in exercises and update form values

const onSubmit = form.handleSubmit(async (values) => {
    console.log("joo")
    saveRecord('/coupons', values, true, updatedRecordId.value,
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
    <form @submit="onSubmit" class="space-y-4">
        <FormField name="code" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Code</FormLabel>
                <FormControl>
                    <Input type="text" placeholder="Enter coupon code" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>
        <FormField name="discount_percentage" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Discount Percentage</FormLabel>
                <FormControl>
                    <Input type="number" placeholder="Enter discount percentage" v-bind="componentField" />
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
