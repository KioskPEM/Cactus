const Barcode = {
    init: function () {
        Barcode.commands = [];
        Barcode.input = document.getElementById("barcode-input");
        Barcode.input.addEventListener("change", Barcode.handleInput);
        Barcode.input.select();

        Barcode.mapping = [];
        Barcode.mapping["&"] = "1";
        Barcode.mapping["é"] = "2";
        Barcode.mapping["\""] = "3";
        Barcode.mapping["'"] = "4";
        Barcode.mapping["("] = "5";
        Barcode.mapping["-"] = "6";
        Barcode.mapping["è"] = "7";
        Barcode.mapping["_"] = "8";
        Barcode.mapping["ç"] = "9";
        Barcode.mapping["à"] = "0";

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
        Barcode.register(/^FORCEUPDT$/, function () {
            window.location.href = UPDATE_PAGE;
        });
        Barcode.register(/^EASTEREGG$/, function () {
            window.location.href = EASTER_EGG_PAGE;
        });
        Barcode.register(/^USER(\d+)$/, function (matches) {
            console.log("Bienvenue utilisateur", matches[1]);
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
        let mapping = Barcode.mapping;
        for (let key in mapping) {
            if (mapping.hasOwnProperty(key))
                input = input.replace(key, mapping[key]);
        }

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