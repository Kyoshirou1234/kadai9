<?php
ini_set("display_errors", 1);

try {
    //$pdo = new PDO('mysql:dbname=tech-27-k_kadai9;charset=utf8;host=mysql57.tech-27-k.sakura.ne.jp', 'tech-27-k', '52P34w57d3');
    $pdo = new PDO('mysql:dbname=kadai9;charset=utf8;host=localhost', 'root', '');
} catch (PDOException $e) {
    exit('DBError: ' . $e->getMessage());
}

// Fetch worknames
$stmt_workname = $pdo->prepare("SELECT workname FROM work");
$status_workname = $stmt_workname->execute();
if ($status_workname == false) {
    $error = $stmt_workname->errorInfo();
    exit("SQLError: " . $error[2]);
}
$values_work = $stmt_workname->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['select'])) {
    $workname = $_POST["workname"];
    
    // Fetch selected work details
    $stmt_select = $pdo->prepare("SELECT * FROM work WHERE workname = :workname");
    $stmt_select->bindValue(':workname', $workname, PDO::PARAM_STR);
    $status_select = $stmt_select->execute();
    if ($status_select == false) {
        $error = $stmt_select->errorInfo();
        exit("SQLError: " . $error[2]);
    }
    $values_select = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
    if (isset($values_select[0]['id'])) {
        $id = $values_select[0]["id"];
        $tablename = "24_" . $id;
    } else {
        exit("No data found for the given workname.");
    }
}

    if (isset($_POST['send'])) {
        $start = $_POST["start"];
        $finish = $_POST["finish"];
        $task = $_POST["task"];
        $importance = $_POST["importance"];
        $done = $_POST["done"];
        $tablename = $_POST["tablename"];
        $id = $_POST["id"];

        if (isset($id)) {
            $tablename = "24_" . $id;
            $sql_INSERT = "INSERT INTO $tablename (id, start, finish, task, importance, done) VALUES (NULL, :start, :finish, :task, :importance, :done)";
            $stmt_task = $pdo->prepare($sql_INSERT);
            $stmt_task->bindValue(':start', $start, PDO::PARAM_STR);
            $stmt_task->bindValue(':finish', $finish, PDO::PARAM_STR);
            $stmt_task->bindValue(':task', $task, PDO::PARAM_STR);
            $stmt_task->bindValue(':importance', $importance, PDO::PARAM_STR);
            $stmt_task->bindValue(':done', $done, PDO::PARAM_STR);
            $Enterstatus = $stmt_task->execute();
            if ($Enterstatus) {
                $message = "<div>登録しました</div>";
            } else {
                $message = "<div>登録に失敗しました</div>";
            }
        } else {
            exit("Table name could not be set due to missing ID.");
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>業務管理</title>
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
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group select,
        .form-group input[type="text"],
        .form-group input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            border-radius: 4px;
        }
        .form-group input[type="submit"]:hover {
            background-color: #0056b3;
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
            <form action="UpdateWork.php" method="post">
                <input type="submit" name="UpdateTasks" value="タスクの変更" />
            </form>
        </div>
    </div>
</header>
<div>
    <!-- Selection Form -->
    <form action="" method="post">
        <div class="form-group">
            <label for="workname">業務名：</label>
            <select id="workname" name="workname">
                <!-- Options will be inserted here by JavaScript -->
            </select>
        </div>
        <input type="submit" name="select" value="選択" />
    </form>

    <?php if (isset($id)): ?>
    <!-- Data Entry Form -->
    <h1>業務名：<?php echo htmlspecialchars($workname)?></h1>
    <div>
        <form action="" method="post">
            <div class="form-group">
                <label for="task">タスク：</label>
                <input type="text" id="task" name="task">
            </div>
            <div class="form-group">
                <label for="start">開始：</label>
                <input type="date" id="start" name="start">
            </div>
            <div class="form-group">
                <label for="finish">終了：</label>
                <input type="date" id="finish" name="finish">
            </div>
            <div class="form-group">
                <label for="importance">重要度：</label>
                <select id="importance" name="importance">
                    <!-- Options will be inserted here by JavaScript -->
                </select>
            </div>
            <div class="form-group">
                <label for="done">済：</label>
                <select id="done" name="done">
                    <!-- Options will be inserted here by JavaScript -->
                </select>
            </div>
            <input type="hidden" name="tablename" value="<?= htmlspecialchars($tablename) ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <input type="submit" name="send" value="登録" />
        </form>
    </div>
    <?php endif; ?>

    <?php if (isset($message)): ?>
    <div><?= $message ?></div>
    <?php endif; ?>

</div>

<script>
function CreateSelect(arr, name) {
    let sl = document.getElementById(name);
    sl.innerHTML = ''; // Remove existing options
    for (let item of arr) {
        let op = document.createElement('option');
        op.text = item.workname;
        op.value = item.workname;
        sl.appendChild(op);
    }
}

// Create options for the workname select
let values_work = <?= json_encode($values_work); ?>;
CreateSelect(values_work, "workname");

// Create options for importance and done selects
function createSimpleSelect(id, options) {
    let sl = document.getElementById(id);
    sl.innerHTML = ''; // Remove existing options
    options.forEach(option => {
        let op = document.createElement('option');
        op.text = option;
        op.value = option;
        sl.appendChild(op);
    });
}

let importance = ["高", "中", "低"];
let done = ["未", "済"];

createSimpleSelect("importance", importance);
createSimpleSelect("done", done);
</script>
</body>
</html>
