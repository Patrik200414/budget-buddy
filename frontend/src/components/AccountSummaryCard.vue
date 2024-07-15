<script setup>
const props = defineProps({
    accountSummary: Object,
    accountType: String
});

const HOLDINGS_ACCOUNT = 'Holdings account';
</script>

<template>
    <div class="card" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title"><span>Balance: </span>{{ props.accountSummary.balance }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">{{ props.accountSummary.accountName }}</h6>
            <h6 class="card-subtitle mb-2 text-muted">{{ props.accountSummary.accountNumber }}</h6>
            <em>{{ props.accountSummary.accountType }}</em>
            <br>
            <div v-if="accountType !== HOLDINGS_ACCOUNT" class="mt-3">
                <a href="#" class="card-link btn btn-warning">Update</a>
                <button @click="$emit('onModalOpen', props.accountSummary)" class="card-link btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button>
            </div>
        </div>
    </div>
</template>