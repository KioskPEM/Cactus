const CheatCode = {
    init: function () {
        CheatCode.commands = [];
        CheatCode.buffer = '';

        document.addEventListener("keydown", CheatCode.handleKeyPress);

        CheatCode.register("pomme", function () {
            let adminPage = document.getElementById("admin-page");
            window.location.href = adminPage.value;
        });
    },
    register: function (command, action) {
        CheatCode.commands[command] = action;
    },
    handleKeyPress(e) {
        if (e.keyCode === 13) {
            let cmd = CheatCode.commands[CheatCode.buffer];
            if (cmd === undefined)
                return;
            cmd();
            CheatCode.buffer = "";
        } else
            CheatCode.commands += e.char;
    }

};