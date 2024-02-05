document.getElementById("password").addEventListener("input", function(event) {
    document.getElementById("password").value = document.getElementById("password").value.replace(' ', '');
});