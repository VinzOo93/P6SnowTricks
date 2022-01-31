window.addEventListener("load", init);

function init(){
    let btn = document.querySelector("#show-more");
    let loader = document.querySelector(".loader");
    let span = document.querySelector(".content-span");

    btn.addEventListener("click", function (e) {
        btn.style.visibility = "hidden";
        loader.style.visibility = "visible";

        let comments = document.querySelectorAll(".js-card");
        let lastCom = comments[comments.length - 1].getAttribute("id");
        const Params = new URLSearchParams();
        Params.append("comId", lastCom );
        const Url = new URL(window.location.href);

        fetch(Url.pathname + "?" + Params.toString() + "&loadCom=1", {
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

            if (content.innerText === "pas de commentaires"){
                btn.style.visibility = "hidden";
            }
        });
    });
}