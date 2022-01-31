window.addEventListener("load", init);

function init() {
    let btntop = document.querySelector("#js-scroll-top");
    let btnDown = document.querySelector("#js-scroll-down");
    let down = document.querySelector(".down");

    btnDown.addEventListener("click", function (e) {
        btnDown.scrollIntoView({inline:"end", behavior:"smooth"});
    });

    btntop.addEventListener("click", function (e) {
        down.scrollIntoView({block:"start", behavior:"smooth"});
    });


}