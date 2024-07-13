<script setup>
import DisplayErrorMessages from '@/components/DisplayErrorMessages.vue';
import DisplayIsLoading from '@/components/DisplayIsLoading.vue';
import AccountSummaryCard from '@/components/AccountSummaryCard.vue';
import { getLogedInUser } from '@/utility/utility';
import axios from 'axios';
import { onBeforeMount, ref } from 'vue';

const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;

const currUser = ref();
const accountSummaries = ref([]);
const errorMessage = ref();
const isLoading = ref();


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
</script>

<template>
  <div class="container d-flex flex-column justify-content-center align-items-center">
    <h1 class="mt-5">Dash board</h1>
    <div class="container d-flex flex-wrap justify-content-between mt-3">
      <AccountSummaryCard class="m-2" v-for="accountSummary in accountSummaries" :key="accountSummary.id" :accountSummary="accountSummary"/>
    </div>
    <DisplayErrorMessages :errorMessages="[errorMessage]"/>
    <DisplayIsLoading :isLoading="isLoading"/>
  </div>
</template>
