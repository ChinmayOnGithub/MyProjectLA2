<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

// Handle the form submission for adding a new task
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $taskTitle = trim($_POST['title'] ?? '');
            if (!empty($taskTitle)) {
                try {
                    // Prepare the SQL to insert a new task associated with the logged-in user
                    $userId = $_SESSION['user_id']; // Get the logged-in user's ID
                    $sql = "INSERT INTO tasks (user_id, title, checked) VALUES (:user_id, :title, 0)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->bindParam(':title', $taskTitle);
                    $stmt->execute();
                    header("Location: todo.php"); // Redirect back to the main page to show the updated task list
                    exit();
                } catch (PDOException $e) {
                    $errorMessage = "Error adding task: " . $e->getMessage();
                }
            } else {
                $errorMessage = "Task title cannot be empty.";
            }
            break;

        case 'remove':
            $taskId = intval($_POST['id'] ?? 0);
            if ($taskId > 0) {
                try {
                    // Ensure the task belongs to the logged-in user before deletion
                    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
                    $stmt->bindParam(':id', $taskId);
                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                    $stmt->execute();
                    header("Location: todo.php"); // Redirect back to main page
                    exit();
                } catch (PDOException $e) {
                    $errorMessage = "Error removing task: " . $e->getMessage();
                }
            }
            break;

        case 'toggle':
            $taskId = intval($_POST['id'] ?? 0);
            if ($taskId > 0) {
                try {
                    // Toggle the checked status
                    $stmt = $conn->prepare("UPDATE tasks SET checked = NOT checked WHERE id = :id AND user_id = :user_id");
                    $stmt->bindParam(':id', $taskId);
                    $stmt->bindParam(':user_id', $_SESSION['user_id']);
                    $stmt->execute();
                    header("Location: todo.php"); // Redirect back to main page
                    exit();
                } catch (PDOException $e) {
                    $errorMessage = "Error toggling task: " . $e->getMessage();
                }
            }
            break;

        default:
            $errorMessage = "Invalid action.";
            break;
    }
}

// Fetch tasks from the database for the logged-in user
$tasks = $conn->prepare("SELECT id, title, checked, date_time FROM tasks WHERE user_id = :user_id ORDER BY id DESC");
$tasks->bindParam(':user_id', $_SESSION['user_id']);
$tasks->execute();
$tasks = $tasks->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/styles/custom.css">
</head>



<body class="mt-0 bg-gray-900 text-white text-lg">

    <nav class="flex items-center justify-center mt-0 w-full">

        <!-- Centered Title -->
        <h1 class="text-4xl font-bold text-white bg-slate-800 p-4 px-16 mt-0 rounded-bl-xl rounded-br-xl">To~Do List!</h1>

        <!-- User Dropdown -->
        <div class="absolute right-0  inline-block text-left mr-4 ml-4 ">
            <div>
                <button id="userDropdownButton" class="flex items-center px-4 justify-between bg-gray-800 text-white p-2 rounded-md " aria-haspopup="true">
                    <span class="text-lg">
                        Logged in as: <span class="font-bold"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                    </span>
                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 10l5 5 5-5H7z" />
                    </svg>
                </button>
            </div>

            <!-- Dropdown Menu -->
            <div id="userDropdownMenu" class="hidden z-20 mt-2 w-48 bg-gray-800 rounded-md shadow-lg absolute right-0">
                <div class="py-2">
                    <div class="px-4 py-2 text-sm text-gray-400">
                        <?= htmlspecialchars($_SESSION['user_email']) ?>
                    </div>
                    <div class="border-t border-gray-600"></div>
                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-500 hover:bg-gray-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>




    <main class="main-section m-8 max-w-2xl bg-gray-800 rounded-xl mx-auto p-6 shadow-md transition-transform duration-300 
             hover:shadow-2xl hover:opacity-95 
             transform ">

        <div class="add-section mb-6">
            <form action="todo.php" method="POST" autocomplete="off">
                <?php if (!empty($errorMessage)) { ?>
                    <input type="text" name="title" class="border border-red-600 bg-gray-800 text-white p-2 rounded mb-2 w-full  transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="<?= htmlspecialchars($errorMessage) ?>" required />
                <?php } else { ?>
                    <input type="text" name="title" class="border border-gray-600 bg-gray-800 text-white p-2 rounded mb-2 w-full  transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-yellow-500" placeholder="What do you need to do?" required />
                <?php } ?>
                <input type="hidden" name="action" value="add">
                <button type="submit" class="bg-yellow-500 text-black font-bold py-2 px-4 rounded transform transition-transform hover:scale-105">
                    Add &nbsp; <span>&#43;</span>
                </button>
            </form>
        </div>

        <div class="show-todo-section space-y-4">
            <?php if (count($tasks) <= 0) { ?>
                <div class="todo-item text-center w-full flex justify-center items-center" style="height: 200px;">
                    <div class="empty w-full flex flex-col items-center">
                        <img src="assets/images/task-runners-svgrepo-com.svg" width="25%" class="mb-2" />
                        <small class="text-gray-400 text-xl">Add your first task!</small>
                    </div>
                </div>
            <?php } ?>

            <?php foreach ($tasks as $task) { ?>
                <div class="todo-item flex items-center justify-between p-4 bg-gray-900 rounded mb-2 shadow-md transition-transform duration-300 hover:bg-gray-800 hover:shadow-lg hover:opacity-95 hover:border ">
                    <div class="flex items-center flex-grow">
                        <input type="checkbox" class="check-box mr-2" data-task-id="<?= $task['id'] ?>" <?= $task['checked'] ? 'checked' : '' ?> />
                        <h2 class="<?= $task['checked'] ? 'line-through text-gray-400' : '' ?>"><?= htmlspecialchars($task['title']) ?></h2>
                    </div>
                    <small class="text-gray-400">Created: <?= htmlspecialchars($task['date_time']) ?></small>
                    <span class="remove-task text-red-500 cursor-pointer hover:text-red-700 transition ml-4" data-task-id="<?= $task['id'] ?>"><img src="./assets/images/delete-red.svg" width="24" alt="delete"></span>
                </div>
            <?php } ?>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        $(document).ready(function() {
            $('.remove-task').click(function() {
                const id = $(this).data('task-id');
                $.post("todo.php", {
                    action: 'remove',
                    id: id
                }, function() {
                    window.location.reload();
                });
            });

            $('.check-box').click(function() {
                const id = $(this).data('task-id');
                $.post("todo.php", {
                    action: 'toggle',
                    id: id
                }, function() {
                    window.location.reload();
                });
            });
        });


        $(document).ready(function() {
            $('.fade-in').addClass('fade-in-visible');

            $('#userDropdownButton').click(function() {
                const menu = $('#userDropdownMenu');
                menu.toggleClass('hidden');
                menu.css('opacity', 0).animate({
                    opacity: 1
                }, 300);
            });

            $(document).click(function(event) {
                if (!$(event.target).closest('#userDropdownButton').length) {
                    $('#userDropdownMenu').addClass('hidden');
                }
            });
        });
    </script>

</body>

</html>