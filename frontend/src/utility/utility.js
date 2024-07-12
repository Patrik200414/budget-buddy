import router from '@/router';

export function formatErrorMessages(error){
    const responseMessages = error.response.data.errors;
    const errors = [];
    for(const error in responseMessages){
        errors.push(responseMessages[error][0]);
    }

    return errors;
}

export function inputFactory(label, id, type, isDisabled, value, placeholder){
    return {
        label,
        id,
        type,
        isDisabled,
        value,
        placeholder
    }
}

export function convertGeneratedInputToRequestInput(formInputs){
    const inputRequestValues = {};
    for(const input of formInputs.value){
        inputRequestValues[input.id] = input.value;
    }

    return inputRequestValues;
}

export function getLogedInUser(){
    const tokenName = import.meta.env.VITE_AUTH_KEY_NAME;
    const token = localStorage.getItem(tokenName);

    if(!token){
        router.push('/login');
        return;
    }

    return JSON.parse(token);
}

export function changeInputsDisable(inputs, disableValue){
    const inputCopy = [...inputs];
    for(const input of inputCopy){
        input.isDisabled = disableValue;
    }

    return inputCopy;
}