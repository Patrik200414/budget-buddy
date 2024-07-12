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