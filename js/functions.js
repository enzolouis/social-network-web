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