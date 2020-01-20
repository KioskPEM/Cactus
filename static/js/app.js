const Cactus = {
    idleDelay: 300,
    init: function () {
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
    resetIdleDelay: function () {
        if (Cactus.idleTimeoutId)
            window.clearTimeout(Cactus.idleTimeoutId);
        Cactus.idleTimeoutId = window.setTimeout(Cactus.home, Cactus.idleDelay * 1000);
    }
};