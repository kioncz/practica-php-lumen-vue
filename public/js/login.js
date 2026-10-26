const { createApp, ref } = Vue;

createApp({
    setup() {
        const email = ref('');
        const password = ref('');
        const mensaje = ref('');
        const tipoMensaje = ref('');
        const cargando = ref(false);

        async function iniciarSesion() {
            cargando.value = true;
            mensaje.value = '';

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email.value,
                        password: password.value
                    })
                });
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.error || data.email?.[0] || 'No se pudo iniciar sesión.'
                    );
                }

                localStorage.removeItem('userName');
                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                localStorage.setItem('userName', data.user.name);
                tipoMensaje.value = 'success';
                mensaje.value = 'Inicio de sesión correcto. Redirigiendo...';
                setTimeout(() => {
                    window.location.href = data.user.role === 'client'
                        ? '/client/tickets'
                        : '/tickets';
                }, 700);
            } catch (error) {
                tipoMensaje.value = 'error';
                mensaje.value = error.message;
            } finally {
                cargando.value = false;
            }
        }

        return {
            email,
            password,
            mensaje,
            tipoMensaje,
            cargando,
            iniciarSesion
        };
    }
}).mount('#app');
