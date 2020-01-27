const Cactus = {
    idleDelay: 300,
    init: function () {

        /*let links = document.getElementsByTagName("A");
        for(let i = 0; i < links.length; i++)
            links[i].addEventListener("click", Cactus.showLoadingPanel);*/

        ["mousedown", "mousemove", "keypress", "scroll", "touchstart"].forEach(name => {
            document.addEventListener(name, Cactus.resetIdleDelay, true);
        });

        CheatCode.init()
    },
    home: function () {
        let welcomeBtn = document.getElementById("app-home-btn");
        if (welcomeBtn !== null)
            welcomeBtn.click();
    },
    showLoadingPanel: function () {
        let loadingPanel = document.getElementById("loading-panel");
        if (loadingPanel !== null)
            loadingPanel.style.display = "flex";
    },
    resetIdleDelay: function () {
        if (Cactus.idleTimeoutId)
            window.clearTimeout(Cactus.idleTimeoutId);
        Cactus.idleTimeoutId = window.setTimeout(Cactus.home, Cactus.idleDelay * 1000);
    }
};