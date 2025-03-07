function serializeWithExtra(formId, extraData) {
    let serializedData = $(`#${formId}`).serialize();

    for (let key in extraData) {
        if (serializedData) {
            serializedData += '&';
        }
        serializedData += `${encodeURIComponent(key)}=${encodeURIComponent(extraData[key])}`;
    }

    return serializedData;
}
