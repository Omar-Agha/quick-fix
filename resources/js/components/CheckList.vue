<script lang="ts" setup>
const props = defineProps<{
    features: string[];
}>();
</script>

<template>
    <div class="features-groupss my-4">
        <ul class="row gx-3 gy-4 p-0">
            <li class="fw-medium col-xl-6 col-lg-6 col-6" v-for="value in features"><span
                    class="square--30 circle d-inline-flex align-items-center justify-content-center text-success bg-light-success me-2"><i
                        class="fa-solid fa-check"></i></span>{{ value }}</li>

        </ul>
    </div>
</template>