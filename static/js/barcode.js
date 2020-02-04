const Barcode = {
    init: function () {
        Barcode.commands = [];
        Barcode.buffer = '';

        document.addEventListener("keydown", Barcode.handleKeyPress);

        Barcode.register("SENDNUDES", function () {
            window.location.href = ADMIN_PAGE;
        });
        Barcode.register("FORCEUPDT", function () {
            window.location.href = UPDATE_PAGE;
        });
        Barcode.register("EASTEREGG", function () {
            window.location.href = EASTER_EGG_PAGE;
        });
    },
    register: function (command, action) {
        Barcode.commands[command] = action;
    },
    handleKeyPress(e) {
        if (e.key === "Enter") {
            let cmd = Barcode.commands[Barcode.buffer];
            if (cmd === undefined) {
                console.error("Unknown command", Barcode.buffer);
                Barcode.buffer = "";
                return;
            }
            console.log("Executing command", Barcode.buffer);
            Barcode.buffer = "";
            cmd();
        } else if (/^[A-Z0-9]$/.test(e.key))
            Barcode.buffer += e.key;
    }

};