<?php
require_once './models/Cuenta.php';

class CuentaController{
    /*8-CuentaAlta.php:
    a- La validación de cuenta existente deberá hacerse considerando los atributos:
    nro de cuenta y tipo de cuenta que deben ser únicos en el sistema.
    b- Se debe adecuar la lógica desarrollada para permitir el ingreso por parámetro
    opcional de saldo, que permita definir un saldo inicial en el alta de la cuenta.
    c- Se identificó una mejora funcional por la que el atributo “tipo de cuenta”
    contendrá la moneda siendo CA$ para caja de ahorro en pesos, CAU$S para caja
    de ahorro en dólares y CC$ y CCU$S para las mismas variantes de Cuenta
    Corriente
    IMPORTANTE: verificar y adecuar todos los puntos funcionales donde estos cambios
    pueden impactar.*/
    function AltaCuenta($request, $response, $args){
        $parametros = $request->getParsedBody();
        $nombre = $parametros['nombre'];
        $tipoDoc = $parametros['tipoDoc'];
        $numeroDoc = $parametros['numeroDoc'];
        $mail = $parametros['mail'];
        $tipoCuenta = $parametros['tipoCuenta'];
        if(isset($parametros["saldo"])){
            $saldo = $parametros['saldo'];
        }else{
            $saldo = 0;
        }
        $imagen = $_FILES['imagen'];
        $cuenta = new Cuenta($nombre, $tipoDoc, $numeroDoc, $mail, $tipoCuenta, $saldo);
        if(!Cuenta::TraerCuentaPorTipoYDni($numeroDoc, $tipoCuenta)){
            $numero = $cuenta->CrearCuenta();
            //var_dump($numero);
            $rutaImagenCuenta = 'C:\xampp\htdocs\SP_Progra3\ImagenesCuentas\2023';
            move_uploaded_file($imagen['tmp_name'], $cuenta->DestinoImagenCuenta($rutaImagenCuenta, $numero));
            
            $payload = json_encode(array("mensaje" => "Cuenta creada con exito"));
        }else{
            $cuenta = Cuenta::TraerCuentaPorTipoYDni($numeroDoc, $tipoCuenta);
            Cuenta::ActualizarSaldo($cuenta->numeroCuenta, $cuenta->tipoCuenta, $saldo);
            $payload = json_encode(array("mensaje" => "'La cuenta ya existe, y su saldo fue actualizado"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    /*ConsultarCuenta.php: (por POST) Se ingresa Tipo y Nro. de Cuenta, si coincide con
    algún registro del archivo banco.json, retornar la moneda/s y saldo de la cuenta/s. De
    lo contrario informar si no existe la combinación de nro y tipo de cuenta o, si existe el
    número y no el tipo para dicho número, el mensaje: “tipo de cuenta incorrecto”. */
    function consultarCuenta($request, $response, $args){
        $parametros = $request->getParsedBody();
        $numeroCuenta = $parametros['numeroCuenta'];
        $tipoCuenta = $parametros['tipoCuenta'];
        $cuenta = Cuenta::TraerCuentaPorTipoYNro($numeroCuenta, $tipoCuenta);
        if($cuenta){
            $moneda = $cuenta->getMoneda();
            $payload = json_encode(array("mensaje" => "La moneda de la cuenta consultada es $moneda y su saldo es de $cuenta->saldo"));
        }else{
            $payload = json_encode(array("mensaje" => "No existe la cuenta"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /*5- ModificarCuenta.php (por PUT)
    Debe recibir todos los datos propios de una cuenta (a excepción del saldo); si dicha
    cuenta existe (comparar por Tipo y Nro. de Cuenta) se modifica, de lo contrario
    informar que no existe esa cuenta.*/
    function modificarCuenta($request, $response, $args){
        $parametros = $request->getParsedBody();
        $numeroCuenta = $parametros['numeroCuenta'];
        $tipoCuenta = $parametros['tipoCuenta'];
        $cuenta = Cuenta::TraerCuentaPorTipoYNro($numeroCuenta, $tipoCuenta);
        if($cuenta){
            $cuenta->nombre = $parametros['nombre'];
            $cuenta->tipoDoc = $parametros['tipoDoc'];
            $cuenta->numeroDoc = $parametros['numeroDoc'];
            $cuenta->mail = $parametros['mail'];
            $cuenta->ModificarUsuario();
            $payload = json_encode(array("mensaje" => "Cuenta modificado con exito"));
        }else{
            $payload = json_encode(array("mensaje" => "Error en modificar cuenta"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**9- BorrarCuenta.php (por DELETE), debe recibir un número el tipo y número de cuenta
    y debe realizar la baja de la cuenta (soft-delete, no físicamente) y la foto relacionada a
    esa venta debe moverse a la carpeta /ImagenesBackupCuentas/2023. */

    function borrarCuenta($request, $response, $args){
        $rutaBackUpCuentas = 'C:\xampp\htdocs\SP_Progra3\ImagenesBackupCuenta';
        $rutaImagenCuenta = 'C:\xampp\htdocs\SP_Progra3\ImagenesCuentas\2023';

        $parametros = $request->getParsedBody();
        $tipoCuenta = $parametros["tipoCuenta"];
        $numeroCuenta = $parametros["numeroCuenta"];
        $cuenta = Cuenta::TraerCuentaPorTipoYNro($numeroCuenta, $tipoCuenta);
        if($cuenta){
            $imagenOrigen = $cuenta->DestinoImagenCuenta($rutaImagenCuenta, $cuenta->numeroCuenta);
            $imagenBorrada = $cuenta->DestinoImagenCuenta($rutaBackUpCuentas, $cuenta->numeroCuenta);
            if(rename($imagenOrigen, $imagenBorrada)){
                echo 'Se movió la imagen a backup. ';
            }else{
                echo 'No se pudo mover la imagen. ';
            }
            $cuenta->EliminarCuenta();
            $payload = json_encode(array("mensaje" => "Cuenta eliminada con exito"));
        }else{
            $payload = json_encode(array("mensaje" => "Error en eliminar Cuenta"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}


?>