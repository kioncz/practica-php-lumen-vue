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
                <p class="eyebrow">Área del cliente, {{ userName }}</p>
                <h1>Mis tickets</h1>
                <p class="subtitle">Consulta el estado y la prioridad de tus solicitudes.</p>
            </div>
            <button class="logout-button" type="button" @click="cerrarSesion">Cerrar sesión</button>
        </header>

        <button class="add-ticket-button" type="button" @click="mostrarFormulario = true">
            <span aria-hidden="true">+</span>
            Nuevo ticket
        </button>

        <div v-if="mostrarFormulario" class="modal-backdrop" @click.self="cerrarFormulario">
            <section class="ticket-form-card" role="dialog" aria-modal="true" aria-labelledby="new-ticket-title">
                <div class="form-header">
                    <div>
                        <p class="eyebrow">Nueva solicitud</p>
                        <h2 id="new-ticket-title">Crear ticket</h2>
                    </div>
                    <button class="close-button" type="button" aria-label="Cerrar" @click="cerrarFormulario">×</button>
                </div>

                <form @submit.prevent="crearTicket">
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
                            {{ guardando ? 'Guardando...' : 'Crear ticket' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <p v-if="cargando" class="status-message">Cargando tus tickets...</p>
        <p v-else-if="error" class="status-message error">{{ error }}</p>
        <section v-else-if="tickets.length" class="tickets-grid">
            <article v-for="ticket in tickets" :key="ticket.id" class="ticket-card">
                <div class="ticket-top">
                    <span class="ticket-id">Ticket #{{ ticket.id }}</span>
                    <span class="badge" :class="`priority-${ticket.priority}`">
                        {{ ticket.priority }}
                    </span>
                </div>
                <h2>{{ ticket.title }}</h2>
                <p class="description">{{ ticket.description }}</p>
                <div class="ticket-footer">
                    <span>Estado</span>
                    <strong :class="`status-${ticket.status}`">{{ ticket.status }}</strong>
                </div>
            </article>
        </section>
        <div v-else class="empty-state">
            <h2>Aún no tienes tickets</h2>
            <p>Cuando registres una solicitud, aparecerá aquí.</p>
        </div>
    </main>

    <script src="/js/client-tickets.js"></script>
</body>
</html>
