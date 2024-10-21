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
  if (!Nette.validateForm(e.target)) {
    e.preventDefault();
    return;
  }

  // show rockloader while submitting
  const loader = e.target.getAttribute("rockloader");
  document.body.setAttribute("rockloader", loader);
});
// hide rockloader
document.addEventListener("htmx:afterRequest", (e) => {
  document.body.removeAttribute("rockloader");
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
  let submitAfterAjax = false;

  // add class submitting to the form on submit
  document.addEventListener("submit", (e) => {
    const form = e.target;
    if (form.tagName !== "FORM") return;
    if (!form.classList.contains("RockForm")) return;
    form.classList.add("submitting");

    // show rockloader while submitting
    const loader = form.getAttribute("rockloader");
    document.body.setAttribute("rockloader", loader);
  });

  // delay form submit if CSRF is loaded via ajax
  document.addEventListener("submit", (e) => {
    const form = e.target;
    if (form.tagName !== "FORM") return;
    if (!form.classList.contains("RockForm")) return;

    // if csrf field is empty that means it is still loading
    const csrf = form.querySelector("[name=csrf]");
    if (!csrf) return;

    // if csrf is already loaded we don't prevent the submit
    if (csrf.value) return;

    // set flag to submit the form after ajax fetch
    submitAfterAjax = true;
    e.preventDefault();
  });

  // monitor input and focus events to load csrf
  "input,focusin".split(",").forEach((event) => {
    document.addEventListener(event, (e) => {
      let form = e.target.closest("form");
      if (!form) return;

      let input = form.querySelector('input[name="csrf"]');
      if (!input) return;
      if (input.value) return;

      // we only load a token once for every form
      if (form.classList.contains("csrf-loaded")) return;
      form.classList.add("csrf-loaded");

      // reset the value and load a new token
      fetch("/rockforms-csrf/", {
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      })
        .then((response) => response.text())
        .then((tokenValue) => {
          input.value = tokenValue;
          if (submitAfterAjax) form.submit();
        })
        .catch((error) => {
          alert("Error fetching CSRF token");
          console.error(error);
          input.value = "";
        });
    });
  });

  // reset all csrf inputfields on page load
  let resetCSRF = function () {
    document.querySelectorAll(".RockForm input[name=csrf]").forEach((input) => {
      if (input.hasAttribute("no-reset")) return;
      input.value = "";
      input.closest(".RockForm").classList.remove("csrf-loaded");
    });
  };
  document.addEventListener("DOMContentLoaded", resetCSRF);
  document.addEventListener("htmx:afterSwap", resetCSRF);

  // error handling
  // this shows the default error if no custom alert is setup
  document.addEventListener("htmx:beforeSwap", function (e) {
    setTimeout(() => {
      if (document.body.classList.contains("rf-custom-alert")) return;
      if (document.querySelector("#" + e.target.id)) return;
      alert("Something went wrong - please contact support (HTMX swap failed)");
    }, 1000);
  });
  // this shows an error with helpful instructions in the console
  document.addEventListener("htmx:beforeSwap", function (e) {
    setTimeout(() => {
      if (document.querySelector("#" + e.target.id)) return;
      console.error(
        "Error on HTMX swap. Inspect returned markup in network tab!"
      );
    }, 400);
  });
})();
