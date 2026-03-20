<script setup lang="ts">
import BadgeLabel from './BadgeLabel.vue';

defineProps<{
    title: string;
    description: string;
    image: string;
    cost: string;
    open: number;
}>();
</script>

<template>
    <div class="job-instructor-layout border">
        <div class="left-tags-capt">
            <BadgeLabel label="Featured" type="featured" />
            <BadgeLabel label="Urgent" type="warn" />
        </div>
        <div class="brows-job-type">
            <BadgeLabel label="Enternship" type="danger" />
        </div>
        <div class="job-instructor-thumb">
            <a href="job-detail.html"><img :src="image" class="img-fluid" alt=""></a>
        </div>
        <div class="job-instructor-content">
            <h4 class="instructor-title"><a href="job-detail.html">{{ title }}</a></h4>
            <div class="instructor-skills">
                {{ description }}
            </div>
        </div>
        <div class="job-instructor-footer">
            <div class="instructor-students">
                <h5 class="instructor-scount">{{ cost }}</h5>
            </div>
            <div class="instructor-corses">
                <span class="c-counting">{{ open }} Open</span>
            </div>
        </div>
    </div>
</template>
