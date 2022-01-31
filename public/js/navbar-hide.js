window.addEventListener("load", init);

function init() {
    var prevScrollpos = window.pageYOffset;

    window.onscroll = function () {
        var currentScrollPos = window.pageYOffset;

        let navbar = document.querySelector(".navbar");
        if (prevScrollpos > currentScrollPos && currentScrollPos === 0) {
            navbar.style.opacity = "1";
        } else {
            navbar.style.opacity = "0";
        }
        prevScrollpos = currentScrollPos;
    };
}