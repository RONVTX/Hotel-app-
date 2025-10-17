<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?> 
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hotel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/hotel_app/assets/css/estilos.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/hotel_app/public/index.php">Hotel</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['admin'])): ?>
          <li class="nav-item"><a class="nav-link" href="/hotel_app/admin/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/hotel_app/admin/habitaciones.php">Habitaciones</a></li>
          <li class="nav-item"><a class="nav-link" href="/hotel_app/admin/huespedes.php">Huéspedes</a></li>
          <li class="nav-item"><a class="nav-link" href="/hotel_app/admin/reservas.php">Reservas</a></li>
          <li class="nav-item"><a class="nav-link" href="/hotel_app/admin/mantenimiento.php">Mantenimiento</a></li>
          <li class="nav-item"><a class="nav-link" href="/hotel_app/logout.php">Cerrar sesión</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/hotel_app/admin/login.php">Admin</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
