<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Usuario</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <link rel="stylesheet" href="style.css">
</head>
<body>

  <div class="form-container">
    <div class="form-header">
      <h2>Registrar Usuario</h2>
    </div>
    <?php if (isset($_GET['error'])) { ?>
  		<p class="error">
  			<?=htmlspecialchars($_GET['error'])?>
  		</p>
  	<?php } ?>
  	
  	<?php if (isset($_GET['success'])) { ?>
  		<p class="success">
  			<?=htmlspecialchars($_GET['success'])?>
  		</p>
  	<?php } ?>

    <form action="contact.php" method="POST">
      <div class="form-group">
        <label for="dni">DNI</label>
        <input type="text" id="dni" name="dni" maxlength="8" inputmode="numeric" pattern="\d{8}" title="Debe ingresar 8 números" required>
      </div>

      <div class="form-group">
        <label for="nombres">Nombres</label>
        <input type="text" id="nombres" name="nombres" required>
      </div>
      
      <div class="form-group">
        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" required>
      </div>

      <div class="form-group">
        <label for="correo">Correo Electrónico</label>
        <input type="email" id="correo" name="correo" required>
      </div>

      <div class="form-group">
        <label for="correo">Correo Electrónico Destino</label>
        <input type="email" id="correo" name="correo_destino" required>
      </div>

      <button type="submit">Registrar</button>
    </form>
  </div>
</body>
</html>