<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() == PHP_SESSION_NONE) session_start();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admins WHERE username = :u LIMIT 1');
    $stmt->execute(['u' => $user]);
    $row = $stmt->fetch();
    if ($row && password_verify($pass, $row['password_hash'])) {
        $_SESSION['admin'] = ['id' => $row['id'], 'username' => $row['username']];
        header('Location: dashboard.php'); exit;
    }
    $err = 'Usuario o contraseña incorrectos.';
}
include __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-5">
    <h3>Admin - Iniciar sesión</h3>
    <?php if($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
    <form method="post">
      <div class="mb-3"><label>Usuario</label><input class="form-control" name="username" required></div>
      <div class="mb-3"><label>Contraseña</label><input type="password" class="form-control" name="password" required></div>
      <button class="btn btn-primary">Entrar</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>