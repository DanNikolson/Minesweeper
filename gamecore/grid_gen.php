<?php

class grid_gen
{
    public $table_html = "";
    public $pre_table = "";
    public $post_table = "";
    public $color = [
        1 => "blue",
        2 => "green",
        3 => "red",
        4 => "purple",
        5 => "brown",
        6 => "pink",
        7 => "yellow",
        8 => "red"
    ];

    function form_content()
    {
        global $data;

        if (($data->mode == "game") || ($data->mode == "new") || ($data->mode == "start")) {
            $this->pre_table .= "<body class='minesweeper'><link rel='stylesheet' href='./css/styles.css'><form action ='/minesweeper.php' method='post' class = 'minesweeper'>\n";
        } else {
            $this->post_table .= "<body class='minesweeper'><link rel='stylesheet' href='./css/styles.css'><a href ='http://minesweeper.loc/minesweeper.php'> Новая игра</a><br><a href ='http://minesweeper.loc/profile.php'>Назад в профиль</a><br>";
        }

        if ($data->mode == "new") {
            $this->pre_table .= " Выберите сложность: <select name = 'difficulty'>\n";
            $this->pre_table .= " <option value='easy'>Easy</option>\n";
            $this->pre_table .= " <option value='medium'>Medium</option>\n";
            $this->pre_table .= " <option value='hard'>Hard</option>\n";
            $this->pre_table .= " <option value='custom'>Custom</option>\n";
            $this->pre_table .= "</select>\n";
            $this->pre_table .= "<br>Кастомные настройки<br>";
            $this->pre_table .= "Ряды: <select name = 'num_rows'>\n";
            $this->options_builder(50);
            $this->pre_table .= "</select>\n";
            $this->pre_table .= "Колонки: <select name = 'num_columns'>\n";
            $this->options_builder(50);
            $this->pre_table .= "</select>\n";
            $this->pre_table .= "Мины: <select name = 'num_mines'>\n";
            $this->options_builder(50);
            $this->pre_table .= "</select>\n";
            $this->pre_table .= "<br><input type='submit' name='mode' value='start'>";
        }

        if ($data->mode == "start" || $data->mode == "game") {
            $this->pre_table .= "<input type='hidden' name='grid_reference' value='" . htmlspecialchars(serialize($data->grid_reference)) . "'>\n";
            $this->pre_table .= "<input type='hidden' name='num_columns' value='" . $data->num_columns . "'>\n";
            $this->pre_table .= "<input type='hidden' name='num_rows' value='" . $data->num_rows . "'>\n";
            $this->pre_table .= "<input type='hidden' name='num_mines' value='" . $data->num_mines . "'>\n";
            $this->pre_table .= "Mines:" . ($data->num_mines - count($data->marked_cells)) . "<br>\n";
        }


        if ($data->mode == "game") {
            $this->pre_table .= "<input type='hidden' name='mode' value='game'>";
            $this->pre_table .= "<input type='hidden' name='cell_values' value='" . htmlspecialchars(serialize($data->cell_values)) . "'>\n";
            $this->pre_table .= "<input type='hidden' name='mine_cells' value='" . htmlspecialchars(serialize($data->mine_cells)) . "'>\n";
            $this->pre_table .= "<input type='hidden' name='visible_cells' value='" . htmlspecialchars(serialize($data->visible_cells)) . "'>\n";
            $this->pre_table .= "<input type='hidden' name='marked_cells' value='" . htmlspecialchars(serialize($data->marked_cells)) . "'>\n";
            $this->pre_table .= "Переключение режима разминирования <input type='checkbox' name='mark_toggle'";
            if ($data->mark_toggle == true) {
                $this->pre_table .= " checked='checked'";
            }
            $this->pre_table .= ">\n";
        }

        if ($data->mode == "start") {
            $this->pre_table .= "<input type='hidden' name='mode' value='game'>";
            $this->pre_table .= "Переключение режима разминирования\n";
        }

        if ($data->mode != "game_won") {
            $this->post_table .= "</form>\n";
        }
        if ($data->mode == "game_over") {
            $this->pre_table .= "Вы взорваны &#128557;\n";
        }
        if ($data->mode == "game_won") {
            $this->pre_table .= "Поздравляю, вы победили!</br><div class='victory'> </div>";
        }
    }

    function options_builder($number)
    {
        for ($x = 8; $x <= $number; $x++) {
            $this->pre_table .= " <option value='$x'>$x</option>\n";
        }
    }

    function create_table()
    {
        global $data;
        $this->table_html .= "<table>\n";
        for ($x = 10; $x < ($data->num_rows + 10); $x++) {
            $this->table_html .= "<tr>\n";
            for ($y = 10; $y < ($data->num_columns + 10); $y++) {
                $block = $x . $y;
                if (($data->mode == "game_over") && ($data->submitted_block == $block)) {
                    $extra = "style='background-image: url(css/мина.png)'";
                } else {
                    $extra = "";
                }
                $this->table_html .= "<td $extra>";
                $this->cell_content($block);
                $this->table_html .= "</td>\n";
            }
            $this->table_html .= "</tr>\n";
        }
        $this->table_html .= "</table>\n";
    }

    function cell_content($block)
    {
        global $data;
        if ($data->mode == "start") {
            $this->table_html .= "<input type='hidden' name='mode' value='game'><input type='submit' name='submitted_block' class='cell' value='" . $block . "' style='height:18px; width=18px; text-indent:-9999px' />";
        } else {
            if (in_array($block, $data->visible_cells)) {
                if (array_key_exists($block, $data->cell_values)) {
                    $this->color_number($data->cell_values[$block]);
                } else {
                    if (in_array($block, $data->mine_cells)) {
                        $this->table_html .= "<div class='mine'>.</div>";
                    } else {
                        $this->table_html .= "";
                    }
                }
            } else {
                $this->table_html .= "<input type='submit' name='submitted_block' class='cell' value='" . $block . "' style=' text-indent:-9999px";
                if (in_array($block, $data->marked_cells)) {
                    $this->table_html .= "; background-image: url(css/флажок.png)";
                }
                $this->table_html .= "'/>";
            }
        }
    }

    function color_number($number)
    {
        $this->table_html .= "<font style='color:" . $this->color[$number] . "'>$number</font>";
    }


    function generate()
    {
        global $data;
        $this->form_content();
        echo $this->pre_table;
        if ((!isset($data->mode)) || ($data->mode != "new")) {
            $this->create_table();
            echo $this->table_html;
        }
        echo $this->post_table;
    }
}
$grid_gen = new grid_gen();
?>