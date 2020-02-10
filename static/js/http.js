const HTTP = {
    doGet: function (url, params) {
        HTTP.do("GET", url, params);
    },
    doPost: function (url, params) {
        HTTP.do("POST", url, params);
    },
    do: function (method, url, params) {
        let form = document.createElement("FORM");
        form.method = method;
        form.action = url;

        for (const param in params) {
            if (!params.hasOwnProperty(param))
                continue;

            let field = document.createElement("INPUT");
            field.type = "hidden";
            field.name = param;
            field.value = params[param];

            form.appendChild(field);
        }

        document.body.appendChild(form);
        form.submit();
    }
};