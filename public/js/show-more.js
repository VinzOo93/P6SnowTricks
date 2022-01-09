window.onload = () => {
   let btn = document.querySelector("#show-more")
    let loader = document.querySelector(".loader")

    btn.addEventListener("click", function (e) {
        btn.style.visibility = "hidden";
        loader.style.visibility = "visible"


    });
}