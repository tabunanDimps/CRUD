<?php
// Include configuration file
require_once "config.php";

// Define variables and initialize with empty values
$student_name = $student_id = $tuition_fee = "";
$name_error = $id_error = $fee_error = "";

// Process form submission
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate student name
    $input_student_name = trim($_POST["student_name"]);
    if(empty($input_student_name)){
        $name_error = "Please provide the student's name.";
    } elseif(!preg_match("/^[a-zA-Z\s]+$/", $input_student_name)){
        $name_error = "Only letters and spaces are allowed in the name.";
    } else{
        $student_name = $input_student_name;
    }

    // Validate student ID
    $input_student_id = trim($_POST["student_id"]);
    if(empty($input_student_id)){
        $id_error = "Student ID cannot be empty.";
    } else{
        $student_id = $input_student_id;
    }

    // Validate tuition fee
    $input_tuition_fee = trim($_POST["tuition_fee"]);
    if(empty($input_tuition_fee)){
        $fee_error = "Tuition fee must be specified.";
    } elseif(!ctype_digit($input_tuition_fee)){
        $fee_error = "Tuition fee should be a positive numeric value.";
    } else{
        $tuition_fee = $input_tuition_fee;
    }

    // Check for errors before inserting data into the database
    if(empty($name_error) && empty($id_error) && empty($fee_error)){
        // Prepare SQL query
        $sql = "INSERT INTO students (name, student_id, tuition_fee) VALUES (?, ?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind parameters to the prepared statement
            mysqli_stmt_bind_param($stmt, "sss", $param_name, $param_id, $param_fee);

            // Set parameters
            $param_name = $student_name;
            $param_id = $student_id;
            $param_fee = $tuition_fee;

            // Execute the statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to the homepage upon successful insertion
                header("location: index.php");
                exit();
            } else{
                echo "An error occurred while saving the record. Please try again.";
            }
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    }

    // Close the database connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper {
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
                    <h2 class="mt-5">Add Student Record</h2>
                    <p>Complete the form below to add a new student to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Student Name</label>
                            <input type="text" name="student_name" class="form-control <?php echo (!empty($name_error)) ? 'is-invalid' : ''; ?>" value="<?php echo $student_name; ?>">
                            <span class="invalid-feedback"><?php echo $name_error;?></span>
                        </div>
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id" class="form-control <?php echo (!empty($id_error)) ? 'is-invalid' : ''; ?>" value="<?php echo $student_id; ?>">
                            <span class="invalid-feedback"><?php echo $id_error;?></span>
                        </div>
                        <div class="form-group">
                            <label>Tuition Fee</label>
                            <input type="text" name="tuition_fee" class="form-control <?php echo (!empty($fee_error)) ? 'is-invalid' : ''; ?>" value="<?php echo $tuition_fee; ?>">
                            <span class="invalid-feedback"><?php echo $fee_error;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
