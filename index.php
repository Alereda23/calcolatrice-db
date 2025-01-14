<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatrice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .calculator {
            display: inline-block;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="number"] {
            padding: 5px;
            margin: 5px;
            width: 100px;
        }
        select {
            padding: 5px;
            margin: 5px;
        }
        button {
            padding: 5px 10px;
            margin: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .result {
            margin-top: 20px;
            font-size: 1.2em;
        }
        .history {
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="calculator">
        <h1>Calcolatrice</h1>
        <form method="POST">
            <input type="number" name="num1" placeholder="Numero 1" required>
            <select name="operation">
                <option value="addizione">+</option>
                <option value="sottrazione">-</option>
                <option value="moltiplicazione">x</option>
                <option value="divisione">:</option>
            </select>
            <input type="number" name="num2" placeholder="Numero 2" required>
            <br>
            <button type="submit">Calcola</button>
        </form>

        <div class="result">
            <?php
            $db = new SQLite3('calculations.db');
            $db->exec("CREATE TABLE IF NOT EXISTS calculations (id INTEGER PRIMARY KEY, num1 REAL, num2 REAL, operation TEXT, result REAL)");

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $num1 = $_POST['num1'];
                $num2 = $_POST['num2'];
                $operation = $_POST['operation'];
                $result = null;

                if (is_numeric($num1) && is_numeric($num2)) {
                    switch ($operation) {
                        case 'addizione':
                            $result = $num1 + $num2;
                            echo "Risultato: $num1 + $num2 = $result";
                            break;
                        case 'sottrazione':
                            $result = $num1 - $num2;
                            echo "Risultato: $num1 - $num2 = $result";
                            break;
                        case 'moltiplicazione':
                            $result = $num1 * $num2;
                            echo "Risultato: $num1 x $num2 = $result";
                            break;
                        case 'divisione':
                            if ($num2 != 0) {
                                $result = $num1 / $num2;
                                echo "Risultato: $num1 : $num2 = $result";
                            } else {
                                echo "Errore: divisione per zero non permessa.";
                            }
                            break;
                        default:
                            echo "Operazione non valida.";
                            break;
                    }

                    if ($result !== null) {
                        $stmt = $db->prepare('INSERT INTO calculations (num1, num2, operation, result) VALUES (:num1, :num2, :operation, :result)');
                        $stmt->bindValue(':num1', $num1, SQLITE3_FLOAT);
                        $stmt->bindValue(':num2', $num2, SQLITE3_FLOAT);
                        $stmt->bindValue(':operation', $operation, SQLITE3_TEXT);
                        $stmt->bindValue(':result', $result, SQLITE3_FLOAT);
                        $stmt->execute();
                    }
                } else {
                    echo "Inserisci valori numerici validi.";
                }
            }
            ?>
        </div>

        <div class="history">
            <h2>Storico Calcoli</h2>
            <table border="1" style="width: 100%; margin-top: 10px;">
                <tr>
                    <th>#</th>
                    <th>Numero 1</th>
                    <th>Numero 2</th>
                    <th>Operazione</th>
                    <th>Risultato</th>
                </tr>
                <?php
                $results = $db->query('SELECT * FROM calculations ORDER BY id DESC');
                while ($row = $results->fetchArray()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['num1']}</td>
                            <td>{$row['num2']}</td>
                            <td>{$row['operation']}</td>
                            <td>{$row['result']}</td>
                          </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>