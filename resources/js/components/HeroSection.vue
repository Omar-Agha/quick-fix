<script lang="ts" setup>
import CheckList from './CheckList.vue';
import FeatureLabel from './FeatureLabel.vue';
import FindJob from './FindJob.vue';
import MainHeading from './MainHeading.vue';
import ServicesImages from './ServicesImages.vue';

const features = [
    "Browse trusted plumbers, electricians, and handymen",
    "Book verified professionals for any home service",
    "Upfront pricing and easy online scheduling",
    "Support for emergencies and same-day service",
]
</script>
<template>
    <div class="row justify-content-between align-items-center">
        <div class="col-xl-7 col-lg-7 col-md-12 col-sm-12 pe-xl-5 pe-lg-4">
            <FeatureLabel title="New" description="On-demand home services" />
            <MainHeading title="Easiest Way To Fix & Upgrade Your Home"
                description="Need repair, installation, or maintenance? Instantly find qualified local experts for plumbing, electrical work, and more—all at your fingertips." />
            <CheckList :features="features" />
            <!-- <FindJob /> -->
        </div>

        <div class="col-xl-5 col-lg-5 col-md-12 col-sm-12">
            <ServicesImages />
        </div>
    </div>
</template>