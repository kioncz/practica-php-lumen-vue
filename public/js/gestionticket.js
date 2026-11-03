const { computed, createApp, onMounted, ref } = Vue;

createApp({
    setup() {
        const tickets = ref([]);
        const userName = ref('');
        const filtro = ref('todos');
        const busqueda = ref('');
        const cargando = ref(true);
        const error = ref('');
        const ticketVisto = ref(null);

        const ticketsAbiertos = computed(() => tickets.value.filter((ticket) => ['abierta', 'en_progreso'].includes(ticket.status)));
        const ticketsEnProgreso = computed(() => tickets.value.filter((ticket) => ticket.status === 'en_progreso'));
        const ticketsCerrados = computed(() => tickets.value.filter((ticket) => ticket.status === 'cerrada'));

        const ticketsFiltrados = computed(() => {
            const query = busqueda.value.trim().toLowerCase();
            let resultado = tickets.value;

            if (filtro.value === 'abierto') {
                resultado = ticketsAbiertos.value;
            } else if (filtro.value === 'cerrado') {
                resultado = ticketsCerrados.value;
            }

            if (!query) {
                return resultado;
            }

            return resultado.filter((ticket) => {
                const userName = ticket.user ? ticket.user.name : '';
                return `${ticket.id} ${ticket.title} ${userName}`.toLowerCase().includes(query);
            });
        });

        function obtenerToken() {
            const token = localStorage.getItem('token');

            if (!token) {
                window.location.href = '/login';
                return null;
            }

            return token;
        }

        async function cargarTickets() {
            const token = obtenerToken();

            if (!token) {
                return;
            }

            try {
                const response = await fetch('/admin/tickets/data', {
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

                if (response.status === 403) {
                    throw new Error('No tienes permisos para acceder a esta sección.');
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

        function filtrarTickets(nuevoFiltro) {
            filtro.value = nuevoFiltro;
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

        async function actualizarEstado(ticket, nuevoEstado) {
            const estadoAnterior = ticket.status;
            const token = obtenerToken();

            if (!token) {
                return;
            }

            ticket.status = nuevoEstado;

            try {
                const response = await fetch(`/tickets/${ticket.id}`, {
                    method: 'PUT',
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status: nuevoEstado })
                });
                const data = await response.json();

                if (response.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    throw new Error(data.error || 'No se pudo actualizar el estado.');
                }
            } catch (exception) {
                ticket.status = estadoAnterior;
                error.value = exception.message;
            }
        }

        function cargarUsuario() {
            const storedUser = localStorage.getItem('user');

            if (storedUser) {
                const user = JSON.parse(storedUser);
                userName.value = user.name || '';
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
            ticketsFiltrados,
            tickets,
            ticketsAbiertos,
            ticketsEnProgreso,
            ticketsCerrados,
            userName,
            filtro,
            busqueda,
            cargando,
            error,
            filtrarTickets,
            actualizarEstado,
            ticketVisto,
            verTicket,
            cerrarDetalle,
            formatFecha,
            formatEstado,
            cerrarSesion
        };
    }
}).mount('#app');
