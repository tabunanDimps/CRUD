<?php
// Check for the existence of the student_id parameter before processing further
if(isset($_GET["student_id"]) && !empty(trim($_GET["student_id"]))){
    // Include configuration file
    require_once "config.php";
    
    // Prepare a select statement
    $sql = "SELECT * FROM students WHERE student_id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["student_id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field values
                $student_name = $row["name"];
                $student_id = $row["student_id"];
                $tuition_fee = $row["tuition_fee"];
            } else{
                // URL doesn't contain valid student_id parameter. Redirect to error page
                header("location: error.php");
                exit();
            }
            
        } else{
            echo "An error occurred. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else{
    // URL doesn't contain student_id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student Record</title>
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
                    <h1 class="mt-5 mb-3">View Student Record</h1>
                    <div class="form-group">
                        <label>Student Name</label>
                        <p><b><?php echo $row["name"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Student ID</label>
                        <p><b><?php echo $row["student_id"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Tuition Fee</label>
                        <p><b><?php echo $row["tuition_fee"]; ?></b></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
