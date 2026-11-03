const { createApp, ref } = Vue;

createApp({
    setup() {
        const app = document.getElementById('app');
        const modoRegistro = ref(app.dataset.initialMode === 'register');
        const email = ref('');
        const password = ref('');
        const name = ref('');
        const role = ref('client');
        const passwordConfirmation = ref('');
        const mensaje = ref('');
        const tituloMensaje = ref('');
        const tipoMensaje = ref('');
        const cargando = ref(false);

        function cambiarModo(modoRegistroNuevo) {
            modoRegistro.value = modoRegistroNuevo;
            email.value = '';
            password.value = '';
            name.value = '';
            role.value = 'client';
            passwordConfirmation.value = '';
            mensaje.value = '';
            tituloMensaje.value = '';
            tipoMensaje.value = '';
            window.history.replaceState({}, '', modoRegistroNuevo ? '/register' : '/login');
        }

        function mostrarRegistro() {
            cambiarModo(true);
        }

        function mostrarLogin() {
            cambiarModo(false);
        }

        function cerrarMensaje() {
            mensaje.value = '';
            tituloMensaje.value = '';
        }

        async function iniciarSesion() {
            cargando.value = true;
            mensaje.value = '';

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: email.value, password: password.value })
                });
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || data.email?.[0] || 'No se pudo iniciar sesión.');
                }

                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                localStorage.setItem('userName', data.user.name);
                tipoMensaje.value = 'success';
                tituloMensaje.value = 'Sesión iniciada';
                mensaje.value = 'Inicio de sesión correcto. Redirigiendo...';
                setTimeout(() => {
                    window.location.href = data.user.role === 'client' ? '/client/tickets' : '/admin/tickets';
                }, 700);
            } catch (error) {
                tipoMensaje.value = 'error';
                tituloMensaje.value = 'No se pudo iniciar sesión';
                mensaje.value = error.message;
            } finally {
                cargando.value = false;
            }
        }

        async function registrar() {
            cargando.value = true;
            mensaje.value = '';

            try {
                const response = await fetch('/register', {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: name.value,
                        email: email.value,
                        role: role.value,
                        password: password.value,
                        password_confirmation: passwordConfirmation.value
                    })
                });
                const data = await response.json();

                if (!response.ok) {
                    const firstError = Object.values(data)[0];
                    throw new Error(Array.isArray(firstError) ? firstError[0] : (data.error || 'No se pudo crear el usuario.'));
                }

                tipoMensaje.value = 'success';
                tituloMensaje.value = 'Cuenta creada';
                mensaje.value = 'Cuenta creada. Ahora puedes iniciar sesión.';
                setTimeout(mostrarLogin, 1200);
            } catch (error) {
                tipoMensaje.value = 'error';
                tituloMensaje.value = 'No se pudo crear la cuenta';
                mensaje.value = error.message;
            } finally {
                cargando.value = false;
            }
        }

        return {
            modoRegistro, email, password, name, role, passwordConfirmation,
            mensaje, tituloMensaje, tipoMensaje, cargando, mostrarRegistro, mostrarLogin,
            iniciarSesion, registrar, cerrarMensaje
        };
    }
}).mount('#app');
