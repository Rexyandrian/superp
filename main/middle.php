<?php
	$parvaz_det_id = ((isset($_REQUEST['parvaz_det_id']))?$_REQUEST["parvaz_det_id"]:-1);
?>
<html>
	<head>
	</head>
	<body>
		<script language="javascript">
			document.body.width = "25cm";
			window.location = "manifest2.php?parvaz_det_id=<?php echo $parvaz_det_id; ?>&";
		</script>
	</body>
</html>
