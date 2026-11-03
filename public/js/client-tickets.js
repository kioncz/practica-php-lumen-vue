const { computed, createApp, onMounted, reactive, ref } = Vue;

createApp({
    setup() {
        const tickets = ref([]);
        const filtro = ref('todos');
        const userName = ref('');
        const cargando = ref(true);
        const error = ref('');
        const mensaje = ref('');
        const tipoMensaje = ref('');
        const mostrarFormulario = ref(false);
        const ticketEditando = ref(null);
        const guardando = ref(false);
        const formularioError = ref('');
        const ticketVisto = ref(null);
        const nuevoTicket = reactive({
            title: '',
            description: '',
            priority: 'media'
        });
        const ticketsAbiertos = computed(() => tickets.value.filter((ticket) => ['abierta', 'en_progreso'].includes(ticket.status)));
        const ticketsCerrados = computed(() => tickets.value.filter((ticket) => ticket.status === 'cerrada'));
        const ticketsFiltrados = computed(() => {
            if (filtro.value === 'todos') {
                return tickets.value;
            }

            if (filtro.value === 'abierto') {
                return tickets.value.filter((ticket) => ['abierta', 'en_progreso'].includes(ticket.status));
            }

            return tickets.value.filter((ticket) => ticket.status === 'cerrada');
        });

        function filtrarTickets(nuevoFiltro) {
            filtro.value = nuevoFiltro;
        }

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
                        Authorization: `Bearer ${token}`,
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
            ticketEditando.value = null;
            limpiarFormulario();
        }

        function editarTicket(ticket) {
            ticketEditando.value = ticket;
            nuevoTicket.title = ticket.title;
            nuevoTicket.description = ticket.description;
            nuevoTicket.priority = ticket.priority;
            formularioError.value = '';
        }

        function verTicket(ticket) {
            ticketVisto.value = ticket;
        }

        function cerrarDetalle() {
            ticketVisto.value = null;
        }

        function formatFecha(value) {
            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return 'Fecha no disponible';
            }

            return new Intl.DateTimeFormat('es-ES', {
                dateStyle: 'medium',
                timeStyle: 'short'
            }).format(date);
        }

        function formatEstado(value) {
            const estados = {
                abierta: 'Abierta',
                en_progreso: 'En progreso',
                cerrada: 'Cerrada'
            };

            return estados[value] || value;
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

        async function actualizarTicket() {
            const token = obtenerToken();

            if (!token || !ticketEditando.value) {
                return;
            }

            guardando.value = true;
            formularioError.value = '';

            try {
                const response = await fetch(`/tickets/${ticketEditando.value.id}`, {
                    method: 'PUT',
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
                            : (data.error || 'No se pudo actualizar el ticket.')
                    );
                }

                const index = tickets.value.findIndex((ticket) => ticket.id === data.ticket.id);
                if (index !== -1) {
                    tickets.value[index] = data.ticket;
                }

                cerrarFormulario();
                mensaje.value = data.message;
                tipoMensaje.value = 'success';
            } catch (exception) {
                formularioError.value = exception.message;
            } finally {
                guardando.value = false;
            }
        }




        async function eliminarTicket(ticketId) {
            const token = obtenerToken();

            if (!token || !ticketId) {
                return;
            }

            if (!window.confirm('¿Seguro que deseas eliminar este ticket?')) {
                return;
            }

            try {
                const response = await fetch(`/tickets/${ticketId}`, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.error || 'No se pudo eliminar el ticket.');
                }

                const index = tickets.value.findIndex((ticket) => ticket.id === ticketId);
                if (index !== -1) {
                    tickets.value.splice(index, 1);
                }

                mensaje.value = 'Ticket eliminado correctamente';
                tipoMensaje.value = 'success';
            } catch (exception) {
                mensaje.value = exception.message;
                tipoMensaje.value = 'error';
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
            ticketsAbiertos,
            ticketsCerrados,
            ticketsFiltrados,
            filtro,
            filtrarTickets,
            userName,
            cargando,
            error,
            mensaje,
            tipoMensaje,
            mostrarFormulario,
            ticketEditando,
            eliminarTicket,
            guardando,
            formularioError,
            nuevoTicket,
            cerrarSesion,
            cerrarFormulario,
            crearTicket,
            actualizarTicket,
            editarTicket,
            ticketVisto,
            verTicket,
            cerrarDetalle,
            formatFecha,
            formatEstado
        };
    }
}).mount('#app');
