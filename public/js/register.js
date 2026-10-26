const { createApp, ref } = Vue;

createApp({
    setup() {
        const name = ref('');
        const email = ref('');
        const role = ref('client');
        const password = ref('');
        const passwordConfirmation = ref('');
        const mensaje = ref('');
        const tipoMensaje = ref('');
        const cargando = ref(false);

        async function registrar() {
            cargando.value = true;
            mensaje.value = '';

            try {
                const response = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json'
                    },
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
                    throw new Error(
                        Array.isArray(firstError)
                            ? firstError[0]
                            : (data.error || 'No se pudo crear el usuario.')
                    );
                }

                tipoMensaje.value = 'success';
                mensaje.value = 'Usuario creado exitosamente. Redirigiendo al login...';
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } catch (error) {
                tipoMensaje.value = 'error';
                mensaje.value = error.message;
            } finally {
                cargando.value = false;
            }
        }

        function irAlLogin() {
            window.location.href = '/login';
        }

        return {
            name,
            email,
            role,
            password,
            passwordConfirmation,
            mensaje,
            tipoMensaje,
            cargando,
            registrar,
            irAlLogin
        };
    }
}).mount('#app');
