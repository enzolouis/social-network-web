function increaseHeaderWidth() {
	document.getElementById("header").style.width = '390px'
	Array.from(document.getElementsByClassName("precision")).forEach((span) => {
		span.style.opacity = "1";
	})
}
function decreaseHeaderWidth() {
	document.getElementById("header").style.width = '90px'
	Array.from(document.getElementsByClassName("precision")).forEach((span) => {
		span.style.opacity = "0";
	})
}