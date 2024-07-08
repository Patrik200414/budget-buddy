<script setup>
import { RouterLink } from 'vue-router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {faRightFromBracket} from '@fortawesome/free-solid-svg-icons/faRightFromBracket';
import router from '@/router';

function logout(){
    
    const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

    localStorage.removeItem(tokenName);
    router.push('/login');
}
</script>

<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary p-3">
        <RouterLink class="navbar-brand" to="/">Budget-Buddy</RouterLink>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <RouterLink class="nav-link" to="/account/create">Create account</RouterLink>
                </li>
                <li class="nav-item">
                    <RouterLink class="nav-link" to="/account/create">Profile</RouterLink>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                </li>
            </ul>
            <button @click="logout" class="btn btn-dark align-self-end">Logout <font-awesome-icon :icon="faRightFromBracket" /></button>
        </div>
    </nav>
</template>