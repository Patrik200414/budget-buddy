<script setup>
import { onBeforeMount, ref } from 'vue';
import { inputFactory, convertGeneratedInputToRequestInput, getLogedInUser, formatErrorMessages, changeInputsDisable} from '@/utility/utility';
import DynamicForm from '../components/DynamicForm.vue';
import axios from 'axios';

const currDate = new Date().toISOString().split('T')[0];
const currUser = ref();
const information = ref();
const isLoading = ref(false);
const errorMessages = ref([]);
const isCreatedAlertVisible = ref(false);


const formInputs = ref([
    inputFactory('Account name', 'accountName', 'text', false, '','Account name'),
    inputFactory('Balance', 'balance', 'number', false, null, 'Balance'),
    inputFactory('Account number', 'accountNumber', 'text', false, '', 'Account number'),
    inputFactory('Monthly interest', 'monthlyInterest', 'number', false, null, 'Monthly interest'),
    inputFactory('Monthly maintenance fee', 'monthlyMaintenanceFee', 'number', false, '', 'Monthly maintenance fee'),
    inputFactory('Transaction fee', 'transactionFee', 'number', false, null, 'Transaction fee'),
    inputFactory('Minimum balance', 'minimumBalance', 'number', false, null, 'Minimum balance'),
    inputFactory('Max amount of transactions monthly', 'maxAmountOfTransactionsMonthly', 'number', false, 6, 'Max amount of transactions monthly'),
    inputFactory('Limit exceeding fee', 'limitExceedingFee', 'number', false, null, 'Limit exceeding fee'),
    inputFactory('Last interest paid at', 'lastInterestPaiedAt', 'date', false, currDate, 'Last interest paid at')
]);


onBeforeMount(() => {
    currUser.value = getLogedInUser();
});

function handleInputChangeValue(index, inputValue){
    formInputs.value[index].value = inputValue;
}

async function handleSubmit(){
    try{
        isLoading.value = true;
        errorMessages.value = [];

        const inputRequestValues = convertGeneratedInputToRequestInput(formInputs);
        formInputs.value = changeInputsDisable(formInputs.value, true);

        const createSavingsAccountResponse = await axios.post('/api/account/savings', inputRequestValues, {
            headers:{
                Accept: 'application/json',
                Authorization: `Bearer ${currUser.value.auth_token}`
            }
        });

        information.value = createSavingsAccountResponse.data.status;
        isCreatedAlertVisible.value = true;
        
        setTimeout(() => {
            isCreatedAlertVisible.value = false;
        }, 3000);
    } catch(error){
        errorMessages.value = formatErrorMessages(error); 
    } finally{
        isLoading.value = false;
        formInputs.value = changeInputsDisable(formInputs.value, false);
    }
}



</script>


<template>
    <div v-if="isCreatedAlertVisible && information" class="alert alert-success text-center" role="alert">{{ information }}</div>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="w-50">
            <h1 class="text-center">Create savings account</h1>
            <DynamicForm @onInputValueChange="handleInputChangeValue" @onSubmit="handleSubmit" :inputs="formInputs" :errorMessages="errorMessages" :isLoading="isLoading"/>
        </div>
    </div>
</template>