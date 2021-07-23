function docReady(fn) {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

docReady(function () {
    // VARIABLES
    const itemTypeCheckboxes = document.querySelectorAll(".itemType");
    const movieGenresCheckboxes = document.querySelectorAll(".movieGenres");
    const tvGenresCheckboxes = document.querySelectorAll(".tvGenres");

    // Add or remove the "disable" key word inside the input tag
    const checkboxState = (id) => {
        if (id === "movieChecked") {
            movieGenresCheckboxes.forEach((input) => {
                input.toggleAttribute("disabled");
                if (input.disabled) input.checked = false;
            });
        } else if (id === "tvChecked") {
            tvGenresCheckboxes.forEach((input) => {
                input.toggleAttribute("disabled");
                if (input.disabled) input.checked = false;
            });
        }
    };

    // Listener on media type: if clicked, enable genres
    itemTypeCheckboxes.forEach((input) => {
        input.addEventListener("click", (e) => {
            const result = e.target.id.split('-');
            const id = result[result.length - 1];
            checkboxState(id);
        });
    });

});
