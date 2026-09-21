<?php

include "config/database.php";

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$interest = $_GET['interest'] ?? '';


// Get categories
$category_sql = "SELECT * FROM categories ORDER BY category_name";
$category_result = $conn->query($category_sql);


// Get interests
$interest_sql = "SELECT * FROM interests ORDER BY interest_name";
$interest_result = $conn->query($interest_sql);


// Build main query
$sql = "
    SELECT DISTINCT
        places.*,
        categories.category_name,
        place_images.image_path

    FROM places

    INNER JOIN categories
        ON places.category_id = categories.category_id

    LEFT JOIN place_images
        ON places.place_id = place_images.place_id
        AND place_images.is_primary = TRUE

    LEFT JOIN place_interests
        ON places.place_id = place_interests.place_id

    LEFT JOIN interests
        ON place_interests.interest_id = interests.interest_id

    WHERE 1=1
";


// Search by place name
if (!empty($search)) {

    $safe_search = $conn->real_escape_string($search);

    $sql .= "
        AND places.place_name LIKE '%$safe_search%'
    ";
}


// Category filter
if (!empty($category)) {

    $safe_category = (int) $category;

    $sql .= "
        AND places.category_id = $safe_category
    ";
}


// Interest filter
if (!empty($interest)) {

    $safe_interest = (int) $interest;

    $sql .= "
        AND interests.interest_id = $safe_interest
    ";
}


$sql .= "
    ORDER BY places.place_name
";


$result = $conn->query($sql);

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
        Places | Local Tourist Planner
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


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a
            class="navbar-brand fw-bold"
            href="index.php"
        >
            Local Tourist Planner
        </a>


        <div class="navbar-nav ms-auto">

            <a
                class="nav-link"
                href="index.php"
            >
                Home
            </a>

            <a
                class="nav-link active"
                href="places.php"
            >
                Places
            </a>

            <a
                class="nav-link"
                href="day-plan.php"
            >
                My Day Plan
            </a>

        </div>

    </div>

</nav>



<!-- PAGE HEADER -->

<div class="bg-light py-5">

    <div class="container">

        <h1 class="fw-bold">
            Explore Places
        </h1>

        <p class="text-muted mb-0">
            Discover interesting places around Kurunegala.
        </p>

    </div>

</div>



<!-- SEARCH & FILTERS -->

<div class="container py-4">

    <form
        method="GET"
        action="places.php"
        class="card shadow-sm p-4"
    >

        <div class="row g-3">


            <!-- SEARCH -->

            <div class="col-md-4">

                <label class="form-label fw-bold">
                    Search Place
                </label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Enter place name..."
                        value="<?= htmlspecialchars($search) ?>"
                    >

                </div>

            </div>



            <!-- CATEGORY -->

            <div class="col-md-3">

                <label class="form-label fw-bold">
                    Category
                </label>

                <select
                    name="category"
                    class="form-select"
                >

                    <option value="">
                        All Categories
                    </option>


                    <?php while ($cat = $category_result->fetch_assoc()): ?>

                        <option
                            value="<?= $cat['category_id'] ?>"
                            <?= ($category == $cat['category_id']) ? 'selected' : '' ?>
                        >

                            <?= htmlspecialchars($cat['category_name']) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>



            <!-- INTEREST -->

            <div class="col-md-3">

                <label class="form-label fw-bold">
                    Interest
                </label>

                <select
                    name="interest"
                    class="form-select"
                >

                    <option value="">
                        All Interests
                    </option>


                    <?php while ($int = $interest_result->fetch_assoc()): ?>

                        <option
                            value="<?= $int['interest_id'] ?>"
                            <?= ($interest == $int['interest_id']) ? 'selected' : '' ?>
                        >

                            <?= htmlspecialchars($int['interest_name']) ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>



            <!-- BUTTONS -->

            <div class="col-md-2 d-flex align-items-end gap-2">

                <button
                    type="submit"
                    class="btn btn-success w-100"
                >

                    <i class="bi bi-funnel"></i>

                    Filter

                </button>

            </div>

        </div>


        <div class="mt-3">

            <a
                href="places.php"
                class="btn btn-outline-secondary btn-sm"
            >

                <i class="bi bi-x-circle"></i>

                Clear Filters

            </a>

        </div>

    </form>

</div>



<!-- PLACES -->

<div class="container pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h3 class="fw-bold mb-0">
            Tourist Places
        </h3>

        <span class="text-muted">

            <?php
            echo $result->num_rows;
            ?>

            place(s) found

        </span>

    </div>



    <?php if ($result->num_rows > 0): ?>

        <div class="row g-4">


            <?php while ($place = $result->fetch_assoc()): ?>


                <div class="col-md-6 col-lg-4">


                    <div class="card h-100 shadow-sm overflow-hidden">


                        <!-- IMAGE -->

                        <?php if (!empty($place['image_path'])): ?>

                            <img
                                src="<?= htmlspecialchars($place['image_path']) ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($place['place_name']) ?>"
                                style="height:220px; object-fit:cover;"
                            >

                        <?php else: ?>

                            <div
                                class="bg-light d-flex align-items-center justify-content-center"
                                style="height:220px;"
                            >

                                <span class="text-muted">
                                    No image available
                                </span>

                            </div>

                        <?php endif; ?>



                        <!-- CARD BODY -->

                        <div class="card-body">


                            <span class="badge bg-success mb-2">

                                <?= htmlspecialchars($place['category_name']) ?>

                            </span>


                            <h5 class="card-title fw-bold">

                                <?= htmlspecialchars($place['place_name']) ?>

                            </h5>


                            <p class="card-text text-muted">

                                <?= htmlspecialchars($place['description']) ?>

                            </p>


                            <p class="mb-3">

                                <i class="bi bi-geo-alt"></i>

                                <?= htmlspecialchars($place['distance_km']) ?>

                                km

                            </p>


                            <a
                                href="place-details.php?id=<?= $place['place_id'] ?>"
                                class="btn btn-success"
                            >

                                View Details

                                <i class="bi bi-arrow-right"></i>

                            </a>


                        </div>

                    </div>

                </div>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <div class="alert alert-warning">

            <i class="bi bi-info-circle"></i>

            No places found matching your search or filters.

        </div>


    <?php endif; ?>


</div>


</body>

</html>