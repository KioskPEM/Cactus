const CheatCode = {
    init: function () {
        CheatCode.commands = [];
        CheatCode.buffer = '';

        document.addEventListener("keydown", CheatCode.handleKeyPress);

        CheatCode.register("SENDNUDES", function () {
            window.location.href = ADMIN_PAGE;
        });
        CheatCode.register("FORCEUPDT", function () {
            window.location.href = UPDATE_PAGE;
        });
        CheatCode.register("EASTEREGG", function () {
            window.location.href = EASTER_EGG_PAGE;
        });
    },
    register: function (command, action) {
        CheatCode.commands[command] = action;
    },
    handleKeyPress(e) {
        if (e.key === "Enter") {
            let cmd = CheatCode.commands[CheatCode.buffer];
            if (cmd === undefined) {
                console.error("Unknown command", CheatCode.buffer);
                CheatCode.buffer = "";
                return;
            }
            console.log("Executing command", CheatCode.buffer);
            CheatCode.buffer = "";
            cmd();
        } else if (/^[A-Z0-9]$/.test(e.key))
            CheatCode.buffer += e.key;
    }

};