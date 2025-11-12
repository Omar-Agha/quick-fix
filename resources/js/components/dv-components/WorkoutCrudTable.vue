<script setup lang="ts">
import type { Workout } from '@/types';
import type { DataResponse } from '@/types/crud';
import { type ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import { Button } from '@/components/ui/button';
import { Edit, Trash2 } from 'lucide-vue-next';
import CrudTable from './PaginateTable.vue';

// Define columns for Workout model
const columns: ColumnDef<Workout>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => {
            const workout = row.original;
            return workout.name;
        },
    },
    {
        accessorKey: 'description',
        header: 'Description',
        cell: ({ row }) => {
            const workout = row.original;
            return workout.description || 'No description';
        },
    },
    {
        accessorKey: 'duration',
        header: 'Duration',
        cell: ({ row }) => {
            const workout = row.original;
            return `${workout.duration} week${workout.duration > 1 ? 's' : ''}`;
        },
    },
    {
        accessorKey: 'exercises',
        header: 'Exercises',
        cell: ({ row }) => {
            const workout = row.original;
            const exerciseCount = workout.exercises.length;
            
            if (exerciseCount === 0) {
                return 'No exercises';
            }
            
            // Show first 3 exercise names, then count if more
            const displayExercises = workout.exercises.slice(0, 3);
            const exerciseNames = displayExercises
                .map((ex) => ex.name)
                .join(', ');
            const remainingCount = exerciseCount - 3;
            
            if (remainingCount > 0) {
                return `${exerciseCount} exercises: ${exerciseNames} +${remainingCount} more`;
            }
            
            return `${exerciseCount} exercise${exerciseCount !== 1 ? 's' : ''}: ${exerciseNames}`;
        },
    },
    {
        accessorKey: 'difficulty',
        header: 'Difficulty',
        cell: ({ row }) => {
            const workout = row.original;
            const hasAdvanced = workout.exercises.some((ex) => ex.is_advanced);
            const allAdvanced =
                workout.exercises.length > 0 &&
                workout.exercises.every((ex) => ex.is_advanced);

            if (allAdvanced) {
                return 'Advanced';
            } else if (hasAdvanced) {
                return 'Mixed';
            }
            return 'Beginner';
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Created',
        cell: ({ row }) => {
            const workout = row.original;
            const date = new Date(workout.created_at);
            return date.toLocaleDateString();
        },
    },
    {
        id: 'actions',
        header: 'Actions',
        enableHiding: false,
        cell: ({ row }) => {
            const workout = row.original;
            return h('div', { class: 'flex items-center space-x-2' }, [
                h(
                    Button,
                    {
                        variant: 'ghost',
                        size: 'sm',
                        onClick: () => {
                            window.dispatchEvent(
                                new CustomEvent('edit-workout', {
                                    detail: workout,
                                }),
                            );
                        },
                    },
                    [h(Edit, { class: 'h-4 w-4' })],
                ),
                h(
                    Button,
                    {
                        variant: 'ghost',
                        size: 'sm',
                        onClick: () => {
                            window.dispatchEvent(
                                new CustomEvent('delete-workout', {
                                    detail: workout,
                                }),
                            );
                        },
                    },
                    [h(Trash2, { class: 'h-4 w-4' })],
                ),
            ]);
        },
    },
];

// Data fetching function
const fetchWorkouts = async (page: number, perPage: number): Promise<DataResponse<Workout>> => {
    const response = await fetch(`/workouts?view=table&page=${page}&per_page=${perPage}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });
    
    if (!response.ok) {
        throw new Error('Failed to fetch workouts');
    }
    
    const data = await response.json();
    
    return {
        data: data.workouts,
        pagination: data.pagination,
    };
};
</script>

<template>
    <CrudTable
        :columns="columns"
        :fetch-data="fetchWorkouts"
        :initial-page-size="10"
        empty-message="No workouts found."
    />
</template>
