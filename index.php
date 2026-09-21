<?php
require_once "config/database.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Local Tourist Planner</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f9f8;
            color: #24352c;
        }

        /* NAVBAR */

        .main-navbar {
            background: white;
            padding: 18px 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .brand {
            color: #168653;
            font-size: 1.35rem;
            font-weight: 700;
            text-decoration: none;
        }

        .brand i {
            margin-right: 7px;
        }

        .nav-link {
            color: #34443b !important;
            font-weight: 500;
            margin-left: 25px;
        }

        .nav-link:hover {
            color: #168653 !important;
        }


        /* HERO */

        .hero {
            min-height: 650px;

            background-image:
                linear-gradient(
                    rgba(12, 45, 29, 0.55),
                    rgba(12, 45, 29, 0.55)
                ),
                url("assets/images/hero.jpg");

            background-size: cover;
            background-position: center;

            display: flex;
            align-items: center;

            color: white;
        }

        .hero-content {
            max-width: 750px;
        }

        .hero-small-title {
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 15px;
            font-weight: 700;
            color: #b9f2d1;
            margin-bottom: 18px;
        }

        .hero h1 {
            font-size: 58px;
            font-weight: 700;
            line-height: 1.08;
            margin-bottom: 25px;
        }

        .hero-description {
            font-size: 19px;
            line-height: 1.7;
            max-width: 650px;
            margin-bottom: 32px;
        }

        .explore-btn {
            display: inline-block;
            background: #20a464;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: 0.2s;
        }

        .explore-btn:hover {
            background: #17834e;
            color: white;
            transform: translateY(-2px);
        }


        /* FEATURES */

        .features {
            padding: 90px 0;
        }

        .section-heading {
            text-align: center;
            margin-bottom: 55px;
        }

        .section-heading h2 {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .section-heading p {
            color: #6b7770;
            font-size: 17px;
        }


        /* CARDS */

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 40px 30px;
            height: 100%;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.07);
            transition: 0.25s;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.12);
        }

        .feature-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #e4f5eb;
            color: #168653;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            font-size: 30px;
        }

        .feature-card h4 {
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .feature-card p {
            color: #6b7770;
            line-height: 1.7;
        }


        /* FOOTER */

        footer {
            background: #173b2b;
            color: white;
            padding: 45px 0;
            text-align: center;
        }

        footer h5 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        footer p {
            color: #cbded4;
            margin-bottom: 8px;
        }


        /* MOBILE */

        @media (max-width: 768px) {

            .hero {
                min-height: 550px;
            }

            .hero h1 {
                font-size: 40px;
            }

            .hero-description {
                font-size: 17px;
            }

            .nav-link {
                margin-left: 0;
            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg main-navbar">

    <div class="container">

        <a class="brand" href="index.php">
            <i class="bi bi-geo-alt-fill"></i>
            Local Tourist Planner
        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>


        <div
            class="collapse navbar-collapse"
            id="mainNavigation"
        >

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="places.php">
                        Explore Places
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="day-plan.php">
                        <i class="bi bi-calendar-check"></i>
                        My Day Plan
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- HERO -->

<section class="hero">

    <div class="container">

        <div class="hero-content">

            <div class="hero-small-title">
                Discover Local Sri Lanka
            </div>

            <h1>
                Explore. Discover.<br>
                Plan Your Perfect Day.
            </h1>

            <p class="hero-description">
                Discover beautiful places around Kurunegala,
                find experiences that match your interests,
                and create your own one-day travel plan.
            </p>

            <a href="places.php" class="explore-btn">
                Explore Places
                <i class="bi bi-arrow-right ms-2"></i>
            </a>

        </div>

    </div>

</section>


<!-- FEATURES -->

<section class="features">

    <div class="container">

        <div class="section-heading">

            <h2>
                Plan Your Local Adventure
            </h2>

            <p>
                Everything you need to discover and organize
                your perfect day visit.
            </p>

        </div>


        <div class="row g-4">


            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-compass"></i>
                    </div>

                    <h4>
                        Discover Places
                    </h4>

                    <p>
                        Explore interesting tourist destinations,
                        nature spots, historical locations and more.
                    </p>

                </div>

            </div>


            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-funnel"></i>
                    </div>

                    <h4>
                        Find Your Interests
                    </h4>

                    <p>
                        Find places based on activities such as
                        photography, hiking, nature and history.
                    </p>

                </div>

            </div>


            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>

                    <h4>
                        Plan Your Day
                    </h4>

                    <p>
                        Select your favourite places and organize
                        them into a simple one-day itinerary.
                    </p>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- FOOTER -->

<footer>

    <div class="container">

        <h5>
            Local Tourist Day-Visit Planner
        </h5>

        <p>
            Discover places. Plan your day. Explore locally.
        </p>

        <small>
            © 2026 Local Tourist Planner
        </small>

    </div>

</footer>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>