<script setup>
import axios from 'axios';
import { ref } from 'vue';
import {formatErrorMessages} from '../utility/utility';


const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

const props = defineProps({
    currUser: Object,
    onUpdateUser: Function,
    onEditProfile: Function
});

const firstName = ref(props.currUser.firstName);
const lastName = ref(props.currUser.lastName);
const email = ref(props.currUser.email);
const previousPassword = ref();
const password = ref();
const passwordConfirmation = ref();

const isLoading = ref(false);
const errorMessages = ref([]);
const isInputsDisabled = ref(false);

async function handleSubmit(){
    try{
        isLoading.value = true;
        isInputsDisabled.value = true;
        const userUpdate = {
            firstName: firstName.value,
            lastName: lastName.value,
            email: email.value,
            previousPassword: previousPassword.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value
        }

        const userUpdateResponse = await axios.put('/api/user/update', userUpdate, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${props.currUser.auth_token}`
            }
        });

        setValuesToResponse(userUpdateResponse);
        emptyPasswordInformations();
    } catch(error){
        console.log(error)
        const errors = formatErrorMessages(error);
        errorMessages.value = errors;
    } finally{
        isLoading.value = false;
        isInputsDisabled.value = false;
    }
}

function setValuesToResponse(userUpdateResponse){
    const updatedUser = userUpdateResponse.data.user;
    localStorage.setItem(tokenName, JSON.stringify(updatedUser));
    props.onUpdateUser(updatedUser);
    props.onEditProfile()
}

function emptyPasswordInformations(){
    previousPassword.value = '';
    password.value = '';
    passwordConfirmation.value = '';
}

</script>

<template>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="w-75">
            <form v-if="props.currUser" @submit.prevent="handleSubmit" class="d-flex flex-column align-items-center">
                <div class="form-group p-4 w-100">
                    <label for="firstName">First name:</label>
                    <input @input="e => firstName = e.target.value" :disabled="isInputsDisabled" type="text" class="form-control" id="firstName" placeholder="First name" :value="firstName">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="lastName">Last name:</label>
                    <input @input="e => lastName = e.target.value" :disabled="isInputsDisabled" type="text" class="form-control" id="lastName" placeholder="Last name" :value="lastName">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="email">Email:</label>
                    <input @input="e => email = e.target.value" :disabled="isInputsDisabled" type="email" class="form-control" id="email" placeholder="Email address" :value="email">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="previousPassword">Previous password:</label>
                    <input @input="e => previousPassword = e.target.value" :disabled="isInputsDisabled" type="password" class="form-control" id="previousPassword" placeholder="Previous password" :value="previousPassword">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="password">Password:</label>
                    <input @input="e => password = e.target.value" :disabled="isInputsDisabled" type="password" class="form-control" id="password" placeholder="Password" :value="password">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="passwordConfirmation">Password confirmation:</label>
                    <input @input="e => passwordConfirmation = e.target.value" :disabled="isInputsDisabled" type="password" class="form-control" id="passwordConfirmation" placeholder="Password confirmation" :value="passwordConfirmation">
                </div>
                <em v-show="isLoading" class="mb-4">Loading ...</em>
                <em v-for="errorMessage in errorMessages" :key="errorMessage" class="text-danger mb-4">{{errorMessage}}</em>
                <button :disabled="isInputsDisabled" type="submit" class="btn btn-primary w-100">Update!</button>
                <button @click="onEditProfile" :disabled="isInputsDisabled" type="button" class="btn btn-secondary w-100 mt-2">Cancel!</button>
            </form>
        </div>
    </div>
</template>