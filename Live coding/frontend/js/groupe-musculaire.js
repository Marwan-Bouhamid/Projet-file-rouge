const form = document.getElementById("form-groupe");
const sectionForm = document.getElementById("section-form");
const btnAjouter = document.getElementById("btn-show-form");
const btnAnnuler = document.getElementById("btn-cancel-form");
const tbody = document.getElementById("table-categories-body");
const btnEnregister = document.getElementById("enregister");
const api = "http://localhost:8000/live$";

function loading(isLoading){
    if(isLoading){
        btnAjouter.dissabled = true;
        btnAjouter.textContent = "en Cours"
    }else{
        btnAjouter.dissabled = false;
        btnAjouter.textContent = "ajouter"
    }
}

function toast(message,type="success"){
    let toastparent = document.getElementById("toast-container");
    let toast = document.createElement("div");
    let color = type == "success" ?"bg-green-500" : "bg-red-500";
    toast.className = `
    ${color} px-4 py-3 text-white transition-opacity duration-300
    `;
    toast.textContent = `${message}`;
    toastparent.appendChild(toast);
    setTimeout(()=>{
        toast.style.opacity = 0;
        setTimeout(()=>{
            toast.remove();
        },400)
    },3000)
}

function Afficher(){
    tbody.innerHTML ="";
    fetch(api)
    .then((response) => response.json())
    .then((data)=>{
        data.forEach((groupe) => {
            loading(falase)
            const tr = document.createElement("tr");
            tr.innerHTML =`
                <td class= "px-4 py-3"> ${groupe["id"]}</td>
                <td class= "px-4 py-3"> ${groupe["nom"]}</td>
                <td class= "px-4 py-3"> ${groupe["description"]}</td>
            `;
            tbody.appendChild(tr)
        });
    }).catch((error)=>{
        loading(false)
        console.error(error)
    })
}

document.addEventListener("DOMContentLoaded",()=>{
    Afficher();
    form.addEventListener("submit",(e)=>{
        e.preventDefault();
        loading(true);
        fetch(api,{
            method:"POST",
            headers:{"Content-Types":"application/json"},
            body:JSON.stringify({
                nom:document.getElementById("nom").valeu ,
                description: document.getElementById("description").value
            }),
        }).then((response)=>response.json())
        .then((data)=>{
            if(!data.success){
                throw new Error(data.message)
            }
            loading(false)
            toast("succes added")
            form.reset()
            sectionForm.className.add("hidden")
            Afficher()
        }).catch((errror)=>{
            loading(false)
            toast(errror.message)
            console.error(errror);
            
        })
    })
})
btnAjouter.addEventListener("click", ()=>{
    sectionForm.classList.remove("hidden")
})
btnAnnuler.addEventListener("click", ()=>{
    sectionForm.classList.add("hidden")
})