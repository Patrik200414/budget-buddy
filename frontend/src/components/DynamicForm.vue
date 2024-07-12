<script setup>
import DisplayErrorMessages from './DisplayErrorMessages.vue';


const props = defineProps({
    inputs: Array,
    errorMessages: Array,
    isLoading: Boolean
});

</script>

<template>
    <form class="d-flex flex-column align-items-center" @submit.prevent="$emit('onSubmit')">
        <div v-for="(input, index) in props.inputs" :key="input.id" class="form-group p-4 w-100">
            <label :for="input.id">{{ input.label }}:</label>
            <input @input.trim="e => $emit('onInputValueChange', index, e.target.value)" :disabled="input.isDisabled" :type="input.type" class="form-control" :id="input.id" :placeholder="input.placeholder" :value="input.value">
        </div>
        <DisplayErrorMessages :errorMessages="props.errorMessages"/>
        <em v-show="isLoading" class="mb-4">Loading ...</em>
        <button class="btn btn-primary w-100 m-5" type="submit">Submit</button>
    </form>
</template>