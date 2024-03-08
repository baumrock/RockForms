/**
 * This file is for public users on the frontend
 */

// don't send htmx request if form is not valid
document.addEventListener("htmx:beforeRequest", (e) => {
  if (typeof Nette == "undefined") return;
  if (e.target.tagName !== "FORM") return;
  if (!Nette.validateForm(e.target)) e.preventDefault();
});

// load htmx on form interaction
(() => {
  let htmxLoaded = false;
  "input,focusin".split(",").forEach((event) => {
    document.addEventListener(event, (e) => {
      if (htmxLoaded) return;
      htmxLoaded = true;
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
