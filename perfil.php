<?php
// perfil.php - Página de perfil (protegida con login)
require_once 'auth_config.php';
require_once 'auth_functions.php';

// Proteger esta página
requireLogin();

$message = '';
$messageType = '';

// Obtener datos del usuario actual
$currentUser = getCurrentUser($usersFile);
$userEmail = getCurrentUserEmail();

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $nombre = $_POST['nombre'] ?? '';
        $key = $_POST['key'] ?? '';
        
        $result = updateUserProfile($usersFile, $userEmail, [
            'nombre' => $nombre,
            'key' => $key
        ]);
        
        if ($result['success']) {
            $message = '¡Cambios guardados exitosamente!';
            $messageType = 'success';
            // Recargar datos actualizados
            $currentUser = getCurrentUser($usersFile);
        } else {
            $message = $result['error'];
            $messageType = 'error';
        }
    }
    
    elseif ($action === 'update_avatar') {
        $avatar = $_POST['avatar'] ?? '';
        
        $result = updateUserProfile($usersFile, $userEmail, [
            'avatar' => $avatar
        ]);
        
        if ($result['success']) {
            $message = '¡Avatar actualizado!';
            $messageType = 'success';
            $currentUser = getCurrentUser($usersFile);
        } else {
            $message = $result['error'];
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mi perfil — Demo PixelCrew</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    background: #18182b;
    font-family: 'Outfit', sans-serif;
    color: #fafafd;
    margin: 0;
    min-height: 100vh;
}
.topbar {
    position: sticky; top: 0; display: flex; align-items: center; padding: 14px 18px;
    background: #23243b; box-shadow: 0 3px 14px rgba(0,0,0,.13);
}
.volver-btn {
    display: inline-flex; align-items: center; gap: 8px;
    background: #1a90ff; color: #fff; font-weight: bold; font-size: 1.1rem;
    border-radius: 12px; padding: 11px 18px; text-decoration: none; border: none; cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12); transition: background 0.18s;
}
.volver-btn:hover {background: #0066cc;}
.wrap {
    max-width: 430px; margin: 0 auto; padding: 32px 12px 0 12px;
}
.card {
    background: #222a36; border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.13);
    padding: 2.2rem 2rem 2rem 2rem;
    margin-top: 2.2rem;
}
h1 {
    font-size: 2.1rem; font-weight: 900; margin-bottom: 1.1rem;
    background: linear-gradient(90deg,#6c5ce7,#00cec9); -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.avatar-box {
    display: flex; flex-direction: column; align-items: center; margin-bottom: 1.6rem;
}
.avatar {
    width: 120px; height: 120px; border-radius: 50%; overflow: hidden;
    border: 2px solid #00cec9; background: #18182b; box-shadow: 0 2px 14px #23243b44;
    position: relative; margin-bottom: 1rem;
}
.avatar img {
    width: 100%; height: 100%; object-fit: cover; display: block;
}
.avatar-edit-btn {
    position: absolute; right: 6px; bottom: 6px; background: #6c5ce7;
    color: #fff; border: none; border-radius: 50%; width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem; cursor: pointer; box-shadow: 0 2px 10px #23243b22;
    transition: background 0.18s;
}
.avatar-edit-btn:hover {background: #00cec9;}
.avatar-input {display: none;}
.field {
    display: flex; align-items: center; gap: 10px; margin-bottom: 24px;
}
.field i {
    color: #00cec9; font-size: 1.28em; width: 24px; text-align: center;
}
.label {
    font-size: 1.16rem; font-weight: 700; color: #9ad3fa; min-width: 90px;
}
.value {
    font-size: 1.35rem; font-weight: 700; flex: 1; color: #fff; letter-spacing: .4px;
}
#idVal {
    color: #fdcb6e; text-decoration: underline; cursor: pointer;
}
.edit-box {
    margin-top: 30px; padding-top: 18px; border-top: 1px solid #333;
}
.edit-box label {
    font-size: 1rem; color: #9ad3fa; font-weight: 600;
    margin-bottom: 0.3rem; display: block;
}
.edit-box input {
    font-size: 1.14rem; padding: 0.7rem 1rem; border-radius: 10px; border: 1px solid #444;
    background: #23272f; color: #fff; margin-bottom: 18px; width: 100%;
}
.edit-btn {
    background: #28a745; color: #fff; font-size: 1.13rem; font-weight: 700;
    padding: 0.7rem 2rem; border: none; border-radius: 10px; margin-top: 0.4rem; cursor: pointer;
    transition: background 0.16s;
}
.edit-btn:hover {background: #218838;}
.success, .error {
    padding: 10px; border-radius: 10px; margin-bottom: 10px; text-align: center; font-weight: 600;
}
.success{background:rgba(16,185,129,.15);border:1px solid #10b981;color:#10b981}
.error{background:rgba(239,68,68,.15);border:1px solid #ef4444;color:#ef4444}
.modal-id-bg {
    position: fixed; top:0; left:0; width:100vw; height:100vh;
    background: rgba(20, 27, 43, 0.80); display: none; z-index: 9999;
    justify-content: center; align-items: center;
}
.modal-id-bg.active {display: flex;}
.modal-id-box {
    background: #23272f; border-radius: 19px; padding: 2.2rem 2.3rem 1.6rem 2.3rem;
    box-shadow: 0 6px 28px rgba(0,0,0,0.21); min-width: 280px; max-width: 98vw;
    text-align: center; position: relative;
}
.modal-id-title {
    font-size: 1.23rem; font-weight: 700; color: #9ad3fa; margin-bottom: 1.1rem;
}
.modal-id-value {
    font-size: 1.38rem; font-weight: 700; color: #ffc107; word-break: break-all;
}
.modal-id-close {
    position: absolute; top: 12px; right: 18px; background: none; border: none;
    font-size: 1.6rem; color: #fff; cursor: pointer; font-weight: 700;
}
@media(max-width: 500px){
    .wrap, .card {max-width:98vw;}
}
</style>
</head>
<body>
<div class="topbar">
  <a class="volver-btn" href="index.php"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>
<div class="wrap">
  <h1>Mi perfil — Demo PixelCrew</h1>
  
  <?php if ($message): ?>
    <div id="msg">
      <div class="<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
    </div>
  <?php endif; ?>
  
  <div class="card">
    <form method="POST" id="avatarForm">
      <input type="hidden" name="action" value="update_avatar">
      <input type="hidden" name="avatar" id="avatarData">
      
      <div class="avatar-box">
        <div class="avatar" id="avatarContainer">
          <img id="avatarImg" src="<?php echo htmlspecialchars($currentUser['avatar'] ?: 'data:image/svg+xml;utf8,' . urlencode('<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><rect width="100%" height="100%" fill="#23243b"/><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" fill="#9ad3fa" font-family="Outfit,Arial" font-size="25">Avatar</text></svg>')); ?>" alt="Avatar">
          <button type="button" class="avatar-edit-btn" id="avatarEditBtn" title="Cambiar avatar"><i class="fa-solid fa-pen"></i></button>
          <input type="file" id="avatarInput" class="avatar-input" accept="image/png,image/jpeg,image/webp">
        </div>
      </div>
    </form>
    
    <div class="field"><i class="fa-solid fa-user"></i><span class="label">Nombre:</span><span class="value" id="nombreVal"><?php echo htmlspecialchars($currentUser['nombre']); ?></span></div>
    <div class="field"><i class="fa-solid fa-key"></i><span class="label">Key:</span><span class="value" id="keyVal"><?php echo htmlspecialchars($currentUser['key']); ?></span></div>
    <div class="field"><i class="fa-solid fa-coins"></i><span class="label">Saldo:</span><span class="value" id="saldoVal"><?php echo htmlspecialchars($currentUser['saldo']); ?></span></div>
    <div class="field"><i class="fa-solid fa-id-card"></i><span class="label">ID:</span><span class="value" id="idVal"><?php echo htmlspecialchars($currentUser['id']); ?></span></div>
    
    <form method="POST" class="edit-box">
      <input type="hidden" name="action" value="update_profile">
      
      <label for="nombreInput">Editar nombre:</label>
      <input type="text" name="nombre" id="nombreInput" placeholder="Nuevo nombre..." value="<?php echo htmlspecialchars($currentUser['nombre']); ?>">
      
      <label for="keyInput">Editar key:</label>
      <input type="text" name="key" id="keyInput" placeholder="Nueva key..." value="<?php echo htmlspecialchars($currentUser['key']); ?>">
      
      <button type="submit" class="edit-btn"><i class="fa-solid fa-save"></i> Guardar cambios</button>
    </form>
  </div>
</div>

<!-- Modal ID -->
<div class="modal-id-bg" id="modalId">
  <div class="modal-id-box">
    <button class="modal-id-close" id="modalIdClose" aria-label="Cerrar">&times;</button>
    <div class="modal-id-title">Tu ID es:</div>
    <div class="modal-id-value" id="modalIdValue"><?php echo htmlspecialchars($currentUser['id']); ?></div>
  </div>
</div>

<script>
// Modal ID logic
document.getElementById("idVal").onclick = function() {
    document.getElementById("modalId").classList.add("active");
};
document.getElementById("modalIdClose").onclick = function() {
    document.getElementById("modalId").classList.remove("active");
};
document.getElementById("modalId").onclick = function(e) {
    if (e.target === this) {
        document.getElementById("modalId").classList.remove("active");
    }
};

// Avatar logic
document.getElementById("avatarEditBtn").onclick = function() {
    document.getElementById("avatarInput").click();
};

document.getElementById("avatarInput").onchange = function(e) {
    const file = e.target.files && e.target.files[0];
    if (!file) return;
    
    if (!file.type.match(/^image\/(png|jpeg|webp)$/)) {
        alert("El avatar debe ser PNG, JPG o WebP");
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(ev) {
        document.getElementById("avatarData").value = ev.target.result;
        document.getElementById("avatarForm").submit();
    };
    reader.readAsDataURL(file);
};

// Auto-ocultar mensaje después de 3 segundos
<?php if ($message): ?>
setTimeout(function() {
    const msg = document.getElementById('msg');
    if (msg) msg.style.display = 'none';
}, 3000);
<?php endif; ?>
</script>
</body>
</html>