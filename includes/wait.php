<script>
  document.addEventListener('DOMContentLoaded', (event) => {
    let el = document.getElementById('<?= $id ?>');
    setInterval(() => {
      el.value = el.value * 1 + 1;
    }, 1000);
    let form = el.closest('form');
    form.addEventListener('submit', (event) => {
      let hidden = el.closest("div[hidden]");
      if (hidden) hidden.removeAttribute("hidden");
    });
  });
</script>