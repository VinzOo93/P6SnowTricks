window.addEventListener('load', init)

function init() {

    let btn = document.querySelector("#show-more")
    let loader = document.querySelector(".loader")
    let arrow = document.querySelector(".arrow-down")

    btn.addEventListener("click", function (e) {
        btn.style.visibility = "hidden";
        loader.style.visibility = "visible"

        let tricks = document.querySelectorAll(".js-card");
        let lastTrick = tricks[tricks.length - 1].getAttribute("id");
        const Params = new URLSearchParams();

        Params.append("id", lastTrick);

        console.log(Params);
        const Url = new URL(window.location.href)

        fetch(Url.pathname + "?" + Params.toString() + "&load=1", {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        }).then(response => response.json()
        ).then(data => {
            const content = document.querySelector("#content")
            content.innerHTML = data.content
            btn.style.visibility = "visible";
            loader.style.visibility = "hidden"
            arrow.style.visibility = "visible"
        }).catch((e) => console.log(e));
    });
}