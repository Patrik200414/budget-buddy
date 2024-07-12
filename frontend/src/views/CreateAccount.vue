<script setup>
import { getLogedInUser } from '@/utility/utility';
import { onBeforeMount } from 'vue';


onBeforeMount(() => {
    getLogedInUser();
})
</script>

<template>
    <RouterLink to="/account/create/savings-account">Create savings account</RouterLink>
</template>