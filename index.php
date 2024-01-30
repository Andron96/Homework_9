<?php

require_once "TaskStatus.php";
require_once "TaskHandler.php";

$tasksHandler = new TasksHandler("Tasks.txt");                                                  //создаем экземплям класса для работы с списком задач
$tasksHandler->showTasks();                                                                             //выводим список задач из файла в консоль
while (true) {                                                                                          //выбираем что мы хотим сделать
    echo "Введите \"1\", что бы добавать задачу\n";
    echo "Введите \"2\", что бы удалить задачу\n";
    echo "Введите \"3\", что бы изменить статус задачи\n";
    echo "Введите \"4\", что бы вывести отсортированный по приоритетности список задач.\n";
    $select = (int) readline("Ввод чего-либо другого прекратит выполнение программы: ");         //выдор в виде цифры явно типизуем как инт и записываем в переменную
    switch ($select) {
        case 1:                                                                                         //если мы выбрали добавлять задачу, то...
            $taskPriority = (int) readline("Введите приоритет задачи (от 1 до 15): ");           //...запрашиваем ввод приоритета задачи и имя задачи
            $taskName = (string) readline("Введите имя задачи: ");
            try {                                                                                       //далее вызываем методы добавления задачи и записи в файл с отлавливанием исключений
                echo $tasksHandler->addTask($taskName, $taskPriority);
                $tasksHandler->writeToFile("Tasks.txt");
            } catch (Exception $e) {
                echo $e->getMessage();                                                                  //при наличии исключения выводим его сообщение
            }
            $tasksHandler->showTasks();                                                                 //выводим получившийся список задач в консоль
            break;
        case 2:                                                                                         //если мы выбрали удалять задачу, то...
            $taskId = (int) readline("Введите ID задачи (0 и больше): ");                        //запрашиваем ввод айди задачи
            try {                                                                                       //вызываем методы удаления задачи и записи в файл с отлавливанием исклбючений
                echo $tasksHandler->deleteTask($taskId);
                $tasksHandler->writeToFile("Tasks.txt");
            } catch (Exception $e) {
                echo $e->getMessage();                                                                  //при наличии исключения выводим его сообщение
            }
            $tasksHandler->showTasks();                                                                 //выводим получившийся список задач в консоль
            break;
        case 3:
            $taskId = (int) readline("Введите ID задачи (0 и больше): ");                        //если мы выбрали изменить статус задачи, то...
            try {                                                                                       //вызываем метод изменения статуса задачи и записи в файл с отлавливанием исключений
                echo $tasksHandler->changeTaskStatus($taskId, readline("Введите статус задачи (\"Выполнено\" или \"Невыполнено\"): "));
                $tasksHandler->writeToFile("Tasks.txt");
            } catch (Exception $e) {
                echo $e->getMessage();                                                                  //при наличии исключения выводим его сообщение
            }
            $tasksHandler->showTasks();                                                                 //выводим получившийся список задач в консоль
            break;
        case 4:                                                                                         //если мы выбрали получить отсортированный список задач, то...
            $tasksHandler->getSortByPriorityTasksList();                                                //вызываем метод сортировки списка задач (получаем новый список, старый не меняется)
            break;
        default:
            break 2;
    }
}
var_dump($tasksHandler->getTaskListArr());