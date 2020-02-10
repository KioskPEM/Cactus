const Barcode = {
    init: function () {
        Barcode.commands = [];
        Barcode.input = document.getElementById("barcode-input");
        Barcode.input.addEventListener("change", Barcode.handleInput);
        Barcode.input.select();

        let inputs = document.getElementsByTagName("INPUT");
        for (let i = 0; i < inputs.length; i++) {
            let input = inputs[i];
            input.addEventListener("focus", e => {
                clearTimeout(Barcode.timeout);
                Barcode.selectedInput = e.target;
            });
            input.addEventListener("focusout", e => {
                Barcode.timeout = setTimeout(e => {
                    Barcode.input.select();
                }, 50);
            });
        }

        Barcode.register(/^SENDNUDES$/, function () {
            window.location.href = ADMIN_PAGE;
        });
        Barcode.register(/^FORCEUPDATE$/, function () {
            window.location.href = UPDATE_PAGE;
        });
        Barcode.register(/^EASTEREGG$/, function () {
            window.location.href = EASTER_EGG_PAGE;
        });
        Barcode.register(/^USER-(\d+)$/, function (matches) {
            HTTP.doPost(
                LOGIN_PAGE,
                {
                    id: matches[1]
                }
            );
        });
    },
    register: function (command, action) {
        Barcode.commands.push({
            regex: command,
            action: action
        });
    },
    handleInput(e) {
        let target = e.target;
        let input = target.value;
        target.value = "";

        let commands = Barcode.commands;
        for (let command of commands) {
            let matches = command.regex.exec(input);
            if (matches != null) {
                console.log("Executing command", input);
                command.action(matches);
                return;
            }
        }

        console.error("Unknown command", input);
    }

};