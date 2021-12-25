window.addEventListener('load', init)

function init() {
    let form, btnAdd, span;

    form = document.querySelector("#videos");
    span = form.querySelector("span")
    btnAdd = document.createElement("button");
    btnAdd.className = "add-video btn "
    btnAdd.type = "button"
    btnAdd.innerText = "Ajouter une vidéo"

    form.appendChild(btnAdd);

    let newBtn = span.append(btnAdd);

    form.dataset.index = form.querySelectorAll("input").length;

    btnAdd.addEventListener("click", function () {
        addButton(form, newBtn);
    });
}



function addButton(form) {
    let prototype = form.dataset.prototype;
    let index = form.dataset.index;
    prototype = prototype.replace(/__name__/g, index)

    let content = document.createElement("html");
    content.innerHTML = prototype;

    let newForm = content.querySelector("div");

    let btnDel = document.createElement("button");
    btnDel.type = "button";
    btnDel.id = "btn-delete-video btn";
    btnDel.innerText = "Supprimer video";

    newForm.append(btnDel);

    form.dataset.index++;

    let addBtn = form.querySelector(".add-video")
    form.querySelector("span").insertBefore(newForm, addBtn);

    btnDel.addEventListener("click", function () {
        this.previousElementSibling.parentElement.remove();
    })
}




