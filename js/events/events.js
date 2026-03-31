window.addEventListener("load",function(){
    let eventsArr = [];
    fetch(eventInfo.php).then(response=>response.text()).then(data=>{
        eventsArr = data;
    });

    for(let i=0;i<eventsArr.length();i++){
        let enterie = new Event(eventsArr[i])
        enterie.addToDOM(document);
    }



})