<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useVModel } from '@vueuse/core';
import { Plus, X } from 'lucide-vue-next';

import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';

interface Props {
    modelValue?: string[];

    placeholder?: string;
    class?: string;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: () => [],
    placeholder: 'Enter text...',
    disabled: false,
});

const emits = defineEmits<{
    (e: 'update:modelValue', value: string[]): void;
}>();

const modelValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: [],
});

// Internal state for input fields
const inputValues = ref<string[]>(['']);

// Initialize input values when modelValue changes
watch(
    () => props.modelValue,
    (newValue) => {
        if (newValue && newValue.length > 0) {
            inputValues.value = [...newValue, ''];
        } else {
            inputValues.value = [''];
        }
    },
    { immediate: true }
);

// Update modelValue when input values change, filtering out empty strings
watch(
    inputValues,
    (newValues) => {
        const filteredValues = newValues.filter(value => value.trim() !== '');
        modelValue.value = filteredValues;
    },
    { deep: true }
);

// Add new input field
const addInput = () => {
    inputValues.value.push('');
};

// Remove input field at specific index
const removeInput = (index: number) => {
    if (inputValues.value.length > 1) {
        inputValues.value.splice(index, 1);
    }
};

// Handle input change and auto-add new field if current is filled
const handleInputChange = (value: string, index: number) => {
    console.log(value, index);

    inputValues.value[index] = value as string;

    console.log(inputValues.value);

    // If current field is filled and it's the last field, add a new empty field
    if (value.trim() !== '' && index === inputValues.value.length - 1) {
        addInput();
    }
};

// Handle keydown events
const handleKeydown = (event: KeyboardEvent, index: number) => {
    // If Enter is pressed and current field is empty, prevent default and focus next
    if (event.key === 'Enter' && inputValues.value[index].trim() === '') {
        event.preventDefault();
        const nextIndex = index + 1;
        if (nextIndex < inputValues.value.length) {
            // Focus next input
            const nextInput = document.querySelector(`[data-input-index="${nextIndex}"]`) as HTMLInputElement;
            nextInput?.focus();
        } else {
            addInput();
        }
    }

    // If Backspace is pressed on empty field and it's not the only field, remove it
    if (event.key === 'Backspace' && inputValues.value[index] === '' && inputValues.value.length > 1) {
        removeInput(index);
        // Focus previous input
        const prevIndex = index - 1;
        if (prevIndex >= 0) {
            const prevInput = document.querySelector(`[data-input-index="${prevIndex}"]`) as HTMLInputElement;
            prevInput?.focus();
        }
    }
};

// Computed property to check if we can remove items
const canRemove = computed(() => inputValues.value.length > 1);
</script>

<template>
    <div :class="['space-y-2', props.class]">

        <div v-for="(value, index) in inputValues" :key="index" class="flex items-center gap-2">
            <Input :data-input-index="index" :model-value="value" :placeholder="props.placeholder"
                :disabled="props.disabled" @update:model-value="(newValue) => handleInputChange(newValue, index)"
                @keydown="(event: KeyboardEvent) => handleKeydown(event, index)" class="flex-1" />

            <Button v-if="canRemove" type="button" variant="outline" size="sm" :disabled="props.disabled"
                @click="removeInput(index)" class="h-9 w-9 p-0 shrink-0">
                <X class="h-4 w-4" />
                <span class="sr-only">Remove item</span>
            </Button>

            <Button v-else-if="index === inputValues.length - 1 && value.trim() !== ''" type="button" variant="outline"
                size="sm" :disabled="props.disabled" @click="addInput" class="h-9 w-9 p-0 shrink-0">
                <Plus class="h-4 w-4" />
                <span class="sr-only">Add item</span>
            </Button>
        </div>

        <!-- Add button at the bottom if no items are filled -->
        <Button v-if="inputValues.every(v => v.trim() === '')" type="button" variant="outline" size="sm"
            :disabled="props.disabled" @click="addInput" class="w-full">
            <Plus class="h-4 w-4 mr-2" />
            Add item
        </Button>
    </div>
</template>
