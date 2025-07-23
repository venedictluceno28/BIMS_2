<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="0;url=pages/index.php">
<title>Dashboard</title>
<script language="javascript">
    window.location.href = "pages/index.php"
</script>
</head>
<body>
Go to <a href="pages/index.php">/pages/index.html</a>
</body>
</html>


<script>
setInterval(function () {
  // DARK MODE TOGGLE
  var darkEnabled = localStorage.getItem('darkmode') === 'true';
  document.body.classList.toggle('dark-mode', darkEnabled);
  document.documentElement.classList.toggle('dark-mode', darkEnabled);
  console.log('Dark mode enabled:', darkEnabled);

  // FONT SIZE TOGGLE
  var fontSize = localStorage.getItem('fontsize'); // expected: small, medium, large

  // Remove all font size classes first
  document.body.classList.remove('font-small', 'font-medium', 'font-large');
  document.documentElement.classList.remove('font-small', 'font-medium', 'font-large');

  // Apply current font size class
  if (['small', 'medium', 'large'].includes(fontSize)) {
    document.body.classList.add('font-' + fontSize);
    document.documentElement.classList.add('font-' + fontSize);
  }
  console.log('Font size:', fontSize);
}, 500);
</script>
