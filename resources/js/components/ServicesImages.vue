<script lang="ts" setup>
/**
 * Unsplash images sized to match layout: 500×500 (circles) and 500×800 (rounded columns).
 * Query params: fit=crop keeps aspect ratio within the requested box.
 */
const plumberCircle =
    'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=500&h=500&q=85';
const paintingTall =
    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=500&h=800&q=85';
const electricalTall =
    'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=500&h=800&q=85';
const toolsCircle =
    'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=500&h=500&q=85';
</script>
<template>
    <div class="row g-4 position-relative">
        <div class="position-absolute bg-dot main start-0 top-0 w-25 h-25 opacity-50"></div>
        <div class="col-6">
            <div class="mb-4 position-relative z-1">
                <img :src="plumberCircle" class="img-fluid circle" width="500" height="500"
                    alt="Professional plumber fixing a pipe under a sink" decoding="async" />
            </div>
            <div class="position-relative z-1">
                <img :src="paintingTall" class="img-fluid rounded-5" width="500" height="800"
                    alt="Painter applying fresh paint to interior walls during a home renovation" decoding="async" />
            </div>
        </div>
        <div class="col-6">
            <div class="mb-4 position-relative z-1">
                <img :src="electricalTall" class="img-fluid rounded-5" width="500" height="800"
                    alt="Electrician working on residential electrical wiring safely" decoding="async" />
            </div>
            <div class="position-relative z-1">
                <img :src="toolsCircle" class="img-fluid circle" width="500" height="500"
                    alt="Hand tools and equipment ready for home repair work" decoding="async" />
            </div>
        </div>
        <div class="position-absolute bg-dot yellow end-0 bottom-0 w-25 h-25 opacity-50"></div>
    </div>
</template>
