<script lang="ts" setup>
import { useTranslations } from '@/composables/useTranslations';
import CheckList from './CheckList.vue';
import FeatureLabel from './FeatureLabel.vue';
import MainHeading from './MainHeading.vue';
import ServicesImages from './ServicesImages.vue';
import { computed } from 'vue';

const { t } = useTranslations();

const checklistKeys = ['0', '1', '2', '3'] as const;

const features = computed(() => checklistKeys.map((key) => t(`hero.checklist.${key}`)));
</script>
<template>
    <div class="row justify-content-between align-items-center">
        <div class="col-xl-7 col-lg-7 col-md-12 col-sm-12 pe-xl-5 pe-lg-4">
            <FeatureLabel :title="t('hero.feature_label_title')" :description="t('hero.feature_label_description')" />
            <MainHeading :title="t('hero.title')" :description="t('hero.description')" />
            <CheckList :features="features" />
            <!-- <FindJob /> -->
        </div>

        <div class="col-xl-5 col-lg-5 col-md-12 col-sm-12">
            <ServicesImages />
        </div>
    </div>
</template>
