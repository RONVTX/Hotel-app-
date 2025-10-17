<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }
include __DIR__ . '/../includes/header.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $documento = trim($_POST['documento']);
    if (!$nombre || !$email || !$documento) $err = 'Rellena todos los campos.';
    else {
        $stmt = $pdo->prepare('INSERT INTO huespedes (nombre, email, documento_identidad) VALUES (:n,:e,:d)');
        try {
            $stmt->execute(['n'=>$nombre,'e'=>$email,'d'=>$documento]);
            header('Location: huespedes.php'); exit;
        } catch (Exception $ex) { $err = 'Error: '.$ex->getMessage(); }
    }
}
?>
<h2>Agregar huésped</h2>
<?php if($err) echo '<div class="alert alert-danger">'.e($err).'</div>'; ?>
<form method="post">
  <div class="mb-3"><label>Nombre</label><input class="form-control" name="nombre" required></div>
  <div class="mb-3"><label>Email</label><input class="form-control" name="email" type="email" required></div>
  <div class="mb-3"><label>Documento</label><input class="form-control" name="documento" required></div>
  <button class="btn btn-success">Guardar</button>
</form>
<?php include __DIR__ . '/../includes/footer.php'; ?>