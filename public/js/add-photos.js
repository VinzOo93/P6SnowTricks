
window.addEventListener('load', init)

function init() {
    let form, btnAdd, span;

    form = document.querySelector("#photos");
    span = form.querySelector("span")
    btnAdd = document.createElement("button");
    btnAdd.className = "add-photo btn "
    btnAdd.type = "button"
    btnAdd.innerText = "Ajouter les photos"

    form.appendChild(btnAdd);

    let newBtn = span.append(btnAdd);

    form.dataset.index = form.querySelectorAll("input").length;

    btnAdd.addEventListener("click", function () {
        addButton(form, newBtn);
    });
};

function addButton(form) {
    let prototype = form.dataset.prototype;
    let index = form.dataset.index;
    prototype = prototype.replace(/__name__/g, index)

    let content = document.createElement("html");
    content.innerHTML = prototype;

    let newForm = content.querySelector("div");

    let btnDel = document.createElement("button");
    btnDel.type = "button";
    btnDel.id = "btn-delete-photo btn";
    btnDel.innerText = "Supprimer photo";

    newForm.append(btnDel);

    form.dataset.index++;

    let addBtn = form.querySelector(".add-photo")
    form.querySelector("span").insertBefore(newForm, addBtn);

    btnDel.addEventListener("click", function () {
        this.previousElementSibling.parentElement.remove();
    })
}




