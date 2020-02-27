const Barcode = {
    buffer: "",
    commands: [],
    init: function () {
        document.addEventListener("keydown", Barcode.handleKeyPress);
    },
    register: function (command, action) {
        Barcode.commands.push({
            regex: command,
            action: action
        });
    },
    handleKeyPress(e) {
        if (e.key === "Enter") {
            Barcode.executeCommand(Barcode.buffer);
            Barcode.buffer = "";
        } else if (/^[A-Z0-9\\-]$/.test(e.key))
            Barcode.buffer += e.key;
    },
    executeCommand(input) {
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