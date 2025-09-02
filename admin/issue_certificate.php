<?php
session_start(); require '../includes/db.php';
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
if (isset($_GET['id'])) {
  $appId=$_GET['id'];
  $stmt=$conn->prepare("SELECT a.name,i.title FROM applications a JOIN internships i ON a.internship_id=i.id WHERE a.id=:id");
  $stmt->execute([":id"=>$appId]); $app=$stmt->fetch();
  if(!$app) die("Not found");
  $code=uniqid("CERT-"); $date=date("Y-m-d");
  $stmt=$conn->prepare("INSERT INTO certificates(application_id,certificate_code,issue_date) VALUES (:a,:c,:d)");
  $stmt->execute([":a"=>$appId,":c"=>$code,":d"=>$date]);
  echo "✅ Certificate Issued! Code: $code<br><a href='../verify_certificate.php?code=$code'>View Certificate</a>";
}
?>