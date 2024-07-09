<script setup>
const props = defineProps({
    currUser: Object,
    onEditProfile: Function
})
</script>

<template>
    <div class="card mt-5">
        <div class="card-body text-center mb-3">
            <img class="m-4" src="../assets/profile-default-svgrepo-com (1).svg">
            <h5 class="card-title">{{ props.currUser.firstName }} {{ props.currUser.lastName }}</h5>
            <ul class="list-group">
                <li class="list-group-item"><h4>Email: </h4>{{ props.currUser.email }}</li>
                <li class="list-group-item"><h4>First name: </h4>{{ props.currUser.firstName }}</li>
                <li class="list-group-item"><h4>Last name: </h4>{{ props.currUser.lastName }}</li>
            </ul>
            <button @click="onEditProfile" type="button" class="btn btn-dark m-5">Update profile</button>
        </div>
    </div>
</template>