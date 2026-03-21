<script lang="ts" setup>
defineProps<{
    title: string;
    desc: string;
    icon: string;
    stepNumber: number;
    isLast: boolean;
}>();
</script>

<template>
    <div class="hiw-step" :class="{ 'hiw-step--last': isLast }">
        <div class="hiw-step__rai">
            <div class="hiw-step__icon-ring">
                <span class="hiw-step__step-num" aria-hidden="true">{{ stepNumber }}</span>
                <div class="hiw-step__icon-bg">
                    <i :class="icon" aria-hidden="true"></i>
                </div>
            </div>
            <div v-if="!isLast" class="hiw-step__line hiw-step__line--vertical" aria-hidden="true" />
        </div>
        <div class="hiw-step__body">
            <h3 class="hiw-step__title">{{ title }}</h3>
            <p class="hiw-step__desc">{{ desc }}</p>
        </div>
    </div>
</template>

<style scoped>
.hiw-step {
    display: flex;
    flex-direction: row;
    gap: 1.25rem;
    align-items: flex-start;
    text-align: left;
}

.hiw-step__rail {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-shrink: 0;
}

.hiw-step__icon-ring {
    position: relative;
    width: 4.25rem;
    height: 4.25rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 3px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 2px 14px rgba(31, 41, 55, 0.08);
    z-index: 1;
    transition:
        transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.25s ease;
}

.hiw-step:hover .hiw-step__icon-ring {
    transform: scale(1.06);
    box-shadow: 0 6px 22px rgba(31, 41, 55, 0.12);
}

.hiw-step__step-num {
    position: absolute;
    top: -0.45rem;
    left: 50%;
    transform: translateX(-50%);
    min-width: 1.35rem;
    height: 1.35rem;
    padding: 0 0.35rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    line-height: 1.35rem;
    text-align: center;
    color: #fff;
    background: var(--maincolor);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
}

.hiw-step__icon-bg {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--maincolor) 55%, #7ddcc8 130%);
    font-size: 1.35rem;
    color: #fff;
}

.hiw-step__line--vertical {
    width: 3px;
    flex: 1;
    min-height: 1.75rem;
    margin-top: 0.35rem;
    border-radius: 999px;
    background: linear-gradient(180deg, var(--maincolor), rgba(0, 0, 0, 0.04));
    opacity: 0.55;
}

.hiw-step__title {
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--maincolor);
    margin-bottom: 0.45rem;
    letter-spacing: 0.01em;
}

.hiw-step__desc {
    color: #586176;
    font-size: 0.98rem;
    line-height: 1.6;
    margin: 0;
}

@media (min-width: 768px) {
    .hiw-step {
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
        min-width: 0;
        max-width: 17rem;
    }

    .hiw-step__rail {
        margin-bottom: 1rem;
    }

    .hiw-step__line--vertical {
        display: none;
    }
}

@media (prefers-reduced-motion: reduce) {
    .hiw-step__icon-ring {
        transition: none;
    }

    .hiw-step:hover .hiw-step__icon-ring {
        transform: none;
    }
}
</style>
