<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="/css/auth.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <main id="app" class="auth-card">
        <h1>Crear cuenta</h1>
        <p class="subtitle">Regístrate para administrar tus tickets.</p>

        <form @submit.prevent="registrar">
            <label for="name">Nombre</label>
            <input id="name" v-model="name" type="text" autocomplete="name" required>

            <label for="email">Correo electrónico</label>
            <input id="email" v-model="email" type="email" autocomplete="email" required>

            <label for="role">Tipo de usuario</label>
            <select id="role" v-model="role" required>
                <option value="client">Cliente</option>
                <option value="admin">Administrador</option>
            </select>

            <label for="password">Contraseña</label>
            <input id="password" v-model="password" type="password" autocomplete="new-password" minlength="8" required>

            <label for="password-confirmation">Confirmar contraseña</label>
            <input id="password-confirmation" v-model="passwordConfirmation" type="password" autocomplete="new-password" minlength="8" required>

            <button class="primary-button" type="submit" :disabled="cargando">
                {{ cargando ? 'Creando cuenta...' : 'Registrarse' }}
            </button>
            <button class="secondary-button" type="button" @click="irAlLogin">
                Ya tengo una cuenta
            </button>
        </form>

        <p v-if="mensaje" class="message" :class="tipoMensaje">{{ mensaje }}</p>
    </main>

    <script src="/js/register.js"></script>
</body>
</html>
