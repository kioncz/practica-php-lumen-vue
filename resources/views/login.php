<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | Pulso</title>
    <link rel="stylesheet" href="/css/login.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <main id="app" class="auth-shell" data-initial-mode="{{ $initialMode ?? 'login' }}" v-cloak>
        <section class="auth-story">
            <div class="brand-mark"><span>+</span> pulso</div>
            <transition name="story-copy" mode="out-in">
                <div class="story-copy" :key="modoRegistro ? 'register-story' : 'login-story'">
                    <p class="story-kicker">{{ modoRegistro ? 'Un equipo más claro' : 'Soporte sin ruido' }}</p>
                    <h1>{{ modoRegistro ? 'Haz que cada solicitud encuentre su próximo paso.' : 'Todo lo que tu equipo necesita, en un solo pulso.' }}</h1>
                    <p>{{ modoRegistro ? 'Una cuenta, una bandeja compartida y la visibilidad que necesitas para responder mejor.' : 'Recibe solicitudes, ordena prioridades y convierte cada conversación en una respuesta clara.' }}</p>
                </div>
            </transition>
            <div class="story-note"><span>{{ modoRegistro ? '✦' : '●' }}</span> Plataforma de soporte para equipos que avanzan.</div>
        </section>

        <section class="auth-panel">
            <div class="auth-form-stage">
                <transition :name="modoRegistro ? 'auth-form-forward' : 'auth-form-back'" mode="out-in">
                    <div v-if="!modoRegistro" key="login">
                        <p class="panel-kicker">Bienvenido de vuelta</p>
                        <h2>Inicia sesión</h2>
                        <p class="subtitle">Continúa donde lo dejaste.</p>
                        <form @submit.prevent="iniciarSesion">
                            <label for="login-email">Correo electrónico <span class="required-mark" aria-hidden="true">*</span></label>
                            <input id="login-email" v-model="email" type="email" autocomplete="email" placeholder="tu@empresa.com" required aria-required="true">
                            <label for="login-password">Contraseña <span class="required-mark" aria-hidden="true">*</span></label>
                            <input id="login-password" v-model="password" type="password" autocomplete="current-password" placeholder="••••••••" required aria-required="true">
                            <p class="form-hint"><span class="required-mark" aria-hidden="true">*</span> Campo obligatorio</p>
                            <button class="submit-button" type="submit" :disabled="cargando">
                                {{ cargando ? 'Ingresando...' : 'Entrar al espacio' }}
                            </button>
                            <button class="switch-button" type="button" @click="mostrarRegistro">Crear una cuenta</button>
                        </form>
                    </div>
                    <div v-else key="register">
                        <p class="panel-kicker">Primer paso</p>
                        <h2>Crea tu cuenta</h2>
                        <p class="subtitle">Configura tu espacio de soporte.</p>
                        <form @submit.prevent="registrar">
                            <label for="register-name">Nombre <span class="required-mark" aria-hidden="true">*</span></label>
                            <input id="register-name" v-model="name" type="text" autocomplete="name" placeholder="Tu nombre" required aria-required="true">
                            <label for="register-email">Correo electrónico <span class="required-mark" aria-hidden="true">*</span></label>
                            <input id="register-email" v-model="email" type="email" autocomplete="email" placeholder="tu@empresa.com" required aria-required="true">
                            <label for="register-role">Tipo de usuario <span class="required-mark" aria-hidden="true">*</span></label>
                            <select id="register-role" v-model="role" required aria-required="true">
                                <option value="client">Cliente</option>
                                <option value="admin">Administrador</option>
                            </select>
                            <div class="form-columns">
                                <div>
                                    <label for="register-password">Contraseña <span class="required-mark" aria-hidden="true">*</span></label>
                                    <input id="register-password" v-model="password" type="password" autocomplete="new-password" minlength="8" placeholder="Mínimo 8 caracteres" required aria-required="true">
                                </div>
                                <div>
                                    <label for="register-confirmation">Confirmar <span class="required-mark" aria-hidden="true">*</span></label>
                                    <input id="register-confirmation" v-model="passwordConfirmation" type="password" autocomplete="new-password" minlength="8" placeholder="Repite la contraseña" required aria-required="true">
                                </div>
                            </div>
                            <p class="form-hint"><span class="required-mark" aria-hidden="true">*</span> Todos los campos son obligatorios</p>
                            <button class="submit-button" type="submit" :disabled="cargando">
                                {{ cargando ? 'Creando cuenta...' : 'Crear mi cuenta' }}
                            </button>
                            <button class="switch-button" type="button" @click="mostrarLogin">Ya tengo una cuenta</button>
                        </form>
                    </div>
                </transition>
            </div>
            <transition name="notification">
                <div v-if="mensaje" class="message" :class="tipoMensaje" role="alert" aria-live="polite">
                    <span class="message-icon" aria-hidden="true">{{ tipoMensaje === 'success' ? '✓' : '!' }}</span>
                    <span class="message-content">
                        <strong>{{ tituloMensaje }}</strong>
                        <span>{{ mensaje }}</span>
                    </span>
                    <button class="message-close" type="button" aria-label="Cerrar mensaje" @click="cerrarMensaje">×</button>
                </div>
            </transition>
        </section>
    </main>
    <script src="/js/auth.js"></script>
</body>
</html>
