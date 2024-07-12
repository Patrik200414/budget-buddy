<script setup>

const props = defineProps({
    inputs: Array
});

</script>

<template>
    <form class="d-flex flex-column align-items-center">
        <div v-for="(input, index) in props.inputs" :key="input.id" class="form-group p-4 w-100">
            <label :for="input.id">{{ input.label }}:</label>
            <input @input.trim="e => $emit('onInputValueChange', index, e.target.value)" :disabled="input.isDisabled" :type="input.type" class="form-control" :id="input.id" :placeholder="input.placeholder" :value="input.value">
        </div>
    </form>
</template>