<?php
session_start();
if (!isset($_SESSION['admin_id'])) { http_response_code(403); exit('Forbidden'); }
require_once __DIR__ . '/../database/database.php';
$id=(int)($_GET['id']??0);
$stmt=mysqli_prepare($conn,'SELECT college_id_file FROM users WHERE id=? LIMIT 1');
mysqli_stmt_bind_param($stmt,'i',$id); mysqli_stmt_execute($stmt); $res=mysqli_stmt_get_result($stmt); $row=$res?mysqli_fetch_assoc($res):null;
if(!$row || empty($row['college_id_file'])) { http_response_code(404); exit('File not found'); }
$file=realpath(__DIR__.'/../uploads/college_ids/'.basename($row['college_id_file']));
$base=realpath(__DIR__.'/../uploads/college_ids');
if(!$file || !$base || strpos($file,$base.DIRECTORY_SEPARATOR)!==0 || !is_file($file)) { http_response_code(404); exit('File not found'); }
$mime=(new finfo(FILEINFO_MIME_TYPE))->file($file); if(!in_array($mime,['image/jpeg','image/png','image/webp','application/pdf'],true)){http_response_code(415);exit('Unsupported file');}
header('Content-Type: '.$mime); header('Content-Disposition: inline; filename="college-id-'.$id.'"'); header('X-Content-Type-Options: nosniff'); readfile($file);
