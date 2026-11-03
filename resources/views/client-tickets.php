<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis tickets</title>
    <link rel="stylesheet" href="/css/client-tickets.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <main id="app" class="tickets-page" v-cloak>
        <header class="page-header">
            <div>
                <div class="brand-mark brand-mark-dark"><span>+</span> pulso</div>
                <p class="eyebrow">Tu espacio de soporte</p>
                <h1>Hola, {{ userName || 'cliente' }}.</h1>
                <p class="subtitle">Aquí tienes el pulso de tus solicitudes.</p>
            </div>
            <button class="logout-button" type="button" @click="cerrarSesion"><span aria-hidden="true">↗</span> Salir</button>
        </header>
        <section class="client-overview" aria-label="Resumen de mis tickets">
            <div>
                <span class="overview-label">Tu actividad</span>
                <strong>{{ tickets.length }} tickets en total</strong>
                <span>El seguimiento empieza aquí.</span>
            </div>
            <div><span>Abiertos</span><strong>{{ ticketsAbiertos.length }}</strong></div>
            <div><span>Cerrados</span><strong>{{ ticketsCerrados.length }}</strong></div>
        </section>
        <div v-if="mensaje" class="status-message" :class="tipoMensaje">{{ mensaje }}</div>
        <menu class="ticket-filters" aria-label="Filtrar tickets">
            <span class="filter-label">Mostrar</span>
            <button class="filter-button" type="button" @click="filtrarTickets('todos')" :class="{ active: filtro === 'todos' }">Todos</button>
            <button class="filter-button" type="button" @click="filtrarTickets('abierto')" :class="{ active: filtro === 'abierto' }">Abiertos</button>
            <button class="filter-button" type="button" @click="filtrarTickets('cerrado')" :class="{ active: filtro === 'cerrado' }">Cerrados</button>
            <button class="add-ticket-button" type="button" @click="mostrarFormulario = true">
                <span aria-hidden="true">+</span>
                Nuevo ticket
            </button>
        </menu>

        <div v-if="mostrarFormulario || ticketEditando" class="modal-backdrop" @click.self="cerrarFormulario">
            <section class="ticket-form-card" role="dialog" aria-modal="true" :aria-labelledby="ticketEditando ? 'edit-ticket-title' : 'new-ticket-title'">
                <div class="form-header">
                    <div>
                        <p class="eyebrow">{{ ticketEditando ? 'Editar solicitud' : 'Nueva solicitud' }}</p>
                        <h2 :id="ticketEditando ? 'edit-ticket-title' : 'new-ticket-title'">
                            {{ ticketEditando ? 'Editar ticket' : 'Crear ticket' }}
                        </h2>
                    </div>
                    <button class="close-button" type="button" aria-label="Cerrar" @click="cerrarFormulario">×</button>
                </div>

                <form @submit.prevent="ticketEditando ? actualizarTicket() : crearTicket()">
                    <label for="ticket-title">Título</label>
                    <input id="ticket-title" v-model="nuevoTicket.title" type="text" maxlength="255" required>

                    <label for="ticket-description">Descripción</label>
                    <textarea id="ticket-description" v-model="nuevoTicket.description" rows="5" required></textarea>

                    <label for="ticket-priority">Prioridad</label>
                    <select id="ticket-priority" v-model="nuevoTicket.priority" required>
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                    </select>

                    <p v-if="formularioError" class="form-error">{{ formularioError }}</p>
                    <div class="form-actions">
                        <button class="cancel-button" type="button" @click="cerrarFormulario">Cancelar</button>
                        <button class="submit-button" type="submit" :disabled="guardando">
                            {{ guardando ? 'Guardando...' : (ticketEditando ? 'Guardar cambios' : 'Crear ticket') }}
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <p v-if="cargando" class="status-message">Cargando tus tickets...</p>
        <p v-else-if="error" class="status-message error">{{ error }}</p>
        <section v-else-if="ticketsFiltrados.length" class="tickets-grid">
            <article v-for="ticket in ticketsFiltrados" :key="ticket.id" class="ticket-card">
                <div class="ticket-top">
                    <span class="ticket-id"><span class="ticket-mark">#</span> Ticket {{ ticket.id }}</span>
                    <span class="badge" :class="`priority-${ticket.priority}`">
                        {{ ticket.priority }}
                    </span>
                </div>
                <h2>{{ ticket.title }}</h2>
                <p class="description">{{ ticket.description }}</p>
                <div class="ticket-footer">
                    <span>Estado</span>
                    <strong :class="`status-${ticket.status}`">{{ formatEstado(ticket.status) }}</strong>
                </div>
                <span v-if="ticket.created_at" class="ticket-date">Creado {{ formatFecha(ticket.created_at) }}</span>
                <div class="ticket-actions">
                    <button class="details-button" type="button" @click="verTicket(ticket)">
                        Ver información
                    </button>
                    <button class="edit-ticket-button icon-button" type="button" aria-label="Editar ticket" title="Editar ticket" @click="editarTicket(ticket)">
                        <span aria-hidden="true">✎</span>
                    </button>
                    <button class="delete-ticket-button icon-button" type="button" aria-label="Eliminar ticket" title="Eliminar ticket" @click="eliminarTicket(ticket.id)">
                        <span aria-hidden="true">🗑</span>
                    </button>
                </div>
            </article>
        </section>
        <div v-else class="empty-state">
            <h2>{{ filtro === 'todos' ? 'Aún no tienes tickets' : 'No hay tickets en esta categoría' }}</h2>
            <p>{{ filtro === 'todos' ? 'Cuando registres una solicitud, aparecerá aquí.' : 'Prueba con otro filtro.' }}</p>
        </div>

        <div v-if="ticketVisto" class="modal-backdrop" @click.self="cerrarDetalle">
            <section class="ticket-detail-card" role="dialog" aria-modal="true" aria-labelledby="ticket-detail-title">
                <div class="form-header">
                    <div>
                        <p class="eyebrow">Información del ticket #{{ ticketVisto.id }}</p>
                        <h2 id="ticket-detail-title">{{ ticketVisto.title }}</h2>
                    </div>
                    <button class="close-button" type="button" aria-label="Cerrar" @click="cerrarDetalle">×</button>
                </div>
                <div class="detail-status-row">
                    <span>Estado</span>
                    <strong :class="`status-${ticketVisto.status}`">{{ formatEstado(ticketVisto.status) }}</strong>
                </div>
                <dl class="ticket-details">
                    <div><dt>Prioridad</dt><dd>{{ ticketVisto.priority }}</dd></div>
                    <div><dt>Descripción</dt><dd>{{ ticketVisto.description }}</dd></div>
                    <div v-if="ticketVisto.created_at"><dt>Creado</dt><dd>{{ formatFecha(ticketVisto.created_at) }}</dd></div>
                </dl>
            </section>
        </div>
    </main>

    <script src="/js/client-tickets.js"></script>
</body>
</html>
