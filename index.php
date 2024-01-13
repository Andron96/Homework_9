<?php

include "TaskStatus.php";
include "TaskHandler.php";

$tasksList = [];
$tasksHandler = new TasksHandler($tasksList);
$tasksHandler->readFromFile("Tasks.txt");
echo $tasksHandler->showTasks();
while (true) {
    echo "Введите \"1\", что бы добавать задачу\n";
    echo "Введите \"2\", что бы удалить задачу\n";
    echo "Введите \"3\", что бы изменить статус задачи\n";
    echo "Введите \"4\", что бы вывести отсортированный по приоритетности список задач.\n";
    $select = (int) readline("Ввод чего-либо другого прекратит выполнение программы: ");
    switch ($select) {
        case 1:
            $taskPriority = (int) readline("Введите приоритет задачи (от 1 до 15): ");
            $taskName = (string) readline("Введите имя задачи: ");
            echo $tasksHandler->addTask($taskName, $taskPriority);
            $tasksHandler->writeToFile("Tasks.txt");
            echo $tasksHandler->showTasks();
            break;
        case 2:
            $taskId = (int) readline("Введите ID задачи (0 и больше): ");
            echo $tasksHandler->deleteTask($taskId);
            $tasksHandler->writeToFile("Tasks.txt");
            echo $tasksHandler->showTasks();
            break;
        case 3:
            $taskId = (int) readline("Введите ID задачи (0 и больше): ");
            echo $tasksHandler->changeTaskStatus($taskId, readline("Введите статус задачи (\"Выполнено\" или \"Невыполнено\"): "));
            $tasksHandler->writeToFile("Tasks.txt");
            echo $tasksHandler->showTasks();
            break;
        case 4:
            $tasksHandler->getSortByPriorityTasksList();
            break;
        default:
            break 2;
    }
}
var_dump($tasksList);