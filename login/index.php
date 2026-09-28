<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Cyber Threat Intelligence Dashboard</title>

    <!-- Bootstrap -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Font Awesome -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          rel="stylesheet">

    <!-- Custom CSS -->

    <link rel="stylesheet"
          href="../assets/css/login.css">

</head>

<body>

<div class="background-overlay"></div>

<div class="login-container">

    <div class="login-card">

        <div class="logo">

            <i class="fa-solid fa-shield-halved"></i>

        </div>

        <h2>Cyber Threat Intelligence</h2>

        <p class="subtitle">
            Secure Monitoring Dashboard
        </p>

        <?php

        session_start();

        if(isset($_SESSION['error']))
        {
            echo '
            <div class="alert alert-danger text-center">
            '.$_SESSION['error'].'
            </div>';

            unset($_SESSION['error']);
        }

        ?>

        <form action="login_process.php"
              method="POST">

            <div class="mb-3">

                <label class="form-label">

                    Username

                </label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="fa fa-user"></i>

                    </span>

                    <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Enter Username"
                    required>

                </div>

            </div>

            <div class="mb-4">

                <label class="form-label">

                    Password

                </label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="fa fa-lock"></i>

                    </span>

                    <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Enter Password"
                    required>

                    <button
                    type="button"
                    class="btn btn-outline-secondary"
                    onclick="togglePassword()">

                        <i class="fa fa-eye"
                           id="eye"></i>

                    </button>

                </div>

            </div>

            <button
            type="submit"
            class="btn login-btn">

                <i class="fa-solid fa-right-to-bracket"></i>

                Login

            </button>

        </form>

    </div>

</div>

<script>

function togglePassword()
{

    let password =
        document.getElementById("password");

    let eye =
        document.getElementById("eye");

    if(password.type==="password")
    {
        password.type="text";

        eye.classList.remove("fa-eye");

        eye.classList.add("fa-eye-slash");
    }
    else
    {
        password.type="password";

        eye.classList.remove("fa-eye-slash");

        eye.classList.add("fa-eye");
    }

}

</script>

</body>

</html>