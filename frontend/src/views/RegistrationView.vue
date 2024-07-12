<script setup>
import { RouterLink } from 'vue-router';
import {ref} from 'vue';
import axios from 'axios';
import InformationAlert from '../components/InformationAlert.vue';
import {changeInputsDisable, convertGeneratedInputToRequestInput, formatErrorMessages, inputFactory} from '../utility/utility';
import DynamicForm from '@/components/DynamicForm.vue';

const formInputs = ref([
    inputFactory('First name', 'firstName', 'text', false, '', 'First name'),
    inputFactory('Last name', 'lastName', 'text', false, '', 'Last name'),
    inputFactory('Email', 'email', 'email', false, '', 'Email'),
    inputFactory('Password', 'password', 'password', false, '', 'Password'),
    inputFactory('Password confirmation', 'password_confirmation', 'password', false, '', 'Passcord confirmation')
]);

const isLoading = ref(false);
const errorMessages = ref([]);
const isInputsDisabled = ref(false);
const information = ref();

async function handleSubmit(){
    try{
        errorMessages.value = [];
        isLoading.value = true;
        isInputsDisabled.value = true;

        const registrationInformations = convertGeneratedInputToRequestInput(formInputs);
        formInputs.value = changeInputsDisable(formInputs.value, true);
        const registrationInformation = await axios.post('/api/user/registration', 
        registrationInformations, {
            headers: {
                'Accept': 'application/json'
            }
        });



        information.value = registrationInformation.data.message;
    } catch(error){
        formInputs.value = changeInputsDisable(formInputs.value, false);
        const errors = formatErrorMessages(error);
        errorMessages.value = errors;
        isInputsDisabled.value = false;
    } finally{
        isLoading.value = false;
    }
}

function handleInputChangeValue(index, inputValue){
    formInputs.value[index].value = inputValue;
}
</script>

<template>
    <InformationAlert :message="information"/>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="w-50">
            <h1 class="text-center">Registration</h1>
            <DynamicForm @onInputValueChange="handleInputChangeValue" @onSubmit="handleSubmit" :inputs="formInputs" :errorMessages="errorMessages" :isLoading="isLoading"/>
            <RouterLink to="/login" class="text-center mt-4">Login!</RouterLink>
        </div>
    </div>
</template>