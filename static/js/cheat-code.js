const CheatCode = {
    init: function () {
        CheatCode.commands = [];
        CheatCode.buffer = '';

        document.addEventListener("keydown", CheatCode.handleKeyPress);

        CheatCode.register("PASSWORD", function () {
            let adminPage = document.getElementById("admin-page");
            window.location.href = adminPage.value;
        });
    },
    register: function (command, action) {
        CheatCode.commands[command] = action;
    },
    handleKeyPress(e) {
        if (e.repeat || e.altKey || e.ctrlKey || e.metaKey || e.shiftKey)
            return;

        if (e.key === "Enter") {
            let cmd = CheatCode.commands[CheatCode.buffer];
            if (cmd === undefined) {
                console.error("Unknown command", CheatCode.buffer);
                return;
            }
            console.log("Executing command", CheatCode.buffer);
            CheatCode.buffer = "";
            cmd();
        } else
            CheatCode.buffer += e.key;
    }

};