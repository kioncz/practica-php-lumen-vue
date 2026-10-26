<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="/css/login.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <main id="app" class="login-card">
        <h1>Iniciar sesión</h1>
        <p class="subtitle">Accede a tu cuenta para administrar tus tickets.</p>

        <form @submit.prevent="iniciarSesion">
            <label for="email">Correo electrónico</label>
            <input id="email" v-model="email" type="email" autocomplete="email" required>

            <label for="password">Contraseña</label>
            <input id="password" v-model="password" type="password" autocomplete="current-password" required>

            <button class="submit-button" type="submit" :disabled="cargando">
                {{ cargando ? 'Ingresando...' : 'Iniciar sesión' }}
            </button>
            <button class="register-button" type="button" onclick="window.location.href='/register'">Registrarse</button>
        </form>

        <p v-if="mensaje" class="message" :class="tipoMensaje">{{ mensaje }}</p>
    </main>

    <script src="/js/login.js"></script>
</body>
</html>
