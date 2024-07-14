<script setup>
import axios from 'axios';
import { onBeforeMount, ref } from 'vue';
import SelectTransferAccount from '@/components/SelectTransferAccount.vue';
import AccountSummaryCard from './AccountSummaryCard.vue';
import { formatErrorMessages } from '@/utility/utility';
import DisplayErrorMessages from './DisplayErrorMessages.vue';


const props = defineProps({
    deletedAccount: Object,
    token: String
});
const emit = defineEmits(['onAccountDeleteSuccess']);

const transferAccounts = ref([]);
const selectedTransferAccountIndex = ref();

const errors = ref([]);

onBeforeMount(() => {
    getTransferToAccounts();
});


async function getTransferToAccounts(){
    try{
        const transferToAccountsResponse = await axios.get(`/api/account/transfer/${props.deletedAccount.id}`, {
            headers:{
                Accept: 'application/json',
                Authorization: 'Bearer ' + props.token
            }
        });
        transferAccounts.value = transferToAccountsResponse.data.accounts;
    } catch(error){
        console.log(error);
    }
}

async function handleDeletion(){
    try{
        const transferAccountId = transferAccounts.value[selectedTransferAccountIndex.value] ? transferAccounts.value[selectedTransferAccountIndex.value].id : null;
        const deleteAccountResponse = await axios.delete(`/api/account/`, {
            params: {
                deletedAccount: props.deletedAccount.id,
                transferAccount: transferAccountId
            },
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + props.token
            }
        });
        emit('onAccountDeleteSuccess');
    } catch(error){
        errors.value = formatErrorMessages(error);
    }
}
</script>

<template>
    <div class="modal fade show" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false" style="display: block;">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Delete account</h5>
                  <button @click="$emit('onModalClose')" type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                <h3>{{ props.deletedAccount.accountName }}</h3>
                <h6 class="text-muted">Account number: {{ props.deletedAccount.accountNumber }}</h6>
                <h6 class="text-muted">Balance: {{ props.deletedAccount.balance }}</h6>
                <SelectTransferAccount @onSelectTransferAccount="selectedIndex => selectedTransferAccountIndex = selectedIndex" :transferAccounts="transferAccounts"/>
                <AccountSummaryCard class="w-100 mt-4" v-if="selectedTransferAccountIndex" :isDeletable="false" :accountSummary="transferAccounts[selectedTransferAccountIndex]"/>
              </div>
              <DisplayErrorMessages :errorMessages="errors"/>
              <div class="modal-footer">
                  <button @click="$emit('onModalClose')" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button @click="handleDeletion" type="button" class="btn btn-danger">Delete account</button>
              </div>
          </div>
      </div>
    </div>
</template>