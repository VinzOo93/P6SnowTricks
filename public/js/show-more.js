window.addEventListener("load", init);

function init() {

    let btn = document.querySelector("#show-more");
    let loader = document.querySelector(".loader");
    let arrow = document.querySelector(".arrow-down");
    let span = document.querySelector(".content-span");

    btn.addEventListener("click", function (e) {
        btn.style.visibility = "hidden";
        loader.style.visibility = "visible";

        let tricks = document.querySelectorAll(".js-card");
        let lastTrick = tricks[tricks.length - 1].getAttribute("id");
        const Params = new URLSearchParams();

        Params.append("id", lastTrick);
        const Url = new URL(window.location.href);

        fetch(Url.pathname + "?" + Params.toString() + "&load=1", {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        }).then(response => response.json()
        ).then(data => {
            let content = document.createElement("div");

            content.innerHTML = data.content
            span.appendChild(content);
            btn.style.visibility = "visible";
            loader.style.visibility = "hidden";
            arrow.style.visibility = "visible";

            if (content.innerText === "no records found"){
                btn.style.visibility = "hidden";
            }

        });
    });
}