<?php

require 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password

    // Check if the email is already registered
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // If the email is found, show an error message
    if ($stmt->rowCount() > 0) {
        echo "<div class='text-red-600'>Registration failed: Email is already registered!</div>";
    } else {
        // If the email is not found, proceed with the registration
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            echo "<div class='text-green-600'>Registration successful!</div>";
        } else {
            echo "<div class='text-red-600'>Registration failed!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Create an Account</h2>
        <form method="POST" action="register.php">
            <input type="text" name="name" placeholder="Name" required class="border border-gray-600 bg-gray-700 text-white p-2 rounded mb-4 w-full">
            <input type="email" name="email" placeholder="Email" required class="border border-gray-600 bg-gray-700 text-white p-2 rounded mb-4 w-full">
            <input type="password" name="password" placeholder="Password" required class="border border-gray-600 bg-gray-700 text-white p-2 rounded mb-4 w-full">
            <button type="submit" class="bg-yellow-500 text-black font-bold py-2 rounded w-full">Register</button>
        </form>
        <p class="mt-4 text-center">Already have an account? <a href="login.php" class="text-yellow-400 hover:underline">Login here</a></p>
    </div>
</body>
</html>
