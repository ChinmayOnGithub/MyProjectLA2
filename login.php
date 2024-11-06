<?php
session_start(); 
require 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['user_name'] = $user['name']; 
        $_SESSION['user_email'] = $user['email'];

        header("Location: todo.php");
        exit();
    } else {
        echo "<div class='text-red-600'>Invalid email or password.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Welcome Back</h2>
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required class="border border-gray-600 bg-gray-700 text-white p-2 rounded mb-4 w-full">
            <input type="password" name="password" placeholder="Password" required class="border border-gray-600 bg-gray-700 text-white p-2 rounded mb-4 w-full">
            <button type="submit" class="bg-yellow-500 text-black font-bold py-2 rounded w-full">Login</button>
        </form>
        <p class="mt-4 text-center">Don't have an account? <a href="register.php" class="text-yellow-400 hover:underline">Register here</a></p>
    </div>
</body>

</html>
