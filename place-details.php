<?php

include "config/database.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid place ID.");
}

$place_id = (int) $_GET['id'];

$sql = "
    SELECT
        places.*,
        categories.category_name,
        place_images.image_path

    FROM places

    INNER JOIN categories
        ON places.category_id = categories.category_id

    LEFT JOIN place_images
        ON places.place_id = place_images.place_id
        AND place_images.is_primary = TRUE

    WHERE places.place_id = $place_id
";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Place not found.");
}

$place = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($place['place_name']) ?> | Local Tourist Planner
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

<body>

<nav class="navbar navbar-expand-lg bg-dark navbar-dark">

    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            Local Tourist Planner
        </a>

        <div class="navbar-nav ms-auto">

            <a class="nav-link" href="index.php">
                Home
            </a>

            <a class="nav-link" href="places.php">
                Places
            </a>

            <a class="nav-link" href="day-plan.php">
                My Day Plan
            </a>

        </div>

    </div>

</nav>


<div class="container py-5">

    <a href="places.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i>
        Back to Places
    </a>


    <div class="card shadow-sm overflow-hidden">

        <?php if (!empty($place['image_path'])): ?>

            <img
                src="<?= htmlspecialchars($place['image_path']) ?>"
                class="card-img-top"
                alt="<?= htmlspecialchars($place['place_name']) ?>"
                style="height: 450px; object-fit: cover;"
            >

        <?php else: ?>

            <div
                class="bg-light d-flex align-items-center justify-content-center"
                style="height: 450px;"
            >

                <span class="text-muted">
                    No image available
                </span>

            </div>

        <?php endif; ?>


        <div class="card-body p-4">

            <span class="badge bg-success mb-2">

                <?= htmlspecialchars($place['category_name']) ?>

            </span>


            <h1 class="fw-bold mb-3">

                <?= htmlspecialchars($place['place_name']) ?>

            </h1>


            <p class="text-muted">

                <?= htmlspecialchars($place['description']) ?>

            </p>


            <hr>

<div class="row g-4">

    <div class="col-md-4">

        <h6 class="fw-bold">
            <i class="bi bi-geo-alt"></i>
            Distance
        </h6>

        <p>
            <?= htmlspecialchars($place['distance_km']) ?> km
        </p>

    </div>


    <div class="col-md-4">

        <h6 class="fw-bold">
            <i class="bi bi-clock"></i>
            Opening Hours
        </h6>

        <p>
            <?= htmlspecialchars($place['opening_hours']) ?>
        </p>

    </div>


    <div class="col-md-4">

        <h6 class="fw-bold">
            <i class="bi bi-pin-map"></i>
            Coordinates
        </h6>

        <p>
            <?= htmlspecialchars($place['latitude']) ?>,
            <?= htmlspecialchars($place['longitude']) ?>
        </p>

    </div>

</div>


<hr class="my-4">


<h4 class="fw-bold mb-3">
    <i class="bi bi-map"></i>
    Location Map
</h4>


<div class="ratio ratio-16x9">

    <iframe
        src="https://www.google.com/maps?q=<?= urlencode($place['latitude'] . ',' . $place['longitude']) ?>&output=embed"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>

</div>

                    <h6 class="fw-bold">
                        <i class="bi bi-geo-alt"></i>
                        Distance
                    </h6>

                    <p>
                        <?= htmlspecialchars($place['distance_km']) ?> km
                    </p>

                </div>


                <div class="col-md-4">

                    <h6 class="fw-bold">
                        <i class="bi bi-clock"></i>
                        Opening Hours
                    </h6>

                    <p>
                        <?= htmlspecialchars($place['opening_hours']) ?>
                    </p>

                </div>


                <div class="col-md-4">

                    <h6 class="fw-bold">
                        <i class="bi bi-map"></i>
                        Location
                    </h6>

                    <p>
                        <?= htmlspecialchars($place['latitude']) ?>,
                        <?= htmlspecialchars($place['longitude']) ?>
                    </p>

                </div>

            </div>


            <?php if (!empty($place['historical_significance'])): ?>

    <hr class="my-4">

    <h4 class="fw-bold">
        <i class="bi bi-hourglass-split"></i>
        Historical Significance
    </h4>

    <p>
        <?= nl2br(htmlspecialchars($place['historical_significance'])) ?>
    </p>



                <hr>

                <h5 class="fw-bold">
                    Travel Tips
                </h5>

                <p>
                    <?= nl2br(htmlspecialchars($place['travel_tips'])) ?>
                </p>

            <?php endif; ?>


            <div class="mt-4">

                <button
                    class="btn btn-success btn-lg"
                    onclick="addToPlan(<?= $place['place_id'] ?>)"
                >

                    <i class="bi bi-plus-circle"></i>

                    Add to My Day Plan

                </button>

            </div>

        </div>

    </div>

</div>


<script>

function addToPlan(placeId) {

    let plan = JSON.parse(localStorage.getItem("dayPlan")) || [];

    if (plan.includes(placeId)) {

        alert("This place is already in your day plan.");

        return;
    }

    plan.push(placeId);

    localStorage.setItem("dayPlan", JSON.stringify(plan));

    alert("Place added to your day plan!");

}

</script>


</body>

</html>