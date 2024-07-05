<script setup>
import router from '@/router';
import axios from 'axios';
import { ref } from 'vue';

const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

const email = defineModel('email');
const password = defineModel('password');
const isLoading = ref(false);
const errorMessage = ref();

async function handleSubmit(){
    try{
        errorMessage.value = null;
        isLoading.value = true;
        const {data} = await axios.post('/api/user/login', {
            email: email.value,
            password: password.value
        });

        localStorage.setItem(tokenName, JSON.stringify(data));
        router.push('/');
    } catch(error){
        console.log(error);
        const responseMessage = error.response.data.error
        errorMessage.value = responseMessage;
    } finally{
        isLoading.value = false;
    }
}
</script>

<template>
     <div class="container d-flex justify-content-center">
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
                <em v-if="errorMessage" class="text-danger mb-4">{{errorMessage}}</em>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>
</template>