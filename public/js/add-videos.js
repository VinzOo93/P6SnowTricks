window.addEventListener("load", init);

function init() {
    let form, btnAdd, span, inputs, links;

    form = document.querySelector("#videos");
    span = form.querySelector("span");
    btnAdd = document.createElement("button");
    btnAdd.className = "add-video btn";
    btnAdd.type = "button";
    btnAdd.className = "btn-add";
    btnAdd.innerText = "+";
    inputs = form.querySelectorAll("input");
    links = form.querySelectorAll("a");
    form.appendChild(btnAdd);

    if (inputs != null){
        for (let i=0;i<inputs.length; i++ ){
            inputs[i].parentElement.appendChild(links[i]);
        }
    }

    let newBtn = span.append(btnAdd);

    form.dataset.index = form.querySelectorAll("input").length;

    btnAdd.addEventListener("click", function () {
        addButton(form, newBtn);
    });
}

function addButton(form) {
    let prototype = form.dataset.prototype;
    let index = form.dataset.index;
    prototype = prototype.replace(/__name__/g, index);
    let content = document.createElement("html");
    content.innerHTML = prototype;

    let newForm = content.querySelector("div");

    let btnDel = document.createElement("button");
    btnDel.type = "button";
    btnDel.id = "btn-delete-video btn";
    btnDel.className = "btn-del";
    btnDel.innerText = "X";

    newForm.append(btnDel);
    newForm.className = "new-item"
    form.dataset.index++;

    let addBtn = form.querySelector(".add-video")
    form.querySelector("span").insertBefore(newForm, addBtn);

    btnDel.addEventListener("click", function () {
        this.previousElementSibling.parentElement.remove();
    })
}




