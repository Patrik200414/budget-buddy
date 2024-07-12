<script setup>
import axios from 'axios';
import { ref } from 'vue';
import {changeInputsDisable, convertGeneratedInputToRequestInput, formatErrorMessages, inputFactory} from '../utility/utility';
import DynamicForm from './DynamicForm.vue';


const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

const props = defineProps({
    currUser: Object,
    onUpdateUser: Function,
    onEditProfile: Function
});

const formInputs = ref([
    inputFactory('First name', 'firstName', 'text', false, props.currUser.firstName, 'First name'),
    inputFactory('Last name', 'lastName', 'text', false, props.currUser.lastName, 'Last name'),
    inputFactory('Email', 'email', 'text', false, props.currUser.email, 'Email'),
    inputFactory('Previous password', 'previousPassword', 'password', false, '', 'Previous password'),
    inputFactory('Password', 'password', 'password', false, '', 'Password'),
    inputFactory('Password confirmation', 'password_confirmation', 'password', false, '', 'Password confirmation')
]);


const isLoading = ref(false);
const errorMessages = ref([]);
const isInputsDisabled = ref(false);

async function handleSubmit(){
    try{
        isLoading.value = true;
        isInputsDisabled.value = true;
        const userUpdate = convertGeneratedInputToRequestInput(formInputs);
        changeInputsDisable(formInputs.value, true);

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
        changeInputsDisable(formInputs.value, false);
        isLoading.value = false;
    }
}

function setValuesToResponse(userUpdateResponse){
    const updatedUser = userUpdateResponse.data.user;
    localStorage.setItem(tokenName, JSON.stringify(updatedUser));
    props.onUpdateUser(updatedUser);
    props.onEditProfile()
}

function emptyPasswordInformations(){
    for(let i = 2; i < formInputs.value.length; i++){
        formInputs.value[i].value = '';
    }
}

function handleInputChangeValue(index, inputValue){
    formInputs.value[index].value = inputValue;
}
</script>

<template>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="w-75">
            <form v-if="props.currUser" @submit.prevent="handleSubmit" class="d-flex flex-column align-items-center">
                <DynamicForm @onInputValueChange="handleInputChangeValue" @onSubmit="handleSubmit" :inputs="formInputs" :errorMessages="errorMessages" :isLoading="isLoading"/>
                <button @click="onEditProfile" :disabled="isInputsDisabled" type="button" class="btn btn-secondary w-100 mt-2">Cancel!</button>
            </form>
        </div>
    </div>
</template>