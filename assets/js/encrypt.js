function enc(plaintText){

    var encryptedBase64Key = 'ZnNjY29vcEA5MTJ1cGJlYW4=';
    var parsedBase64Key = CryptoJS.enc.Base64.parse(encryptedBase64Key);

    return  CryptoJS.AES.encrypt(plaintText, parsedBase64Key, {
        mode: CryptoJS.mode.ECB,
    });

}