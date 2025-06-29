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
    console.warn("form is not valid", e.target);

    // this is to help avoid confusion when a form does not submit but
    // also does not show any errors. this can be the case when the browser
    // autofills honeypot fields.
    const errors = e.target.querySelectorAll(".has-error");
    if (errors.length > 0) {
      const visibleErrors = Array.from(errors).filter((error) => {
        return error.offsetParent !== null;
      });
      if (visibleErrors.length === 0) alert("Form is not valid");
    }

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

      // get data-rooturl from <form> element
      const form = e.target.closest("form");
      if (!form) return;
      const rootUrl = form.getAttribute("data-rooturl");
      script.src = rootUrl + "site/modules/RockForms/dst/htmx.min.js";

      // add script tag to head
      document.head.appendChild(script);
    });
  });
})();

// load CSRF token on form interaction
(() => {
  let submitAfterAjax = false;

  const loadCSRF = (form) => {
    form = form.closest("form");
    let input = form.querySelector('input[name="csrf"]');
    if (!input) return;
    if (input.value) return;

    form.classList.add("csrf-loaded");
    const rootUrl = form.getAttribute("data-rooturl");
    fetch(rootUrl + "rockforms-csrf/", {
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
  };

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
      if (form && !form.classList.contains("csrf-loaded")) loadCSRF(form);
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

  // load csrf on domready
  document.addEventListener("DOMContentLoaded", () => {
    const inputs = document.querySelectorAll(
      ".RockForm input[name=csrf].domready"
    );
    inputs.forEach((input) => loadCSRF(input));
  });
})();
