const CHEAT_CODES = [
    'p', 'o', 'm', 'm', 'e'
];

let cursor = 0;
document.addEventListener("keydown", e => {
    if (e.Key === CHEAT_CODES[cursor]) {
        cursor++;
        if (cursor === CHEAT_CODES.length) {

            let adminPage = document.getElementById("admin-page");
            window.location.href = adminPage.value;

            cursor = 0;
        }
    } else
        cursor = 0;
})