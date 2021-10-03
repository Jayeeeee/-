 <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>M3</title>
</head>
<body>
    <style>
        input[type=text], select {
            width: 20%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        input[type=password], select {
            width: 20%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
            textarea{
            width: 50%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            resize: none;
        }
        
        input[type=submit] {
            width: 10%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        input[type=submit]:hover {
            background-color: #45a049;
        }
        
        div {
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
        }
        </style>
        <h1 style="text-align:center">掲示板</h1>
        
    <?php
    $dsn = 'mysql:dbname=ʼデータベース名ʼ;host=localhost';
    $user = ʼユーザー名ʼ;
    $password = ʼパスワードʼ;
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    $sql = "CREATE TABLE IF NOT EXISTS tbtest(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name char(32),
        comment TEXT,
        pass char(32),
        date TEXT
        )";
    $stmt = $pdo->query($sql);
    $pdo->exec($sql);
    echo "TABLE tbtest is created succeed<br>";
     
    if(!empty($_POST["edit"])){
        $edit=$_POST["edit"];
        $editpass=$_POST["editpass"];
        $sql = "SELECT * FROM tbtest";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchALL();
        foreach($results as $row){
            if($row["id"]==$edit && $row["pass"]==$editpass){
                $editname = $row["name"];
                $editcomment = $row["comment"];
                $editpass = $row["pass"];
            }if($row["id"]==$edit && $row["pass"]!=$editpass){
                echo "番号またパスワードを再度ご確認ください<br>";
            }
        }
        
    }
    
    if(!empty($_POST["delete"])){
        $del=$_POST["delete"];
        $delpass=$_POST["delpass"];
        $sql = "SELECT * FROM tbtest";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchALL();
        foreach($results as $row){
            if($row["id"]==$del && $row["pass"]==$delpass){
                $id = $del;
                $sql = 'delete from tbtest where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }if($row["id"]==$del && $row["pass"]!=$delpass){
                echo "番号またパスワードを再度ご確認ください<br>";
            }
        }
    }
    
    if(!empty($_POST["name"]) && !empty($_POST["comment"])){
        if(!empty($_POST["pass"])){
            $name = $_POST["name"];
            $comment = $_POST["comment"]; 
            $pass = $_POST["pass"];
            $date = date("Y/m/d/ H:i:s");
            if(!empty($_POST["edit_post"])){
                $edit_post=$_POST["edit_post"];
                $id = $edit_post; 
                $sql = 'UPDATE tbtest SET name=:name,comment=:comment,pass=:pass,date=:date 
                WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':pass',$pass, PDO::PARAM_STR);
                $stmt->bindParam(':date',$date, PDO::PARAM_STR);          
                $stmt->execute();
            }else{
                $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, pass, date) 
                VALUES (:name, :comment, :pass, :date)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $_POST["pass"], PDO::PARAM_STR);
                $sql -> bindParam(':date',$date, PDO::PARAM_STR);
                $sql -> execute();
            }
        }
    }

?>
    <form action="" method="post">
        Name:&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" name="name" 
        value="<?php if (isset($editname)){echo $editname;}?>" 
        placeholder="名前"><br>
        Comment:<input type="text" name="comment" 
        value="<?php if (isset($editcomment)){echo $editcomment;}
        ?>"
        placeholder="コメント">
        Password:<input type="password" name="pass" 
        value="<?php if (isset($editpass)){echo $editpass;}?>"
        placeholder="パスワード">
        <input type="submit" name="submit">
        <input type="hidden" name="edit_post" value="<?php if (isset($edit))
        {echo $edit;} ?>">
        
    <br>
    </form>
        
    <form action="" method="post">    
        削除:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
        <input type="text" name="delete" placeholder="番号を入力ください">
        Password:<input type="password" name="delpass" placeholder="パスワード">
        <input type="submit" name="submit" value="削除">
        
    <br>
    </form>
        
    <form action="" method="post">
        編集:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
        <input type="text" name="edit" placeholder="番号を入力ください">
        Password:<input type="password" name="editpass" placeholder="パスワード">
        <input type="submit" name="submit" value="編集">
    </form>

    <?php
    echo "<hr>";
    echo "<h2>入力履歴：</h2>";
    
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo "<b>".$row['name']."</b>".'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
        echo $row['date'].'<br>';
        echo $row['comment'];
        echo "<hr>";
    }
    
?>
  
</body>
</html>