<script setup>
import {ref, onBeforeMount} from 'vue';
import axios from 'axios';
import router from '@/router';

const information = ref();
const errorMessage = ref();


onBeforeMount(async () => {
    try{

        const userId = router.currentRoute.value.params.userId;
        const verificationResponse = await axios.get(`http://localhost:8000/api/user/email/verify/${userId}`, {}, {
            headers: {
                'Accept': 'application/json'
            }
        });

        information.value = verificationResponse.data.message;
    } catch(error){
        errorMessage.value = error.response.data.error
    }
})
</script>

<template>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div v-if="information && !errorMessage" class="alert alert-primary text-center" role="alert">
            <h2>{{ information }}</h2>
        </div>
        <div v-if="!information && errorMessage" class="alert alert-danger text-center" role="alert">
            <h2>{{ errorMessage }}</h2>
        </div>
    </div>
</template>