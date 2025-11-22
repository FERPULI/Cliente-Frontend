<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Gestión de Usuarios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="principal.css">
</head>
<body>

    <div class="container">
        <header class="page-header">
            <h2>Usuarios Registrados</h2>
            <button class="btn btn-primary" onclick="window.location.href='registro.php'">
                <i class="fas fa-plus"></i> Registrar Nuevo Usuario
            </button>
        </header>

        <div class="toolbar">
            <div class="search-bar">
                <input type="number" id="buscarId" placeholder="Buscar por ID...">
                <button class="btn" onclick="buscarUsuarioPorId()"><i class="fas fa-search"></i> Buscar</button>
                <button class="btn btn-secondary" onclick="cargarUsuarios()"><i class="fas fa-sync"></i> Mostrar Todos</button>
            </div>
        </div>

        <main class="tabla-container">
            <table id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Correo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </main>
    </div>

    <div id="modalEditar" class="modal-overlay">
        <div class="modal-content">
            <form id="formEditar">
                <h3>Editar Usuario</h3>
                <input type="hidden" id="editId">
        
                <div class="form-group">
                  <label for="editDni">DNI</label>
                  <input type="text" id="editDni" maxlength="8" required>
                </div>
                
                <div class="form-group">
                  <label for="editNombres">Nombres</label>
                  <input type="text" id="editNombres" required>
                </div>

                <div class="form-group">
                  <label for="editApellidos">Apellidos</label>
                  <input type="text" id="editApellidos" required>
                </div>

                <div class="form-group">
                  <label for="editCorreo">Correo Electrónico</label>
                  <input type="email" id="editCorreo" required>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
  <script>
    const API_URL = 'http://localhost:8088/APIANAKONDA/apiweb/public/index.php?resource=usuario';
    const tablaBody = document.querySelector('#tablaUsuarios tbody');

    async function cargarUsuarios() {
      tablaBody.innerHTML = "<tr><td colspan='8'>Cargando...</td></tr>";
      try {
        const res = await fetch(API_URL);
        const usuarios = await res.json();

        tablaBody.innerHTML = '';
        usuarios.forEach(user => {
          tablaBody.innerHTML += `
            <tr>
              <td>${user.id}</td>
              <td>${user.dni}</td>
              <td>${user.nombres}</td>
              <td>${user.apellidos}</td>
              <td>${user.correo}</td>
              <td>
                <button onclick="editarUsuario(${user.id})" title="Editar"><i class="fas fa-pen"></i></button>
                <button onclick="eliminarUsuario(${user.id})" title="Eliminar"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          `;
        });
      } catch (e) {
        tablaBody.innerHTML = "<tr><td colspan='8'>Error al cargar usuarios.</td></tr>";
        console.error(e);
      }
    }

    async function buscarUsuarioPorId() {
      const id = document.getElementById('buscarId').value.trim();
      if (!id) {
        Swal.fire('Campo vacío', 'Ingresa un ID válido', 'warning');
        return;
      }

      try {
        const res = await fetch(`${API_URL}&id=${id}`);
        const user = await res.json();

        if (!user.id) {
          Swal.fire('No encontrado', `Usuario con ID ${id} no existe.`, 'info');
          return;
        }

        tablaBody.innerHTML = `
          <tr>
           <td>${user.id}</td>
            <td>${user.dni}</td>
            <td>${user.nombres}</td>
            <td>${user.apellidos}</td>
            <td>${user.correo}</td>
            <td>
              <button onclick="editarUsuario(${user.id})" title="Editar"><i class="fas fa-pen"></i></button>
              <button onclick="eliminarUsuario(${user.id})" title="Eliminar"><i class="fas fa-trash"></i></button>
            </td>
          </tr>
        `;
      } catch (e) {
        Swal.fire('Error', 'No se pudo buscar el usuario.', 'error');
        console.error(e);
      }
    }

    async function eliminarUsuario(id) {
      const confirm = await Swal.fire({
        title: '¿Eliminar usuario?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      });

      if (!confirm.isConfirmed) return;

      try {
        const res = await fetch(`${API_URL}&id=${id}`, {
          method: 'DELETE'
        });
        const result = await res.json();
        Swal.fire('Eliminado', result.message || 'Usuario eliminado.', 'success');
        cargarUsuarios();
      } catch (e) {
        Swal.fire('Error', 'No se pudo eliminar.', 'error');
      }
    }

    async function editarUsuario(id) {
      try {
        const res = await fetch(`${API_URL}&id=${id}`);
        const user = await res.json();

        document.getElementById('editId').value = user.id;
        document.getElementById('editDni').value = user.dni;
        document.getElementById('editNombres').value = user.nombres;
        document.getElementById('editApellidos').value = user.apellidos;
        document.getElementById('editCorreo').value = user.correo;

        document.getElementById('modalEditar').classList.add('activo');
        //cargarUsuarios();
      } catch (e) {
        Swal.fire('Error', 'No se pudo obtener el usuario.', 'error');
      }
    }

    document.getElementById('formEditar').addEventListener('submit', async (e) => {
      e.preventDefault();

      const id = document.getElementById('editId').value;
      const data = {        
        dni: document.getElementById('editDni').value,
        nombres: document.getElementById('editNombres').value,
        apellidos: document.getElementById('editApellidos').value,
        correo: document.getElementById('editCorreo').value
      };

      try {
        const res = await fetch(`${API_URL}&id=${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams(data)
        });

        const result = await res.json();
        Swal.fire('Actualizado', result.message || 'Usuario actualizado.', 'success');
        cerrarModal();
        cargarUsuarios();
      } catch (e) {
        Swal.fire('Error', 'No se pudo actualizar.', 'error');
      }
    });

    function cerrarModal() {
      document.getElementById('modalEditar').classList.remove('activo');
    }

    cargarUsuarios();
  </script>

</body>
</html>
