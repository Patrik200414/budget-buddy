<script setup>
import { ref } from 'vue';
import { inputFactory } from '@/utility/utility';
import DynamicForm from '../components/DynamicForm.vue';

const currDate = new Date().toISOString().split('T')[0];

const formInputs = ref([
    inputFactory('Account name', 'accountName', 'text', false, '','Account name'),
    inputFactory('Balance', 'balance', 'number', false, 0, 'Balance'),
    inputFactory('Account number', 'accountNumber', 'text', false, '', 'Account number'),
    inputFactory('Monthly interest', 'monthlyInterest', 'number', false, 0, 'Monthly interest'),
    inputFactory('Monthly maintenance fee', 'monthlyMaintenanceFee', 'number', false, '', 'Monthly maintenance fee'),
    inputFactory('Transaction fee', 'transactionFee', 'number', false, 0, 'Transaction fee'),
    inputFactory('Minimum balance', 'minimumBalance', 'number', false, 0, 'Minimum balance'),
    inputFactory('Max amount of transactions monthly', 'maxAmountOfTransactionsMonthly', 'number', false, 6, 'Max amount of transactions monthly'),
    inputFactory('Limit exceeding fee', 'limitExceedingFee', 'number', false, 0, 'Limit exceeding fee'),
    inputFactory('Last interest paid at', 'lastInterestPaiedAt', 'date', false, currDate, 'Last interest paid at')
]);

function handleInputChangeValue(index, inputValue){
    formInputs.value[index].value = inputValue;
}


</script>


<template>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="w-50">
            <h1 class="text-center">Create account</h1>
            <DynamicForm @onInputValueChange="handleInputChangeValue" :inputs="formInputs"/>
        </div>
    </div>
</template>