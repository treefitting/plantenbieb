<?php 
include_once("../template/header.php"); 
require_once "../includes/config.php";
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] != true){
    header("location: /users/login.php");
    exit;
}



$title = $kind = $category = $description = $amount = $picture = $error = $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$title = $_POST['title']; 
	if(isset($_POST["kind"])) $kind = $_POST['kind']; 
	if(isset($_POST["category"])) $category = $_POST['category']; 
	$description = $_POST['description']; 
	$amount = $_POST['amount']; 
	$user = $_SESSION["id"];
	
	// Image upload
	if($_FILES['fileToUpload']['name'] == ""){
	}else{		
	$picture = $randomname = basename(generateRandomString().$_FILES["fileToUpload"]["name"]);
		
		$target_dir = "../uploads/";
		$target_file = $target_dir . $randomname ;
		$uploadOk = 1;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				//echo "File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
			} else {
				//echo "File is not an image.";
				$uploadOk = 0;
			}
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			//echo "Sorry, your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			} else {
				//echo "Sorry, there was an error uploading your file.";
			}
		}
	}
	
	if($title==""||$kind==""||$category==""||$description==""||$amount==""){
		$error = "U heeft zo te zien nog niet alle velden ingevuld/geselecteerd.";
	}else{
		$sql = "INSERT INTO offers (offer_title, offer_kind, offer_category, offer_description,offer_amount,offer_picture, offer_user)
		VALUES (' $title', '$kind', '$category','$description','$amount','$picture','$user')";
		
		if ($link->query($sql) === TRUE) {
			$success = "Gefeliciteerd! Uw aanbod is met success toegevoegd!";
			$title = $kind = $category = $description = $amount = $picture = $error = "";
		} else {
			echo "Error: " . $sql . "<br>" . $link->error;
		}
	}
	
}





?> 


<h1 class="display-4">Nieuwe aanbod!</h1>

<?php if($error!=""){ ?>
			<div class="alert alert-warning"><?php echo $error; ?></div>
<?php } ?>
<?php if($success!=""){ ?>
			<div class="alert alert-success"><?php echo $success; ?></div>
<?php } ?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"  enctype="multipart/form-data">
				<div class="form-group">
					 <label>Plantnaam</label>
					 <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
				  </div>
				<div class="row">
				  <div class="col">
				<div class="form-group">
					 <label>Soort</label><br>
					<select class="js-example-basic-single form-control" name="kind">
						<option value="" selected disabled hidden value="">Maak een keuze</option>
						<option <?php if (isset($kind) && $kind=="Stek") echo "selected";?> value="Stek">Stek</option>
						<option <?php if (isset($kind) && $kind=="Zaad") echo "selected";?> value="Zaad">Zaad</option>
						<option <?php if (isset($kind) && $kind=="Plant") echo "selected";?> value="Plant">Plant</option>
					</select> 
				  </div>
				  
				<div class="form-group">
					 <label>Omschrijving van uw aanbod (max 3000 karakters)</label><br>
						<textarea style="height:300px" class="form-control" name="description" maxlength="3000"><?php echo $description; ?></textarea>
				  </div>
				  </div>
				  <div class="col">
				<div class="form-group">
					 <label>Categorie</label><br>
					<select class="js-example-basic-single form-control" name="category">
						<option value="" selected disabled hidden>Maak een keuze</option>
					
					  
					  <?php
					  $sql = "SELECT * FROM categories WHERE cat_parent = 0 AND cat_visible = 1";$result = $link->query($sql);
						if ($result->num_rows > 0) {
							while($row = $result->fetch_assoc()) {			
								echo "<optgroup label='". $row['cat_name'] . "'>";
								$sql2 = "SELECT * FROM categories WHERE cat_parent = " . $row["cat_id"]; $result2 = $link->query($sql2);
								if ($result->num_rows > 0) {
									while($row2 = $result2->fetch_assoc()) {
									echo "<option " . (($row2["cat_id"]==$category)?'selected':"") ." value=" . $row2["cat_id"] . ">" . $row2["cat_name"] . "</option>";
								}}
								echo "</optgroup>";						
							}					
						} else {
							echo "0 results";
						}
					  ?>
					  
					  
					</select>					 
				  </div>
				  <div class="form-group">
					 <label>Beschikbare hoeveelheid</label>
					 <input type="text" name="amount" class="form-control" value="<?php echo $amount; ?>">
				  </div>
				  
				  
				  
				  <div class="form-group">
					 <label>Foto</label>
					 <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
					 <br><img id="image_upload_preview" src="http://placehold.it/200x200" alt="your image"  width="200px"/>
				  </div>
				  
				  
				  </div>
				  </div>
				 
				  <div class="form-group">
					 <input type="submit" name="submit" class="btn btn-primary" value="Publiceren">
				  </div>
</form>


<?php include_once("../template/footer.php"); ?>

<script>
function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function (e) {
			$('#image_upload_preview').attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	}
}

$("#fileToUpload").change(function () {
	readURL(this);
});

</script>
 