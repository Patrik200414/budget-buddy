<script setup>
import router from '@/router';
import axios from 'axios';
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import {formatErrorMessages} from '../utility/utility';

const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

const email = defineModel('email');
const password = defineModel('password');
const isLoading = ref(false);
const errorMessages = ref([]);

async function handleSubmit(){
    try{
        errorMessages.value = [];
        isLoading.value = true;
        const {data} = await axios.post('/api/user/login', {
            email: email.value,
            password: password.value
        }, {
            headers: {
                'Accept': 'application/json'
            }
        });

        localStorage.setItem(tokenName, JSON.stringify(data));
        router.push('/');
    } catch(error){
        const errors = formatErrorMessages(error);
        errorMessages.value = errors;
    } finally{
        isLoading.value = false;
    }
}
</script>

<template>
     <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="w-50">
            <h1 class="text-center">Login</h1>
            <form @submit.prevent="handleSubmit" class="d-flex flex-column align-items-center">
                <div class="form-group p-4 w-100">
                    <label for="email">Email:</label>
                    <input v-model="email" type="email" class="form-control" id="email" placeholder="Email address">
                </div>
                <div class="form-group p-4 w-100">
                    <label for="password">Password</label>
                    <input v-model="password" type="password" class="form-control" id="password" placeholder="Password">
                </div>
                <em v-show="isLoading" class="mb-4">Loading ...</em>
                <em v-for="errorMessage in errorMessages" :key="errorMessage" class="text-danger mb-4">{{errorMessage}}</em>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
                <RouterLink to="/registration" class="text-center mt-4">Create account!</RouterLink>
                <RouterLink to="/password/forget/request" class="text-center mt-4">Forgot password!</RouterLink>
            </form>
        </div>
    </div>
</template>