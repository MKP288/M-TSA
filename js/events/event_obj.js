class Event{
    constructor(eventInfo){
        this.eventInfo = eventInfo;
    }

    addToDOM(dom){
        this.eventBlock = dom.createElement("div");
        this.header = dom.createElement("h3");
        this.location = dom.createElement("p");
        this.disc = dom.createElement("p");
        this.link = dom.createElement("a");
        this.header.innerHTML = this.eventInfo.Title;
        this.location.innerHTML = this.eventInfo.Location;
        this.disc.innerHTML = this.eventInfo.Discription;
        this.link.innerHTML = "Sign Up";
        this.link.href = "eventSignup.php";
        this.eventBlock.classList.add("eventBlock");
        this.eventBlock.appendChild(this.header);
        this.eventBlock.appendChild(this.location);
        this.eventBlock.appendChild(this.disc);
        this.eventBlock.appendChild(this.link);
    }
}