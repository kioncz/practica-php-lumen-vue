const { createApp, onMounted, reactive, ref } = Vue;

createApp({
    setup() {
        const tickets = ref([]);
        const userName = ref('');
        const cargando = ref(true);
        const error = ref('');
        const mostrarFormulario = ref(false);
        const guardando = ref(false);
        const formularioError = ref('');
        const nuevoTicket = reactive({
            title: '',
            description: '',
            priority: 'media'
        });

        function obtenerToken() {
            const token = localStorage.getItem('token');

            if (!token) {
                window.location.href = '/login';
                return null;
            }

            return token;
        }

        function cargarUsuario() {
            const storedUser = localStorage.getItem('user');

            if (storedUser) {
                try {
                    const user = JSON.parse(storedUser);

                    if (user.name) {
                        userName.value = user.name;
                        return;
                    }
                } catch (exception) {
                    localStorage.removeItem('user');
                }
            }

            const storedUserName = localStorage.getItem('userName');

            if (storedUserName && storedUserName !== 'client' && storedUserName !== 'cliente') {
                userName.value = storedUserName;
            }
        }

        async function cargarTickets() {
            const token = obtenerToken();

            if (!token) {
                return;
            }

            try {
                const response = await fetch('/tickets', {
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${token}`
                    }
                });
                const data = await response.json();

                if (response.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    throw new Error(data.error || 'No se pudieron cargar los tickets.');
                }

                tickets.value = data;
            } catch (exception) {
                error.value = exception.message;
            } finally {
                cargando.value = false;
            }
        }

        function limpiarFormulario() {
            nuevoTicket.title = '';
            nuevoTicket.description = '';
            nuevoTicket.priority = 'media';
            formularioError.value = '';
        }

        function cerrarFormulario() {
            mostrarFormulario.value = false;
            limpiarFormulario();
        }

        async function crearTicket() {
            const token = obtenerToken();

            if (!token) {
                return;
            }

            guardando.value = true;
            formularioError.value = '';

            try {
                const response = await fetch('/tickets', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(nuevoTicket)
                });
                const data = await response.json();

                if (response.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    const firstError = Object.values(data)[0];
                    throw new Error(
                        Array.isArray(firstError)
                            ? firstError[0]
                            : (data.error || 'No se pudo crear el ticket.')
                    );
                }

                tickets.value.unshift(data.ticket);
                cerrarFormulario();
            } catch (exception) {
                formularioError.value = exception.message;
            } finally {
                guardando.value = false;
            }
        }

        function cerrarSesion() {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            localStorage.removeItem('userName');
            window.location.href = '/login';
        }

        cargarUsuario();
        onMounted(cargarTickets);

        return {
            tickets,
            userName,
            cargando,
            error,
            mostrarFormulario,
            guardando,
            formularioError,
            nuevoTicket,
            cerrarSesion,
            cerrarFormulario,
            crearTicket
        };
    }
}).mount('#app');
