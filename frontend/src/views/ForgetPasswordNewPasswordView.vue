<script setup>
import {onBeforeMount, ref} from 'vue';
import axios from 'axios';
import router from '@/router';
import {changeInputsDisable, convertGeneratedInputToRequestInput, formatErrorMessages, inputFactory} from '../utility/utility';
import DynamicForm from '@/components/DynamicForm.vue';

const formInputs = ref([
    inputFactory('Password', 'password', 'password', false, '', 'Password'),
    inputFactory('Re-enter password', 'password_confirmation', 'password', false, '', 'Re-enter password'),
])
const isVerified = ref(false);
const errorMessage = ref();
const isVerificationLoading = ref(false);
const resetPasswordToken = ref();
const isDisabled = ref(false);

const password = defineModel('password');
const reEnterPassword = defineModel('reEnterPassword');

const passwordChangeErrorMessages = ref([]);
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
        errorMessage.value = error.response.data.errors;
    } finally{
        isVerificationLoading.value = false;
    }
});

async function handleSubmit(){
    try{
        isPasswordChangeLoading.value = true;
        passwordChangeErrorMessages.value = [];

        const updatedPassword = convertGeneratedInputToRequestInput(formInputs);
        console.log(updatedPassword);
        changeInputsDisable(formInputs.value, true);
        const passwordChangeResponse = await axios.put(`/api/user/password/reset/${resetPasswordToken.value}`, 
            updatedPassword, {
            headers: {
                Accept: 'application/json'
            }
        });
      
        passwordUpdateInformation.value = passwordChangeResponse.data.message;
        isDisabled.value = true;
    } catch(error){
        changeInputsDisable(formInputs.value, false);
        const errors = formatErrorMessages(error)
        passwordChangeErrorMessages.value = errors;
    } finally{
        isPasswordChangeLoading.value = false;
    }
}

function handleInputChangeValue(index, inputValue){
    formInputs.value[index].value = inputValue;
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
                <DynamicForm @onInputValueChange="handleInputChangeValue" @onSubmit="handleSubmit" :inputs="formInputs" :errorMessages="passwordChangeErrorMessages" :isLoading="isPasswordChangeLoading"/>
                <div v-if="passwordUpdateInformation" class="m-4 text-center">
                    <em class="text-center">{{passwordUpdateInformation}}</em>
                    <RouterLink to="/login" class="text-center mt-4">
                        <button type="button" class="btn btn-primary w-50 d-block mx-auto">Login</button>
                    </RouterLink>
                </div>
            </form>
        </div>

    </div>
</template>