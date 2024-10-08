<h1 class="nombre-pagina">login</h1>
<p class="descripcion-pagina">Iniciar Sesión con tus datos</p>


<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>

<form class="formulario" method="POST" action="/">
    <div class="campo">
        <label for="email">Email</label>
        <input 
            type="email"
            id="email"
            placeholder="Tu Email"
            name="email"
        />
    </div>
    <div class="campo">
        <label for="contraseña">Contraseña</label>
        <input 
            type="password"
            id="contraseña"
            placeholder="Tu Contraseña"
            name="contraseña"
        />
    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">
</form>

<div class="acciones">
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
    <a href="/olvide">¿Olvidaste tu contraseña/a>
</div>