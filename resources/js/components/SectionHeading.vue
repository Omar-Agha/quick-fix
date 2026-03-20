<script lang="ts" setup>
const props = defineProps<{
    title: string;
    description: string;
}>();
</script>
<template>
    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-7 col-md-10 text-center">
            <div class="sec-heading center">
                <h2>{{ title }}</h2>
                <p>{{ description }}</p>
            </div>
        </div>
    </div>
</template>
