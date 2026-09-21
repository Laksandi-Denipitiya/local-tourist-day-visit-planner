<?php

include "config/database.php";

/* Get all places */

$sql = "
    SELECT 
        places.place_id,
        places.place_name,
        places.description,
        places.distance_km,
places.latitude,
places.longitude,
places.opening_hours,
categories.category_name,
        place_images.image_path
    FROM places
    INNER JOIN categories 
        ON places.category_id = categories.category_id
    LEFT JOIN place_images 
        ON places.place_id = place_images.place_id
        AND place_images.is_primary = TRUE
    ORDER BY places.place_name
";

$result = $conn->query($sql);

$places = [];

while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Day Plan - Local Tourist Day-Visit Planner</title>

    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Day Plan CSS -->

    <link
        rel="stylesheet"
        href="assets/css/day-plan.css">


    <style>

        /* =========================
           DAY PLAN
        ========================= */

        .day-plan-wrapper {
            padding: 50px 0 70px;
        }


        /* Header */

        .day-plan-header {
            background: linear-gradient(
                135deg,
                #198754,
                #146c43
            );

            color: white;

            padding: 55px 30px;

            border-radius: 20px;

            text-align: center;

            margin-bottom: 40px;
        }


        .day-plan-header h1 {

            font-size: 40px;

            font-weight: 700;

            margin-bottom: 12px;

        }


        .day-plan-header p {

            font-size: 17px;

            margin: 0;

        }


        /* Section */

        .plan-section {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }


        .plan-section h3 {

            font-weight: 700;

            margin: 0;

        }


        /* Card */

        .plan-card {

            background: white;

            border-radius: 16px;

            overflow: hidden;

            margin-bottom: 25px;

            box-shadow:
                0 4px 15px rgba(0,0,0,0.08);

            border: none;

        }


        .plan-card:hover {

            box-shadow:
                0 8px 25px rgba(0,0,0,0.12);

        }


        /* Image */

        .plan-image {

            width: 100%;

            height: 220px;

            object-fit: cover;

        }


        /* Content */

        .plan-content {

            padding: 25px;

        }


        .stop-badge {

            background: #198754;

            color: white;

            padding: 6px 14px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 600;

        }


        .category-badge {

            background: #e8f5ee;

            color: #198754;

            padding: 6px 14px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 600;

            margin-left: 6px;

        }


        .place-title {

            font-size: 24px;

            font-weight: 700;

            margin-top: 18px;

            margin-bottom: 10px;

        }


        .description {

            color: #6c757d;

            line-height: 1.6;

        }


        .place-info {

            color: #6c757d;

            font-size: 14px;

            margin-top: 15px;

        }


        .place-info div {

            margin-bottom: 7px;

        }


        .place-info i {

            color: #198754;

            margin-right: 5px;

        }


        /* Buttons */

        .plan-actions {

            border-left: 1px solid #eeeeee;

            display: flex;

            flex-direction: column;

            justify-content: center;

            align-items: center;

            gap: 8px;

            padding: 20px;

        }


        .action-button {

            width: 40px;

            height: 40px;

            border-radius: 50%;

        }


        /* Empty */

        .empty-plan {

            background: white;

            border-radius: 20px;

            text-align: center;

            padding: 70px 20px;

            box-shadow:
                0 4px 15px rgba(0,0,0,0.06);

        }


        .empty-plan i {

            font-size: 60px;

            color: #198754;

        }


        .empty-plan h3 {

            margin-top: 20px;

            font-weight: 700;

        }


        /* Mobile */

        @media (max-width: 767px) {

            .day-plan-wrapper {

                padding: 30px 15px 50px;

            }


            .day-plan-header {

                padding: 40px 20px;

            }


            .day-plan-header h1 {

                font-size: 30px;

            }


            .plan-section {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }


            .plan-actions {

                border-left: none;

                border-top: 1px solid #eeeeee;

                flex-direction: row;

            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg bg-white shadow-sm">

    <div class="container">

        <a
            class="navbar-brand fw-bold text-success"
            href="index.php">

            Local Tourist

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>


        <div
            class="collapse navbar-collapse"
            id="navbarNav">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="index.php">

                        Home

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="places.php">

                        Explore Places

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        class="nav-link active text-success"
                        href="day-plan.php">

                        My Day Plan

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- PAGE -->

<main class="day-plan-wrapper">

    <div class="container">


        <!-- Header -->

        <div class="day-plan-header">

            <h1>

                <i class="bi bi-map"></i>

                My Day Plan

            </h1>


            <p>

                Organize the places you want to visit
                during your one-day trip.

            </p>

        </div>


        <!-- Empty Plan -->

        <div
            id="emptyPlan"
            class="empty-plan"
            style="display:none;">

            <i class="bi bi-map"></i>


            <h3>

                Your day plan is empty

            </h3>


            <p class="text-muted">

                Explore places and add destinations
                to your day plan.

            </p>


            <a
                href="places.php"
                class="btn btn-success mt-3">

                <i class="bi bi-compass"></i>

                Explore Places

            </a>

        </div>


        <!-- Selected Places -->

        <div
            id="planContainer"
            style="display:none;">


            <div class="plan-section">

                <h3>

                    Selected Places

                </h3>


                <button
                    class="btn btn-outline-danger"
                    onclick="clearPlan()">

                    <i class="bi bi-trash"></i>

                    Clear Plan

                </button>

            </div>


            <div id="planList"></div>

        </div>

    </div>

</main>


<!-- FOOTER -->

<footer class="bg-dark text-white text-center py-4">

    <div class="container">

        <p class="mb-1">

            Local Tourist Day-Visit Planner

        </p>


        <small>

            Discover places. Plan your day. Explore locally.

        </small>

    </div>

</footer>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<script>

/* =================================
   DATABASE PLACES
================================= */

const places =
    <?= json_encode($places); ?>;


/* =================================
   LOAD PLAN
================================= */

let plan =
    JSON.parse(
        localStorage.getItem("dayPlan")
    ) || [];


/* =================================
   DISPLAY PLAN
================================= */

function displayPlan() {

    const emptyPlan =
        document.getElementById("emptyPlan");

    const planContainer =
        document.getElementById("planContainer");

    const planList =
        document.getElementById("planList");


    planList.innerHTML = "";


    /* Empty */

    if (plan.length === 0) {

        emptyPlan.style.display = "block";

        planContainer.style.display = "none";

        return;

    }


    /* Has places */

    emptyPlan.style.display = "none";

    planContainer.style.display = "block";


    plan.forEach(
        (placeId, index) => {


            const place =
                places.find(
                    p =>
                    Number(p.place_id)
                    ===
                    Number(placeId)
                );


            if (!place) {

                return;

            }


            const image =
                place.image_path
                ?
                place.image_path
                :
                "assets/images/places/default.jpg";


            const card =
                document.createElement("div");


            card.className =
                "plan-card";


            card.innerHTML = `

                <div class="row g-0">


                    <!-- IMAGE -->

                    <div class="col-md-3">

                        <img
                            src="${image}"
                            class="plan-image"
                            alt="${escapeHtml(place.place_name)}">

                    </div>


                    <!-- CONTENT -->

                    <div class="col-md-7">

                        <div class="plan-content">


                            <span class="stop-badge">

                                Stop ${index + 1}

                            </span>


                            <span class="category-badge">

                                ${escapeHtml(
                                    place.category_name
                                )}

                            </span>


                            <h4 class="place-title">

                                ${escapeHtml(
                                    place.place_name
                                )}

                            </h4>


                            <p class="description">

                                ${escapeHtml(
                                    place.description
                                )}

                            </p>


                            <div class="place-info">

                                <div>

                                    <i class="bi bi-geo-alt-fill"></i>

                                    ${place.distance_km ?? "N/A"} km

                                </div>


                                ${
                                    place.opening_hours
                                    ?
                                    `
                                    <div>

                                        <i class="bi bi-clock-fill"></i>

                                        ${escapeHtml(
                                            place.opening_hours
                                        )}

                                    </div>
                                    `
                                    :
                                    ""
                                }

                            </div>

                        </div>

                    </div>


                    <!-- ACTIONS -->

                   <!-- ACTIONS -->

<div class="col-md-2 plan-actions">

    <!-- UP -->

    <button 
        class="btn btn-outline-secondary action-button" 
        onclick="moveUp(${index})" 
        ${index === 0 ? "disabled" : ""}>

        <i class="bi bi-arrow-up"></i>

    </button>


    <!-- DOWN -->

    <button 
        class="btn btn-outline-secondary action-button" 
        onclick="moveDown(${index})" 
        ${
            index === plan.length - 1
            ? "disabled"
            : ""
        }>

        <i class="bi bi-arrow-down"></i>

    </button>


    <!-- VIEW -->

    <a 
        href="place-details.php?id=${place.place_id}" 
        class="btn btn-outline-success action-button d-flex align-items-center justify-content-center">

        <i class="bi bi-eye"></i>

    </a>


    <!-- DIRECTIONS -->

    <a
        href="https://www.google.com/maps/dir/?api=1&destination=${place.latitude},${place.longitude}"
        target="_blank"
        class="btn btn-outline-primary action-button d-flex align-items-center justify-content-center">

        <i class="bi bi-geo-alt"></i>

    </a>


    <!-- REMOVE -->

    <button 
        class="btn btn-outline-danger action-button" 
        onclick="removeFromPlan(${place.place_id})">

        <i class="bi bi-x-lg"></i>

    </button>

</div>

                       
            `;


            planList.appendChild(card);

        }
    );

}


/* =================================
   REMOVE
================================= */

function removeFromPlan(placeId) {

    plan =
        plan.filter(
            id =>
            Number(id)
            !==
            Number(placeId)
        );


    localStorage.setItem(
        "dayPlan",
        JSON.stringify(plan)
    );


    displayPlan();

}


/* =================================
   CLEAR
================================= */

function clearPlan() {

    if (
        !confirm(
            "Are you sure you want to clear your entire day plan?"
        )
    ) {

        return;

    }


    plan = [];


    localStorage.removeItem(
        "dayPlan"
    );


    displayPlan();

}


/* =================================
   MOVE UP
================================= */

function moveUp(index) {

    if (index <= 0) {

        return;

    }


    [
        plan[index - 1],
        plan[index]
    ]
    =
    [
        plan[index],
        plan[index - 1]
    ];


    localStorage.setItem(
        "dayPlan",
        JSON.stringify(plan)
    );


    displayPlan();

}


/* =================================
   MOVE DOWN
================================= */

function moveDown(index) {

    if (
        index >= plan.length - 1
    ) {

        return;

    }


    [
        plan[index],
        plan[index + 1]
    ]
    =
    [
        plan[index + 1],
        plan[index]
    ];


    localStorage.setItem(
        "dayPlan",
        JSON.stringify(plan)
    );


    displayPlan();

}


/* =================================
   ESCAPE HTML
================================= */

function escapeHtml(text) {

    const div =
        document.createElement("div");


    div.textContent =
        text ?? "";


    return div.innerHTML;

}


/* =================================
   START
================================= */

displayPlan();

</script>


</body>

</html>