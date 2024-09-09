<?php
ini_set("display_errors", 1);

try {
    $pdo = new PDO('mysql:dbname=kadai9;charset=utf8;host=localhost', 'root', '');
} catch (PDOException $e) {
    exit('DBError: '.$e->getMessage());
}

// Fetch data for the form
$stmt_name = $pdo->prepare("SELECT * FROM name");
$status_name = $stmt_name->execute();
$values_name = $stmt_name->fetchAll(PDO::FETCH_ASSOC);

$error_message = ''; // Variable for error messages

// Handle form submission
if (isset($_POST['send'])) {
    $username = htmlspecialchars($_POST["workname"], ENT_QUOTES, 'UTF-8');
    $workname = htmlspecialchars($_POST["workname"], ENT_QUOTES, 'UTF-8');
    $overview = htmlspecialchars($_POST["overview"], ENT_QUOTES, 'UTF-8');
    $phase = htmlspecialchars($_POST["phase"], ENT_QUOTES, 'UTF-8');

    // Check for duplicate workname
    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM work WHERE workname = :workname");
    $check_stmt->bindValue(':workname', $workname, PDO::PARAM_STR);
    $check_stmt->execute();
    $workname_exists = $check_stmt->fetchColumn();

    if ($workname_exists > 0) {
        $error_message = "エラー: 同じ業務名がすでに存在します。";
    } else {
        $sql_INSERT = "INSERT INTO work (id, workname, overview, phase) VALUES (NULL, :workname, :overview, :phase)";
        $stmt_name = $pdo->prepare($sql_INSERT);
        $stmt_name->bindValue(':workname', $workname, PDO::PARAM_STR);
        $stmt_name->bindValue(':overview', $overview, PDO::PARAM_STR);
        $stmt_name->bindValue(':phase', $phase, PDO::PARAM_STR);
        $Enterstatus = $stmt_name->execute();

        if ($Enterstatus) {
            echo "<div>登録しました</div>";
            $lastId = $pdo->lastInsertId(); // Get the last inserted ID
            try {
                $tableName = '24_' . preg_replace('/[^a-zA-Z0-9_]/', '', $lastId);
                $sql_CREATE = "CREATE TABLE IF NOT EXISTS $tableName (
                    id INT(16) AUTO_INCREMENT PRIMARY KEY,
                    start DATE,
                    finish DATE,
                    task VARCHAR(255),
                    importance VARCHAR(4),
                    done VARCHAR(4)
                )";
                $pdo->exec($sql_CREATE);
                echo "Table $tableName created successfully";
            } catch (PDOException $e) {
                echo $sql_CREATE . "<br>" . $e->getMessage();
            }
        } else {
            echo "<div>登録に失敗しました</div>";
            exit(); // Exit on error
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登録者管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
            color: #333;
            margin: 0;
        }
        header {
            margin-bottom: 20px;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fff;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            text-align: center;
            font-size: 14px;
        }
        .tab:hover {
            background-color: #f0f0f0;
        }
        .tab.active {
            background-color: #007bff;
            color: white;
            border-bottom: 1px solid transparent;
        }
        .tab form {
            display: inline;
        }
        .tab input[type="submit"] {
            background-color: transparent;
            color: #007bff;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .tab input[type="submit"]:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        form {
            display: inline-block;
            margin-right: 10px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        select, input[type="date"], textarea {
            margin-right: 10px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        textarea {
            width: 100%;
            resize: vertical;
        }
    </style>
</head>
<body id="main">
    <header>
        <div class="tabs">
            <div class="tab">
                <form action="ConfirmWork.php" method="post">
                    <input type="submit" name="ini" value="業務を確認" />
                </form>
            </div>
            <div class="tab">
                <form action="ConfirmWork.php" method="post">
                    <input type="submit" name="all" value="すべてのタスクを表示" />
                </form>
            </div>
            <div class="tab">
                <form action="addWork.php" method="post">
                    <input type="submit" value="業務を追加" />
                </form>
            </div>
            <div class="tab">
                <form action="AddTask.php" method="post">
                    <input type="submit" value="タスクを追加" />
                </form>
            </div>
            <div class="tab">
                <form action="UpdateWork.php" method="post">
                    <input type="submit" name="UpdateWorks" value="業務内容の変更" />
                </form>
            </div>
            <div class="tab">
            <form action="UpdateTask.php" method="post">
                <input type="submit" name="UpdateTasks" value="タスクの変更" />
            </form>
            </div>
        </div>
    </header>

    <div>
        <!-- Display error message if any -->
        <?php if (!empty($error_message)): ?>
        <div style="color:red;"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Form for entering data -->
        <form action="AddWork.php" method="post">
            <div class="form-group">
                <label for="workname">業務名：</label>
                <input type="text" id="workname" name="workname" value="" required />
            </div>

            <div class="form-group">
                <label for="phase">フェーズ：</label>
                <select id="phase" name="phase">
                    <!-- Options will be added by JavaScript -->
                </select>
            </div>

            <div class="form-group">
                <label for="overview">概要：</label>
                <textarea name="overview" id="overview" cols="50" rows="5"></textarea>
            </div>

            <input type="submit" name="send" value="送信" />
        </form>

        <script>
        function CreateSelect(arr, name) {
            let sl = document.getElementById(name);
            sl.innerHTML = ''; // Remove existing options
            for (let item of arr) {
                let op = document.createElement('option');
                op.text = item.name || item; // Use item.name if it's an object
                op.value = item.name || item;
                sl.appendChild(op);
            }
        }

        let values_name = <?= json_encode($values_name); ?>;
        let phase = ["提案中", "構築中", "運用中"];

        CreateSelect(values_name, "workname");
        CreateSelect(phase, "phase");
        </script>
    </div>
</body>
</html>

