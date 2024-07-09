<script setup>
import { ref } from 'vue';
import ProfileInformations from '../components/ProfileInformations.vue';
import ProfileInformationUpdateForm from '../components/ProfileInformationUpdateForm.vue'

const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;
const currUser = ref(JSON.parse(localStorage.getItem(tokenName)));
const isProfileUpdateFormOn = ref(false);
const isCreatedAlertVisible = ref(false);

function handleUpdateValue(updatedUser){
    currUser.value = updatedUser;
    isCreatedAlertVisible.value = true;
    setTimeout(() => {
        isCreatedAlertVisible.value = false;
    }, 3000);
}

function handleEditProfile(){
    isProfileUpdateFormOn.value = !isProfileUpdateFormOn.value;
}
</script>

<template>
    <div v-if="isCreatedAlertVisible" class="alert alert-success text-center" role="alert">User updated!</div>
    <div class="container d-flex justify-content-center align-items-center mt-5">
        <div class="w-50">
            <h1 class="text-center">Profile</h1>
            <ProfileInformationUpdateForm v-if="isProfileUpdateFormOn" :onUpdateUser="handleUpdateValue" :currUser="currUser" :onEditProfile="handleEditProfile"/>
            <ProfileInformations v-else :currUser="currUser" :onEditProfile="handleEditProfile"/>
        </div>
    </div>
</template>