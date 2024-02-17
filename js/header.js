
function increaseHeaderWidth() {
	clearTimeout(typingTimer);

	typingTimer = setTimeout(() => {
		document.getElementById("header").style.width = '390px'
		Array.from(document.getElementsByClassName("precision")).forEach((span) => {
			span.style.opacity = "1";
		})
	}, 100);
}

function decreaseHeaderWidth() {
	clearTimeout(typingTimer);
	
	document.getElementById("header").style.width = '90px'
	Array.from(document.getElementsByClassName("precision")).forEach((span) => {
		span.style.opacity = "0";
	})
}

function disconnect() {
	$.ajax({
		type: 'POST',
		url: '../functions/databaseFunctions.php',
		data: {
			disconnect: true,
		},
		success: function(){
			window.location.href = "../../index.php";
		}
	})
}