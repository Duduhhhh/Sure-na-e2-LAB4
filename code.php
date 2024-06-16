<?php

require 'dbcon.php';

if(isset($_POST['save_student']) && isset($_FILES['image'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $course = mysqli_real_escape_string($con, $_POST['course']);
    $studentno = mysqli_real_escape_string($con, $_POST['studentno']);
    
    $target_dir = "uploads/";
    if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
        echo json_encode(['status' => 500, 'message' => 'Failed to create upload directory']);
        return;
    }
    $target_file = $target_dir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        echo json_encode(['status' => 400, 'message' => 'File is not an image.']);
        return;
    }

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        echo json_encode(['status' => 500, 'message' => 'Failed to upload image']);
        return;
    }

    if($name == NULL || $email == NULL || $phone == NULL || $course == NULL || $studentno == NULL) {
        echo json_encode(['status' => 422, 'message' => 'All fields are mandatory']);
        return;
    }

    $query = "INSERT INTO students (name,email,phone,course,studentno,img) VALUES ('$name','$email','$phone','$course','$studentno','$target_file')";
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        echo json_encode(['status' => 200, 'message' => 'Student Created Successfully']);
        return;
    } else {
        echo json_encode(['status' => 500, 'message' => 'Student Not Created']);
        return;
    }
}

if(isset($_POST['update_student'])) {
    $student_id = mysqli_real_escape_string($con, $_POST['student_id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $course = mysqli_real_escape_string($con, $_POST['course']);
    $studentno = mysqli_real_escape_string($con, $_POST['studentno']);

    $target_file = null;
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            echo json_encode(['status' => 400, 'message' => 'File is not an image.']);
            return;
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            echo json_encode(['status' => 500, 'message' => 'Failed to upload image']);
            return;
        }
    }

    if($name == NULL || $email == NULL || $phone == NULL || $course == NULL || $studentno == NULL) {
        echo json_encode(['status' => 422, 'message' => 'All fields are mandatory']);
        return;
    }

    $query = "UPDATE students SET name='$name', email='$email', phone='$phone', course='$course', studentno='$studentno'";
    if ($target_file) {
        $query .= ", img='$target_file'";
    }
    $query .= " WHERE id='$student_id'";
    
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        echo json_encode(['status' => 200, 'message' => 'Student Updated Successfully']);
        return;
    } else {
        echo json_encode(['status' => 500, 'message' => 'Student Not Updated']);
        return;
    }
}

if(isset($_GET['student_id'])) {
    $student_id = mysqli_real_escape_string($con, $_GET['student_id']);

    $query = "SELECT * FROM students WHERE id='$student_id'";
    $query_run = mysqli_query($con, $query);

    if(mysqli_num_rows($query_run) == 1) {
        $student = mysqli_fetch_array($query_run);

        $res = [
            'status' => 200,
            'message' => 'Student Fetch Successfully by id',
            'data' => $student
        ];
        echo json_encode($res);
        return;
    } else {
        $res = [
            'status' => 404,
            'message' => 'Student Id Not Found'
        ];
        echo json_encode($res);
        return;
    }
}

if(isset($_POST['delete_student'])) {
    $student_id = mysqli_real_escape_string($con, $_POST['student_id']);

    $query = "DELETE FROM students WHERE id='$student_id'";
    $query_run = mysqli_query($con, $query);

    if($query_run) {
        $res = [
            'status' => 200,
            'message' => 'Student Deleted Successfully'
        ];
        echo json_encode($res);
        return;
    } else {
        $res = [
            'status' => 500,
            'message' => 'Student Not Deleted'
        ];
        echo json_encode($res);
        return;
    }
}
?>
