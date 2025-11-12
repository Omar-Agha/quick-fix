<script setup lang="ts">
import { router } from '@inertiajs/vue3';

import { useForm } from 'vee-validate';

import { toast } from 'vue-sonner';

import Button from '@/components/ui/button/Button.vue';
import { FormField } from '@/components/ui/form';
import FormControl from '@/components/ui/form/FormControl.vue';
import FormItem from '@/components/ui/form/FormItem.vue';
import FormLabel from '@/components/ui/form/FormLabel.vue';
import FormMessage from '@/components/ui/form/FormMessage.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';

import { Example, updateExampleSchema } from '../dtos/data';

interface Props {
    record: Example;
    onSuccess?: () => void;
    onCancel?: () => void;
}

const props = defineProps<Props>();




const form = useForm({
    validationSchema: updateExampleSchema,
    initialValues: {
        name: props.record.name,
        description: props.record.description,

    },
});


const onSubmit = form.handleSubmit(async (values) => {


    try {
        await router.put(`/examples/${props.record.id}`, {
            ...values,

        });

        toast.success('Workout updated successfully!');
        props.onSuccess?.();
    } catch (error) {
        toast.error('Failed to update workout');
    }
});
</script>

<template>
    <form @submit="onSubmit" class="space-y-4">
        <FormField name="name" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Name</FormLabel>
                <FormControl>
                    <Input type="text" placeholder="Enter workout name" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField name="description" v-slot="{ componentField }">
            <FormItem>
                <FormLabel>Description</FormLabel>
                <FormControl>
                    <Textarea placeholder="Enter workout description" v-bind="componentField" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>


        <div class="flex justify-end space-x-2">
            <Button type="button" variant="outline" @click="props.onCancel">
                Cancel
            </Button>
            <Button type="submit">
                Update Example
            </Button>
        </div>
    </form>
</template>
