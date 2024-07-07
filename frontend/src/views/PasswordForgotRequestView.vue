<script setup>
import axios from 'axios';
import {ref} from 'vue';
import InformationAlert from '../components/InformationAlert.vue'

const email = defineModel('email');

const isLoading = ref(false);
const errorMessages = ref([]);
const information = ref();
const isDisabled = ref(false);

async function handleSubmit(){
    try{
        isLoading.value = true;
        errorMessages.value = [];
        const passwordResetRequestResponse = await axios.post('/api/user/password/reset', {
            email: email.value
        }, {
            headers: {
                'Accept': 'application/json'
            }
        });

        information.value = passwordResetRequestResponse.data.message;
        isDisabled.value = true;
    } catch(error){
        errorMessages.value = error.response.data.error;
    } finally{
        isLoading.value = false;
    }
}
</script>

<template>
    <InformationAlert :message="information"/>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="w-50">
            <h1 class="text-center">Request resetting password</h1>
            <p class="text-center mt-5">Please give the email that we should send the reset link.</p>
            <form @submit.prevent="handleSubmit" class="d-flex flex-column align-items-center">
                <div class="form-group p-4 w-100">
                    <label for="email">Email:</label>
                    <input v-model="email" :disabled="isDisabled" type="email" class="form-control" id="email" placeholder="Email address">
                </div>
                <em v-show="isLoading" class="mb-4">Loading ...</em>
                <em v-for="errorMessage in errorMessages" :key="errorMessage" class="text-danger mb-4">{{errorMessage}}</em>
                <button :disabled="isDisabled" type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>
</template>