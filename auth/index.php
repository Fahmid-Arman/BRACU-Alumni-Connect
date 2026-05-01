<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Connect</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="wrapper">
        <form action="/auth/signin.php" method='post'>
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" name="fname" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="pass" placeholder="Password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="/auth/register.php">Register</a></p>
            </div>

        </form>

    </div>
</body>

</html>