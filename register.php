<?php
require 'auth.php';
require 'config/db.php';
if (!empty($_SESSION['user_id'])) { header('Location: skills.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_auth_token();
 $name=trim((string)($_POST['full_name']??''));
 $email=strtolower(trim((string)($_POST['email']??'')));
 $pass=(string)($_POST['password']??'');
 if (mb_strlen($name)<2 || mb_strlen($name)>150 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>190 || strlen($pass)<10) {
   $error='Provide a name, valid email, and password of at least 10 characters.';
 } else {
   mysqli_begin_transaction($conn);
   try {
     $q=mysqli_prepare($conn,'INSERT INTO profile (full_name,email,headline) VALUES (?, ?, ?)');
     $headline='Student'; mysqli_stmt_bind_param($q,'sss',$name,$email,$headline);
     if(!mysqli_stmt_execute($q)) throw new Exception('profile');
     $pid=mysqli_insert_id($conn); mysqli_stmt_close($q);
     $hash=password_hash($pass,PASSWORD_DEFAULT);
     $q=mysqli_prepare($conn,'INSERT INTO users (profile_id,email,password_hash) VALUES (?, ?, ?)');
     mysqli_stmt_bind_param($q,'iss',$pid,$email,$hash);
     if(!mysqli_stmt_execute($q)) throw new Exception('user');
     mysqli_stmt_close($q);
     mysqli_commit($conn); header('Location: login.php?registered=1'); exit;
   } catch(Throwable $e) {
     mysqli_rollback($conn); $error='Could not create account. Email may already be in use.';
   }
 }
}
?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Register</title><link rel="stylesheet" href="assets/css/crud.css"></head><body><main class="wrap"><div class="panel"><h1>Register</h1><p>สร้างบัญชีสำหรับ Resume CRUD Lab</p>
<?php if($error): ?><p class="err"><?=auth_escape($error)?></p><?php endif; ?>
<form method="post"><input type="hidden" name="auth_csrf" value="<?=auth_escape(auth_token())?>"><label>Full name</label><input name="full_name" required maxlength="150"><label>Email</label><input name="email" type="email" required maxlength="190"><label>Password (10+ characters)</label><input name="password" type="password" required minlength="10"><p><button>Create account</button></p></form><p><a href="login.php">Login</a></p></div></main></body></html>
