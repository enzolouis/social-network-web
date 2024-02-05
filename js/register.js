let secure8letters = false;
let secureCapital = false;
let secureSpecial = false;
let secureNumber = false;


document.getElementById("password").addEventListener("input", function(event) {
    let content = document.getElementById("password").value;
    document.getElementById("password").value = document.getElementById("password").value.replace(' ', '');
    content = document.getElementById("password").value;
    

    let formatSpecial = /^[a-zA-Z0-9]*$/
    let formatCapital = /[A-Z]/
    let formatNumber  = /[0-9]/

    secure8letters= content.length >= 8
    secureCapital = formatCapital.test(content);
    secureSpecial = !formatSpecial.test(content);
    secureNumber  = formatNumber.test(content);

    document.getElementById("secure-8letters").style.display = secure8letters ? 'none' : 'block'
    document.getElementById("secure-capital").style.display  = secureCapital  ? 'none' : 'block'
    document.getElementById("secure-special").style.display  = secureSpecial  ? 'none' : 'block'
    document.getElementById("secure-number").style.display   = secureNumber   ? 'none' : 'block'

    let sumSecure;

    if (content.length == 0) {
        sumSecure = 0;
        document.getElementById("secure-password-details").style.height = "0";
    } else {
        sumSecure = secure8letters * 20 + 20 + secureCapital * 20 + secureSpecial * 20 + secureNumber * 20;
        if (sumSecure == 100) {
            document.getElementById("secure-password-details").style.height = "0";
        } else {
            document.getElementById("secure-password-details").style.height = "40px";            
        }
    }

    let barWidth = sumSecure+"%";
    let barColor;

    if (sumSecure <= 20) {
        barColor = "rgb(192, 57, 43)";
    } else if (sumSecure <= 40) {
        barColor = "rgb(231, 76, 60)";
    } else if (sumSecure <= 60) {
        barColor = "rgb(230, 126, 34)";
    } else if (sumSecure <= 80) {
        barColor = "rgb(241, 196, 15)";
    } else {
        barColor = "rgb(46, 204, 113)";
    }

    document.getElementById("secure-password").style.color = barColor;
    
    let style = document.getElementById("passwordDiv").style;
    style.setProperty('--register-bar-password-color', barColor);
    style.setProperty('--register-bar-password-width', barWidth);

    passwordVerifyEventListener()
});


document.getElementById("password-verify").addEventListener("input", function() {
    passwordVerifyEventListener()
});

document.getElementById("username").addEventListener('input', function(event) {
    document.getElementById("username").value = document.getElementById("username").value.trimStart().replace(/[^a-zA-Z0-9 ]| (?= )/g, '');
});

function passwordVerifyEventListener() {
    let contentPassword = document.getElementById("password").value;
    let contentPasswordVerify = document.getElementById("password-verify").value;
    document.getElementById("password-verify").value = contentPasswordVerify.replace(' ', '');
    contentPasswordVerify = document.getElementById("password-verify").value;

    let style = document.getElementById("passwordVerifyDiv").style;

    if (contentPasswordVerify.length == 0) {
        style.setProperty('--register-bar-password-verify-width', '0%');
        document.getElementById("secure-password-verify-details").style.height = "0";
    } else {
        style.setProperty('--register-bar-password-verify-width', '100%');
        document.getElementById("secure-password-verify-details").style.height = "20px";            
    }

    if (contentPassword == contentPasswordVerify) {
        style.setProperty('--register-bar-password-verify-color', 'rgb(46, 204, 113)');
        document.getElementById("secure-password-verify-details").style.height = "0";
    } else {
        style.setProperty('--register-bar-password-verify-color', 'rgb(231, 76, 60)');
    }
}