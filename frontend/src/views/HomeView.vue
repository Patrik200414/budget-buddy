<script setup>
import router from '@/router';
import { onBeforeMount } from 'vue';
import Navbar from '@/components/Navbar.vue';

onBeforeMount(() => {
  const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

  if(!localStorage.getItem(tokenName)){
    router.push('/login');
    return;
  }
})
</script>

<template>
  <h1>Home page!</h1>
</template>
