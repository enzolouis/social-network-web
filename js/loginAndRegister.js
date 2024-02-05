function deleteGetURL() {
    var url = window.location.href;

    if (url.indexOf('passlogin=') !== -1) {
        var newURL = url.replace(/([?&])passlogin=[^&]+(&|$)/, '$1');
        
        window.history.replaceState({}, document.title, newURL);
    }
}


/* delete url */
deleteGetURL();

/* listeners */

document.getElementById("login").addEventListener('input', function(event) {
    document.getElementById("login").value = document.getElementById("login").value.replace(/[^a-zA-Z0-9]/g, '');
});