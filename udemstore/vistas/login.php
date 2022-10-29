<body>
	<link rel="stylesheet" href="./css/estilos_login.css">
		<div class="superior">
			<h1><font color="black">UDEM STORE</font></h1>
			<img class="logo" src="./img/logo.png" alt="Logo de la aplicación">
			<img class="logo_u" src="./img/logo_udemedellin.png" alt="Logo de la universidad">
		</div>
		<div class="main-container">
			<img class="avatar" src="./img/logo.png" alt="Logo de la aplicación">
			<form class="box login" action="" method="POST" autocomplete="off">
				
				<h5 class="title is-5 has-text-centered is-uppercase"><font color="#000";>UdeM Store</font></h5>

				<div class="field">
					<label class="label">Usuario</label>
					<div class="control">
						<input class="input" type="text" name="login_usuario" placeholder="Ingresar Usuario" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required >
					</div>
				</div>

				<div class="field">
					<label class="label">Contraseña</label>
					<div class="control">
						<input class="input" type="password" name="login_clave" placeholder="Ingresar Contraseña" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required >
					</div>
				</div>

				<p class="has-text-centered mb-4 mt-3">
					<button type="submit" class="button is-info is-rounded">Iniciar sesion</button>
				</p>

				<?php
					if(isset($_POST['login_usuario']) && isset($_POST['login_clave'])){
						require_once "./php/main.php";
						require_once "./php/iniciar_sesion.php";
					}
					?>
			<!-- <br><br><a href="#" onclick="Mostrar_Algo(Nombre)">Olvido su contraseña</a><br>
			<a href="#" onclick="Mostrar_Algo(Cuenta)">Crear nueva cuenta</a> -->
			</form>


		</div>
</body>