<script setup>
import { RouterLink } from 'vue-router';
import {ref} from 'vue';
import axios from 'axios';

const firstName = defineModel('firstName');
const lastName = defineModel('lastName');
const email = defineModel('email');
const password = defineModel('password');
const passwordConfirmation = defineModel('passwordConfirmation');

const isLoading = ref(false);
const errorMessages = ref([]);
const isInputsDisabled = ref(false);
const information = ref();

async function handleSubmit(){
    try{
        errorMessages.value = [];
        isLoading.value = true;
        isInputsDisabled.value = true;
        const registrationInformation = await axios.post('/api/user/registration', {
            firstName: firstName.value,
            lastName: lastName.value,
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value
        }, {
            headers: {
                'Accept': 'application/json'
            }
        });

        information.value = registrationInformation.data.message;
    } catch(error){
        errorMessages.value = error.response.data.error;
        isInputsDisabled.value = false;
    } finally{
        isLoading.value = false;
    }
}


</script>

<template>
    <div v-if="information" class="alert alert-primary text-center" role="alert">
        {{ information }}
    </div>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="w-50">
            <h1 class="text-center">Registration</h1>
            <form @submit.prevent="handleSubmit" class="d-flex flex-column align-items-center">
                <div class="form-group p-4 w-100">
                    <label for="firstName">First name:</label>
                    <input :disabled="isInputsDisabled" v-model="firstName" type="text" class="form-control" id="firstName" placeholder="First name">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="lastName">Last name:</label>
                    <input :disabled="isInputsDisabled" v-model="lastName" type="text" class="form-control" id="lastName" placeholder="Last name">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="email">Email:</label>
                    <input :disabled="isInputsDisabled" v-model="email" type="email" class="form-control" id="email" placeholder="Email address">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="password">Password:</label>
                    <input :disabled="isInputsDisabled" v-model="password" type="password" class="form-control" id="password" placeholder="Password">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="passwordConfirmation">Password confirmation:</label>
                    <input :disabled="isInputsDisabled" v-model="passwordConfirmation" type="password" class="form-control" id="passwordConfirmation" placeholder="Password confirmation">
                </div>
                <em v-show="isLoading" class="mb-4">Loading ...</em>
                <em v-for="errorMessage in errorMessages" :key="errorMessage" class="text-danger mb-4">{{errorMessage}}</em>
                <button :disabled="isInputsDisabled" type="submit" class="btn btn-primary w-100">Submit</button>
                <RouterLink to="/login" class="text-center mt-4">Login!</RouterLink>
            </form>
        </div>
    </div>
</template>