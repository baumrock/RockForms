<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RockForms Opt-In</title>
  <style>
    html,
    body {
      height: 100%;
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>

<body>
  <div>
    Please click on the following Link to confirm your signup:
    <a href="./?confirm=1">Click here</a>.
  </div>
  <?php
  if (!$event->input->get->confirm) {
    echo '<script>location.href = "./?confirm=1"</script>';
  }
  ?>
</body>

</html>