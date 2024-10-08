<h1 class="nombre-pagina">Reestablece tu contraseña</h1>
<p class="descripcion-pagina">Coloca tu nueva contraseña a continuacion</p>

<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>
<?php if($error) return; ?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="contraseña">Nueva contraseña</label>
        <input 
            type="password"
            id="contraseña"
            placeholder="Tu Nueva Contraseña"
            name="contraseña"
        />
    </div>
    <input type="submit" class="boton" value="Guardar Nueva Contraseña">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>