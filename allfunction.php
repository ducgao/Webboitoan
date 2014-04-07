<?php
	$SERVER = "localhost";
	$USERNAME = "root";
	$PASSWORD = "";
	$DBNAME = "forum";
	
	$conn = mysql_connect($SERVER, $USERNAME, $PASSWORD);
	if ( !$conn ) {
	die("không nết nối được vào MySQL server: ".mysql_error($conn));
	}
	
	mysql_select_db($DBNAME, $conn)
	or die("Không thể chọn được CSDL: ".mysql_error($conn));
	mysql_set_charset('utf8',$conn);
	
	function getcomments($id_thread, $conn){
		$sql = "SELECT COUNT(comment) AS comments FROM comments WHERE id_thread = $id_thread";
		$result = mysql_query($sql, $conn);
		$comments = 0;
		while($row = mysql_fetch_assoc($result)){
			$comments=$row['comments'];
		}
		return $comments;
	}
	function getlastuser($id_thread, $conn){
		$sql = "SELECT username, id_comment FROM `comments` WHERE id_thread = $id_thread ORDER BY id_comment DESC LIMIT 1";
		$result = mysql_query($sql, $conn);
		$username = "";
		while($row = mysql_fetch_assoc($result)){
			$username=$username.$row['username'];
		}
		return $username;
	}
	function getlasttimecmt($id_thread, $conn){
		$sql = "SELECT time, id_comment FROM `comments` WHERE id_thread = $id_thread ORDER BY id_comment DESC LIMIT 1";
		$result = mysql_query($sql, $conn);
		$lasttime = "";
		$demtime = 0;
		while($row = mysql_fetch_assoc($result)){
			for($i = 0; $i < strlen($row["time"]); $i++){
				if(substr($row["time"], $i,1) != "/"){
					$lasttime = $lasttime."".substr($row["time"], $i,1);
					
				}else{
					if($demtime == 2){
						$lasttime = $lasttime."".substr($row["time"], $i,1);
					}
					if($demtime == 1){
						$lasttime = $lasttime." ";
						$demtime++;
					}
					if($demtime == 0){
						$lasttime = $lasttime.":";
						$demtime++;
					}
					
					
				}
			}
		}
		return $lasttime;
	}
	
	if($_GET['fc'] == 'checkuser')
	{
		$sql = "SELECT * FROM user WHERE username = '".$_GET['user']."'";
		$result = mysql_query($sql, $conn);
		if ( !$result )
		die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
		
		if(mysql_num_rows($result)>0){
			echo "!";
		}
		mysql_close($conn);
	}
	if($_GET['fc'] == 'adduser')
	{	
		$username = $_GET['username'];
		$password = $_GET['password'];
		$email = $_GET['email'];
		$facemail = "";
		$phone = $_GET['phone'];
		$year = $_GET['year'];
		$born = $_GET['born'];
		$sql = "INSERT INTO user VALUES ('$username','$password','$email','$facemail','$phone','$year','$born')";
		$result = mysql_query($sql, $conn);
		if ( !$result )
		die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
		echo "Tài Khoản Được Tại Thành Công";
		mysql_close($conn);
	}
	if($_GET['fc'] == 'login')
	{
		$username = $_GET['username'];
		$password = $_GET['password'];
		$sql = "SELECT * FROM user WHERE username = '$username' AND password = '$password'";
		$result = mysql_query($sql, $conn);
		if ( !$result )
		die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
		
		if(mysql_num_rows($result)>0){
			echo "true";
		}else{
			echo "false";
		}
		mysql_close($conn);
	}
	if($_GET['fc'] == 'userinfo'){
		$username = $_GET['username'];
		$sql = "SELECT * FROM user WHERE username = '$username'";
		$result = mysql_query($sql,$conn);
		if ( !$result )
		die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
		
		while($row = mysql_fetch_assoc($result)){
			echo $row['password']."/".$row['phone']."/".$row['year']."/".$row['born'];
		}
		
	}
	if($_GET['fc'] == 'updateuser'){
		$username = $_GET['username'];
		$oldpassword = $_GET['oldpass'];
		$newpass = $_GET['newpass'];
		$repass = $_GET['repass'];	
		$phone = $_GET['phone'];
		$year = $_GET['year'];
		$born = $_GET['born'];
		
		$sql = "SELECT * FROM user WHERE username = '$username'";
		$result = mysql_query($sql,$conn);
		if ( !$result )
		die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
		
		while($row = mysql_fetch_assoc($result)){
			if($oldpassword == ""){
				$oldpassword = $row['password'];
				$repass = $row['password'];
				$newpass = $row['password'];
			}
			if($phone == ""){
				$phone = $row['phone'];
			}
			if($year == ""){
				$year = $row['year'];
			}
			if($born == ""){
				$born = $row['born'];
			} 
			if($oldpassword == $row['password']){
				if($newpass == $repass){
					$sqlupdate = "UPDATE user SET password='$repass', phone='$phone', year='$year', born='$born' WHERE username='$username'";
					$resultupdate = mysql_query($sqlupdate, $conn);
					if ( !$result ){
					die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
					}else{
						echo "Update Thành Công";
					}
				}	
				else{
					echo "Password nhập lại không trùng";
				}
			}
		}
		
	}
	if($_GET['fc'] == 'showthreads'){
		$file = 'showthreads.txt';
		$f = fopen($file, 'w+');
		$context = "";
		$box = $_GET['box'];
		$showline = 0;
		$sql = "SELECT * FROM threads WHERE box='$box' AND istop = 1";
		$result = mysql_query($sql, $conn);
		if ( !$result ){
		die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
		}else{
			if($box == 'noiquythongbao'){
				$context = $context.'<div class="catalogofthreads">
							Nội Quy - Thông Báo
						</div>	';
			}
			if($box == 'huongdansudungdiendanboitoan'){
				$context = $context.'<div class="catalogofthreads">
							Hướng Dẫn Sử Dụng Diễn Đàn Bói Toán
						</div>	';	
			}
			if($box == 'sukienhoatdongchung'){
				$context = $context.'<div class="catalogofthreads">
							Sự Kiện - Hoạt Động Chung
						</div>	';	
			}
			if($box == 'dongbacbo'){
				$context = $context.'<div class="catalogofthreads">
							Đông Bắc Bộ
						</div>	';	
			}
			if($box == 'taybacbo'){
				$context = $context.'<div class="catalogofthreads">
							Tây Bắc Bộ
						</div>	';	
			}
			if($box == 'haiphongnamdinhthaibinh'){
				$context = $context.'<div class="catalogofthreads">
							Hải Phòng - Nạm Định - Thái Bình
						</div>	';	
			}
			if($box == 'hanoi'){
				$context = $context.'<div class="catalogofthreads">
							Hà Nội
						</div>	';	
			}
			if($box == 'thanhnghetinh'){
				$context = $context.'<div class="catalogofthreads">
							Thanh Nghệ Tĩnh
						</div>	';	
			}
			if($box == 'binhtrithuathienhue'){
				$context = $context.'<div class="catalogofthreads">
							Bình Trị Thừa Thiên Huế
						</div>	';	
			}
			if($box == 'quangnamdanang'){
				$context = $context.'<div class="catalogofthreads">
							Quảng Nam - Đà Nẵng
						</div>	';	
			}
			if($box == 'taynguyen'){
				$context = $context.'<div class="catalogofthreads">
							Tây Nguyên
						</div>	';	
			}
			if($box == 'namtrungbo'){
				$context = $context.'<div class="catalogofthreads">
							Nam Trung Bộ
						</div>	';	
			}
			if($box == 'thanhphohochiminh'){
				$context = $context.'<div class="catalogofthreads">
							Thành Phố Hồ Chí Minh
						</div>	';	
			}
			if($box == 'dongnambo'){
				$context = $context.'<div class="catalogofthreads">
							Đông Nam Bộ
						</div>	';	
			}
			if($box == 'canthotaynambo'){
				$context = $context.'<div class="catalogofthreads">
							Cần Thơ - Tây Nam Bộ
						</div>	';	
			}

			
		}
		while($row = mysql_fetch_assoc($result)){
			$comments = getcomments($row['id_thread'], $conn);
			$lastuser = getlastuser($row['id_thread'], $conn);
			$lasttime = getlasttimecmt($row['id_thread'], $conn);
			$time = "";
			$demtime = 0;
			for($i = 0; $i < strlen($row["time"]); $i++){
				if(substr($row["time"], $i,1) != "/"){
					$time = $time."".substr($row["time"], $i,1);
					
				}else{
					if($demtime == 2){
						$time = $time."".substr($row["time"], $i,1);
					}
					if($demtime == 1){
						$time = $time." ";
						$demtime++;
					}
					if($demtime == 0){
						$time = $time.":";
						$demtime++;
					}
					
					
				}
			}
			if($showline == 1){
				$context = $context.'<div class="lineofbox">
								
					</div>';	
			}
			$textname = "'".$row['username']."'";
			$textlastname = "'".$lastuser."'";
			$textidthread = "'".$row['id_thread']."'";
			$context = $context.'<div class="thread">
						<div class="threadtitle" onclick="showthread('.$textidthread.');">
							'.$row["title"].'
						</div>
						<div class="thongtinthread" onclick="showuserinfo('.$textname.');">
							'.$row["username"].'
						</div>
						<div class="chitietthread">
							'.$time.'<br />Comment: '.$comments.'
						</div>
						<div class="newestsender">
							<div class="newestsendername" onclick="showuserinfo('.$textlastname.');" >
								'.$lastuser.'
							</div>
							<div class="newestsendertime">
								'.$lasttime.'
							</div>
						</div>
						
					</div>';
			$showline = 1;
		}
		
		$showline = 0;
		$sql = "SELECT * FROM threads WHERE box='$box' AND istop = 0";
		$result = mysql_query($sql, $conn);
		if ( !$result ){
		die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
		}else{
			
			$context = $context.'<div class="catalogofthreads">
						Bài Mới Nhất
					</div>	';
			
		}
		while($row = mysql_fetch_assoc($result)){
			$comments = getcomments($row['id_thread'], $conn);
			$lastuser = getlastuser($row['id_thread'], $conn);
			$lasttime = getlasttimecmt($row['id_thread'], $conn);
			$time = "";
			$demtime = 0;
			for($i = 0; $i < strlen($row["time"]); $i++){
				if(substr($row["time"], $i,1) != "/"){
					$time = $time."".substr($row["time"], $i,1);
					
				}else{
					if($demtime == 2){
						$time = $time."".substr($row["time"], $i,1);
					}
					if($demtime == 1){
						$time = $time." ";
						$demtime++;
					}
					if($demtime == 0){
						$time = $time.":";
						$demtime++;
					}
					
					
				}
			}
			if($showline == 1){
				$context = $context.'<div class="lineofbox">
								
					</div>';	
			}
			$textname = "'".$row['username']."'";
			$textlastname = "'".$lastuser."'";
			$textidthread = "'".$row['id_thread']."'";
			$context = $context.'<div class="thread">
						<div class="threadtitle" onclick="showuserinfo('.$textidthread.')">
							'.$row["title"].'
						</div>
						<div class="thongtinthread" onclick="showuserinfo('.$textname.')">
							'.$row["username"].'
						</div>
						<div class="chitietthread">
							'.$time.'<br />Comment: '.$comments.'
						</div>
						<div class="newestsender">
							<div class="newestsendername" onclick="showuserinfo('.$textlastname.')">
								'.$lastuser.'
							</div>
							<div class="newestsendertime">
								'.$lasttime.'
							</div>
						</div>
						
					</div>';
			$showline = 1;
		}
		$context = $context.'<div style="height: 100px;" class="catalog">
						THIẾT KẾ SAU
					</div>';
					
		$file="\xEF\xBB\xBF".$file;
   		fputs($f, $context);
		fclose($f);
		echo $context;
		
	}
	if($_GET['fc'] == 'showuserinfo'){
		$username = $_GET['username'];
		$sql = "SELECT * FROM user where username='$username'";
		$result = mysql_query($sql, $conn);
		if ( !$result )
		die("Không thể thực hiện được câu lệnh SQL: ".mysql_error($conn));
		while($row = mysql_fetch_assoc($result)){
			echo $row['username']."/".$row['email']."/".$row['phone']."/".$row['year']."/".$row['born'];
			
		}
	}
?>