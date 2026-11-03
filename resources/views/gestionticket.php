<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de tickets</title>
    <link rel="stylesheet" href="/css/gestionticket.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <main id="app" class="tickets-page" v-cloak>
        <header class="page-header">
            <div>
                <div class="brand-mark"><span>+</span> pulso</div>
                <p class="eyebrow">Mesa de operaciones · {{ userName }}</p>
                <h1>Gestión de tickets</h1>
                <p class="subtitle">Prioriza cada solicitud y mantén al equipo al tanto.</p>
            </div>
            <button class="logout-button" type="button" @click="cerrarSesion"><span aria-hidden="true">↗</span> Salir</button>
        </header>

        <div v-if="error" class="status-message error">{{ error }}</div>
        <section class="overview" aria-label="Resumen de tickets">
            <div class="overview-intro">
                <span class="section-kicker">Resumen de hoy</span>
                <strong>{{ tickets.length }} solicitudes</strong>
                <span>Seguimiento en un solo lugar</span>
            </div>
            <div class="stat-card">
                <span>Por atender</span>
                <strong>{{ ticketsAbiertos.length }}</strong>
            </div>
            <div class="stat-card stat-card-accent">
                <span>En progreso</span>
                <strong>{{ ticketsEnProgreso.length }}</strong>
            </div>
            <div class="stat-card">
                <span>Resueltos</span>
                <strong>{{ ticketsCerrados.length }}</strong>
            </div>
        </section>

        <section class="toolbar" aria-label="Herramientas de tickets">
            <label class="search-box">
                <span aria-hidden="true">⌕</span>
                <span class="visually-hidden">Buscar tickets</span>
                <input v-model="busqueda" type="search" placeholder="Buscar por ticket, título o usuario">
            </label>
            <menu class="ticket-filters" aria-label="Filtrar tickets">
                <button class="filter-button" type="button" @click="filtrarTickets('todos')" :class="{ active: filtro === 'todos' }">Todos</button>
                <button class="filter-button" type="button" @click="filtrarTickets('abierto')" :class="{ active: filtro === 'abierto' }">Abiertos</button>
                <button class="filter-button" type="button" @click="filtrarTickets('cerrado')" :class="{ active: filtro === 'cerrado' }">Cerrados</button>
            </menu>
        </section>

        <p v-if="cargando" class="status-message">Cargando tickets...</p>
        <section v-else-if="ticketsFiltrados.length" class="tickets-grid">
            <article v-for="ticket in ticketsFiltrados" :key="ticket.id" class="ticket-card">
                <div class="ticket-top">
                    <span class="ticket-id"><span class="ticket-mark">#</span> Ticket {{ ticket.id }}</span>
                    <span class="badge" :class="`priority-${ticket.priority}`">{{ ticket.priority }}</span>
                </div>
                <div class="user-info">
                    <span class="user-label">Solicitante</span>
                    <strong>{{ ticket.user ? ticket.user.name : 'Usuario no disponible' }}</strong>
                </div>
                <h2>{{ ticket.title }}</h2>
                <p class="description">{{ ticket.description }}</p>
                <div class="ticket-footer">
                    <span>Estado</span>
                    <select
                        class="status-select"
                        :class="`status-${ticket.status}`"
                        :value="ticket.status"
                        aria-label="Cambiar estado del ticket"
                        @change="actualizarEstado(ticket, $event.target.value)"
                    >
                        <option value="abierta">Abierta</option>
                        <option value="en_progreso">En progreso</option>
                        <option value="cerrada">Cerrada</option>
                    </select>
                </div>
                <span v-if="ticket.created_at" class="ticket-date">Creado {{ formatFecha(ticket.created_at) }}</span>
                <button class="details-button" type="button" @click="verTicket(ticket)">
                    Ver información
                </button>
            </article>
        </section>
        <div v-else class="empty-state">
            <h2>{{ filtro === 'todos' ? 'No hay tickets registrados' : 'No hay tickets en esta categoría' }}</h2>
            <p>{{ filtro === 'todos' ? 'Las solicitudes de los clientes aparecerán aquí.' : 'Prueba con otro filtro.' }}</p>
        </div>

        <div v-if="ticketVisto" class="modal-backdrop" @click.self="cerrarDetalle">
            <section class="ticket-detail-card" role="dialog" aria-modal="true" aria-labelledby="admin-ticket-detail-title">
                <div class="form-header">
                    <div>
                        <p class="eyebrow">Información del ticket #{{ ticketVisto.id }}</p>
                        <h2 id="admin-ticket-detail-title">{{ ticketVisto.title }}</h2>
                    </div>
                    <button class="close-button" type="button" aria-label="Cerrar" @click="cerrarDetalle">×</button>
                </div>
                <div class="detail-status-row">
                    <span>Estado</span>
                    <select
                        class="status-select detail-status-select"
                        :class="`status-${ticketVisto.status}`"
                        :value="ticketVisto.status"
                        aria-label="Cambiar estado del ticket"
                        @change="actualizarEstado(ticketVisto, $event.target.value)"
                    >
                        <option value="abierta">Abierta</option>
                        <option value="en_progreso">En progreso</option>
                        <option value="cerrada">Cerrada</option>
                    </select>
                </div>
                <dl class="ticket-details">
                    <div><dt>Usuario</dt><dd>{{ ticketVisto.user ? ticketVisto.user.name : 'Usuario no disponible' }}</dd></div>
                    <div><dt>Prioridad</dt><dd>{{ ticketVisto.priority }}</dd></div>
                    <div><dt>Descripción</dt><dd>{{ ticketVisto.description }}</dd></div>
                    <div v-if="ticketVisto.created_at"><dt>Creado</dt><dd>{{ formatFecha(ticketVisto.created_at) }}</dd></div>
                </dl>
            </section>
        </div>
    </main>
    <script src="/js/gestionticket.js"></script>
</body>
</html>