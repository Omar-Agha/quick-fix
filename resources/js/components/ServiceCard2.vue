<script lang="ts" setup>
defineProps<{
    title: string;
    description: string;
    icon: string;
    activeJobs: number;
    inverted?: boolean;
}>();
</script>
<template>
    <div class="card border-0 py-4 px-4 rounded-4 mb-0" :class="{ 'gray-simple': inverted }">
        <div class="ctrd-icons mb-3">
            <i :class="icon" class="text-main fs-1"></i>
        </div>
        <div class="ctrd-caps">
            <h4 class="fs-5"><a href="browse-jobs-grid.html">{{ title }}</a></h4>
            <p class="mb-3">{{ description }}</p>
            <p class="text-main fw-medium mb-0">{{ activeJobs }} Active Jobs</p>
        </div>
    </div>
</template>
