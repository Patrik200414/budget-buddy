<script setup>
const props = defineProps({
    errorMessages: Array
});
</script>

<template>
    <em v-for="errorMessage in props.errorMessages" :key="errorMessage" class="text-danger mb-4 text-center">{{errorMessage}}</em>
    <br />
</template>