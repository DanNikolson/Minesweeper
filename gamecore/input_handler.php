<?php
session_start();
require_once 'vendor/connect.php';
class data_handler
{
    public $mode = ""; //Игровой режим new/start/game/game_won/game_over
    public $grid_reference = []; // сюда пихаем координаты клеток х и у, получаем значения вида 1010,1110 и т.д.
    public $cell_values = []; //значения в каждой клетке, только для отображения
    public $mine_cells = []; // Массив клеток с минами в них
    public $visible_cells = []; //массив всех grid_references, все видимые клетки
    public $marked_cells = []; // Массив всех grid_references, которые помечены
    public $difficulty = "easy"; // Сложность сетки: лёгкая, средняя, тяжёлая, кастомная
    public $num_rows; //Количество рядов в сетке
    public $num_columns; //Количество колонок в сетке
    public $num_mines; //Количество мин в сетке
    public $submitted_block; // Клетка,на которую нажали
    public $mark_toggle; // Переключение режима разминирования

    function __construct()
    {

        if (isset($_POST['mode'])) {
            $this->mode = $_POST['mode'];
            if ($this->mode == "game") {
                $this->submitted_block = $_POST['submitted_block'];
                $this->num_rows = $_POST['num_rows'];
                $this->num_columns = $_POST['num_columns'];
                $this->num_mines = $_POST['num_mines'];

                if (!isset($_POST['cell_values'])) {
                    $this->grid_reference = unserialize($_POST['grid_reference']);
                    $this->generate_values();
                    $this->play_game();
                } else {
                    $this->grid_reference = unserialize($_POST['grid_reference']);
                    $this->cell_values = unserialize($_POST['cell_values']);
                    $this->mine_cells = unserialize($_POST['mine_cells']);
                    $this->visible_cells = unserialize($_POST['visible_cells']);
                    $this->marked_cells = unserialize($_POST['marked_cells']);
                    if (isset($_POST['mark_toggle'])) {
                        $this->mark_toggle = $_POST['mark_toggle'];
                    } else {
                        $this->mark_toggle = false;
                    }
                    $this->play_game();
                }
            }
            if ($this->mode == "start") {
                $this->generate_grid();
            }
        } else {
            $this->mode = "new";
        }
    }

    function generate_grid()
    {
        // Задаём число рядов, колонок и мин, основываясь на сложности
        switch ($_POST['difficulty']) {
            case "easy":
                $this->num_rows = "8";
                $this->num_columns = "8";
                $this->num_mines = "10";
                break;
            case "medium":
                $this->num_rows = "16";
                $this->num_columns = "16";
                $this->num_mines = "40";
                break;
            case "hard":
                $this->num_rows = "24";
                $this->num_columns = "24";
                $this->num_mines = "99";
                break;
            case "custom":
                $this->num_rows = $_POST['num_rows'];
                $this->num_columns = $_POST['num_columns'];
                $this->num_mines = $_POST['num_mines'];
                break;
        }
        //генерирование сетки, отправление в массив
        for ($x = 10; $x < ($this->num_rows + 10); $x++) {
            for ($y = 10; $y < ($this->num_columns + 10); $y++) {
                array_push($this->grid_reference, $x . $y);
            }
        }
    }

    function generate_values()
    {
        // Генерирование mine_cells(Массив клеток с минами в них) и cell_values(значения в каждой клетке, только для отображения) - нужно знать, что mine_cells идут до cell_values
        // mine_cells создаются с ипользованием grid_references, исключая нажатую клетку, затем удаляются
        $this->mine_cells = $this->grid_reference;// передаём значение в mine_cells (клетки с минами)
        $key = array_search($this->submitted_block, $this->mine_cells); //ищем значение sumbitted_block в mine_cells
        unset($this->mine_cells[$key]); // удаление mine_cells по ключу выше
        shuffle($this->mine_cells); //перемешиваем мины
        $this->mine_cells = array_values(array_slice($this->mine_cells, 0, $this->num_mines)); //срезаем массив mine_cells от 0 индекса до "количество мин" индекса и возвращаем индексированный массив в mine_cells

        // Вычисляем, как много мин окружает каждую клетку, если клетка не является миной
        // Если ни одной - не задаём значение

        foreach ($this->grid_reference as $cell) {                  
            if (!in_array($cell, $this->mine_cells)) {
                $cells_to_check = [];
                $cells_to_check = $this->get_surrounding_cells($cell);
                $number = count(array_intersect($cells_to_check, $this->mine_cells));
                if ($number > 0) {
                    $this->cell_values[$cell] = $number;
                }
            }
        }
        // В первом круге сделаем отправленный блок видимым
        $this->process_cell($this->submitted_block);
    }

    function play_game()
    {

        if ($this->mark_toggle == true) {
            $this->process_cell($this->submitted_block);
            $this->is_game_won();
            return;
        } else {
            if (!in_array($this->submitted_block, $this->marked_cells)) {
                if (in_array($this->submitted_block, $this->mine_cells)) {
                    $this->click_mine();
                    return;
                } elseif (isset($this->cell_values[$this->submitted_block])) {
                    $this->click_number();
                    return;
                } else {
                    $this->click_blank();
                    return;
                }
            }
        }
    }

    function click_mine()
    {
        //нажатие на мину - конец игры
        $this->game_over();
    }

    function click_number()
    {
        //Если нажать на номер, только ячейки вокруг него станут видимыми
        $this->process_cell($this->submitted_block);
    }

    function click_blank()
    {
        //Когда нажата пустая ячейка - все окружающие пустые ячейки становятся видимыми
        //Повторение каждый раз, когда пустые ячейки становятся видимыми
        //Чтобы уменьшить количество проверок, каждый раз, когда проверяется клетка что она в массиве
        // чтобы она не проверялась ещё раз

        $cells_to_check = $this->get_surrounding_cells($this->submitted_block);
        $cells_checked = [];
        $this->process_cell($this->submitted_block);
        $x = 1;
        while ($x > 0) {
            $x = 0;
            foreach ($cells_to_check as $cell) {
                $this->process_cell($cell);
                //Если клетка пустая и не отмечена, добавляем её в отмеченные клетки
                //добавляем окружающие пустые клетки в массив для того,что бы они стали отмеченными. Увеличиваем х, чтобы была петля
                if ((!isset($this->cell_values[$cell])) && (!in_array($cell, $cells_checked))) {
                    array_push($cells_checked, $cell);
                    $cells_to_check = array_merge($cells_to_check, $this->get_surrounding_cells($cell));
                    array_diff($cells_to_check, array($cell));
                    $x++;
                }
            }
        }
    }

    function game_over()
    {
        //Метод завершения игры.Сделаем все клетки видимыми и выставим режим на 'game_over'
        $this->visible_cells = $this->grid_reference;
        $this->mode = "game_over";
    }

    function get_surrounding_cells($cell)
    {
        //Генерирует массив всех клеток, окружающих отмеченную клетку, убеждаемся, что значения только те, что в массиве ссылок
        //Убираем отмеченные клетки, чтобы они не включались в отмеченную клетку
        $cells_to_check = [];
        array_push($cells_to_check, substr($cell, 0, 2) - 1 . substr($cell, 2, 2) - 1);
        array_push($cells_to_check, substr($cell, 0, 2) - 1 . substr($cell, 2, 2));
        array_push($cells_to_check, substr($cell, 0, 2) - 1 . substr($cell, 2, 2) + 1);
        array_push($cells_to_check, substr($cell, 0, 2) . substr($cell, 2, 2) - 1);
        array_push($cells_to_check, substr($cell, 0, 2) . substr($cell, 2, 2) + 1);
        array_push($cells_to_check, substr($cell, 0, 2) + 1 . substr($cell, 2, 2) - 1);
        array_push($cells_to_check, substr($cell, 0, 2) + 1 . substr($cell, 2, 2));
        array_push($cells_to_check, substr($cell, 0, 2) + 1 . substr($cell, 2, 2) + 1);
        $cells_to_check = array_intersect($cells_to_check, $this->grid_reference);
        $cells_to_check = array_diff($cells_to_check, $this->marked_cells);
        return $cells_to_check;
    }

    function process_cell($cell)
    {
        //общий метод для обработки отметки отправленной клетки
        // Если клетка должна быть отмечена, добавляем её в отмеченные, в другом случае, если отмеченная и тумблер отметки
        // активирован - убираем из отмеченных клеток
        if (($cell == $this->submitted_block) && ($this->mark_toggle == true) && (!in_array($this->submitted_block, $this->marked_cells))) {
            array_push($this->marked_cells, $this->submitted_block);
            return;
        } elseif (($cell == $this->submitted_block) && ($this->mark_toggle == true) && (in_array($this->submitted_block, $this->marked_cells))) {
            $key = array_search($this->submitted_block, $this->marked_cells);
            unset($this->marked_cells[$key]);
            return;
        }

        //Если не в отмеченных клетках, сделаем видмой и проверим, выйграна ли игра
        if (!in_array($cell, $this->marked_cells)) {
            array_push($this->visible_cells, $cell);
            $this->visible_cells = array_unique($this->visible_cells);
            $this->is_game_won();
        }
    }

    function is_game_won()
    {
        //Проверка выйграна ли игра: все клетки - все видимые клетки = клетки с минами
        if ((isset($_POST)) && ((count($this->grid_reference) - count($this->visible_cells)) == count($this->mine_cells))) {
            $this->mode = "game_won";
            global $connect;
            $stmt= $connect->prepare("UPDATE `users` SET `Victory_Count` = `Victory_Count` +1 WHERE `id` = ? ");
            $stmt->bind_param("s", $_SESSION['user']['id']);
            $stmt->execute();
        }
    }
}

//instance input_handler class
$data = new data_handler();
