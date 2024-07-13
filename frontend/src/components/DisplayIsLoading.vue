<script setup>
const props = defineProps({
    isLoading: Boolean
})
</script>

<template>
    <em v-show="props.isLoading" class="mb-4">Loading ...</em>
</template>