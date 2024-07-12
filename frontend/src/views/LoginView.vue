<script setup>
import router from '@/router';
import axios from 'axios';
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import {changeInputsDisable, convertGeneratedInputToRequestInput, formatErrorMessages, inputFactory} from '../utility/utility';
import DynamicForm from '@/components/DynamicForm.vue';

const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

const formInputs = ref([
    inputFactory('Email', 'email', 'email', false, '', 'Email'),
    inputFactory('Password', 'password', 'password', false, '', 'Password')
]);

const email = defineModel('email');
const password = defineModel('password');
const isLoading = ref(false);
const errorMessages = ref([]);

async function handleSubmit(){
    try{
        errorMessages.value = [];
        isLoading.value = true;

        const loginInformation = convertGeneratedInputToRequestInput(formInputs);
        formInputs.value = changeInputsDisable(formInputs.value, true);
        const {data} = await axios.post('/api/user/login', loginInformation, {
            headers: {
                'Accept': 'application/json'
            }
        });

        localStorage.setItem(tokenName, JSON.stringify(data));
        router.push('/');
    } catch(error){
        formInputs.value = changeInputsDisable(formInputs.value, false);
        const errors = formatErrorMessages(error);
        errorMessages.value = errors;
    } finally{
        isLoading.value = false;
    }
}

function handleInputChangeValue(index, inputValue){
    formInputs.value[index].value = inputValue;
}
</script>

<template>
     <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="w-50">
            <h1 class="text-center">Login</h1>
            <DynamicForm @onInputValueChange="handleInputChangeValue" @onSubmit="handleSubmit" :inputs="formInputs" :errorMessages="errorMessages" :isLoading="isLoading"/>
            <RouterLink to="/registration" class="text-center mt-4">Create account!</RouterLink>
            <br>
            <RouterLink to="/password/forget/request" class="text-center mt-4">Forgot password!</RouterLink>
        </div>
    </div>
</template>