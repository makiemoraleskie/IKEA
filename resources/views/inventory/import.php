<?php
$baseUrl = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inventory Import</title>
	<script>
		(function(){
			const target = '<?php echo htmlspecialchars($baseUrl); ?>/inventory?openImport=1';
			window.location.replace(target);
		})();
	</script>
</head>
<body>
	<p>Redirecting to inventory import...</p>
</body>
</html>

