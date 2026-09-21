<?php

session_start();

include "../config/database.php";


// Protect admin page
if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit;

}


$message = "";
$error = "";


// Get categories
$category_sql = "SELECT * FROM categories ORDER BY category_name";
$category_result = $conn->query($category_sql);


// Get interests
$interest_sql = "SELECT * FROM interests ORDER BY interest_name";
$interest_result = $conn->query($interest_sql);


// Handle form submission
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


    // Basic validation
    if (
        empty($place_name) ||
        $category_id <= 0 ||
        empty($description)
    ) {

        $error = "Please fill in all required fields.";

    } else {


        // Insert place
        $sql = "
            INSERT INTO places
            (
                place_name,
                category_id,
                description,
                historical_significance,
                distance_km,
                latitude,
                longitude,
                opening_hours,
                travel_tips
            )

            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";


        $stmt = $conn->prepare($sql);


        $stmt->bind_param(
            "sissdddss",
            $place_name,
            $category_id,
            $description,
            $historical_significance,
            $distance_km,
            $latitude,
            $longitude,
            $opening_hours,
            $travel_tips
        );


        if ($stmt->execute()) {

            $place_id = $stmt->insert_id;


            // Save interests
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


            // Handle image upload
            if (
                isset($_FILES["place_image"]) &&
                $_FILES["place_image"]["error"] === UPLOAD_ERR_OK
            ) {

                $upload_dir = "../assets/images/places/";


                // Create directory if it does not exist
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


            $message = "Place added successfully!";


            // Clear form values
            $_POST = [];

        } else {

            $error = "Failed to add the place.";

        }

    }

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
        Add Place | Admin
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


<!-- NAVBAR -->

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

                <i class="bi bi-plus-circle text-success"></i>

                Add New Tourist Place

            </h3>

            <p class="text-muted mb-0">

                Enter the information about the tourist destination.

            </p>

        </div>



        <div class="card-body p-4">


            <?php if (!empty($message)): ?>

                <div class="alert alert-success">

                    <i class="bi bi-check-circle"></i>

                    <?= htmlspecialchars($message) ?>

                </div>

            <?php endif; ?>


            <?php if (!empty($error)): ?>

                <div class="alert alert-danger">

                    <i class="bi bi-exclamation-circle"></i>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>



            <form
                method="POST"
                enctype="multipart/form-data"
            >


                <!-- BASIC INFORMATION -->

                <h5 class="fw-bold mb-3">
                    Basic Information
                </h5>


                <div class="row g-3">


                    <div class="col-md-8">

                        <label class="form-label fw-bold">

                            Place Name
                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="place_name"
                            class="form-control"
                            required
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-bold">

                            Category
                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="category_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Category
                            </option>


                            <?php while ($cat = $category_result->fetch_assoc()): ?>

                                <option
                                    value="<?= $cat["category_id"] ?>"
                                >

                                    <?= htmlspecialchars($cat["category_name"]) ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <div class="col-12">

                        <label class="form-label fw-bold">

                            Description
                            <span class="text-danger">*</span>

                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            required
                        ></textarea>

                    </div>


                    <div class="col-12">

                        <label class="form-label fw-bold">

                            Historical Significance

                        </label>

                        <textarea
                            name="historical_significance"
                            class="form-control"
                            rows="4"
                            placeholder="Describe the historical background or significance of this place."
                        ></textarea>

                    </div>


                </div>



                <hr class="my-4">



                <!-- LOCATION -->

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
                            min="0"
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
                        >

                    </div>


                    <div class="col-12">

                        <div class="alert alert-info">

                            <i class="bi bi-info-circle"></i>

                            Enter the latitude and longitude of the place.
                            These coordinates will be used to display the
                            location on Google Maps.

                        </div>

                    </div>


                </div>



                <hr class="my-4">



                <!-- VISITOR INFORMATION -->

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
                            placeholder="Example: 6:00 AM - 6:00 PM"
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label fw-bold">

                            Place Image

                        </label>

                        <input
                            type="file"
                            name="place_image"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label fw-bold">

                            Travel Tips

                        </label>

                        <textarea
                            name="travel_tips"
                            class="form-control"
                            rows="3"
                            placeholder="Useful advice for visitors..."
                        ></textarea>

                    </div>


                </div>



                <hr class="my-4">



                <!-- INTERESTS -->

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
                        class="btn btn-success btn-lg"
                    >

                        <i class="bi bi-check-circle"></i>

                        Add Place

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