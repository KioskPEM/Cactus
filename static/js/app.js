const Cactus = {
    idleDelay: 60,
    init: function () {
        /*["mousedown", "mousemove", "keypress", "scroll", "touchstart"].forEach(name => {
            document.addEventListener(name, Cactus.resetIdleDelay, true);
        });*/

        CheatCode.init()
    },
    goToWelcome: function () {
        let welcomeBtn = document.getElementById("welcome-btn");
        if (welcomeBtn !== null)
            welcomeBtn.click();
    },
    resetIdleDelay: function () {
        if (Cactus.idleTimeoutId)
            window.clearTimeout(Cactus.idleTimeoutId);
        Cactus.idleTimeoutId = window.setTimeout(Cactus.goToWelcome, Cactus.idleDelay * 1000);
    }
};