<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $student_id = $tuition_fee = "";
$name_err = $student_id_err = $tuition_fee_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["student_id"]) && !empty($_POST["student_id"])){
    // Get hidden input value
    $id = $_POST["student_id"];
    
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate student ID
    $input_student_id = trim($_POST["student_id"]);
    if(empty($input_student_id)){
        $student_id_err = "Please enter the student ID.";     
    } elseif(!ctype_digit($input_student_id)){
        $student_id_err = "Please enter a valid integer value.";
    } else{
        $student_id = $input_student_id;
    }
    
    // Validate tuition fee
    $input_tuition_fee = trim($_POST["tuition_fee"]);
    if(empty($input_tuition_fee)){
        $tuition_fee_err = "Please enter the tuition fee amount.";     
    } elseif(!ctype_digit($input_tuition_fee)){
        $tuition_fee_err = "Please enter a positive integer value.";
    } else{
        $tuition_fee = $input_tuition_fee;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($student_id_err) && empty($tuition_fee_err)){
        // Prepare an update statement
        $sql = "UPDATE students SET name=?, student_id=?, tuition_fee=? WHERE student_id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "siii", $param_name, $param_student_id, $param_tuition_fee, $param_id);
            
            // Set parameters
            $param_name = $name;
            $param_student_id = $student_id;
            $param_tuition_fee = $tuition_fee;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of student_id parameter before processing further
    if(isset($_GET["student_id"]) && !empty(trim($_GET["student_id"]))){
        // Get URL parameter
        $id =  trim($_GET["student_id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM students WHERE student_id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $name = $row["name"];
                    $student_id = $row["student_id"];
                    $tuition_fee = $row["tuition_fee"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain student_id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Student Record</h2>
                    <p>Please edit the input values and submit to update the student record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id" class="form-control <?php echo (!empty($student_id_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $student_id; ?>">
                            <span class="invalid-feedback"><?php echo $student_id_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Tuition Fee</label>
                            <input type="text" name="tuition_fee" class="form-control <?php echo (!empty($tuition_fee_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tuition_fee; ?>">
                            <span class="invalid-feedback"><?php echo $tuition_fee_err;?></span>
                        </div>
                        <input type="hidden" name="student_id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
