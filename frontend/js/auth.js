/**
 * auth.js — helper compartido por todas las vistas del frontend.
 *
 * El frontend es 100% estático (HTML + CSS + JS, servido por Nginx) y
 * NO accede nunca directamente a la base de datos: todo pasa por las
 * APIs (api-usuarios y api-gestion), tal como pide la arquitectura del
 * proyecto. La autenticación es por TOKEN (no por cookies), porque
 * cada servicio corre en un puerto/origen distinto.
 */

// URLs base de las APIs. Si el frontend se sirve con Docker Compose
// (ver docker-compose.yml) quedan expuestas en estos puertos.
const API_USUARIOS = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? `${window.location.protocol}//${window.location.hostname}:8081`
    : '/api-usuarios';

const API_GESTION = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? `${window.location.protocol}//${window.location.hostname}:8082`
    : '/api-gestion';

const TOKEN_KEY = 'geobrillo_token';

function getToken() {
    return localStorage.getItem(TOKEN_KEY) || '';
}

function setToken(token) {
    localStorage.setItem(TOKEN_KEY, token);
}

function clearToken() {
    localStorage.removeItem(TOKEN_KEY);
}

/**
 * Consulta a api-usuarios/sesion.php si el token guardado sigue
 * siendo válido. Devuelve el objeto usuario, o null si no hay sesión.
 */
async function obtenerSesionActual() {
    const token = getToken();
    if (!token) return null;

    try {
        const res = await fetch(`${API_USUARIOS}/sesion.php?token=${encodeURIComponent(token)}`);
        const data = await res.json();
        return data.exito ? data.usuario : null;
    } catch (err) {
        console.error('Error consultando la sesión:', err);
        return null;
    }
}

/** true si el usuario está logueado y su rol NO es Ciudadano. */
function puedeGestionar(usuario) {
    return !!usuario && usuario.nombre_rol !== 'Ciudadano';
}

async function cerrarSesion() {
    const token = getToken();
    try {
        await fetch(`${API_USUARIOS}/logout.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ token })
        });
    } catch (err) {
        console.error('Error cerrando sesión:', err);
    } finally {
        clearToken();
        window.location.href = 'index.html';
    }
}

/**
 * Protege una vista de gestión (usuarios/camiones/contenedores/centros).
 * Se llama al principio de cada página protegida. Si no hay sesión o
 * el rol es Ciudadano, redirige al home. Esta es una capa de UX: la
 * protección real y obligatoria está en las APIs (ver middleware/Auth.php
 * en api-usuarios y api-gestion), que devuelven 401/403 igual si
 * alguien intenta saltarse esta pantalla y llamar al endpoint directo.
 */
async function protegerVistaDeGestion() {
    const usuario = await obtenerSesionActual();
    if (!puedeGestionar(usuario)) {
        alert('No tenés permiso para acceder a esta sección.');
        window.location.href = 'index.html';
        return null;
    }
    return usuario;
}

/**
 * Arma el menú (sidebar + navbar) del index según el estado de sesión:
 *  - Sin sesión / Ciudadano: solo Inicio, Mapa, Contacto, Login, Registro.
 *  - Logueado con rol de gestión (Chofer/Operador/Cuadrilla/Administrador):
 *    aparecen además Usuarios, Contenedores, Camiones, Centros de Acopio,
 *    y el botón de Login/Registro se reemplaza por "Cerrar sesión".
 */
async function renderizarNavSegunSesion() {
    const usuario = await obtenerSesionActual();
    const gestion = puedeGestionar(usuario);

    const bloqueGestion = document.querySelectorAll('.nav-solo-gestion');
    bloqueGestion.forEach(el => el.classList.toggle('hidden', !gestion));

    const botonesInvitado = document.querySelectorAll('.nav-invitado');
    const botonesSesion = document.querySelectorAll('.nav-con-sesion');

    if (usuario) {
        botonesInvitado.forEach(el => el.classList.add('hidden'));
        botonesSesion.forEach(el => el.classList.remove('hidden'));

        const saludo = document.querySelectorAll('.nav-saludo');
        saludo.forEach(el => {
            el.textContent = `Hola, ${usuario.pri_nom} (${usuario.nombre_rol})`;
        });
    } else {
        botonesInvitado.forEach(el => el.classList.remove('hidden'));
        botonesSesion.forEach(el => el.classList.add('hidden'));
    }

    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', cerrarSesion);
    }
}
