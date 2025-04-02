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
</head>
<body>
    <div class="container">
        <h2>Search Land</h2>
        <form method="POST">
            <input type="text" name="land_title_no" placeholder="Enter Land Title Number" required>
            <button type="submit">Search</button>
        </form>

        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <?php if (isset($land) && $land) { ?>
            <div class="result">
                <h3>Land Information</h3>
                <p><strong>Land Title No:</strong> <?= htmlspecialchars($land['land_title_no']); ?></p>
                <p><strong>Owner:</strong> <?= htmlspecialchars($land['first_name'] . ' ' . $land['last_name']); ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($land['phone_number']); ?></p>
                <p><strong>Land Size:</strong> <?= htmlspecialchars($land['land_size']); ?> sqm</p>
                <p><strong>Land Use:</strong> <?= htmlspecialchars($land['land_use']); ?></p>
                <p><strong>Latitude:</strong> <?= htmlspecialchars($land['latitude']); ?></p>
                <p><strong>Longitude:</strong> <?= htmlspecialchars($land['longitude']); ?></p>
                <p><strong>Region:</strong> <?= htmlspecialchars($land['region_name']); ?></p>
                <p><strong>District:</strong> <?= htmlspecialchars($land['district_name']); ?></p>
                <p><strong>Ward:</strong> <?= htmlspecialchars($land['ward_name']); ?></p>
                <p><strong>Village:</strong> <?= htmlspecialchars($land['village_name']); ?></p>
                <p><strong>Registration Status:</strong> <?= htmlspecialchars($land['registration_status']); ?></p>
            </div>
        <?php } ?>
    </div>
</body>
</html>
