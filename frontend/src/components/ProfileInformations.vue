<script setup>
import { ref } from 'vue';

const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;
const currUser = ref(JSON.parse(localStorage.getItem(tokenName)));

const isWantToUpdate = ref(false);

function onWantToUpdateChange(){
    isWantToUpdate.value = !isWantToUpdate.value;
}
</script>

<template>
    <div class="card mt-5">
        <div v-if="!isWantToUpdate" class="card-body text-center">
            <img class="m-4" src="../assets/profile-default-svgrepo-com (1).svg">
            <h5 class="card-title">{{ currUser.firstName }} {{ currUser.lastName }}</h5>
            <ul class="list-group">
                <li class="list-group-item"><h4>Email: </h4>{{ currUser.email }}</li>
                <li class="list-group-item"><h4>First name: </h4>{{ currUser.firstName }}</li>
                <li class="list-group-item"><h4>Last name: </h4>{{ currUser.lastName }}</li>
            </ul>
            <button @click="onWantToUpdateChange" type="button" class="btn btn-dark m-5">Update profile</button>
        </div>
    </div>
</template>