<?php
# Edited by Amish
// Check if the necessary parameters are set
if (isset($_POST['userId'], $_POST['email'], $_POST['phone'])) {
    // Database connection details
    $host = "localhost";
    $dbname = "ott";
    $username = "root";
    $password = "";

    // Establish database connection using mysqli
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the SQL UPDATE statement
    try {
        // Prepare the statement
        $stmt = $conn->prepare("UPDATE user SET email = ?, mobile_number = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $_POST['email'], $_POST['phone'], $_POST['userId']); // "ssi" means string, string, integer

        // Execute the statement
        if ($stmt->execute()) {
            echo "User details updated successfully.";
        } else {
            echo "Error updating user details: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close the connection
    $conn->close();
} else {
    echo "Missing parameters"; // Handle missing parameters
}
?>
