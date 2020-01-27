const CheatCode = {
    init: function () {
        CheatCode.commands = [];
        CheatCode.buffer = '';

        document.addEventListener("keydown", CheatCode.handleKeyPress);

        CheatCode.register("SENDNUDES", function () {
            window.location.href = document.getElementById("admin-page").value;
        });
        CheatCode.register("FORCEUPDT", function () {
            window.location.href = document.getElementById("update-page").value;
        });
        CheatCode.register("EASTEREGG", function () {
            window.location.href = document.getElementById("easter-egg-page").value;
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