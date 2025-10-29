// admin.php


<?php
// admin.php - Panel de administración (solo para admins)
require_once 'auth_config.php';
require_once 'auth_functions.php';

// Proteger esta página - solo admins
requireAdmin();

$currentUser = getCurrentUser($usersFile);
$isSuperAdminUser = isSuperAdmin();

// Procesar acciones AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['ajax_action'];
    $users = json_decode(file_get_contents($usersFile), true);
    
    switch($action) {
        case 'get_users':
            $usersList = [];
            foreach ($users as $email => $user) {
                $usersList[] = array_merge($user, ['email' => $email]);
            }
            echo json_encode(['success' => true, 'users' => $usersList, 'total' => count($usersList)]);
            exit;
            
        case 'get_admins':
            $adminsList = [];
            foreach ($users as $email => $user) {
                if (isset($user['role']) && $user['role'] === 'admin') {
                    $adminsList[] = array_merge($user, ['email' => $email]);
                }
            }
            echo json_encode(['success' => true, 'admins' => $adminsList]);
            exit;
            
        case 'update_user':
            $email = $_POST['email'] ?? '';
            $updates = json_decode($_POST['updates'] ?? '{}', true);
            
            if (isset($users[$email])) {
                // No permitir cambiar el email del super admin
                if (isset($users[$email]['is_super_admin']) && $users[$email]['is_super_admin'] && isset($updates['email']) && $updates['email'] !== $email) {
                    echo json_encode(['success' => false, 'error' => 'No se puede cambiar el email del super admin']);
                    exit;
                }
                
                foreach ($updates as $key => $value) {
                    if ($key === 'password' && !empty($value)) {
                        $users[$email]['password'] = password_hash($value, PASSWORD_DEFAULT);
                    } elseif ($key === 'email' && $value !== $email) {
                        // Cambiar email (mover la cuenta)
                        $newEmail = $value;
                        if (isset($users[$newEmail])) {
                            echo json_encode(['success' => false, 'error' => 'El nuevo email ya existe']);
                            exit;
                        }
                        $users[$newEmail] = $users[$email];
                        $users[$newEmail]['email'] = $newEmail;
                        unset($users[$email]);
                        $email = $newEmail;
                    } else {
                        $users[$email][$key] = $value;
                    }
                }
                file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                echo json_encode(['success' => true, 'message' => 'Usuario actualizado']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            }
            exit;
            
        case 'delete_user':
            $email = $_POST['email'] ?? '';
            
            // No permitir eliminar super admin
            if (isset($users[$email]['is_super_admin']) && $users[$email]['is_super_admin']) {
                echo json_encode(['success' => false, 'error' => 'No se puede eliminar el super admin']);
                exit;
            }
            
            if (isset($users[$email])) {
                unset($users[$email]);
                file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                echo json_encode(['success' => true, 'message' => 'Usuario eliminado']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            }
            exit;
            
        case 'rotate_key':
            $email = $_POST['email'] ?? '';
            if (isset($users[$email])) {
                $users[$email]['key'] = 'demo-' . bin2hex(random_bytes(8));
                file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                echo json_encode(['success' => true, 'message' => 'Key rotada exitosamente', 'new_key' => $users[$email]['key']]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            }
            exit;
            
        case 'toggle_admin':
            $email = $_POST['email'] ?? '';
            
            // No permitir quitar admin al super admin
            if (isset($users[$email]['is_super_admin']) && $users[$email]['is_super_admin']) {
                echo json_encode(['success' => false, 'error' => 'No se puede quitar admin al super admin']);
                exit;
            }
            
            if (isset($users[$email])) {
                $currentRole = $users[$email]['role'] ?? 'user';
                $users[$email]['role'] = ($currentRole === 'admin') ? 'user' : 'admin';
                file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                $newRole = $users[$email]['role'];
                echo json_encode(['success' => true, 'message' => 'Rol cambiado a ' . $newRole, 'new_role' => $newRole]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
            }
            exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    exit;
}

// Variable para saber qué vista mostrar
$view = $_GET['view'] ?? 'users'; // users, admins, status, home
?>
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Panel Admin — Demo PixelCrew</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--primary-color:#f8f9fd;--primary-hover:#e6e8f0;--secondary-color:#6c5ce7;--accent-color:#00cec9;--success-color:#00b894;--error-color:#ff7675;--warning-color:#fdcb6e;--radius:16px;--transition:all .3s cubic-bezier(.25,.8,.25,1)}
.light{--background-color:#ffffff;--card-background:rgba(248,249,253,.5);--text-color:#2d3436;--text-muted:#636e72;--border-color:rgba(0,0,0,.08);--highlight-color:rgba(108,92,231,.12);--shadow:0 10px 22px rgba(0,0,0,.08);--shadow-2:0 16px 40px rgba(108,92,231,.20)}
.dark{--background-color:#1a1b2e;--card-background:rgba(37,38,64,.5);--text-color:#e5e5e5;--text-muted:#b2becd;--border-color:rgba(255,255,255,.08);--highlight-color:rgba(162,155,254,.18);--shadow:0 10px 24px rgba(0,0,0,.25);--shadow-2:0 18px 42px rgba(0,0,0,.35)}
*{box-sizing:border-box;min-inline-size:0}
html,body{height:100%;margin:0;font-family:'Outfit',sans-serif;background:var(--background-color);color:var(--text-color);min-height:100dvh;overflow-x:hidden}
img,video,canvas,svg{max-width:100%;height:auto;display:block}
body::-webkit-scrollbar{width:8px}
body::-webkit-scrollbar-track{background:var(--background-color)}
body::-webkit-scrollbar-thumb{background:linear-gradient(45deg,var(--primary-color),var(--secondary-color));border-radius:10px}
.topbar{position:sticky;top:0;z-index:30;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:clamp(10px,2.5vw,14px) clamp(12px,3vw,16px);background:var(--card-background);border-bottom:1px solid var(--border-color);box-shadow:var(--shadow);backdrop-filter:blur(14px)}
.tb-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.tb-actions a,.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 14px;border-radius:12px;border:1px solid var(--secondary-color);background:linear-gradient(135deg,var(--secondary-color),var(--primary-color));font-weight:700;color:#fff;text-decoration:none;box-shadow:0 6px 20px rgba(108,92,231,.3);cursor:pointer;transition:var(--transition);min-height:40px}
.btn:hover{transform:translateY(-2px) scale(1.03)}
.btn.ghost{background:transparent;border:1px solid var(--secondary-color);color:var(--secondary-color)}
.btn.alt{background:var(--error-color);border:1px solid #b91c1c}
.btn.ok{background:var(--success-color);border:1px solid #0f8f6a}
.wrap{max-width:1100px;margin:0 auto;padding:clamp(14px,3.5vw,20px);position:relative;z-index:1;color:var(--text-color)}
.hero{display:flex;flex-direction:column;align-items:center;gap:8px;margin:18px 0;text-align:center}
.heading{margin:0;font-size:clamp(22px,5.5vw,32px);font-weight:900;line-height:1.15;word-break:break-word;background:linear-gradient(135deg,var(--secondary-color),var(--accent-color));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.subwelcome{margin:0;font-size:clamp(14px,3.8vw,16px);color:var(--text-muted)}
.card{background:var(--card-background);border:1px solid var(--border-color);border-radius:var(--radius);padding:clamp(12px,3vw,16px);margin-bottom:clamp(10px,2.6vw,16px);box-shadow:var(--shadow);transition:var(--transition)}
.card:hover{transform:translateY(-6px);box-shadow:var(--shadow-2);border-color:var(--secondary-color)}
.card h3{margin:0 0 10px;display:flex;align-items:center;gap:8px;color:var(--text-color)}
.card>*{min-width:0}
.input,select,textarea{width:100%;padding:12px 14px;border-radius:12px;border:1px solid var(--border-color);background:var(--card-background);color:var(--text-color);outline:none;transition:.3s}
.input:focus,select:focus,textarea:focus{border-color:var(--secondary-color);box-shadow:0 0 0 3px var(--highlight-color)}
.kpigrid{display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))}
.users{display:grid;gap:12px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
.badge{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;border:1px solid var(--border-color);font-size:12px;color:var(--text-color);background:var(--card-background);max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.actions{display:flex;gap:10px;flex-wrap:wrap}
.modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:40}
.modal.show{display:flex}
.modal .overlay{position:absolute;inset:0;background:rgba(0,0,0,.6)}
.modal .content{position:relative;width:min(560px,92vw);background:var(--card-background);border:1px solid var(--border-color);border-radius:16px;box-shadow:var(--shadow-2);padding:16px;z-index:1;max-height:90vh;overflow-y:auto}
.formgrid{display:grid;gap:10px;grid-template-columns:1fr}
.formgrid .full{grid-column:1/-1}
.avatar{width:42px;height:42px;border-radius:50%;overflow:hidden;display:grid;place-items:center;background:var(--highlight-color);font-weight:900;flex:0 0 auto}
.avatar img{width:100%;height:100%;object-fit:cover;display:block}
.avatar.large{width:48px;height:48px}
@media (min-width:680px){.formgrid{grid-template-columns:1fr 1fr}}
@media (max-width:480px){.tb-actions{gap:6px}.btn{padding:9px 12px}}
</style>
</head>
<body class="dark">
<div class="topbar">
  <div style="font-weight:800;font-size:1.2rem">Panel Admin</div>
  <div class="tb-actions">
    <a href="index.php" class="btn ghost"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    <button id="themeToggle" class="btn ghost" aria-label="Cambiar tema"><i class="fa-solid fa-moon"></i></button>
    <a href="logout.php" class="btn alt"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
  </div>
</div>

<div class="wrap">
  <div class="hero">
    <h1 class="heading">Panel de Administración</h1>
    <p class="subwelcome">Gestiona usuarios, perfiles y configuraciones del sistema</p>
  </div>

  <div class="card">
    <h3><i class="fa-solid fa-filter"></i> Filtros y acciones</h3>
    <div class="kpigrid" style="margin-bottom:10px">
      <input id="q" class="input" placeholder="Buscar por usuario o correo">
      <select id="roleFilter" class="input">
        <option value="">Todos los roles</option>
        <option value="user">Usuario</option>
        <option value="admin">Admin</option>
      </select>
    </div>
    <div class="actions">
      <button id="showUsers" class="btn"><i class="fa-solid fa-users"></i> Cargar usuarios</button>
      <button id="refreshBtn" class="btn ghost"><i class="fa-solid fa-rotate"></i> Actualizar</button>
    </div>
  </div>

  <div class="card">
    <h3><i class="fa-solid fa-users-gear"></i> Usuarios del sistema</h3>
    <div id="usersMeta" class="kpigrid" style="margin:8px 0 12px"></div>
    <div id="users" class="users"></div>
  </div>
</div>

<!-- Modal de edición -->
<div class="modal" id="editorModal">
  <div class="overlay" id="modalClose"></div>
  <div class="content">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
      <div class="avatar large" id="edAvatar"></div>
      <div style="min-width:0">
        <h4 id="edTitle" class="heading" style="font-size:20px;margin:0"></h4>
        <p class="subwelcome" id="edEmail" style="margin:0;word-break:break-all"></p>
      </div>
    </div>
    
    <div class="formgrid">
      <div>
        <label style="display:block;margin-bottom:6px;font-weight:600">Nombre</label>
        <input id="edNombre" class="input" placeholder="Nombre de usuario">
      </div>
      <div>
        <label style="display:block;margin-bottom:6px;font-weight:600">Rol</label>
        <select id="edRole" class="input">
          <option value="user">Usuario</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div>
        <label style="display:block;margin-bottom:6px;font-weight:600">Saldo</label>
        <input id="edSaldo" class="input" type="number" min="0" placeholder="0">
      </div>
      <div>
        <label style="display:block;margin-bottom:6px;font-weight:600">API Key</label>
        <input id="edKey" class="input" placeholder="demo-api">
      </div>
      <div class="full">
        <label style="display:block;margin-bottom:6px;font-weight:600">Nueva contraseña (dejar vacío para no cambiar)</label>
        <input id="edPassword" class="input" type="password" placeholder="••••••••">
      </div>
    </div>
    
    <div class="actions" style="margin-top:16px;justify-content:space-between">
      <div class="actions">
        <button id="btnRotateKey" class="btn"><i class="fa-solid fa-key"></i> Rotar Key</button>
        <button id="btnSaveUser" class="btn ok"><i class="fa-solid fa-check"></i> Guardar</button>
      </div>
      <div class="actions">
        <button id="btnDeleteUser" class="btn alt"><i class="fa-solid fa-trash"></i> Eliminar</button>
        <button id="btnCloseModal" class="btn ghost">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
const body=document.body;
const themeBtn=document.getElementById('themeToggle');
const savedTheme=localStorage.getItem('theme');
if(savedTheme==='light'){body.classList.remove('dark');body.classList.add('light')}
function setIcon(){themeBtn.innerHTML=body.classList.contains('dark')?'<i class="fa-solid fa-moon"></i>':'<i class="fa-solid fa-sun"></i>'}
setIcon();
themeBtn.addEventListener('click',()=>{
  if(body.classList.contains('dark')){
    body.classList.remove('dark');body.classList.add('light');localStorage.setItem('theme','light')
  }else{
    body.classList.remove('light');body.classList.add('dark');localStorage.setItem('theme','dark')
  }
  setIcon()
});

const list=document.getElementById('users');
const usersMeta=document.getElementById('usersMeta');
const qInput=document.getElementById('q');
const roleFilter=document.getElementById('roleFilter');
const refreshBtn=document.getElementById('refreshBtn');
const showUsers=document.getElementById('showUsers');

function badge(t,icon){return`<span class="badge">${icon?`<i class="${icon}"></i>`:''}${t}</span>`}
function initials(name){const p=(name||'?').split(' ');return (p[0][0]||'U')+((p[1]||'')[0]||'')}
function avatarHTML(u,size){
  const w=size||42;
  const ini=initials(u.nombre||'Usuario');
  const avatarSrc = u.avatar || '';
  if(avatarSrc){
    return `<div class="avatar${w>42?' large':''}" style="width:${w}px;height:${w}px"><img src="${avatarSrc}" alt="" onerror="this.style.display='none';this.parentElement.innerHTML='${ini}'"></div>`;
  }
  return `<div class="avatar${w>42?' large':''}" style="width:${w}px;height:${w}px">${ini}</div>`;
}

function userCard(u){
  return `
<section class="card usr" data-email="${u.email}" data-role="${u.role||'user'}">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:10px">
    <div style="display:flex;align-items:center;gap:10px;min-width:0">
      ${avatarHTML(u,42)}
      <div style="min-width:0">
        <div style="font-weight:800;word-break:break-all">${u.nombre||'Usuario'}</div>
        <div class="subwelcome" style="word-break:break-all">${u.email}</div>
      </div>
    </div>
    <button class="btn" data-action="open_editor"><i class="fa-solid fa-pen-to-square"></i> Editar</button>
  </div>
  <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px">
    ${badge(u.role==='admin'?'Admin':'Usuario','fa-solid fa-user-shield')}
    ${badge('Key: '+(u.key||'demo-api'),'fa-solid fa-key')}
    ${badge('Saldo: '+(u.saldo||0),'fa-solid fa-coins')}
    ${badge('ID: '+(u.id||'N/A'),'fa-solid fa-id-card')}
  </div>
</section>`;
}

let allUsers=[];
async function fetchUsers(q=''){
  try{
    const formData = new FormData();
    formData.append('ajax_action', 'get_users');
    const res=await fetch('admin.php',{method:'POST',body:formData});
    const data=await res.json();
    
    if(!data.success){
      list.innerHTML='<div class="badge">Error al cargar usuarios</div>';
      return;
    }
    
    allUsers=data.users||[];
    const filtered=allUsers.filter(u=>{
      const mText=!q||u.nombre.toLowerCase().includes(q.toLowerCase())||u.email.toLowerCase().includes(q.toLowerCase());
      const mRole=!roleFilter.value||u.role===roleFilter.value;
      return mText&&mRole;
    });
    
    list.innerHTML=filtered.map(u=>userCard(u)).join('');
    const total=allUsers.length;
    const admins=allUsers.filter(u=>u.role==='admin').length;
    usersMeta.innerHTML=[badge('Total: '+total,'fa-solid fa-users'),badge('Admins: '+admins,'fa-solid fa-crown')].join('');
  }catch(err){
    list.innerHTML='<div class="badge">Error: '+err.message+'</div>';
  }
}

let usersVisible=false;
showUsers.addEventListener('click',()=>{
  if(!usersVisible){
    usersVisible=true;
    fetchUsers();
  }else{
    list.innerHTML='';
    usersMeta.innerHTML='';
    usersVisible=false;
  }
});

qInput.addEventListener('input',()=>{if(usersVisible)fetchUsers(qInput.value.trim())});
roleFilter.addEventListener('change',()=>{if(usersVisible)fetchUsers(qInput.value.trim())});
refreshBtn.addEventListener('click',()=>{if(usersVisible)fetchUsers(qInput.value.trim())});

// Editor modal
const editorModal=document.getElementById('editorModal');
const modalClose=document.getElementById('modalClose');
const btnCloseModal=document.getElementById('btnCloseModal');
const btnRotateKey=document.getElementById('btnRotateKey');
const btnDeleteUser=document.getElementById('btnDeleteUser');
const btnSaveUser=document.getElementById('btnSaveUser');
const edTitle=document.getElementById('edTitle');
const edEmail=document.getElementById('edEmail');
const edAvatar=document.getElementById('edAvatar');
const edNombre=document.getElementById('edNombre');
const edRole=document.getElementById('edRole');
const edSaldo=document.getElementById('edSaldo');
const edKey=document.getElementById('edKey');
const edPassword=document.getElementById('edPassword');

let currentUserEmail=null;

function showModal(){editorModal.classList.add('show')}
function hideModal(){editorModal.classList.remove('show')}

list.addEventListener('click',e=>{
  const btn=e.target.closest('button[data-action]');
  if(!btn)return;
  const card=e.target.closest('.usr');
  const email=card.dataset.email;
  if(btn.dataset.action==='open_editor'){
    openEditor(email);
  }
});

function openEditor(email){
  const u=allUsers.find(x=>x.email===email);
  if(!u)return;
  
  currentUserEmail=email;
  edTitle.textContent='Editar usuario';
  edEmail.textContent=u.email;
  edAvatar.innerHTML=avatarHTML(u,48);
  edNombre.value=u.nombre||'Usuario';
  edRole.value=u.role||'user';
  edSaldo.value=u.saldo||0;
  edKey.value=u.key||'demo-api';
  edPassword.value='';
  
  showModal();
}

modalClose.addEventListener('click',hideModal);
btnCloseModal.addEventListener('click',hideModal);

btnRotateKey.addEventListener('click',async()=>{
  if(!currentUserEmail)return;
  if(!confirm('¿Rotar la API Key de este usuario?'))return;
  
  const formData=new FormData();
  formData.append('ajax_action','rotate_key');
  formData.append('email',currentUserEmail);
  
  try{
    const res=await fetch('admin.php',{method:'POST',body:formData});
    const data=await res.json();
    alert(data.message||data.error||'Key rotada');
    if(data.success && data.new_key){
      edKey.value=data.new_key;
    }
    if(usersVisible)fetchUsers(qInput.value.trim());
  }catch(err){
    alert('Error: '+err.message);
  }
});

btnDeleteUser.addEventListener('click',async()=>{
  if(!currentUserEmail)return;
  if(!confirm('¿Eliminar esta cuenta permanentemente?'))return;
  
  const formData=new FormData();
  formData.append('ajax_action','delete_user');
  formData.append('email',currentUserEmail);
  
  try{
    const res=await fetch('admin.php',{method:'POST',body:formData});
    const data=await res.json();
    alert(data.message||data.error||'Usuario eliminado');
    hideModal();
    if(usersVisible)fetchUsers(qInput.value.trim());
  }catch(err){
    alert('Error: '+err.message);
  }
});

btnSaveUser.addEventListener('click',async()=>{
  if(!currentUserEmail)return;
  
  const updates={
    nombre:edNombre.value.trim(),
    role:edRole.value,
    saldo:parseInt(edSaldo.value)||0,
    key:edKey.value.trim()
  };
  
  if(edPassword.value.trim()){
    updates.password=edPassword.value.trim();
  }
  
  const formData=new FormData();
  formData.append('ajax_action','update_user');
  formData.append('email',currentUserEmail);
  formData.append('updates',JSON.stringify(updates));
  
  try{
    const res=await fetch('admin.php',{method:'POST',body:formData});
    const data=await res.json();
    alert(data.message||data.error||'Usuario actualizado');
    hideModal();
    if(usersVisible)fetchUsers(qInput.value.trim());
  }catch(err){
    alert('Error: '+err.message);
  }
});
</script>
</body>
</html>