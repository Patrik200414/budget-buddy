<script setup>
import DisplayErrorMessages from '@/components/DisplayErrorMessages.vue';
import DisplayIsLoading from '@/components/DisplayIsLoading.vue';
import AccountSummaryCard from '@/components/AccountSummaryCard.vue';
import DeleteModal from '@/components/DeleteModal.vue';
import { getLogedInUser } from '@/utility/utility';
import axios from 'axios';
import { onBeforeMount, ref } from 'vue';

const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

const currUser = ref();
const accountSummaries = ref([]);
const errorMessage = ref();
const isLoading = ref();

const isModalOpen = ref(false);
const selectedAccount = ref();

onBeforeMount(() => {
  currUser.value = getLogedInUser();
  getAccountSummaries();
});


async function getAccountSummaries(){
  try{
    isLoading.value = true;
    const accountSummariesResponse = await axios.get('/api/account/summary', {
      headers: {
        Accept: 'application/json',
        Authorization: 'Bearer ' + currUser.value[tokenName]
      }
    });
    accountSummaries.value = accountSummariesResponse.data.accounts;
  } catch(error){ 
    errorMessage.value = error.response.data.message;
  } finally{
    isLoading.value = false;
  }
}

function openModal(accountId){
  isModalOpen.value = true
  selectedAccount.value = accountId;
}

function closeModal(){
  isModalOpen.value = false;
  getAccountSummaries();
  selectedAccount.value = undefined;
}
</script>

<template>
  <div class="container d-flex flex-column justify-content-center align-items-center">
    <h1 class="mt-5">Dash board</h1>
    <DeleteModal v-if="isModalOpen" @onAccountDeleteSuccess="closeModal" :deletedAccount="selectedAccount" :token="currUser[tokenName]" @onModalClose="() => isModalOpen = false"/>
    <div v-if="!isModalOpen" class="container d-flex flex-wrap justify-content-between mt-3" data-bs-backdrop="static">
      <AccountSummaryCard @onModalOpen="openModal" class="m-2" v-for="accountSummary in accountSummaries" :key="accountSummary.id" :accountType="accountSummary.accountType" :accountSummary="accountSummary"/>
    </div>
    <DisplayErrorMessages :errorMessages="[errorMessage]"/>
    <DisplayIsLoading :isLoading="isLoading"/>
  </div>
</template>
