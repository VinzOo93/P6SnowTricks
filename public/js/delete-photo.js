window.addEventListener("load", init);

function init() {
    let links = document.querySelectorAll("[data-delete-photo]");

    for (let link of links) {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            let eArray = e.composedPath();

            if (confirm("Voulez-vous supprimer cette photo ?")) {
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(response => response.json()
                ).then(data => {
                    if (data.success) {
                        document.getElementById(eArray[4].id).remove();
                    } else {
                        location.reload();
                        alert(data.error());
                    }
                });
            }
        });
    }
}