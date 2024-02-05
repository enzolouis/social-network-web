function deleteGetURL() {
    var url = window.location.href;

    if (url.indexOf('passlogin=') !== -1) {
        var newURL = url.replace(/([?&])passlogin=[^&]+(&|$)/, '$1');
        
        window.history.replaceState({}, document.title, newURL);
    }
}

/* delete url */
deleteGetURL();

document.getElementById("password").addEventListener("input", function(event) {
    document.getElementById("password").value = document.getElementById("password").value.replace(' ', '');
});

document.getElementById("login").addEventListener('input', function(event) {
    document.getElementById("login").value = document.getElementById("login").value.replace(/[^a-zA-Z0-9]/g, '');
});