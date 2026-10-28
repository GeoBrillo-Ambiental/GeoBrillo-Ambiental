const API_USUARIOS = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? `${window.location.protocol}//${window.location.hostname}:8081`
    : '/api-usuarios';

const API_GESTION = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? `${window.location.protocol}//${window.location.hostname}:8082`
    : '/api-gestion';

const API_RECOLECCION = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
    ? `${window.location.protocol}//${window.location.hostname}:8083`
    : '/api-recoleccion';

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

async function protegerVistaConSesion() {
    const usuario = await obtenerSesionActual();
    if (!usuario) {
        window.location.href = 'login.html';
        return null;
    }
    return usuario;
}

async function protegerVistaDeGestion() {
    const usuario = await obtenerSesionActual();
    if (!puedeGestionar(usuario)) {
        alert('No tenés permiso para acceder a esta sección.');
        window.location.href = 'index.html';
        return null;
    }
    return usuario;
}

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

function esc(valor, vacio = '-') {
    return String(valor ?? vacio)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

function attr(objeto) {
    return esc(JSON.stringify(objeto));
}

document.addEventListener('click', function (e) {
    const enlace = e.target.closest('a.js-volver');
    if (!enlace) return;
    if (history.length > 1) {
        e.preventDefault();
        history.back();
    }
});
