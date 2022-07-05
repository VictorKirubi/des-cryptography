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

    //return false;
}