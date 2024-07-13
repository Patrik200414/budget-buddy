<script setup>
const props = defineProps({
    transferAccounts: Array
});
</script>

<template>
    <form>
        <label for="transferAccount">Select transfer account</label>
        <select id="transferAccount" class="form-select" aria-label="Default select example" @change="e => $emit('onSelectTransferAccount', e.target.value)">
            <option>Holdings account</option>
            <option v-for="(transferAccount, index) in props.transferAccounts" :value="index" :key="transferAccount.id">{{ transferAccount.accountName }}</option>
        </select>
    </form>
</template>