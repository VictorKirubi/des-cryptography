//Send message to API for encryption and sharing via email
async function sendMessage() {
    let msg = document.querySelector("#esubject").value;
    let email = document.querySelector("#email").value;
    let phone = document.querySelector("#phone").value;

    var bodyParams = new URLSearchParams('message=' + msg);
    bodyParams.append("recepemail", email);
    bodyParams.append("recepphone", phone);

    fetch('handler.php', {
        method: 'POST',
        body: bodyParams
    }).then((response) => {
        response.json().then((data) => {
            console.log(data);
        })
    })

}


//Send message to API for decryption
async function decryptMessage() {
    let encmsg = document.querySelector("#encmessage").value;
    let ivcode = document.querySelector("#ivcode").value;
    let dkey = document.querySelector("#dkey").value;

    var bodyParams = new URLSearchParams('encmessage=' + encodeURIComponent(encmsg));
    bodyParams.append("ivcode", ivcode);
    bodyParams.append("dkey", dkey);

    fetch('handler.php', {
        method: 'POST',
        body: bodyParams
    }).then((response) => {
        response.json().then((data) => {
            displayDecrypted(data);
            console.log(data);
        })
    })

    return false;
}

//Display decrypted message
function displayDecrypted(decmsg) {
    let dec = document.querySelector("#decmessage");
    dec.value = decmsg;
}