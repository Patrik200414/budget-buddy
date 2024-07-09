<script setup>
import { ref } from 'vue';
import ProfileInformations from '../components/ProfileInformations.vue';
import ProfileInformationUpdateForm from '../components/ProfileInformationUpdateForm.vue'

const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;
const currUser = ref(JSON.parse(localStorage.getItem(tokenName)));
</script>

<template>
    <div class="container d-flex justify-content-center align-items-center mt-5">
        <div class="w-50">
            <h1 class="text-center">Profile</h1>
            <ProfileInformationUpdateForm :currUser="currUser"/>
            <!-- <ProfileInformations :currUser="currUser"/> -->
        </div>
    </div>
</template>