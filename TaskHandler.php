<?php
class TasksHandler
{
    private array $tasksList = [];
    private static int $maxTasks = 1000;
    function __construct(string $fileName)
    {
        $this->readFromFile($fileName);
    }
    private function setTaskListArr(array $resultArr): void
    {
        $this->tasksList = $resultArr;
    }
    private function setNewElOfTaskListArr(array $newEl): void
    {
        $this->tasksList[] = $newEl;
    }
    private function unsetElOfTaskListArr(int $key): void
    {
        unset($this->tasksList[$key]);
    }
    private function validateTaskPriority($taskPriority): bool
    {
        if (
            ($taskPriority >= 1)
            && ($taskPriority <= 15)
            && (count($this->getTaskListArr()) <= self::$maxTasks)
        ) {
            return true;
        }
        return false;
    }
    public function getTaskListArr(): array
    {
        return $this->tasksList;
    }
    public function writeToFile(string $filename): string
    {
        $wData = "";
        foreach ($this->getTaskListArr() as $key => $item) {
            $wData = $wData .
                "ID задачи: " .
                $item['Task ID'] .
                "; Приоритет задачи: " .
                $item['Task priority'] .
                "; Имя задачи: " .
                $item['Task name'] .
                "; Статус задачи: " .
                $item['Task status']->value;
            if ($key !== array_key_last($this->getTaskListArr())) {
                $wData .= ".\n";
            } else {
                $wData .= ".";
            }
        }
        $wResult = file_put_contents($filename, $wData);
        if ($wResult > 0) {
            return "Данные успешно записаны.\n";
        }
        throw new Exception("Данные записаны неуспешно.\n");
    }
    public function readFromFile(string $fileName): void
    {
        $resultRArray = [];
        if (file_exists($fileName)) {
            $rData = file_get_contents($fileName);
            if ($rData !== "") {
                $tempRArray = explode("\n", $rData);
                foreach ($tempRArray as $key => $item) {
                    $item = rtrim($item, ".");
                    $item = str_replace("ID задачи: ", "", $item);
                    $item = str_replace(" Приоритет задачи: ", "", $item);
                    $item = str_replace(" Имя задачи: ", "", $item);
                    $item = str_replace(" Статус задачи: ", "", $item);
                    $tempRArray[$key] = explode(";", $item);
                }
                foreach ($tempRArray as $key => $item) {
                    $resultRArray[$key] = [
                        'Task ID' => (int) $item[0],
                        'Task priority' => (int) $item[1],
                        'Task name' => $item[2],
                        'Task status' => TaskStatus::from($item[3])
                    ];
                }
                $this->setTaskListArr($resultRArray);
            }
        } else {
            echo "Файл со списком задач не найден.\n";
        }
    }
    public function addTask(string $taskName, int $taskPriority): string
    {
        if ($this->validateTaskPriority($taskPriority)) {
            $taskId = 0;
            if ($this->getTaskListArr()) {
                while ($taskId <= self::$maxTasks) {
                    foreach ($this->getTaskListArr() as $key => $item) {
                        if ($item['Task ID'] === $taskId) {
                            break;
                        }
                        if ($key === array_key_last($this->getTaskListArr())) {
                            break 2;
                        }
                    }
                    $taskId++;
                }
            }
            $this->setNewElOfTaskListArr([
                'Task ID' => $taskId,
                'Task priority' => $taskPriority,
                'Task name' => $taskName,
                'Task status' => TaskStatus::Undone
            ]);
            return "Задача добавлена.\n";
        }
        throw new Exception("Задача не добавлена или из-за неправильного значения приоритета, или из-за достижения лимита задач.\n");
    }
    public function deleteTask(int $taskId): string
    {
//        if ($this->getTaskListArr()) {
//            if ($taskId >= 0) {
//                foreach ($this->getTaskListArr() as $key => $item) {
//                    if ($item['Task ID'] === $taskId) {
//                        $this->unsetElOfTaskListArr($key);
//                        $this->setTaskListArr(array_values($this->getTaskListArr()));
//                        return "Задача удалена.\n";
//                    }
//                }
//                throw new Exception("Задача c введенным ID не найдена.\n");
//            }
//            throw new Exception("Задача не удалена из-за неправильно введенного ID.\n");
//        }
//        throw new Exception("Список задач пуст. Удалять нечего.\n");

        if (($this->getTaskListArr()) && ($taskId < 0)) {
            throw new Exception("Задача не удалена из-за неправильно введенного ID.\n");
        }
        if (($this->getTaskListArr()) && ($taskId >= 0)) {
            foreach ($this->getTaskListArr() as $key => $item) {
                if ($item['Task ID'] === $taskId) {
                    $this->unsetElOfTaskListArr($key);
                    $this->setTaskListArr(array_values($this->getTaskListArr()));
                    return "Задача удалена.\n";
                }
            }
            throw new Exception("Задача c введенным ID не найдена.\n");
        }
        throw new Exception("Список задач пуст. Удалять нечего.\n");
    }
    public function showTasks(): array
    {
        if (!$this->getTaskListArr()) {
            echo "*****\nСписок задач пуст.\n*****\n";
            return $this->getTaskListArr();
        }
        echo "*****\nНачало списка задач.\n";
        foreach ($this->getTaskListArr() as $item) {
            echo "ID задачи: " .
                $item['Task ID'] .
                "; Приоритет задачи: " .
                $item['Task priority'] .
                "; Имя задачи: " .
                $item['Task name'] .
                "; Статус задачи: " .
                $item['Task status']->value .
                "." .
                "\n";
        }
        echo "Конец списка задач.\n*****\n";
        return $this->getTaskListArr();
    }
    public function getSortByPriorityTasksList(): array
    {
        $sortedTasksList = [];
        if ($this->getTaskListArr()) {
            for ($priority = 15; $priority >= 1; $priority--) {
                foreach ($this->getTaskListArr() as $item) {
                    if ($item['Task priority'] === $priority) {
                        $sortedTasksList[] = $item;
                    }

                }
            }
            echo "*****\nНачало отсортированного списка задач.\n";
            foreach ($sortedTasksList as $item) {
                echo "ID задачи: " .
                    $item['Task ID'] .
                    "; Приоритет задачи: " .
                    $item['Task priority'] .
                    "; Имя задачи: " .
                    $item['Task name'] .
                    "; Статус задачи: " .
                    $item['Task status']->value .
                    "." .
                    "\n";
            }
            echo "Конец отсортированного списка задач.\n*****\n";
        } else {
            echo "Список задач пуст. Сортировать нечего.\n";
        }
        return $sortedTasksList;
    }
    public function changeTaskStatus(int $taskId, $taskStatus): string
    {
//        if ($this->getTaskListArr()) {
//            if ($taskId >= 0) {
//                $taskStatus = TaskStatus::tryFrom($taskStatus);
//                if ($taskStatus === null) {
//                    throw new Exception("Вы ввели неверное значение статуса задачи. Статус задачи остался прежним.\n");
//                }
//                foreach ($this->getTaskListArr() as $key => $item) {
//                    if ($item['Task ID'] === $taskId) {
//                        $this->tasksList[$key]['Task status'] = $taskStatus;
//                        return "Статус задачи изменен.\n";
//                    }
//                }
//                throw new Exception("Зачада с введенным ID не найдена.\n");
//            }
//            throw new Exception("Вы ввели неправильное значение ID задачи. Статусы задач остались прежними.\n");
//        }
//        throw new Exception("Список задач пуст. Изменять нечего.\n");

        if (($this->getTaskListArr()) && ($taskId < 0)) {
            throw new Exception("Вы ввели неправильное значение ID задачи. Статусы задач остались прежними.\n");
        }
        if (($this->getTaskListArr()) && ($taskId >= 0)) {
            $taskStatus = TaskStatus::tryFrom($taskStatus);
            if ($taskStatus === null) {
                throw new Exception("Вы ввели неверное значение статуса задачи. Статус задачи остался прежним.\n");
            }
            foreach ($this->getTaskListArr() as $key => $item) {
                if ($item['Task ID'] === $taskId) {
                    $this->tasksList[$key]['Task status'] = $taskStatus;
                    return "Статус задачи изменен.\n";
                }
            }
            throw new Exception("Зачада с введенным ID не найдена.\n");
        }
        throw new Exception("Список задач пуст. Изменять нечего.\n");
    }
}
