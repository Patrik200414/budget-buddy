<script setup>
const information = defineProps({
    message: String
});

</script>

<template>
    <div v-if="information.message" class="alert alert-primary text-center" role="alert">
        {{ information.message }}
    </div>
</template>