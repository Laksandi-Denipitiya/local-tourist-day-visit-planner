<?php

session_start();

include "../config/database.php";


// Protect admin page
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// Validate ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: dashboard.php");
    exit;
}

$place_id = (int) $_GET["id"];


// Get existing place
$sql = "
    SELECT *
    FROM places
    WHERE place_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $place_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Place not found.");
}

$place = $result->fetch_assoc();


// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $place_name = trim($_POST["place_name"]);
    $category_id = (int) $_POST["category_id"];
    $description = trim($_POST["description"]);
    $historical_significance = trim($_POST["historical_significance"]);
    $distance_km = (float) $_POST["distance_km"];
    $latitude = (float) $_POST["latitude"];
    $longitude = (float) $_POST["longitude"];
    $opening_hours = trim($_POST["opening_hours"]);
    $travel_tips = trim($_POST["travel_tips"]);

    $selected_interests = $_POST["interests"] ?? [];


    if (
        empty($place_name) ||
        $category_id <= 0 ||
        empty($description)
    ) {

        $error = "Please fill in all required fields.";

    } else {


        // Update place
        $update_sql = "
            UPDATE places
            SET
                place_name = ?,
                category_id = ?,
                description = ?,
                historical_significance = ?,
                distance_km = ?,
                latitude = ?,
                longitude = ?,
                opening_hours = ?,
                travel_tips = ?

            WHERE place_id = ?
        ";


        $update_stmt = $conn->prepare($update_sql);

        $update_stmt->bind_param(
            "sissdddssi",
            $place_name,
            $category_id,
            $description,
            $historical_significance,
            $distance_km,
            $latitude,
            $longitude,
            $opening_hours,
            $travel_tips,
            $place_id
        );


        if ($update_stmt->execute()) {


            // Remove old interests
            $delete_interests = $conn->prepare("
                DELETE FROM place_interests
                WHERE place_id = ?
            ");

            $delete_interests->bind_param(
                "i",
                $place_id
            );

            $delete_interests->execute();

            $delete_interests->close();


            // Add selected interests
            foreach ($selected_interests as $interest_id) {

                $interest_id = (int) $interest_id;

                $interest_stmt = $conn->prepare("
                    INSERT INTO place_interests
                    (place_id, interest_id)
                    VALUES (?, ?)
                ");

                $interest_stmt->bind_param(
                    "ii",
                    $place_id,
                    $interest_id
                );

                $interest_stmt->execute();

                $interest_stmt->close();
            }


            // Handle new image
            if (
                isset($_FILES["place_image"]) &&
                $_FILES["place_image"]["error"] === UPLOAD_ERR_OK
            ) {

                $upload_dir = "../assets/images/places/";

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }


                $file_name = $_FILES["place_image"]["name"];

                $file_tmp = $_FILES["place_image"]["tmp_name"];

                $file_ext = strtolower(
                    pathinfo($file_name, PATHINFO_EXTENSION)
                );


                $allowed_extensions = [
                    "jpg",
                    "jpeg",
                    "png",
                    "webp"
                ];


                if (in_array($file_ext, $allowed_extensions)) {


                    // Remove previous primary image
                    $old_image_sql = "
                        DELETE FROM place_images
                        WHERE place_id = ?
                        AND is_primary = TRUE
                    ";

                    $old_image_stmt = $conn->prepare($old_image_sql);

                    $old_image_stmt->bind_param(
                        "i",
                        $place_id
                    );

                    $old_image_stmt->execute();

                    $old_image_stmt->close();


                    $new_file_name =
                        "place_" .
                        $place_id .
                        "_" .
                        time() .
                        "." .
                        $file_ext;


                    $destination =
                        $upload_dir .
                        $new_file_name;


                    if (move_uploaded_file(
                        $file_tmp,
                        $destination
                    )) {

                        $image_path =
                            "assets/images/places/" .
                            $new_file_name;


                        $image_stmt = $conn->prepare("
                            INSERT INTO place_images
                            (
                                place_id,
                                image_path,
                                is_primary
                            )

                            VALUES (?, ?, TRUE)
                        ");


                        $image_stmt->bind_param(
                            "is",
                            $place_id,
                            $image_path
                        );

                        $image_stmt->execute();

                        $image_stmt->close();
                    }
                }
            }


            header(
                "Location: dashboard.php?updated=1"
            );

            exit;


        } else {

            $error = "Failed to update the place.";

        }

    }

}


// Get categories
$category_result = $conn->query("
    SELECT *
    FROM categories
    ORDER BY category_name
");


// Get interests
$interest_result = $conn->query("
    SELECT *
    FROM interests
    ORDER BY interest_name
");


// Get selected interests
$selected_sql = "
    SELECT interest_id
    FROM place_interests
    WHERE place_id = ?
";

$selected_stmt = $conn->prepare($selected_sql);

$selected_stmt->bind_param(
    "i",
    $place_id
);

$selected_stmt->execute();

$selected_result = $selected_stmt->get_result();

$selected_interests = [];

while ($row = $selected_result->fetch_assoc()) {
    $selected_interests[] = $row["interest_id"];
}


// Get current image
$image_sql = "
    SELECT image_path
    FROM place_images
    WHERE place_id = ?
    AND is_primary = TRUE
    LIMIT 1
";

$image_stmt = $conn->prepare($image_sql);

$image_stmt->bind_param(
    "i",
    $place_id
);

$image_stmt->execute();

$image_result = $image_stmt->get_result();

$current_image = null;

if ($image_result->num_rows > 0) {
    $current_image = $image_result->fetch_assoc()["image_path"];
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Edit Place | Admin
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body class="bg-light">


<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="dashboard.php"
        >

            <i class="bi bi-speedometer2"></i>

            Admin Dashboard

        </a>


        <a
            href="logout.php"
            class="btn btn-outline-light btn-sm"
        >

            Logout

        </a>

    </div>

</nav>



<div class="container py-5">


    <div class="mb-4">

        <a
            href="dashboard.php"
            class="btn btn-outline-secondary"
        >

            <i class="bi bi-arrow-left"></i>

            Back to Dashboard

        </a>

    </div>



    <div class="card shadow-sm border-0">


        <div class="card-header bg-white p-4">

            <h3 class="fw-bold mb-1">

                <i class="bi bi-pencil-square text-primary"></i>

                Edit Tourist Place

            </h3>

            <p class="text-muted mb-0">

                Update the information for this destination.

            </p>

        </div>



        <div class="card-body p-4">


            <?php if (!empty($error)): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>



            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <h5 class="fw-bold mb-3">
                    Basic Information
                </h5>


                <div class="row g-3">


                    <div class="col-md-8">

                        <label class="form-label fw-bold">
                            Place Name
                        </label>

                        <input
                            type="text"
                            name="place_name"
                            class="form-control"
                            value="<?= htmlspecialchars($place["place_name"]) ?>"
                            required
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-bold">
                            Category
                        </label>

                        <select
                            name="category_id"
                            class="form-select"
                            required
                        >

                            <?php while ($cat = $category_result->fetch_assoc()): ?>

                                <option
                                    value="<?= $cat["category_id"] ?>"
                                    <?= ($cat["category_id"] == $place["category_id"]) ? "selected" : "" ?>
                                >

                                    <?= htmlspecialchars($cat["category_name"]) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="col-12">

                        <label class="form-label fw-bold">
                            Description
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            required
                        ><?= htmlspecialchars($place["description"]) ?></textarea>

                    </div>


                    <div class="col-12">

                        <label class="form-label fw-bold">
                            Historical Significance
                        </label>

                        <textarea
                            name="historical_significance"
                            class="form-control"
                            rows="4"
                        ><?= htmlspecialchars($place["historical_significance"]) ?></textarea>

                    </div>

                </div>



                <hr class="my-4">



                <h5 class="fw-bold mb-3">
                    Location Information
                </h5>


                <div class="row g-3">


                    <div class="col-md-4">

                        <label class="form-label fw-bold">
                            Distance (km)
                        </label>

                        <input
                            type="number"
                            name="distance_km"
                            class="form-control"
                            step="0.01"
                            value="<?= htmlspecialchars($place["distance_km"]) ?>"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-bold">
                            Latitude
                        </label>

                        <input
                            type="number"
                            name="latitude"
                            class="form-control"
                            step="0.00000001"
                            value="<?= htmlspecialchars($place["latitude"]) ?>"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-bold">
                            Longitude
                        </label>

                        <input
                            type="number"
                            name="longitude"
                            class="form-control"
                            step="0.00000001"
                            value="<?= htmlspecialchars($place["longitude"]) ?>"
                        >

                    </div>

                </div>



                <hr class="my-4">



                <h5 class="fw-bold mb-3">
                    Visitor Information
                </h5>


                <div class="row g-3">


                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Opening Hours
                        </label>

                        <input
                            type="text"
                            name="opening_hours"
                            class="form-control"
                            value="<?= htmlspecialchars($place["opening_hours"]) ?>"
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label fw-bold">
                            Replace Image
                        </label>

                        <input
                            type="file"
                            name="place_image"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                    </div>


                    <?php if ($current_image): ?>

                        <div class="col-12">

                            <p class="fw-bold mb-2">
                                Current Image
                            </p>

                            <img
                                src="../<?= htmlspecialchars($current_image) ?>"
                                alt="Current place image"
                                style="
                                    width:250px;
                                    height:160px;
                                    object-fit:cover;
                                "
                                class="rounded shadow-sm"
                            >

                        </div>

                    <?php endif; ?>


                    <div class="col-12">

                        <label class="form-label fw-bold">
                            Travel Tips
                        </label>

                        <textarea
                            name="travel_tips"
                            class="form-control"
                            rows="3"
                        ><?= htmlspecialchars($place["travel_tips"]) ?></textarea>

                    </div>

                </div>



                <hr class="my-4">



                <h5 class="fw-bold mb-3">
                    Visitor Interests
                </h5>


                <div class="row">


                    <?php while ($int = $interest_result->fetch_assoc()): ?>


                        <div class="col-md-3 col-sm-6 mb-2">


                            <div class="form-check">


                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="interests[]"
                                    value="<?= $int["interest_id"] ?>"
                                    id="interest<?= $int["interest_id"] ?>"
                                    <?= in_array($int["interest_id"], $selected_interests) ? "checked" : "" ?>
                                >


                                <label
                                    class="form-check-label"
                                    for="interest<?= $int["interest_id"] ?>"
                                >

                                    <?= htmlspecialchars($int["interest_name"]) ?>

                                </label>


                            </div>


                        </div>


                    <?php endwhile; ?>

                </div>



                <div class="mt-4">


                    <button
                        type="submit"
                        class="btn btn-primary btn-lg"
                    >

                        <i class="bi bi-save"></i>

                        Save Changes

                    </button>


                    <a
                        href="dashboard.php"
                        class="btn btn-outline-secondary btn-lg ms-2"
                    >

                        Cancel

                    </a>


                </div>


            </form>


        </div>

    </div>

</div>


</body>

</html>