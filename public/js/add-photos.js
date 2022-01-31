
window.addEventListener("load", init);

function init() {
    let form, btnAdd, span, inputs, imgs, placements;

    form = document.querySelector("#photos");
    span = form.querySelector("span");
    btnAdd = document.createElement("button");
    btnAdd.className = "add-photo btn";
    btnAdd.type = "button";
    btnAdd.className = "btn-add";
    btnAdd.innerText = "+";
    inputs = form.querySelectorAll("input");
    imgs = form.querySelectorAll(".photo-button");
    placements = form.querySelectorAll(".placement");


    if (inputs != null) {
        let number = 0;
        inputs.forEach( function (input){
        input.id = "trick_photos_"+ number +"_file";
        input.name = "trick[photos]["+ number +"][file]";
        number ++;
        if (input.value != null){
            input.required = false;
        }
        });
    }

    if (imgs != null){
        for (let i=0; i<inputs.length; i++){
            placements[i].appendChild(imgs[i]);
        }
    }
    form.append(btnAdd);


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
    newForm.className = "new-item"

    let btnDel = document.createElement("button");
    btnDel.type = "button";
    btnDel.id = "btn-delete-photo btn";
    btnDel.className = "btn-del";
    btnDel.innerText = "X";

    newForm.append(btnDel);

    form.dataset.index++;

    let addBtn = form.querySelector(".add-photo");
    form.querySelector("span").insertBefore(newForm, addBtn);

    btnDel.addEventListener("click", function () {
        this.previousElementSibling.parentElement.remove();
    });
}




