<?php
class Producto{

    public static function produ_actualizar(){
        require_once "main.php";

        /*== Almacenando id ==*/
        $id=limpiar_cadena($_POST['producto_id']);
    
    
        /*== Verificando producto ==*/
        $check_producto=conexion();
        $check_producto=$check_producto->query("SELECT * FROM producto WHERE producto_id='$id'");
    
        if($check_producto->rowCount()<=0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El producto no existe en el sistema
                </div>
            ';
            exit();
        }else{
            $datos=$check_producto->fetch();
        }
        $check_producto=null;
    
    
        /*== Almacenando datos ==*/
        $codigo=limpiar_cadena($_POST['producto_codigo']);
        $nombre=limpiar_cadena($_POST['producto_nombre']);
    
        $precio=limpiar_cadena($_POST['producto_precio']);
        $stock=limpiar_cadena($_POST['producto_stock']);
        $categoria=limpiar_cadena($_POST['producto_categoria']);
    
    
        /*== Verificando campos obligatorios ==*/
        if($codigo=="" || $nombre=="" || $precio=="" || $stock=="" || $categoria==""){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando integridad de los datos ==*/
        if(verificar_datos("[a-zA-Z0-9- ]{1,70}",$codigo)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El CODIGO de BARRAS no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}",$nombre)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El NOMBRE no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[0-9.]{1,25}",$precio)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El PRECIO no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[0-9]{1,25}",$stock)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El STOCK no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando codigo ==*/
        if($codigo!=$datos['producto_codigo']){
            $check_codigo=conexion();
            $check_codigo=$check_codigo->query("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
            if($check_codigo->rowCount()>0){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El CODIGO de BARRAS ingresado ya se encuentra registrado, por favor elija otro
                    </div>
                ';
                exit();
            }
            $check_codigo=null;
        }
    
    
        /*== Verificando nombre ==*/
        if($nombre!=$datos['producto_nombre']){
            $check_nombre=conexion();
            $check_nombre=$check_nombre->query("SELECT producto_nombre FROM producto WHERE producto_nombre='$nombre'");
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
    
    
        /*== Verificando categoria ==*/
        if($categoria!=$datos['categoria_id']){
            $check_categoria=conexion();
            $check_categoria=$check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");
            if($check_categoria->rowCount()<=0){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        La categoría seleccionada no existe
                    </div>
                ';
                exit();
            }
            $check_categoria=null;
        }
    
    
        /*== Actualizando datos ==*/
        $actualizar_producto=conexion();
        $actualizar_producto=$actualizar_producto->prepare("UPDATE producto SET producto_codigo=:codigo,producto_nombre=:nombre,producto_precio=:precio,producto_stock=:stock,categoria_id=:categoria WHERE producto_id=:id");
    
        $marcadores=[
            ":codigo"=>$codigo,
            ":nombre"=>$nombre,
            ":precio"=>$precio,
            ":stock"=>$stock,
            ":categoria"=>$categoria,
            ":id"=>$id
        ];
    
    
        if($actualizar_producto->execute($marcadores)){
            echo '
                <div class="notification is-info is-light">
                    <strong>¡PRODUCTO ACTUALIZADO!</strong><br>
                    El producto se actualizo con exito
                </div>
            ';
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo actualizar el producto, por favor intente nuevamente
                </div>
            ';
        }
        $actualizar_producto=null;
    }
    public static function produ_eliminar(){
                /*== Almacenando datos ==*/
        $product_id_del=limpiar_cadena($_GET['product_id_del']);

        /*== Verificando producto ==*/
        $check_producto=conexion();
        $check_producto=$check_producto->query("SELECT * FROM producto WHERE producto_id='$product_id_del'");

        if($check_producto->rowCount()==1){

            $datos=$check_producto->fetch();

            $eliminar_producto=conexion();
            $eliminar_producto=$eliminar_producto->prepare("DELETE FROM producto WHERE producto_id=:id");

            $eliminar_producto->execute([":id"=>$product_id_del]);

    	if($eliminar_producto->rowCount()==1){

    		if(is_file("./img/producto/".$datos['producto_foto'])){
    			chmod("./img/producto/".$datos['producto_foto'], 0777);
				unlink("./img/producto/".$datos['producto_foto']);
    		}

	        echo '
	            <div class="notification is-info is-light">
	                <strong>¡PRODUCTO ELIMINADO!</strong><br>
	                Los datos del producto se eliminaron con exito
	            </div>
	        ';
	    }else{
	        echo '
	            <div class="notification is-danger is-light">
	                <strong>¡Ocurrio un error inesperado!</strong><br>
	                No se pudo eliminar el producto, por favor intente nuevamente
	            </div>
	        ';
	    }
	    $eliminar_producto=null;
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El PRODUCTO que intenta eliminar no existe
                </div>
            ';
        }
        $check_producto=null;
    }
    public static function produ_guardar(){
        require_once "../inc/session_start.php";

        require_once "main.php";
    
        /*== Almacenando datos ==*/
        $codigo=limpiar_cadena($_POST['producto_codigo']);
        $nombre=limpiar_cadena($_POST['producto_nombre']);
    
        $precio=limpiar_cadena($_POST['producto_precio']);
        $stock=limpiar_cadena($_POST['producto_stock']);
        $categoria=limpiar_cadena($_POST['producto_categoria']);
    
    
        /*== Verificando campos obligatorios ==*/
        if($codigo=="" || $nombre=="" || $precio=="" || $stock=="" || $categoria==""){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando integridad de los datos ==*/
        if(verificar_datos("[a-zA-Z0-9- ]{1,70}",$codigo)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El CODIGO de BARRAS no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}",$nombre)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El NOMBRE no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[0-9.]{1,25}",$precio)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El PRECIO no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
        if(verificar_datos("[0-9]{1,25}",$stock)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El STOCK no coincide con el formato solicitado
                </div>
            ';
            exit();
        }
    
    
        /*== Verificando codigo ==*/
        $check_codigo=conexion();
        $check_codigo=$check_codigo->query("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
        if($check_codigo->rowCount()>0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El CODIGO de BARRAS ingresado ya se encuentra registrado, por favor elija otro
                </div>
            ';
            exit();
        }
        $check_codigo=null;
    
    
        /*== Verificando nombre ==*/
        $check_nombre=conexion();
        $check_nombre=$check_nombre->query("SELECT producto_nombre FROM producto WHERE producto_nombre='$nombre'");
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
    
    
        /*== Verificando categoria ==*/
        $check_categoria=conexion();
        $check_categoria=$check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");
        if($check_categoria->rowCount()<=0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La categoría seleccionada no existe
                </div>
            ';
            exit();
        }
        $check_categoria=null;
    
    
        /* Directorios de imagenes */
        $img_dir='../img/producto/';
    
    
        /*== Comprobando si se ha seleccionado una imagen ==*/
        if($_FILES['producto_foto']['name']!="" && $_FILES['producto_foto']['size']>0){
    
            /* Creando directorio de imagenes */
            if(!file_exists($img_dir)){
                if(!mkdir($img_dir,0777)){
                    echo '
                        <div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            Error al crear el directorio de imagenes
                        </div>
                    ';
                    exit();
                }
            }
    
            /* Comprobando formato de las imagenes */
            if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png"){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        La imagen que ha seleccionado es de un formato que no está permitido
                    </div>
                ';
                exit();
            }
    
    
            /* Comprobando que la imagen no supere el peso permitido */
            if(($_FILES['producto_foto']['size']/1024)>3072){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        La imagen que ha seleccionado supera el límite de peso permitido
                    </div>
                ';
                exit();
            }
    
    
            /* extencion de las imagenes */
            switch(mime_content_type($_FILES['producto_foto']['tmp_name'])){
                case 'image/jpeg':
                  $img_ext=".jpg";
                break;
                case 'image/png':
                  $img_ext=".png";
                break;
            }
    
            /* Cambiando permisos al directorio */
            chmod($img_dir, 0777);
    
            /* Nombre de la imagen */
            $img_nombre=renombrar_fotos($nombre);
    
            /* Nombre final de la imagen */
            $foto=$img_nombre.$img_ext;
    
            /* Moviendo imagen al directorio */
            if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir.$foto)){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        No podemos subir la imagen al sistema en este momento, por favor intente nuevamente
                    </div>
                ';
                exit();
            }
    
            }else{
                $foto="";
            }
        
        
            /*== Guardando datos ==*/
            $guardar_producto=conexion();
            $guardar_producto=$guardar_producto->prepare("INSERT INTO producto(producto_codigo,producto_nombre,producto_precio,producto_stock,producto_foto,categoria_id,usuario_id) VALUES(:codigo,:nombre,:precio,:stock,:foto,:categoria,:usuario)");
        
            $marcadores=[
                ":codigo"=>$codigo,
                ":nombre"=>$nombre,
                ":precio"=>$precio,
                ":stock"=>$stock,
                ":foto"=>$foto,
                ":categoria"=>$categoria,
                ":usuario"=>$_SESSION['id']
            ];
        
            $guardar_producto->execute($marcadores);
        
            if($guardar_producto->rowCount()==1){
                echo '
                    <div class="notification is-info is-light">
                        <strong>¡PRODUCTO REGISTRADO!</strong><br>
                        El producto se registro con exito
                    </div>
                ';
            }else{
        
                if(is_file($img_dir.$foto)){
                    chmod($img_dir.$foto, 0777);
                    unlink($img_dir.$foto);
                }
        
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        No se pudo registrar el producto, por favor intente nuevamente
                    </div>
                ';
            }
            $guardar_producto=null;
        }
    public static function produ_img_actualizar(){
        require_once "main.php";

        /*== Almacenando datos ==*/
        $product_id=limpiar_cadena($_POST['img_up_id']);
    
        /*== Verificando producto ==*/
        $check_producto=conexion();
        $check_producto=$check_producto->query("SELECT * FROM producto WHERE producto_id='$product_id'");
    
        if($check_producto->rowCount()==1){
            $datos=$check_producto->fetch();
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La imagen del PRODUCTO que intenta actualizar no existe
                </div>
            ';
            exit();
        }
        $check_producto=null;
    
    
        /*== Comprobando si se ha seleccionado una imagen ==*/
        if($_FILES['producto_foto']['name']=="" || $_FILES['producto_foto']['size']==0){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No ha seleccionado ninguna imagen o foto
                </div>
            ';
            exit();
        }
    
    
        /* Directorios de imagenes */
        $img_dir='../img/producto/';
    
    
        /* Creando directorio de imagenes */
        if(!file_exists($img_dir)){
            if(!mkdir($img_dir,0777)){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        Error al crear el directorio de imagenes
                    </div>
                ';
                exit();
            }
        }
    
    
        /* Cambiando permisos al directorio */
        chmod($img_dir, 0777);
    
    
        /* Comprobando formato de las imagenes */
        if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png"){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La imagen que ha seleccionado es de un formato que no está permitido
                </div>
            ';
            exit();
        }
    
    
        /* Comprobando que la imagen no supere el peso permitido */
        if(($_FILES['producto_foto']['size']/1024)>3072){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La imagen que ha seleccionado supera el límite de peso permitido
                </div>
            ';
            exit();
        }
    
    
        /* extencion de las imagenes */
        switch(mime_content_type($_FILES['producto_foto']['tmp_name'])){
            case 'image/jpeg':
              $img_ext=".jpg";
            break;
            case 'image/png':
              $img_ext=".png";
            break;
        }
    
        /* Nombre de la imagen */
        $img_nombre=renombrar_fotos($datos['producto_nombre']);
    
        /* Nombre final de la imagen */
        $foto=$img_nombre.$img_ext;
    
        /* Moviendo imagen al directorio */
        if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir.$foto)){
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No podemos subir la imagen al sistema en este momento, por favor intente nuevamente
                </div>
            ';
            exit();
        }
    
    
        /* Eliminando la imagen anterior */
        if(is_file($img_dir.$datos['producto_foto']) && $datos['producto_foto']!=$foto){
    
            chmod($img_dir.$datos['producto_foto'], 0777);
            unlink($img_dir.$datos['producto_foto']);
        }
    
    
        /*== Actualizando datos ==*/
        $actualizar_producto=conexion();
        $actualizar_producto=$actualizar_producto->prepare("UPDATE producto SET producto_foto=:foto WHERE producto_id=:id");
    
        $marcadores=[
            ":foto"=>$foto,
            ":id"=>$product_id
        ];
    
        if($actualizar_producto->execute($marcadores)){
            echo '
                <div class="notification is-info is-light">
                    <strong>¡IMAGEN O FOTO ACTUALIZADA!</strong><br>
                    La imagen del producto ha sido actualizada exitosamente, pulse Aceptar para recargar los cambios.
    
                    <p class="has-text-centered pt-5 pb-5">
                        <a href="index.php?vista=product_img&product_id_up='.$product_id.'" class="button is-link is-rounded">Aceptar</a>
                    </p">
                </div>
            ';
        }else{
    
            if(is_file($img_dir.$foto)){
                chmod($img_dir.$foto, 0777);
                unlink($img_dir.$foto);
            }
    
            echo '
                <div class="notification is-warning is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No podemos subir la imagen al sistema en este momento, por favor intente nuevamente
                </div>
            ';
        }
        $actualizar_producto=null;
    }
    public static function produ_img_eliminar(){
        require_once "main.php";

        /*== Almacenando datos ==*/
        $product_id=limpiar_cadena($_POST['img_del_id']);
    
        /*== Verificando producto ==*/
        $check_producto=conexion();
        $check_producto=$check_producto->query("SELECT * FROM producto WHERE producto_id='$product_id'");
    
        if($check_producto->rowCount()==1){
            $datos=$check_producto->fetch();
        }else{
            echo '
                <div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La imagen del PRODUCTO que intenta eliminar no existe
                </div>
            ';
            exit();
        }
        $check_producto=null;
    
    
        /* Directorios de imagenes */
        $img_dir='../img/producto/';
    
        /* Cambiando permisos al directorio */
        chmod($img_dir, 0777);
    
    
        /* Eliminando la imagen */
        if(is_file($img_dir.$datos['producto_foto'])){
    
            chmod($img_dir.$datos['producto_foto'], 0777);
    
            if(!unlink($img_dir.$datos['producto_foto'])){
                echo '
                    <div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        Error al intentar eliminar la imagen del producto, por favor intente nuevamente
                    </div>
                ';
                exit();
            }
        }
    
    
        /*== Actualizando datos ==*/
        $actualizar_producto=conexion();
        $actualizar_producto=$actualizar_producto->prepare("UPDATE producto SET producto_foto=:foto WHERE producto_id=:id");
    
        $marcadores=[
            ":foto"=>"",
            ":id"=>$product_id
        ];
    
        if($actualizar_producto->execute($marcadores)){
            echo '
                <div class="notification is-info is-light">
                    <strong>¡IMAGEN O FOTO ELIMINADA!</strong><br>
                    La imagen del producto ha sido eliminada exitosamente, pulse Aceptar para recargar los cambios.
    
                    <p class="has-text-centered pt-5 pb-5">
                        <a href="index.php?vista=product_img&product_id_up='.$product_id.'" class="button is-link is-rounded">Aceptar</a>
                    </p">
                </div>
            ';
        }else{
            echo '
                <div class="notification is-warning is-light">
                    <strong>¡IMAGEN O FOTO ELIMINADA!</strong><br>
                    Ocurrieron algunos inconvenientes, sin embargo la imagen del producto ha sido eliminada, pulse Aceptar para recargar los cambios.
    
                    <p class="has-text-centered pt-5 pb-5">
                        <a href="index.php?vista=product_img&product_id_up='.$product_id.'" class="button is-link is-rounded">Aceptar</a>
                    </p">
                </div>
            ';
        }
        $actualizar_producto=null;
    }
    public static function produ_lista(){
        $inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;
        $tabla="";
    
        $campos="producto.producto_id,producto.producto_codigo,producto.producto_nombre,producto.producto_precio,producto.producto_stock,producto.producto_foto,producto.categoria_id,producto.usuario_id,categoria.categoria_id,categoria.categoria_nombre,usuario.usuario_id,usuario.usuario_nombre,usuario.usuario_apellido";
    
        if(isset($busqueda) && $busqueda!=""){
    
            $consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id WHERE producto.producto_codigo LIKE '%$busqueda%' OR producto.producto_nombre LIKE '%$busqueda%' ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";
    
            $consulta_total="SELECT COUNT(producto_id) FROM producto WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%'";
    
        }elseif($categoria_id>0){
    
            $consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id WHERE producto.categoria_id='$categoria_id' ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";
    
            $consulta_total="SELECT COUNT(producto_id) FROM producto WHERE categoria_id='$categoria_id'";
    
        }else{
    
            $consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";
    
            $consulta_total="SELECT COUNT(producto_id) FROM producto";
    
        }
    
        $conexion=conexion();
    
        $datos = $conexion->query($consulta_datos);
        $datos = $datos->fetchAll();
    
        $total = $conexion->query($consulta_total);
        $total = (int) $total->fetchColumn();
    
        $Npaginas =ceil($total/$registros);
    
        if($total>=1 && $pagina<=$Npaginas){
            $contador=$inicio+1;
            $pag_inicio=$inicio+1;
            foreach($datos as $rows){
                $tabla.='
                    <article class="media">
                        <figure class="media-left">
                            <p class="image is-64x64">';
                            if(is_file("./img/producto/".$rows['producto_foto'])){
                                $tabla.='<img src="./img/producto/'.$rows['producto_foto'].'">';
                            }else{
                                $tabla.='<img src="./img/producto.png">';
                            }
                   $tabla.='</p>
                        </figure>
                        <div class="media-content">
                            <div class="content">
                              <p>
                                <strong>'.$contador.' - '.$rows['producto_nombre'].'</strong><br>
                                <strong>CODIGO:</strong> '.$rows['producto_codigo'].', <strong>PRECIO:</strong> $'.$rows['producto_precio'].', <strong>STOCK:</strong> '.$rows['producto_stock'].', <strong>CATEGORIA:</strong> '.$rows['categoria_nombre'].', <strong>REGISTRADO POR:</strong> '.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'
                              </p>
                            </div>
                            <div class="has-text-right">
                                <a href="index.php?vista=product_img&product_id_up='.$rows['producto_id'].'" class="button is-link is-rounded is-small">Imagen</a>
                                <a href="index.php?vista=product_update&product_id_up='.$rows['producto_id'].'" class="button is-success is-rounded is-small">Actualizar</a>
                                <a href="'.$url.$pagina.'&product_id_del='.$rows['producto_id'].'" class="button is-danger is-rounded is-small">Eliminar</a>
                            </div>
                        </div>
                    </article>
    
                    <hr>
                ';
                $contador++;
            }
            $pag_final=$contador-1;
        }else{
            if($total>=1){
                $tabla.='
                    <p class="has-text-centered" >
                        <a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
                            Haga clic acá para recargar el listado
                        </a>
                    </p>
                ';
            }else{
                $tabla.='
                    <p class="has-text-centered" >No hay registros en el sistema</p>
                ';
            }
        }
    
        if($total>0 && $pagina<=$Npaginas){
            $tabla.='<p class="has-text-right">Mostrando productos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
        }
    
        $conexion=null;
        echo $tabla;
    
        if($total>=1 && $pagina<=$Npaginas){
            echo paginador_tablas($pagina,$Npaginas,$url,7);
        }
    }
}