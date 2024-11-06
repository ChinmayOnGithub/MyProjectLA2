<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "to~do_list"; 

try {
    // Create connection using PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    echo "
    <div class='absolute left-0 top-0 flex flex-start items-center h-fit bg-gray-900 m-4 rounded-lg'>
        <div class='bg-gray-800 border-l-4 border-green-500 p-4 shadow-lg rounded-lg'>
            <h1 class='text-md font-regular italic'>Connected successfully to the database: <span class='font-bold'>$dbname</span></h1>
        </div>
    </div>";
} catch (PDOException $e) {

    echo "
    <div class='absolute left-0 top-0 flex flex-start items-center h-fit bg-gray-900 m-4 rounded-lg'>
        <div class='bg-gray-800 border-l-4 border-red-500 p-4 shadow-lg rounded-lg'>
            <h1 class='text-md font-regular italic'>Connection failed: <span class='font-bold text-red-600'>" . htmlspecialchars($e->getMessage()) . "</span></h1>
        </div>
    </div>";
    exit();
}
