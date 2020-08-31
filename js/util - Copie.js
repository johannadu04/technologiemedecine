$('.parallax-window').parallax({
	imageSrc: 'img//BIO.MP4'
});


window.onscroll = function () {
	myFunction()
};

function myFunction() {
	if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
		document.getElementById("myP").className = "myP";
		playVid();
		//alert("tu et passer");
	} else {
		document.getElementById("myP").className = "";
	}
}

var vid = document.getElementById("myP");

function playVid() {
	vid.play();
}

function pauseVid() {
	vid.pause();
}

document.getElementById("myP").controls = false


$(function () {
	$('.tol1').tooltip()
	$('.tol2').tooltip()
	$('.tol3').tooltip()
	$('.tol4').tooltip()
})