<?php

session_start();

include "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];


    $sql = "
        SELECT *
        FROM admins
        WHERE username = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();


        if (password_verify($password, $admin["password"])) {
    $_SESSION["admin_id"] = $admin["admin_id"];
    $_SESSION["admin_username"] = $admin["username"];
    header("Location: dashboard.php");
    exit;
}

    } else {

        $error = "Admin account not found.";

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

    <title>Admin Login</title>


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


<div class="container">

    <div
        class="row justify-content-center align-items-center"
        style="min-height:100vh;"
    >

        <div class="col-md-5 col-lg-4">


            <div class="card shadow-sm border-0">

                <div class="card-body p-4">


                    <div class="text-center mb-4">

                        <i
                            class="bi bi-shield-lock-fill text-success"
                            style="font-size:3rem;"
                        ></i>

                        <h3 class="fw-bold mt-2">
                            Admin Login
                        </h3>

                        <p class="text-muted">
                            Local Tourist Planner
                        </p>

                    </div>


                    <?php if (!empty($error)): ?>

                        <div class="alert alert-danger">

                            <i class="bi bi-exclamation-circle"></i>

                            <?= htmlspecialchars($error) ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST">


                        <div class="mb-3">

                            <label class="form-label fw-bold">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label class="form-label fw-bold">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="btn btn-success w-100"
                        >

                            <i class="bi bi-box-arrow-in-right"></i>

                            Login

                        </button>


                    </form>


                    <div class="text-center mt-4">

                        <a
                            href="../index.php"
                            class="text-decoration-none"
                        >

                            <i class="bi bi-arrow-left"></i>

                            Back to Website

                        </a>

                    </div>


                </div>

            </div>


        </div>

    </div>

</div>


</body>

</html>