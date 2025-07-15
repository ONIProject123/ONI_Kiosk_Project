<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records Search</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-image: url('public/20250709_084103.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-color: #f5f7fa; /* fallback color */
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 800px;
            position: relative;
        }
        
        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 60px;
            height: 60px;
            object-fit: contain;
            z-index: 10;
        }
        
        h1 {
            color: #2d3748;
            margin: 0 0 25px 80px;
            font-size: 24px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            padding-top: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
        }
        
        .required:after {
            content: " *";
            color: #e53e3e;
        }
        
        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="date"] {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn-submit {
            background-color: #4299e1;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        
        .btn-submit:hover {
            background-color: #3182ce;
        }
        
        .results-container {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .patient-record {
            background-color: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .patient-record h3 {
            color: #2d3748;
            margin-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }
        
        .record-field {
            margin-bottom: 10px;
        }
        
        .record-field strong {
            color: #4a5568;
            display: inline-block;
            width: 120px;
        }
        
        .error-message {
            color: #e53e3e;
            background-color: #fed7d7;
            padding: 10px;
            border-radius: 6px;
            margin-top: 20px;
        }
        
        .success-message {
            color: #38a169;
            background-color: #c6f6d5;
            padding: 10px;
            border-radius: 6px;
            margin-top: 20px;
        }
        
        .debug-box {
            background: #f0f0f0;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
        }
        
        .debug-db {
            background: #e8f4f8;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="public/Ph_seal_Imus.png" alt="Imus City Logo" class="logo">
        <h1>Patient Records Search</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="patientId">Patient ID</label>
                <input type="text" id="patientId" name="patientId" placeholder="Optional patient ID" value="<?php echo isset($_POST['patientId']) ? htmlspecialchars($_POST['patientId']) : ''; ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName" class="required">First Name</label>
                    <input type="text" id="firstName" name="firstName" required placeholder="Required first name" value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="middleName">Middle Name</label>
                    <input type="text" id="middleName" name="middleName" placeholder="Optional middle name" value="<?php echo isset($_POST['middleName']) ? htmlspecialchars($_POST['middleName']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="lastName" class="required">Last Name</label>
                    <input type="text" id="lastName" name="lastName" required placeholder="Required last name" value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="suffix">Suffix</label>
                    <select id="suffix" name="suffix">
                        <option value="">None</option>
                        <option value="Jr" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] == 'Jr') ? 'selected' : ''; ?>>Jr</option>
                        <option value="Sr" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] == 'Sr') ? 'selected' : ''; ?>>Sr</option>
                        <option value="II" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] == 'II') ? 'selected' : ''; ?>>II</option>
                        <option value="III" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] == 'III') ? 'selected' : ''; ?>>III</option>
                        <option value="IV" <?php echo (isset($_POST['suffix']) && $_POST['suffix'] == 'IV') ? 'selected' : ''; ?>>IV</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="birthdate" class="required">Date of Birth</label>
                <input type="date" id="birthdate" name="birthdate" required value="<?php echo isset($_POST['birthdate']) ? htmlspecialchars($_POST['birthdate']) : ''; ?>">
            </div>
            
            <button type="submit" name="search" class="btn-submit">Search Patient Records</button>
        </form>

        <?php
        if (isset($_POST['search'])) {
            // Include database connection
            include 'connection.php';
            
            // Get form data and sanitize
            $patientId = trim($_POST['patientId']);
            $firstName = trim($_POST['firstName']);
            $middleName = trim($_POST['middleName']);
            $lastName = trim($_POST['lastName']);
            $suffix = trim($_POST['suffix']);
            $birthdate = trim($_POST['birthdate']);
            
            // Build the search query (using your exact column names)
            $query = "SELECT * FROM patients WHERE 1=1";
            $params = [];
            $types = "";
            
            // Add conditions based on provided data
            if (!empty($patientId)) {
                $query .= " AND `Patient ID` = ?";
                $params[] = $patientId;
                $types .= "s";
            }
            
            // Case-insensitive search for names
            if (!empty($firstName)) {
                $query .= " AND LOWER(`First Name`) LIKE LOWER(?)";
                $params[] = "%" . $firstName . "%";
                $types .= "s";
            }
            
            if (!empty($middleName)) {
                $query .= " AND LOWER(`Middle Name`) LIKE LOWER(?)";
                $params[] = "%" . $middleName . "%";
                $types .= "s";
            }
            
            if (!empty($lastName)) {
                $query .= " AND LOWER(`Last Name`) LIKE LOWER(?)";
                $params[] = "%" . $lastName . "%";
                $types .= "s";
            }
            
            // Date handling - now expects yyyy-mm-dd format from date input
            $dbDate = null;
            if (!empty($birthdate)) {
                // Date is already in YYYY-MM-DD format from HTML5 date input
                $dbDate = $birthdate;
                $query .= " AND `Birthdate` = ?";
                $params[] = $dbDate;
                $types .= "s";
            }
            
            // Optional: Enable debug mode by adding ?debug=1 to URL
            if (isset($_GET['debug']) && $_GET['debug'] == '1') {
                echo '<div class="debug-box">';
                echo '<strong>Debug Query:</strong> ' . htmlspecialchars($query) . '<br>';
                echo '<strong>Parameters:</strong> ' . htmlspecialchars(print_r($params, true)) . '<br>';
                echo '<strong>Formatted Date:</strong> ' . ($dbDate ? $dbDate : 'N/A') . '<br>';
                echo '<strong>Types:</strong> ' . $types . '<br>';
                echo '</div>';
            }
            // Execute the search query
            try {
                $stmt = $conn->prepare($query);
                
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    echo '<div class="results-container">';
                    echo '<h2>Patient Record Found! (' . $result->num_rows . ' record(s) found)</h2>';
                    
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="patient-record">';
                        echo '<h3>Patient Record</h3>';
                        
                        // Display all available fields
                        foreach ($row as $field => $value) {
                            if (!empty($value)) {
                                $fieldName = ucwords(str_replace('_', ' ', $field));
                                echo '<div class="record-field">';
                                echo '<strong>' . htmlspecialchars($fieldName) . ':</strong> ' . htmlspecialchars($value);
                                echo '</div>';
                            }
                        }
                        
                        echo '</div>';
                    }
                    
                    echo '</div>';
                } else {
                    echo '<div class="error-message">';
                    echo 'NO RECORD FOUND!.';
                    echo '</div>';
                }
                
                $stmt->close();
                
            } catch (Exception $e) {
                echo '<div class="error-message">';
                echo 'Database error: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
            
            $conn->close();
        }
        ?>
    </div>
</body>
</html>