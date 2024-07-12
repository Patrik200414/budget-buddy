<script setup>
import router from '@/router';
import { getLogedInUser } from '@/utility/utility';
import { onBeforeMount } from 'vue';

onBeforeMount(() => {
  getLogedInUser();
})
</script>

<template>
  <h1>Home page!</h1>
</template>
