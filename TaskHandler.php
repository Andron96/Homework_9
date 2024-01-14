<?php
class TasksHandler
{
    public array $tasksList = [];
    private static int $maxTasks = 1000;
    function __construct(string $filename)
    {
        try {
            $resultRArray = [];
            if (file_exists($filename)) {
                $rData = file_get_contents($filename);
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
                    $this->tasksList = $resultRArray;
                }
            } else {
                throw new Exception("Файл со списком задач не найден.\n");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    public function addTask(string $taskName, int $taskPriority): string
    {
        try {
            if (
                ($taskPriority >= 1)
                && ($taskPriority <= 15)
                && (count($this->tasksList) <= self::$maxTasks)
            ) {
                $taskId = 0;
                if ($this->tasksList) {
                    while ($taskId <= self::$maxTasks) {
                        foreach ($this->tasksList as $key => $item) {
                            if ($item['Task ID'] === $taskId) {
                                break;
                            }
                            if ($key === array_key_last($this->tasksList)) {
                                break 2;
                            }
                        }
                        $taskId++;
                    }
                }
                $this->tasksList[] = [
                    'Task ID' => $taskId,
                    'Task priority' => $taskPriority,
                    'Task name' => $taskName,
                    'Task status' => TaskStatus::Undone
                ];
                return "Задача добавлена.\n";
            }
            throw new Exception("Задача не добавлена или из-за неправильного значения приоритета, или из-за достижения лимита задач.\n");
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function deleteTask(int $taskId): string
    {
        try {
            if ($this->tasksList) {
                if ($taskId >= 0) {
                    foreach ($this->tasksList as $key => $item) {
                        if ($item['Task ID'] === $taskId) {
                            unset($this->tasksList[$key]);
                            $this->tasksList = array_values($this->tasksList);
                            return "Задача удалена.\n";
                        }
                    }
                    throw new TaskNotFoundException("Задача c введенным ID не найдена.\n");
                }
                throw new TaskNotDeletedException("Задача не удалена из-за неправильно введенного ID.\n");
            }
            throw new NothingToDeleteException("Список задач пуст. Удалять нечего.\n");
        } catch (TaskNotFoundException | TaskNotDeletedException | NothingToDeleteException $e) {
            return $e->getMessage();
        }
    }
    public function showTasks(): string
    {
        try {
            if (!$this->tasksList) {
                throw new Exception("*****\nСписок задач пуст.\n*****\n");
            }
            echo "*****\nНачало списка задач.\n";
            foreach ($this->tasksList as $item) {
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
            return "Конец списка задач.\n*****\n";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function getSortByPriorityTasksList(): array
    {
        $sortedTasksList = [];
        if ($this->tasksList) {
            for ($priority = 15; $priority >= 1; $priority--) {
                foreach ($this->tasksList as $item) {
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
    public function writeToFile(string $filename): string
    {
        $wData = "";
        foreach ($this->tasksList as $key => $item) {
            $wData = $wData .
                "ID задачи: " .
                $item['Task ID'] .
                "; Приоритет задачи: " .
                $item['Task priority'] .
                "; Имя задачи: " .
                $item['Task name'] .
                "; Статус задачи: " .
                $item['Task status']->value;
            if ($key !== array_key_last($this->tasksList)) {
                $wData .= ".\n";
            } else {
                $wData .= ".";
            }
        }
        $wResult = file_put_contents($filename, $wData);
        try {
            if ($wResult > 0) {
                return "Данные успешно записаны.\n";
            } else {
                throw new Exception("Данные записаны неуспешно.\n");
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function changeTaskStatus(int $taskId, $taskStatus): string
    {
        try {
            if ($this->tasksList) {
                if ($taskId >= 0) {
                    $taskStatus = TaskStatus::tryFrom($taskStatus);
                    if ($taskStatus === null) {
                        throw new TaskNotChangedException("Вы ввели неверное значение статуса задачи. Статус задачи остался прежним.\n");
                    }
                    foreach ($this->tasksList as $key => $item) {
                        if ($item['Task ID'] === $taskId) {
                            $this->tasksList[$key]['Task status'] = $taskStatus;
                            return "Статус задачи изменен.\n";
                        }
                    }
                    throw new TaskNotFoundException("Зачада с введенным ID не найдена.\n");
                }
                throw new TaskNotChangedException("Вы ввели неправильное значение ID задачи. Статусы задач остались прежними.\n");
            }
            throw new NothingToChangeException("Список задач пуст. Изменять нечего.\n");
        } catch (TaskNotFoundException | TaskNotChangedException | NothingToChangeException $e) {
            return $e->getMessage();
        }
    }
    public function getTaskListArr(): array
    {
        return $this->tasksList;
    }
}
