<?php
class Usuario{
    public static function Usuario_actualizar(){
        require_once "../inc/session_start.php";

        require_once "main.php";
    
        /*== Almacenando id ==*/
        $id=limpiar_cadena($_POST['usuario_id']);
    
        /*== Verificando usuario ==*/
        $check_usuario=conexion();
        $check_usuario=$check_usuario->query("SELECT * FROM usuario WHERE usuario_id='$id'");
    
        if($check_usuario->rowCount()<=0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario no existe en el sistema
                </div>
            ';
            exit();
        }else{
            $datos=$check_usuario->fetch();
        }
        $check_usuario=null;
    
    
        /*== Almacenando datos del administrador ==*/
        $admin_usuario=limpiar_cadena($_POST['administrador_usuario']);
        $admin_clave=limpiar_cadena($_POST['administrador_clave']);
    
    
        /*== Verificando campos obligatorios del administrador ==*/
        if($admin_usuario=="" || $admin_clave==""){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No ha llenado los campos que corresponden a su USUARIO o CLAVE
                </div>
            ';
            exit();
        }
    
        /*== Verificando integridad de los datos (admin) ==*/
        if(verificar_datos("[a-zA-Z0-9]{4,20}",$admin_usuario)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    Su USUARIO no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$admin_clave)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    Su CLAVE no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando el administrador en DB ==*/
        $check_admin=conexion();
        $check_admin=$check_admin->query("SELECT usuario_usuario,usuario_clave FROM usuario WHERE usuario_usuario='$admin_usuario' AND usuario_id='".$_SESSION['id']."'");
        if($check_admin->rowCount()==1){
    
            $check_admin=$check_admin->fetch();
    
            if($check_admin['usuario_usuario']!=$admin_usuario || !password_verify($admin_clave, $check_admin['usuario_clave'])){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        USUARIO o CLAVE de administrador incorrectos
                    </div>
                ';
                exit();
            }
    
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    USUARIO o CLAVE de administrador incorrectos
                </div>
            ';
            exit();
        }
        $check_admin=null;
    
    
        /*== Almacenando datos del usuario ==*/
        $nombre=limpiar_cadena($_POST['usuario_nombre']);
        $apellido=limpiar_cadena($_POST['usuario_apellido']);
    
        $usuario=limpiar_cadena($_POST['usuario_usuario']);
        $email=limpiar_cadena($_POST['usuario_email']);
    
        $clave_1=limpiar_cadena($_POST['usuario_clave_1']);
        $clave_2=limpiar_cadena($_POST['usuario_clave_2']);
    
    
        /*== Verificando campos obligatorios del usuario ==*/
        if($nombre=="" || $apellido=="" || $usuario==""){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando integridad de los datos (usuario) ==*/
        if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El NOMBRE no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$apellido)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El APELLIDO no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[a-zA-Z0-9]{4,20}",$usuario)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El USUARIO no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando email ==*/
        if($email!="" && $email!=$datos['usuario_email']){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $check_email=conexion();
                $check_email=$check_email->query("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                if($check_email->rowCount()>0){
                    echo '
                        <div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            El correo electrónico ingresado ya se encuentra registrado, por favor elija otro
                        </div>
                    ';
                    exit();
                }
                $check_email=null;
            }else{
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        Ha ingresado un correo electrónico no valido
                    </div>
                ';
                exit();
            } 
        }
    
    
        /*== Verificando usuario ==*/
        if($usuario!=$datos['usuario_usuario']){
            $check_usuario=conexion();
            $check_usuario=$check_usuario->query("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
            if($check_usuario->rowCount()>0){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El USUARIO ingresado ya se encuentra registrado, por favor elija otro
                    </div>
                ';
                exit();
            }
            $check_usuario=null;
        }
    
    
        /*== Verificando claves ==*/
        if($clave_1!="" || $clave_2!=""){
            if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_2)){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        Las CLAVES no coinciden con el formato solicitado
                    </div>
                ';
                exit();
            }else{
                if($clave_1!=$clave_2){
                    echo '
                        <div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            Las CLAVES que ha ingresado no coinciden
                        </div>
                    ';
                    exit();
                }else{
                    $clave=password_hash($clave_1,PASSWORD_BCRYPT,["cost"=>10]);
                }
            }
        }else{
            $clave=$datos['usuario_clave'];
        }
    
    
        /*== Actualizar datos ==*/
        $actualizar_usuario=conexion();
        $actualizar_usuario=$actualizar_usuario->prepare("UPDATE usuario SET usuario_nombre=:nombre,usuario_apellido=:apellido,usuario_usuario=:usuario,usuario_clave=:clave,usuario_email=:email WHERE usuario_id=:id");
    
        $marcadores=[
            ":nombre"=>$nombre,
            ":apellido"=>$apellido,
            ":usuario"=>$usuario,
            ":clave"=>$clave,
            ":email"=>$email,
            ":id"=>$id
        ];
    
        if($actualizar_usuario->execute($marcadores)){
            echo '
                <div class="notification is-info is-light">
                    <strong>¡USUARIO ACTUALIZADO!</strong><br>
                    El usuario se actualizo con exito
                </div>
            ';
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo actualizar el usuario, por favor intente nuevamente
                </div>
            ';
        }
        $actualizar_usuario=null;
    }
    public static function Usuario_eliminar(){
                /*== Almacenando datos ==*/
        $user_id_del=limpiar_cadena($_GET['user_id_del']);

        /*== Verificando usuario ==*/
        $check_usuario=conexion();
        $check_usuario=$check_usuario->query("SELECT usuario_id FROM usuario WHERE usuario_id='$user_id_del'");
        
        if($check_usuario->rowCount()==1){

            $check_productos=conexion();
            $check_productos=$check_productos->query("SELECT usuario_id FROM producto WHERE usuario_id='$user_id_del' LIMIT 1");

            if($check_productos->rowCount()<=0){
                
                $eliminar_usuario=conexion();
                $eliminar_usuario=$eliminar_usuario->prepare("DELETE FROM usuario WHERE usuario_id=:id");

                $eliminar_usuario->execute([":id"=>$user_id_del]);

                if($eliminar_usuario->rowCount()==1){
                    echo '
                        <div class="notification is-info is-light">
                            <strong>¡USUARIO ELIMINADO!</strong><br>
                            Los datos del usuario se eliminaron con exito
                        </div>
                    ';
                }else{
                    echo '
                        <div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            No se pudo eliminar el usuario, por favor intente nuevamente
                        </div>
                    ';
                }
                $eliminar_usuario=null;
            }else{
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        No podemos eliminar el usuario ya que tiene productos registrados por el
                    </div>
                ';
            }
            $check_productos=null;
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El USUARIO que intenta eliminar no existe
                </div>
            ';
        }
        $check_usuario=null;
    }
    public static function Usuario_guardar(){
        require_once "main.php";

        /*== Almacenando datos ==*/
        $nombre=limpiar_cadena($_POST['usuario_nombre']);
        $apellido=limpiar_cadena($_POST['usuario_apellido']);
    
        $usuario=limpiar_cadena($_POST['usuario_usuario']);
        $email=limpiar_cadena($_POST['usuario_email']);
    
        $clave_1=limpiar_cadena($_POST['usuario_clave_1']);
        $clave_2=limpiar_cadena($_POST['usuario_clave_2']);
    
    
        /*== Verificando campos obligatorios ==*/
        if($nombre=="" || $apellido=="" || $usuario=="" || $clave_1=="" || $clave_2==""){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando integridad de los datos ==*/
        if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El NOMBRE no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$apellido)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El APELLIDO no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[a-zA-Z0-9]{4,20}",$usuario)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El USUARIO no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_2)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    Las CLAVES no coinciden con el formato solicitado
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando email ==*/
        if($email!=""){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $check_email=conexion();
                $check_email=$check_email->query("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                if($check_email->rowCount()>0){
                    echo '
                        <div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            El correo electrónico ingresado ya se encuentra registrado, por favor elija otro
                        </div>
                    ';
                    exit();
                }
                $check_email=null;
            }else{
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        Ha ingresado un correo electrónico no valido
                    </div>
                ';
                exit();
            } 
        }
    
    
        /*== Verificando usuario ==*/
        $check_usuario=conexion();
        $check_usuario=$check_usuario->query("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
        if($check_usuario->rowCount()>0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El USUARIO ingresado ya se encuentra registrado, por favor elija otro
                </div>
            ';
            exit();
        }
        $check_usuario=null;
    
    
        /*== Verificando claves ==*/
        if($clave_1!=$clave_2){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    Las CLAVES que ha ingresado no coinciden
                </div>
            ';
            exit();
        }else{
            $clave=password_hash($clave_1,PASSWORD_BCRYPT,["cost"=>10]);
        }
    
    
        /*== Guardando datos ==*/
        $guardar_usuario=conexion();
        $guardar_usuario=$guardar_usuario->prepare("INSERT INTO usuario(usuario_nombre,usuario_apellido,usuario_usuario,usuario_clave,usuario_email) VALUES(:nombre,:apellido,:usuario,:clave,:email)");
    
        $marcadores=[
            ":nombre"=>$nombre,
            ":apellido"=>$apellido,
            ":usuario"=>$usuario,
            ":clave"=>$clave,
            ":email"=>$email
        ];
    
        $guardar_usuario->execute($marcadores);
    
        if($guardar_usuario->rowCount()==1){
            echo '
                <div class="notification is-info is-light">
                    <strong>¡USUARIO REGISTRADO!</strong><br>
                    El usuario se registro con exito
                </div>
            ';
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo registrar el usuario, por favor intente nuevamente
                </div>
            ';
        }
        $guardar_usuario=null;
    }
    public static function Usuario_lista($pagina,$registros,$url){
        $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
        $tabla="";
    
        if(isset($busqueda) && $busqueda!=""){
    
            $consulta_datos="SELECT * FROM usuario WHERE ((usuario_id!='".$_SESSION['id']."') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%')) ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";
    
            $consulta_total="SELECT COUNT(usuario_id) FROM usuario WHERE ((usuario_id!='".$_SESSION['id']."') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%'))";
    
        }else{
    
            $consulta_datos="SELECT * FROM usuario WHERE usuario_id!='".$_SESSION['id']."' ORDER BY usuario_nombre ASC LIMIT $inicio,$registros";
    
            $consulta_total="SELECT COUNT(usuario_id) FROM usuario WHERE usuario_id!='".$_SESSION['id']."'";
            
        }
    
        $conexion=conexion();
    
        $datos = $conexion->query($consulta_datos);
        $datos = $datos->fetchAll();
    
        $total = $conexion->query($consulta_total);
        $total = (int) $total->fetchColumn();
    
        $Npaginas =ceil($total/$registros);
    
        $tabla.='
        <div class="table-container">
            <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                <thead>
                    <tr class="has-text-centered">
                        <th>#</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th colspan="2">Opciones</th>
                    </tr>
                </thead>
                <tbody>
        ';
    
        if($total>=1 && $pagina<=$Npaginas){
            $contador=$inicio+1;
            $pag_inicio=$inicio+1;
            foreach($datos as $rows){
                $tabla.='
                    <tr class="has-text-centered" >
                        <td>'.$contador.'</td>
                        <td>'.$rows['usuario_nombre'].'</td>
                        <td>'.$rows['usuario_apellido'].'</td>
                        <td>'.$rows['usuario_usuario'].'</td>
                        <td>'.$rows['usuario_email'].'</td>
                        <td>
                            <a href="index.php?vista=user_update&user_id_up='.$rows['usuario_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
                        </td>
                        <td>
                            <a href="'.$url.$pagina.'&user_id_del='.$rows['usuario_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
                        </td>
                    </tr>
                ';
                $contador++;
            }
            $pag_final=$contador-1;
        }else{
            if($total>=1){
                $tabla.='
                    <tr class="has-text-centered" >
                        <td colspan="7">
                            <a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
                                Haga clic acá para recargar el listado
                            </a>
                        </td>
                    </tr>
                ';
            }else{
                $tabla.='
                    <tr class="has-text-centered" >
                        <td colspan="7">
                            No hay registros en el sistema
                        </td>
                    </tr>
                ';
            }
        }
    
    
        $tabla.='</tbody></table></div>';
    
        if($total>0 && $pagina<=$Npaginas){
            $tabla.='<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
        }
    
        $conexion=null;
        echo $tabla;
    
        if($total>=1 && $pagina<=$Npaginas){
            echo paginador_tablas($pagina,$Npaginas,$url,7);
        }
    }
}