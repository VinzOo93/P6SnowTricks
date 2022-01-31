window.addEventListener("load", init);

function init() {
    let links = document.querySelectorAll("[data-delete-video]");
    for (let link of links) {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            let eArray = e.composedPath();

            if (confirm("Voulez-vous supprimer cette video ?")) {
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
                        document.getElementById(eArray[2].id).remove();
                    } else {
                        location.reload();
                        alert(data.error());
                    }
                });
            }
        });

    }
}