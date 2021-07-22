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

  const checkboxes = [...itemTypeCheckboxes, ...movieGenresCheckboxes, ...tvGenresCheckboxes];

  const shortcode = document.querySelector("#shortcode");

  const properties = {
    name: "esgi-tmdb",
    movieIDs: [],
    tvIDs: [],
  };

  // FUNCTIONS EXPRESSIONS

  // Add or remove the "disable" key word inside the input tag
  const checkboxState = (id) => {
    if (id === "movie") {
      movieGenresCheckboxes.forEach((input) => {
        input.toggleAttribute("disabled");
        if (input.disabled) input.checked = false;
      });
    } else if (id === "tv") {
      tvGenresCheckboxes.forEach((input) => {
        input.toggleAttribute("disabled");
        if (input.disabled) input.checked = false;
      });
    }
  };

  // Update the shortcode inside the html according to the actual state
  const updateShortcode = () => {
    // lazy code, too lazy to check the existing value inside properties
    properties.movieIDs = [];
    properties.tvIDs = [];

    let movieState;
    let tvState;

    // Update the state, fill array with IDs if necessary
    itemTypeCheckboxes.forEach((input) => {
      if (input.id === "movie") {
        movieState = input.checked ? '"all"' : '"none"';
        if (input.checked) {
          movieGenresCheckboxes.forEach((movieGenreInput) => {
            if (movieGenreInput.checked) {
              properties.movieIDs.push(+movieGenreInput.id);
            }
          });
        }
      } else if (input.id === "tv") {
        tvState = input.checked ? '"all"' : '"none"';
        if (input.checked) {
          tvGenresCheckboxes.forEach((tvGenreInput) => {
            if (tvGenreInput.checked) {
              properties.tvIDs.push(+tvGenreInput.id);
            }
          });
        }
      }
    });

    // Replace "all" with IDs if lenght > O for movie and tv
    if (properties.movieIDs.length > 0) {
      movieState = `"${properties.movieIDs.toString()}"`;
    }
    if (properties.tvIDs.length > 0) {
      tvState = `"${properties.tvIDs.toString()}"`;
    }

    const shortcode = `[${properties.name} movie=${movieState} tv=${tvState}]`;
    window.shortcode.innerHTML = shortcode;
  };

  // MAIN

  // Loop throught all the checkboxes, and if clicked -> update the state and the shortcode
  checkboxes.forEach((input) => {
    input.addEventListener("click", (e) => {
      checkboxState(e.target.id);
      updateShortcode();
    });
  });

  updateShortcode();
});
