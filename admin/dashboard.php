<?php

session_start();

include "../config/database.php";


// Protect admin page
if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");
    exit;

}


// Get all places
$sql = "
    SELECT
        places.*,
        categories.category_name

    FROM places

    INNER JOIN categories
        ON places.category_id = categories.category_id

    ORDER BY places.place_id DESC
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

    <title>Admin Dashboard</title>


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


        <div class="d-flex align-items-center">

            <span class="text-white me-3">

                <i class="bi bi-person-circle"></i>

                <?= htmlspecialchars($_SESSION["admin_username"]) ?>

            </span>


            <a
                href="logout.php"
                class="btn btn-outline-light btn-sm"
            >

                <i class="bi bi-box-arrow-right"></i>

                Logout

            </a>

        </div>

    </div>

</nav>



<!-- MAIN CONTENT -->

<div class="container py-5">


    <div class="d-flex justify-content-between align-items-center mb-4">


        <div>

            <h2 class="fw-bold mb-1">
                Manage Tourist Places
            </h2>

            <p class="text-muted mb-0">
                Add, edit and remove places from the system.
            </p>

        </div>


        <a
            href="add-place.php"
            class="btn btn-success"
        >

            <i class="bi bi-plus-circle"></i>

            Add New Place

        </a>


    </div>



    <!-- STATISTICS -->

    <div class="row g-3 mb-4">


        <div class="col-md-4">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6 class="text-muted">
                                Total Places
                            </h6>

                            <h2 class="fw-bold">
                                <?= $result->num_rows ?>
                            </h2>

                        </div>


                        <i
                            class="bi bi-geo-alt text-success"
                            style="font-size:2.5rem;"
                        ></i>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6 class="text-muted">
                        Admin
                    </h6>

                    <h5 class="fw-bold">
                        <?= htmlspecialchars($_SESSION["admin_username"]) ?>
                    </h5>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h6 class="text-muted">
                        System
                    </h6>

                    <h5 class="fw-bold">
                        Local Tourist Planner
                    </h5>

                </div>

            </div>

        </div>


    </div>



    <!-- PLACES TABLE -->

    <div class="card shadow-sm border-0">


        <div class="card-header bg-white">

            <h5 class="fw-bold mb-0">

                Tourist Places

            </h5>

        </div>


        <div class="card-body p-0">


            <div class="table-responsive">


                <table class="table table-hover align-middle mb-0">


                    <thead class="table-light">

                        <tr>

                            <th>ID</th>

                            <th>Place</th>

                            <th>Category</th>

                            <th>Distance</th>

                            <th>Opening Hours</th>

                            <th class="text-center">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php if ($result->num_rows > 0): ?>


                            <?php while ($place = $result->fetch_assoc()): ?>


                                <tr>


                                    <td>

                                        <?= $place["place_id"] ?>

                                    </td>


                                    <td>

                                        <strong>

                                            <?= htmlspecialchars($place["place_name"]) ?>

                                        </strong>

                                    </td>


                                    <td>

                                        <span class="badge bg-success">

                                            <?= htmlspecialchars($place["category_name"]) ?>

                                        </span>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($place["distance_km"]) ?>

                                        km

                                    </td>


                                    <td>

                                        <?= htmlspecialchars($place["opening_hours"]) ?>

                                    </td>


                                    <td class="text-center">


                                        <a
                                            href="edit-place.php?id=<?= $place["place_id"] ?>"
                                            class="btn btn-sm btn-outline-primary"
                                        >

                                            <i class="bi bi-pencil"></i>

                                            Edit

                                        </a>


                                        <a
                                            href="delete-place.php?id=<?= $place["place_id"] ?>"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Are you sure you want to delete this place?');"
                                        >

                                            <i class="bi bi-trash"></i>

                                            Delete

                                        </a>


                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center py-4 text-muted"
                                >

                                    No places available.

                                </td>

                            </tr>


                        <?php endif; ?>


                    </tbody>


                </table>


            </div>

        </div>

    </div>



    <div class="mt-4">

        <a
            href="../index.php"
            class="btn btn-outline-secondary"
        >

            <i class="bi bi-globe"></i>

            View Website

        </a>

    </div>


</div>


</body>

</html>