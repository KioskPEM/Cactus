const Cactus = {
    idleDelay: 300,
    init: function () {

        let links = document.getElementsByTagName("A");
        for (let i = 0; i < links.length; i++) {
            let link = links[i];
            if (link.href)
                link.addEventListener("click", Cactus.showLoadingPanel);
        }

        ["mousedown", "mousemove", "keypress", "scroll", "touchstart"].forEach(name => {
            document.addEventListener(name, Cactus.resetIdleDelay, true);
        });

        Barcode.init();
        Barcode.register(/^SENDNUDES$/, function () {
            window.location.href = ADMIN_PAGE;
        });
        Barcode.register(/^4MXM6W70$/, function () {
            window.location.href = ADMIN_PAGE;
        });
        Barcode.register(/^FORCEUPDATE$/, function () {
            window.location.href = UPDATE_PAGE;
        });
        Barcode.register(/^EASTEREGG$/, function () {
            window.location.href = EASTER_EGG_PAGE;
        });
    },
    home: function () {
        window.location.href = HOME_PAGE;
    },
    back: function () {
        let parentPage = document.getElementById("parent-page");
        if (parentPage != null)
            window.location.href = parentPage.value;
        else
            this.home();
    },
    showLoadingPanel: function () {
        let loadingPanel = document.getElementById("app-loader-container");
        if (loadingPanel !== null)
            loadingPanel.style.display = "block";
    },
    resetIdleDelay: function () {
        if (Cactus.idleTimeoutId)
            window.clearTimeout(Cactus.idleTimeoutId);
        Cactus.idleTimeoutId = window.setTimeout(Cactus.home, Cactus.idleDelay * 1000);
    }
};