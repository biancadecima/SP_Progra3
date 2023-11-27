<?php 
/*DepositoCuenta.php: (por POST) se recibe el Tipo de Cuenta, Nro de Cuenta y
Moneda y el importe a depositar, si la cuenta existe en banco.json, se incrementa el
saldo existente según el importe depositado y se registra en el archivo depósitos.json
la operación con los datos de la cuenta y el depósito (fecha, monto) e id
autoincremental). Si la cuenta no existe, informar el error.

b- Completar el depósito con imagen del talón de depósito con el nombre: Tipo de
Cuenta, Nro. de Cuenta e Id de Depósito, guardando la imagen en la carpeta
/ImagenesDeDepositos2023.*/
require_once './models/Cuenta.php';
require_once './models/Deposito.php';

class DepositoController{
    function depositoCuenta($request, $response, $args){
        $parametros = $request->getParsedBody();
        $tipoCuenta = $parametros["tipoCuenta"];
        $numeroCuenta = $parametros["numeroCuenta"];
        $deposito = $parametros["deposito"];
        $imagen = $_FILES["imagen"];

        $cuenta = Cuenta::TraerCuentaPorTipoYNro($numeroCuenta, $tipoCuenta);
        if($cuenta){
            $nuevoSaldo = $cuenta->saldo + $deposito;
            Cuenta::ActualizarSaldo($cuenta->numeroCuenta, $cuenta->tipoCuenta, $nuevoSaldo);
            $deposito = new Deposito($tipoCuenta, $numeroCuenta, $deposito); 
            $id = $deposito->CrearDeposito();
            $rutaImagenDeposito = 'C:\xampp\htdocs\SP_Progra3\ImagenesDepositos\2023';
            move_uploaded_file($imagen['tmp_name'], $deposito->DestinoImagenDeposito($rutaImagenDeposito, $id));
            
            $payload = json_encode(array("mensaje" => "Se creó el deposito exitosamente"));
        }else{
            $payload = json_encode(array("mensaje" => "La cuenta no existe"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}




?>