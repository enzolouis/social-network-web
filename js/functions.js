function limitLetters() {
    var elements = document.getElementsByClassName('login');
    for (var i = 0; i < elements.length; i++) {
      elements[i].addEventListener('input', function (event) {
        var inputValue = this.value;
        var emptyValue = inputValue.replace(/[^a-zA-Z0-9]/g, '');
        this.value = emptyValue;
      });
    }
}
limitLetters();

function deleteGetURL() {
    var url = window.location.href;

    if (url.indexOf('passlogin=') !== -1) {
        var newURL = url.replace(/([?&])passlogin=[^&]+(&|$)/, '$1');
        
        window.history.replaceState({}, document.title, newURL);
    }
}
deleteGetURL();