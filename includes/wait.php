<script>
  const parent = document.currentScript.parentElement;
  document.addEventListener('DOMContentLoaded', (event) => {
    const el = parent.querySelector('input[name="timeonpage"]');
    setInterval(() => {
      el.value = el.value * 1 + 1;
    }, 1000);
    const form = el.closest('form');
    form.addEventListener('submit', (event) => {
      // wait for next tick so that live validation has time to run
      setTimeout(() => {
        // get text content of .field element
        const field = parent.querySelector('.field');
        const error = field.textContent.trim();
        // remove hidden attribute from parent if it contains an error
        // otherwise we don't remove it to not mess up with flexboxed forms
        if (error) parent.removeAttribute('hidden');
      }, 0);
    });
  });
</script>