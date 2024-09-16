<?php
// エラー表示
ini_set("display_errors", 1);

// DB接続
try {
    //$pdo = new PDO('mysql:dbname=tech-27-k_kadai9;charset=utf8;host=mysql57.tech-27-k.sakura.ne.jp', 'tech-27-k', '52P34w57d3');
    $pdo = new PDO('mysql:dbname=kadai9;charset=utf8;host=localhost','root','');
} catch (PDOException $e) {
    exit('DBError:' . $e->getMessage());
}

// タスクの更新処理
// タスクの更新処理
if (isset($_POST['UpdatedTask'])) {
    $mode = "UpdatedTask";
    
    foreach ($_POST['task_data'] as $task_data) {
        // タスクデータを分解
        list($id, $tablename) = explode('|', $task_data);

        // フォームからの値を取得
        $task_name = $_POST['task'][$tablename . $id];
        $start = $_POST['start'][$tablename . $id];
        $finish = $_POST['finish'][$tablename . $id];
        $importance = $_POST['importance'][$tablename . $id];
        $done = $_POST['done'][$tablename . $id];

        // データベースのタスクを更新する
        $sql = "UPDATE $tablename SET task=:task, start=:start, finish=:finish, importance=:importance, done=:done WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':task', $task_name, PDO::PARAM_STR);
        $stmt->bindValue(':start', $start, PDO::PARAM_STR);
        $stmt->bindValue(':finish', $finish, PDO::PARAM_STR);
        $stmt->bindValue(':importance', $importance, PDO::PARAM_STR);
        $stmt->bindValue(':done', $done, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
        } else {
            $error = $stmt->errorInfo();
            exit("SQLError:" . $error[2]);
        }
    }
    $mode = "UpdateTask";
}


// すべてのタスクを表示する場合
if (isset($_POST['UpdateTasks']) || $mode = "UpdateTask") {
    $mode = "UpdateTasks"; // すべてのタスク表示モード

    // 業務名を取得
    $stmt_work = $pdo->prepare("SELECT * FROM work");
    $status_work = $stmt_work->execute();

    if ($status_work == false) {
        $error = $stmt_work->errorInfo();
        exit("SQLError:" . $error[2]);
    }

    $works = $stmt_work->fetchAll(PDO::FETCH_ASSOC);
    $all_tasks = [];

    // 各テーブルのデータを取得
    foreach ($works as $work) {
        $tableName = "24_" . $work['id'];
        $sql = "SELECT * FROM `$tableName` WHERE 1";
        $stmt_task = $pdo->prepare($sql);
        $status_task = $stmt_task->execute();
        
        if ($status_task == false) {
            $error = $stmt_task->errorInfo();
            exit("SQLError:" . $error[2]);
        }
    
        // データを配列に保存
        $tasks = $stmt_task->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tasks as $task) {
            $task['workname'] = $work['workname']; // 業務名を追加
            $task['tablename'] = $tableName; // テーブル名を追加
            $all_tasks[] = $task; // すべてのタスクを集約
        }
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>タスク変更</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
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
        textarea {
            width: 100%;
            box-sizing: border-box;
        }
        select {
            width: 100%;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
            color: #333;
            margin: 0;
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
        header {
            margin-bottom: 20px;
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
        select, input[type="date"] {
            margin-right: 10px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
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
    <h1>業務内容変更</h1>

    <!-- 更新用フォーム -->
    <?php if (isset($mode) && $mode == "UpdateTasks") { ?>

 <!-- タスクの一覧を表示 -->
 <h2>すべてのタスク</h2>
<form method="post" action="">
    <table>
        <thead>
            <tr>
                <th>業務名</th>
                <th>タスク名</th>
                <th>開始日</th>
                <th>終了日</th>
                <th>重要度</th>
                <th>済</th>
                <th>更新</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($all_tasks as $task): ?>
<tr>
    <td>
        <?= htmlspecialchars($task["workname"]); ?>
        <input type="hidden" name="workname[<?= htmlspecialchars($task['tablename'] . $task['id']); ?>]" value="<?= htmlspecialchars($task["workname"]); ?>">
    </td>
    <td><input type="text" name="task[<?= htmlspecialchars($task['tablename'] . $task['id']); ?>]" value="<?= htmlspecialchars($task["task"]); ?>"></td>
    <td><input type="date" name="start[<?= htmlspecialchars($task['tablename'] . $task['id']); ?>]" value="<?= htmlspecialchars($task["start"]); ?>"></td>
    <td><input type="date" name="finish[<?= htmlspecialchars($task['tablename'] . $task['id']); ?>]" value="<?= htmlspecialchars($task["finish"]); ?>"></td>
    <td>
        <select name="importance[<?= htmlspecialchars($task['tablename'] . $task['id']); ?>]">
            <option value="低" <?= $task["importance"] === "低" ? 'selected' : ''; ?>>低</option>
            <option value="中" <?= $task["importance"] === "中" ? 'selected' : ''; ?>>中</option>
            <option value="高" <?= $task["importance"] === "高" ? 'selected' : ''; ?>>高</option>
        </select>
    </td>
    <td>
        <select name="done[<?= htmlspecialchars($task['tablename'] . $task['id']); ?>]">
            <option value="済" <?= $task["done"] === "済" ? 'selected' : ''; ?>>済</option>
            <option value="未" <?= $task["done"] === "未" ? 'selected' : ''; ?>>未</option>
        </select>
    </td>
    <td>
        <!-- テーブル名を hidden フィールドで送信 -->
        <input type="hidden" name="tablename[<?= htmlspecialchars($task['tablename'] . $task['id']); ?>]" value="<?= htmlspecialchars($task['tablename']); ?>">
        <input type="hidden" name="task_data[<?= htmlspecialchars($task['tablename'] . $task['id']); ?>]" value="<?= htmlspecialchars($task['id'] . '|' . $task['tablename']); ?>">
        <input type="submit" name="UpdatedTask" value="更新">
    </td>
</tr>
<?php endforeach; ?>
        </tbody>
    </table>
</form>
    <?php } ?>
</body>
</html>
