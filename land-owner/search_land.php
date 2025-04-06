<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $land_title_no = trim($_POST['land_title_no']);

    if (!empty($land_title_no)) {
        try {
            $sql = "SELECT 
                        lp.land_title_no, lp.land_size, lp.land_use, lp.latitude, lp.longitude, lp.registration_status, 
                        u.first_name, u.last_name, u.phone_number,
                        r.region_name, d.district_name, w.ward_name, v.village_name
                    FROM land_parcels lp
                    JOIN users u ON lp.owner_id = u.user_id
                    JOIN regions r ON lp.region_name = r.region_name
                    JOIN districts d ON lp.district_name = d.district_name
                    JOIN wards w ON lp.ward_name = w.ward_name
                    JOIN villages v ON lp.village_name = v.village_name
                    WHERE lp.land_title_no = :land_title_no";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':land_title_no', $land_title_no, PDO::PARAM_STR);
            $stmt->execute();
            $land = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$land) {
                $error = "No land found with the given title number.";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    } else {
        $error = "Please enter a land title number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Land</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .back-btn {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    text-align: center;
    margin: 10px;
    display: inline-block;
    text-decoration: none;
}

.back-btn:hover {
    background-color: #45a049;
}
    </style>
</head>
<body>
    <div class="container">
    <a href="owner_dashboard.php" class="back-btn">Back</a> 
        <h2>Search Land</h2>
        <form method="POST">
            <input type="text" name="land_title_no" placeholder="Enter Land Title Number" required>
            <button type="submit">Search</button>
        </form>

        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <?php if (isset($land) && $land) { ?>
    <div class="result">
        <h3>Land Information</h3>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr><th>Land Title No</th><td><?= htmlspecialchars($land['land_title_no']); ?></td></tr>
            <tr><th>Owner</th><td><?= htmlspecialchars($land['first_name'] . ' ' . $land['last_name']); ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($land['phone_number']); ?></td></tr>
            <tr><th>Land Size</th><td><?= htmlspecialchars($land['land_size']); ?> sqm</td></tr>
            <tr><th>Land Use</th><td><?= htmlspecialchars($land['land_use']); ?></td></tr>
            <tr><th>Latitude</th><td><?= htmlspecialchars($land['latitude']); ?></td></tr>
            <tr><th>Longitude</th><td><?= htmlspecialchars($land['longitude']); ?></td></tr>
            <tr><th>Region</th><td><?= htmlspecialchars($land['region_name']); ?></td></tr>
            <tr><th>District</th><td><?= htmlspecialchars($land['district_name']); ?></td></tr>
            <tr><th>Ward</th><td><?= htmlspecialchars($land['ward_name']); ?></td></tr>
            <tr><th>Village</th><td><?= htmlspecialchars($land['village_name']); ?></td></tr>
            <tr><th>Registration Status</th><td><?= htmlspecialchars($land['registration_status']); ?></td></tr>
        </table>
    </div>
<?php } ?>

    </div>
</body>
</html>
