<script setup>
import {onBeforeMount, ref} from 'vue';
import axios from 'axios';
import router from '@/router';

const isVerified = ref(false);
const errorMessage = ref();
const isVerificationLoading = ref(false);
const resetPasswordToken = ref();
const isDisabled = ref(false);

const password = defineModel('password');
const reEnterPassword = defineModel('reEnterPassword');

const passwordChangeErrorMessage = ref();
const isPasswordChangeLoading = ref(false);
const passwordUpdateInformation = ref('');

onBeforeMount(async () => {
    try{
        isVerificationLoading.value = true;
        const passwordResetVerifyToken = router.currentRoute.value.params.passwordResetVerifyToken;
        resetPasswordToken.value = passwordResetVerifyToken;
        const passwordResetValidateResponse = await axios.get(`/api/user/password/reset/verify/${passwordResetVerifyToken}`, {
            headers: {
                Accept: 'application/json'
            }
        });

        if(passwordResetValidateResponse.data.status === 'Accpeted'){
            isVerified.value = true;
        }
    } catch(error){
        errorMessage.value = error.response.data.error;
    } finally{
        isVerificationLoading.value = false;
    }
});

async function handleSubmit(){
    try{
        isPasswordChangeLoading.value = true;
        const passwordChangeResponse = await axios.put(`/api/user/password/reset/${resetPasswordToken.value}`, {
            password: password.value,
            password_confirmation: reEnterPassword.value
        });

        
        passwordUpdateInformation.value = passwordChangeResponse.data.message;
        isDisabled.value = true;
    } catch(error){
        passwordChangeErrorMessage.value = error.response.data.error;
    } finally{
        isPasswordChangeLoading.value = false;
    }
}
</script>

<template>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <h3 v-show="isVerificationLoading" class="mb-4">Loading ...</h3>
        <div v-if="errorMessage" class="w-75">
            <h2 class="text-center text-danger mb-4">{{errorMessage}}</h2>
            <div class="w-100 text-center">
                <RouterLink to="/login" class="text-center mt-4 w-100">Go back to login page</RouterLink>
            </div>
        </div>

        
        <div v-if="isVerified" class="w-50">
            <h1 class="text-center">New Password</h1>

            <form @submit.prevent="handleSubmit" class="d-flex flex-column align-items-center">
                <div class="form-group p-4 w-100">
                    <label for="password">Password</label>
                    <input :disabled="isDisabled" v-model="password" type="password" class="form-control" id="password" placeholder="Password">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="reEnterPassword">Re-enter password:</label>
                    <input :disabled="isDisabled" v-model="reEnterPassword" type="password" class="form-control" id="reEnterPassword" placeholder="Re-enter password">
                </div>
                <em v-show="isPasswordChangeLoading" class="mb-4">Loading ...</em>
                <em v-if="passwordChangeErrorMessage" class="text-danger mb-4">{{passwordChangeErrorMessage}}</em>
                <div v-if="passwordUpdateInformation" class="m-4 text-center">
                    <em class="text-center">{{passwordUpdateInformation}}</em>
                    <RouterLink to="/login" class="text-center mt-4">
                        <button type="button" class="btn btn-primary w-50 d-block mx-auto">Login</button>
                    </RouterLink>
                </div>
                <button :disabled="isDisabled" type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>

    </div>
</template>