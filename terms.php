<?php include('header.php'); ?>

	<h1>Terms and Conditions</h1>
	<hr class='short-fat'/>

<?php
$name = 'terms';
$stmt2 = $db->prepare("SELECT * FROM pages WHERE name = ?");
$stmt2->bindParam(1, $name);
$stmt2->execute();
$blackboard_data = $stmt2->fetch(PDO::FETCH_ASSOC);
echo $blackboard_data['content'];
?>

<?php include('footer.php'); ?>