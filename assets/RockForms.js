/**
 * This file is for public users on the frontend
 *
 * When using RockFrontend it will automatically be loaded.
 * Otherwise you need to add this line in your <head> section
 * <?= $rockforms->scriptTag(); ?>
 */

// this is for the CSRF missing JS warning
var RockForms = true;

// don't send htmx request if form is not valid
document.addEventListener("htmx:beforeRequest", (e) => {
  if (typeof Nette == "undefined") return;
  if (e.target.tagName !== "FORM") return;
  if (!Nette.validateForm(e.target)) e.preventDefault();
});

// load htmx on form interaction
(() => {
  let htmxAdded = false;
  "input,focusin".split(",").forEach((event) => {
    document.addEventListener(event, (e) => {
      if (htmxAdded) return;
      htmxAdded = true;
      if (typeof htmx !== "undefined") return;

      // create script tag
      let script = document.createElement("script");

      // if pw is installed in a subfolder you need to add this to your frontend:
      // <script>htmxAssetUrl = "/subfolder/site/modules/RockForms...</script>"
      if (typeof htmxAssetUrl == "undefined") {
        script.src = "/site/modules/RockForms/lib/htmx.min.js";
      } else script.src = htmxAssetUrl;

      // add script tag to head
      document.head.appendChild(script);
    });
  });
})();

// load CSRF token on form interaction
(() => {
  let loaded = false;
  "input,focusin".split(",").forEach((event) => {
    document.addEventListener(event, (e) => {
      let form = e.target.closest("form");
      if (!form) return;
      let input = form.querySelector('input[name="csrf"]');
      if (!input) return;

      // we only load a token once
      if (loaded) return;
      loaded = true;

      // reset the value and load a new token
      input.value = "loading";
      fetch("/rockforms-csrf/", {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.text())
        .then((tokenValue) => {
          input.value = tokenValue;
        })
        .catch((error) => {
          alert("Error fetching CSRF token");
          console.error(error);
          input.value = "";
        });
    });
  });

  // reset all csrf inputfields on page load
  document.addEventListener("DOMContentLoaded", () => {
    document
      .querySelectorAll(".RockForm input[name=csrf]")
      .forEach((input) => (input.value = ""));
  });
})();
