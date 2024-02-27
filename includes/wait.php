<script>
  (() => {
    setInterval(() => {
      let el = document.getElementById('<?= $id ?>');
      el.value = el.value * 1 + 1;
    }, 1000);
  })()
</script>