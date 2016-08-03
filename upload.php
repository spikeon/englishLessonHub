<?php
	$uuid 				= uniqid();
	$File_Name          = strtolower($_FILES['upload']['name']);
	$File_Ext           = substr($File_Name, strrpos($File_Name, '.'));


	if(move_uploaded_file($_FILES['upload']['tmp_name'], 'uploads/'.$uuid.$File_Ext )) {
		if($_GET['type'] == "Images"){
			echo json_encode(['uploaded' => 1, 'fileName' => $uuid.$File_Ext, 'url' => 'uploads/'.$uuid.$File_Ext ]);
		}
		else echo "Stored in: uploads/" . $uuid . $File_Ext;
	}
