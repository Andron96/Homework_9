<?php
class TasksHandler
{
    //массив со списком задач:
    private array $tasksList = [];
    //максимальное количество задач в списке:
    private static int $maxTasks = 1000;
    //конструктор класса:
    function __construct(string $fileName)
    {
        $this->readFromFile($fileName);                 //выполнение чтения из файла в конструкторе класса
    }
    //метод для переопредиления массива со списком задач:
    private function setTaskListArr(array $resultArr): void
    {
        $this->tasksList = $resultArr;
    }
    //метод для добавления нового элемента в массив со списком задач:
    private function setNewElOfTaskListArr(array $newEl): void
    {
        $this->tasksList[] = $newEl;
    }
    //метод для удаления элемента из массива со списком задач:
    private function unsetElOfTaskListArr(int $key): void
    {
        unset($this->tasksList[$key]);
    }
    //метод для проверки условий по приоритету задачи и количества задач в массиве:
    private function validateTaskPriority($taskPriority): bool
    {
        if (                                                                    //если приоритет задачи >= 1 и <= 15 и количество <= максимально возможному, то...
            ($taskPriority >= 1)
            && ($taskPriority <= 15)
            && (count($this->getTaskListArr()) <= self::$maxTasks)
        ) {
            return true;                                                        //...валидация пройдена...
        }
        return false;                                                           //...в противном случае валидация не пройдена
    }
    //метод для получения массива со списком задач в "сыром" виде:
    public function getTaskListArr(): array
    {
        return $this->tasksList;
    }
    //метод для записи списка задач в файл:
    public function writeToFile(string $filename): string
    {
        $wData = "";                                                            //иницилизируем строковую переменную в которой будем хранить список задач в "обработанном" виде
        foreach ($this->getTaskListArr() as $key => $item) {                    //каждый элемент массива со списком задач (с дополнительнымы пояснениями) добавляем к одной и то й же строковой переменной
            $wData = $wData .
                "ID задачи: " .
                $item['Task ID'] .
                "; Приоритет задачи: " .
                $item['Task priority'] .
                "; Имя задачи: " .
                $item['Task name'] .
                "; Статус задачи: " .
                $item['Task status']->value;
            if ($key !== array_key_last($this->getTaskListArr())) {             //если это не последний элемент массива (задача), то в конце задачи ставим точку с переводом строки
                $wData .= ".\n";
            } else {
                $wData .= ".";                                                  //если это последний элемент массива (задача), то в конце задачи ставим просто точку.
            }
        }
        $wResult = file_put_contents($filename, $wData);                        //строковую переменную со с "обработанным" спском задач записываем в файл
        if ($wResult > 0) {                                                     //если данные записались в файл успешно, то возвращаем текст про успех
            return "Данные успешно записаны.\n";
        }
        throw new Exception("Данные записаны неуспешно.\n");            //если данные не были записканы успешно, то выбрасываем исключение про не успех
    }
    //метод для чтения списка задач из файла:
    public function readFromFile(string $fileName): void
    {
        $resultRArray = [];                                                                     //инициализируем массив в котором временно будем хранить массив задач в правильном виде
        if (file_exists($fileName)) {                                                           //если файл со списком задач существует, то...
            $rData = file_get_contents($fileName);                                              //...записываем содержимое файла в временную текстовую переменную
            if ($rData !== "") {                                                                //...если при этом переменная не пуста (файл не был пустым), то...
                $tempRArray = explode("\n", $rData);                                    //...для начала разбиваем весь прочитанный текст на куски по символу перевода строки и ложим эти куски во временный массив
                foreach ($tempRArray as $key => $item) {                                        //далее для каждого получившегося элемента данного временного массива...
                    $item = rtrim($item, ".");                                         //...удаляем точку в конце...
                    $item = str_replace("ID задачи: ", "", $item);                 //...заменяем текст на "ничего"...
                    $item = str_replace(" Приоритет задачи: ", "", $item);
                    $item = str_replace(" Имя задачи: ", "", $item);
                    $item = str_replace(" Статус задачи: ", "", $item);
                    $tempRArray[$key] = explode(";", $item);                            //далее каждый элемент данного массива превращаем в массив из строк, которые поручились в результате разбиения по символу ";"
                }
                foreach ($tempRArray as $key => $item) {                                        //каждый элемента получившегося массива (массивы в массиве) преобразуем в нужный тип данных и записываем в результирующий массив
                    $resultRArray[$key] = [
                        'Task ID' => (int) $item[0],
                        'Task priority' => (int) $item[1],
                        'Task name' => $item[2],
                        'Task status' => TaskStatus::from($item[3])
                    ];
                }
                $this->setTaskListArr($resultRArray);                                           //а результирующий массив в свою очередь записываем в основной массив со списком задач
            }
        } else {                                                                                //если же файла не соществует, то выводим соответствующее сообщение
            echo "Файл со списком задач не найден.\n";
        }
    }
    //метод для добавления задач в список:
    public function addTask(string $taskName, int $taskPriority): string
    {
        if ($this->validateTaskPriority($taskPriority)) {                               //если проверка на валидность по приоритету и количество задач пройдена, то...
            $taskId = 0;                                                                //...для начала инициализируем переменную с ID задачи нулем
            if ($this->getTaskListArr()) {                                              //если в списке задач есть хоть одна задача, то...
                while ($taskId <= self::$maxTasks) {                                    //начинаем подбор ID задачи по принципу "от 0 и выше с исключением вероятности пропуска ID", для чего...
                    foreach ($this->getTaskListArr() as $key => $item) {                //...для каждой существующей задачи в списке...
                        if ($item['Task ID'] === $taskId) {                             //...проверяем для начала наличие ID со значением "0"...
                            break;                                                      //...и если такое ID найдено, то прекращаем цикл и увеличиваем значение ID на "1" (смотри ниже по коду) (далее все тоже самое только уже не с "0", а с "1")
                        }
                        if ($key === array_key_last($this->getTaskListArr())) {         //...если ID со значением "0" не найдено в предыдущем условии и если текущий проверяемый элемент является последним в существующем списке задач, то...
                            break 2;                                                    //...выходим из подбора со значением ID задачи "0"
                        }
                    }
                    $taskId++;                                                          //увеличение на "1" ID задачи в случае, если ID со значением "текущий проверяемый ID ($taskId)" найден в списке существующих задач
                }
            }
            $this->setNewElOfTaskListArr([                                              //добавление новой задачи с введенным параметрами и подобранным ID
                'Task ID' => $taskId,
                'Task priority' => $taskPriority,
                'Task name' => $taskName,
                'Task status' => TaskStatus::Undone
            ]);
            return "Задача добавлена.\n";
        }
        throw new Exception("Задача не добавлена или из-за неправильного значения приоритета, или из-за достижения лимита задач.\n");       //если проверка на валидность по приоритету и количество задач не пройдена, то выбрасываем соответствующее сообщение
    }
    //метод для удаления задачи из списка:
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

        if (($this->getTaskListArr()) && ($taskId < 0)) {                                               //если в массиве со списком задач есть хотя бы одна задача и если введенной ID задачи для удаления < 0, то выбрасываем исключение с сообщением
            throw new Exception("Задача не удалена из-за неправильно введенного ID.\n");
        }
        if (($this->getTaskListArr()) && ($taskId >= 0)) {                                      //если в массиве со списком задач есть хотя бы одна задача и если введенной ID задачи для удаления >= 0, то...
            foreach ($this->getTaskListArr() as $key => $item) {                                //...каждую задачу в массиве проверяем на совпадение с введенным ID
                if ($item['Task ID'] === $taskId) {                                             //...если введенное ID найдено в списке задач, то...
                    $this->unsetElOfTaskListArr($key);                                          //...удаляем задачу с этим ID...
                    $this->setTaskListArr(array_values($this->getTaskListArr()));               //...выполняем новую индексацию массива со списком задач
                    return "Задача удалена.\n";                                                 //выводим сообщение об успехе
                }
            }
            throw new Exception("Задача c введенным ID не найдена.\n");                 //если мы так и не нашли задачу с введенным ID, то выбрасываем исключение с сообщением
        }
        throw new Exception("Список задач пуст. Удалять нечего.\n");                    //если в массиве нет ни одной задачи, то выводим исключение с сообщением
    }
    //метод для отображения списка задач в консоли:
    public function showTasks(): array
    {
        if (!$this->getTaskListArr()) {                     //если список задач пуст, то просто возвращаем этот пустой список задач и выводим соответствующее сообщение
            echo "*****\nСписок задач пуст.\n*****\n";
            return $this->getTaskListArr();
        }
        echo "*****\nНачало списка задач.\n";               //если список задач не пуст, то...
        foreach ($this->getTaskListArr() as $item) {        //начинаем выводить по очередно отформатированно все задачи в консоль...
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
        return $this->getTaskListArr();                     //...и так же возвращаем этот список задач
    }
    //метод для сортировки списка задач по приоритетности и вывода его в консоль:
    public function getSortByPriorityTasksList(): array
    {
        $sortedTasksList = [];                                              //инициализируем массив в котором будем хранить отсортированный список задач
        if ($this->getTaskListArr()) {                                      //если список задач не пустой, то...
            for ($priority = 15; $priority >= 1; $priority--) {
                foreach ($this->getTaskListArr() as $item) {                //...проверяем каждую задачу на приоритетность от 15 до 1...
                    if ($item['Task priority'] === $priority) {             //...и записываем элемент с искомой в данном цикле приоритетностью в инициализированный раннеее массив
                        $sortedTasksList[] = $item;
                    }
                }
            }
            echo "*****\nНачало отсортированного списка задач.\n";          //и выводим каждый задачу из результирующего массивам в отформатированом виде в консоль
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
        } else {                                                            //если список задач пустой, то выводим соответствующее сообщение
            echo "Список задач пуст. Сортировать нечего.\n";
        }
        return $sortedTasksList;                                            //в завершение возвращаем отсортированный массив с задачами
    }
    //метод для изменения статуса задачи:
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

        if (($this->getTaskListArr()) && ($taskId < 0)) {                                                                       //если список не пустой и введено неверное значение ID задачи, то...
            throw new Exception("Вы ввели неправильное значение ID задачи. Статусы задач остались прежними.\n");        //выбросить исключение с сообщением
        }
        if (($this->getTaskListArr()) && ($taskId >= 0)) {                                                                      //если список не пустой и введено верное значение ID задачи, то...
            $taskStatus = TaskStatus::tryFrom($taskStatus);                                                                     //пытаемся введенный статус сопоставить с вариантом перечисления "TaskStatus" и записать данный вариант перечисления в переменную
            if ($taskStatus === null) {                                                                                         //если так и не удалось сопоставить введенный статус с вариантом перечисления "TaskStatus" - выбрасываем исключение с сообщением
                throw new Exception("Вы ввели неверное значение статуса задачи. Статус задачи остался прежним.\n");
            }
            foreach ($this->getTaskListArr() as $key => $item) {                                    //если удалось сопоставить введенный статус с вариантом перечисления "TaskStatus", то ищем задачу с введенным айди
                if ($item['Task ID'] === $taskId) {                                                 //если задача с введенным айди найдена, то...
                    $this->tasksList[$key]['Task status'] = $taskStatus;                            //для данной задачи меняем статус на введенный...
                    return "Статус задачи изменен.\n";                                              //и возвращаем сообщение об успехе
                }
            }
            throw new Exception("Зачада с введенным ID не найдена.\n");                     //если не удалось найти задачу с введенный айди, то выбрасываем исключени с сообщением
        }
        throw new Exception("Список задач пуст. Изменять нечего.\n");                       //если список задач пуст, то выбрасываем исключение с сообщением
    }
}
