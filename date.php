<!doctype html>
<html lang="en">






<head>
<meta charset="utf-8">
<link rel="stylesheet" href="date/jquery-ui.css">
<script src="date/jquery-1.9.1.js"></script>
<script src="date/jquery-ui.js"></script>
<script>
$(function() {
$( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        altField: ".date_alternate",
        altFormat: "yy-mm-dd"
    });
});
</script>
</head>


<body>



<?php 
if(isset($_POST['dateee']))
	echo "Date : ".$_POST['dateee']."\n";
else echo "No date\n";
?>



<form method="post" action="date.php">
<input type="text" name="dateee" class="datepicker">
<input type="submit" value="envoyer la date">
</form>


</body>
</html>