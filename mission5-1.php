<h1>簡易掲示板</h1>
投稿フォーム<br>
<br>

<?php
//データベースに接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

#変数を定義
$name = $_POST["name"];
$comment = $_POST["comment"];
$pass = $_POST["pass"];

#新規投稿
if($_POST["toukou"] && empty($_POST["ed_num"])){
    if(!empty($name) && !empty($comment) && !empty($pass)){
        $sql = $pdo->query("INSERT INTO tbtest_5 (id,name,comment,time,pass)
            VALUES (NULL,'$name','$comment',NOW(),'$pass')");

    }else{
      echo "<span style='color:red;'>名前、コメントまたはパスワードが未入力です。</span>";
    }
}



#削除機能
if($_POST["delete"]){
    if(!empty($_POST["delete_num"])){
        if(!empty($_POST["del_pass"])){
            $delete_id = $_POST["delete_num"];
            $del_pass = $_POST["del_pass"];
            
            $sql = 'SELECT * FROM tbtest_5 WHERE id=:delete_id ';
            $stmt = $pdo->prepare($sql);                  
            $stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT); 
            $stmt->execute();                             
            $results = $stmt->fetchAll(); 
                foreach ($results as $row){
		        $corr_pass = $row['pass'];
                }
                if($corr_pass == $del_pass){
    	            $sql = 'delete from tbtest_5 where id=:delete_id';
    	            $stmt = $pdo->prepare($sql);
    	            $stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
    	            $stmt->execute();
                }else{
                    echo "<span style='color:red;'>パスワードが異なるため削除できません。</span>";
                }
        }else{
            echo "<span style='color:red;'>パスワードが未入力です。</span>";
        }
    }else{
        echo "<span style='color:red;'>削除する番号が未入力です。</span>";
    }
}

#編集機能
if($_POST["edit"]){
    if(!empty($_POST["edit_num"])){
        if(!empty($_POST["ed_pass"])){
            $edit_id = $_POST["edit_num"];
            $ed_pass = $_POST["ed_pass"];
            
            $sql = 'SELECT * FROM tbtest_5 WHERE id=:edit_id ';
            $stmt = $pdo->prepare($sql);               
            $stmt->bindParam(':edit_id', $edit_id, PDO::PARAM_INT); 
            $stmt->execute();                            
            $results = $stmt->fetchAll(); 
                foreach ($results as $row){
		        $corr_pass = $row['pass'];
                }
                if($corr_pass == $ed_pass){

                    $sql = 'SELECT * FROM tbtest_5 WHERE id=:edit_id ';
                    $stmt = $pdo->prepare($sql);                
                    $stmt->bindParam(':edit_id', $edit_id, PDO::PARAM_INT); 
                    $stmt->execute();            
                    $results = $stmt->fetchAll(); 
                    foreach ($results as $row){
        		        $editNumber = $row['id'];
        		        $editName = $row['name'];
        		        $editComment = $row['comment'];
        	        }
                }else{
                    echo "<span style='color:red;'>パスワードが異なるため編集できません。</span>";
                }
        }else{
            echo "<span style='color:red;'>パスワードが未入力です。</span>";
        }
    }else{
        echo "<span style='color:red;'>編集する番号が未入力です。</span>";
    }
}

#編集投稿
if($_POST["toukou"] && !empty($_POST["ed_num"])){
    if(!empty($name) && !empty($comment) && !empty($pass)){
        $ed_num = $_POST["ed_num"]; 
    	$sql = 'UPDATE tbtest_5 SET name=:name,comment=:comment, pass=:pass WHERE id=:ed_num';
    	$stmt = $pdo->prepare($sql);
    	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
    	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    	$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    	$stmt->bindParam(':ed_num', $ed_num, PDO::PARAM_INT);
    	$stmt->execute(); 
    }else{
        echo "<span style='color:red;'>名前、コメントまたはパスワードが未入力です。</span>";
    }
}

/*
//データテーブルを作成
$sql = "CREATE TABLE IF NOT EXISTS tbtest_5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "time DATETIME,"
	. "pass char(16)"
	.");";
	$stmt = $pdo->query($sql);

//作成したテーブルを確認
$sql ='SHOW TABLES';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[0];
		echo '<br>';
	}
	echo "<hr>";

//作成したテーブルの詳細を確認
$sql ='SHOW CREATE TABLE tbtest_5';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[1];
	}
	echo "<hr>";
*/	
?>



<br>
<form action="" method="POST">

  <input type="hidden" name="ed_num" value="<?php echo $editNumber;?>"><br>
  <input type="text" name="name" value="<?php echo $editName;?>" placeholder="名前"><br>
  <textarea type="text" name="comment" placeholder="コメント" cols="30" rows="5"><?php echo $editComment;?></textarea><br>
  <input type ="text" name="pass" placeholder="パスワード">(半角英数字 16文字まで)<br> 
  <input type="submit" name="toukou" value="投稿">
  <br>
  <br>
  <hr>
  
  <br>
  削除したい番号を入力してください：<input type="text" name="delete_num"><br>
  パスワード：<input type="text" name="del_pass">
  <input type="submit" name="delete" value="削除" ><br>
  <br>
  編集したい番号を入力してください：<input type="text" name="edit_num"><br>
  パスワード：<input type="text" name="ed_pass">
  <input type="submit" name="edit" value="編集"><br>
</form>
<br>
<hr>

<?php
#表示機能
$n = $pdo -> query("SELECT * FROM tbtest_5 ORDER BY id DESC");
while ($i = $n -> fetch()) {
print "{$i['id']}: {$i['name']} {$i['time']}<br>"
        .nl2br($i['comment'])."<hr>";
}
?>
