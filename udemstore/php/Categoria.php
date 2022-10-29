<?php
class Categoria{

    public static function Catego_actualizar(){
        require_once "main.php";

        /*== Almacenando id ==*/
        $id=limpiar_cadena($_POST['categoria_id']);
    
    
        /*== Verificando categoria ==*/
        $check_categoria=conexion();
        $check_categoria=$check_categoria->query("SELECT * FROM categoria WHERE categoria_id='$id'");
    
        if($check_categoria->rowCount()<=0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La categoría no existe en el sistema
                </div>
            ';
            exit();
        }else{
            $datos=$check_categoria->fetch();
        }
        $check_categoria=null;
    
        /*== Almacenando datos ==*/
        $nombre=limpiar_cadena($_POST['categoria_nombre']);
        $ubicacion=limpiar_cadena($_POST['categoria_ubicacion']);
    
    
        /*== Verificando campos obligatorios ==*/
        if($nombre==""){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando integridad de los datos ==*/
        if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El NOMBRE no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if($ubicacion!=""){
            if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}",$ubicacion)){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        La UBICACION no coincide con el formato solicitado
                    </div>
                ';
                exit();
            }
        }
    
    
        /*== Verificando nombre ==*/
        if($nombre!=$datos['categoria_nombre']){
            $check_nombre=conexion();
            $check_nombre=$check_nombre->query("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
            if($check_nombre->rowCount()>0){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
                    </div>
                ';
                exit();
            }
            $check_nombre=null;
        }
    
    
        /*== Actualizar datos ==*/
        $actualizar_categoria=conexion();
        $actualizar_categoria=$actualizar_categoria->prepare("UPDATE categoria SET categoria_nombre=:nombre,categoria_ubicacion=:ubicacion WHERE categoria_id=:id");
    
        $marcadores=[
            ":nombre"=>$nombre,
            ":ubicacion"=>$ubicacion,
            ":id"=>$id
        ];
    
        if($actualizar_categoria->execute($marcadores)){
            echo '
                <div class="notification is-info is-light">
                    <strong>¡CATEGORIA ACTUALIZADA!</strong><br>
                    La categoría se actualizo con exito
                </div>
            ';
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo actualizar la categoría, por favor intente nuevamente
                </div>
            ';
        }
        $actualizar_categoria=null;
    }

    public static function Catego_eliminar(){#categoria guardar deberia ser renombrada como crear categoria
        /*== Almacenando datos ==*/
        $category_id_del=limpiar_cadena($_GET['category_id_del']);

        /*== Verificando usuario ==*/
        $check_categoria=conexion();
        $check_categoria=$check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$category_id_del'");
        
        if($check_categoria->rowCount()==1){

            $check_productos=conexion();
            $check_productos=$check_productos->query("SELECT categoria_id FROM producto WHERE categoria_id='$category_id_del' LIMIT 1");

            if($check_productos->rowCount()<=0){

                $eliminar_categoria=conexion();
                $eliminar_categoria=$eliminar_categoria->prepare("DELETE FROM categoria WHERE categoria_id=:id");

                $eliminar_categoria->execute([":id"=>$category_id_del]);

                if($eliminar_categoria->rowCount()==1){
                    echo '
                        <div class="notification is-info is-light">
                            <strong>¡CATEGORIA ELIMINADA!</strong><br>
                            Los datos de la categoría se eliminaron con exito
                        </div>
                    ';
                }else{
                    echo '
                        <div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            No se pudo eliminar la categoría, por favor intente nuevamente
                        </div>
                    ';
                }
                $eliminar_categoria=null;
            }else{
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        No podemos eliminar la categoría ya que tiene productos asociados
                    </div>
                ';
            }
    	    $check_productos=null;
            
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La CATEGORIA que intenta eliminar no existe
                </div>
            ';
        }
        $check_categoria=null;
    }

    public static function Catego_lista(){
            $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
            $tabla="";

        if(isset($busqueda) && $busqueda!=""){

            $consulta_datos="SELECT * FROM categoria WHERE categoria_nombre LIKE '%$busqueda%' OR categoria_ubicacion LIKE '%$busqueda%' ORDER BY categoria_nombre ASC LIMIT $inicio,$registros";

            $consulta_total="SELECT COUNT(categoria_id) FROM categoria WHERE categoria_nombre LIKE '%$busqueda%' OR categoria_ubicacion LIKE '%$busqueda%'";

        }else{

            $consulta_datos="SELECT * FROM categoria ORDER BY categoria_nombre ASC LIMIT $inicio,$registros";

            $consulta_total="SELECT COUNT(categoria_id) FROM categoria";
            
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
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th>Productos</th>
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
                        <td>'.$rows['categoria_nombre'].'</td>
                        <td>'.substr($rows['categoria_ubicacion'],0,25).'</td>
                        <td>
                            <a href="index.php?vista=product_category&category_id='.$rows['categoria_id'].'" class="button is-link is-rounded is-small">Ver productos</a>
                        </td>
                        <td>
                            <a href="index.php?vista=category_update&category_id_up='.$rows['categoria_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
                        </td>
                        <td>
                            <a href="'.$url.$pagina.'&category_id_del='.$rows['categoria_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
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
                        <td colspan="5">
                            <a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
                                Haga clic acá para recargar el listado
                            </a>
                        </td>
                    </tr>
                ';
            }else{
                $tabla.='
                    <tr class="has-text-centered" >
                        <td colspan="5">
                            No hay registros en el sistema
                        </td>
                    </tr>
                ';
            }
        }


        $tabla.='</tbody></table></div>';

        if($total>0 && $pagina<=$Npaginas){
            $tabla.='<p class="has-text-right">Mostrando categorías <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
        }

        $conexion=null;
        echo $tabla;

        if($total>=1 && $pagina<=$Npaginas){
            echo paginador_tablas($pagina,$Npaginas,$url,7);
        }
    }
    public static function Catego_guardar(){
        require_once "main.php";

        /*== Almacenando datos ==*/
        $nombre=limpiar_cadena($_POST['categoria_nombre']);
        $ubicacion=limpiar_cadena($_POST['categoria_ubicacion']);
    
    
        /*== Verificando campos obligatorios ==*/
        if($nombre==""){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando integridad de los datos ==*/
        if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El NOMBRE no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if($ubicacion!=""){
            if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}",$ubicacion)){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        La UBICACION no coincide con el formato solicitado
                    </div>
                ';
                exit();
            }
        }
    
    
            /*== Verificando nombre ==*/
            $check_nombre=conexion();
            $check_nombre=$check_nombre->query("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
            if($check_nombre->rowCount()>0){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El NOMBRE ingresado ya se encuentra registrado, por favor elija otro
                    </div>
                ';
                exit();
        }
        $check_nombre=null;
        
        
        /*== Guardando datos ==*/
        $guardar_categoria=conexion();
        $guardar_categoria=$guardar_categoria->prepare("INSERT INTO categoria(categoria_nombre,categoria_ubicacion) VALUES(:nombre,:ubicacion)");
        
        $marcadores=[
            ":nombre"=>$nombre,
            ":ubicacion"=>$ubicacion
        ];
        
        $guardar_categoria->execute($marcadores);
        
        if($guardar_categoria->rowCount()==1){
            echo '
                <div class="notification is-info is-light">
                    <strong>¡CATEGORIA REGISTRADA!</strong><br>
                    La categoría se registro con exito
                </div>
            ';
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo registrar la categoría, por favor intente nuevamente
                </div>
            ';
        }
        $guardar_categoria=null;
    }
}


    
   
    