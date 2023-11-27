<?php
/**7- AjusteCuenta.php (por POST),
Se ingresa el número de extracción o depósito afectado al ajuste y el motivo del
mismo. El número de extracción o depósito debe existir.
Guardar en el archivo ajustes.json
Actualiza en el saldo en el archivo banco.json */

require_once './models/Ajuste.php';
require_once './models/Cuenta.php';
require_once './models/Deposito.php';
require_once './models/Retiro.php';
class AjusteController{
    function ajusteCuenta($request, $response, $args){
        $parametros = $request->getParsedBody();
        $idOperacion = $parametros["idOperacion"];
        $motivo = $parametros["motivo"];
        if($motivo == "deposito"){
            if($deposito = Deposito::TraerDepositoPorID($idOperacion)){
                $ajuste = new Ajuste($idOperacion, $motivo, $deposito->deposito);
                if($cuenta = Cuenta::TraerCuentaPorTipoYNro($deposito->numeroCuenta, $deposito->tipoCuenta)){
                    $nuevoSaldo = $cuenta->saldo - $deposito->deposito;
                    Cuenta::ActualizarSaldo($cuenta->numeroCuenta, $cuenta->tipoCuenta, $nuevoSaldo);
                    $ajuste->CrearAjuste();
                    $payload = json_encode(array("mensaje" => "El ajuste de deposito se realizó exitosamente"));
                }else{
                    $payload = json_encode(array("mensaje" => "No se encontro el numero de cuenta"));
                }  
            }else{
                $payload = json_encode(array("mensaje" => "El deposito no existe"));
            }
        }else if($motivo == "retiro"){
            if($retiro = Retiro::TraerRetiroPorID($idOperacion)){
                $ajuste = new Ajuste($idOperacion, $motivo, $retiro->monto);
                if($cuenta = Cuenta::TraerCuentaPorTipoYNro($retiro->numeroCuenta, $retiro->tipoCuenta)){
                    $nuevoSaldo = $cuenta->saldo + $retiro->monto;
                    Cuenta::ActualizarSaldo($cuenta->numeroCuenta, $cuenta->tipoCuenta, $nuevoSaldo);
                    $ajuste->CrearAjuste();
                    $payload = json_encode(array("mensaje" => "El ajuste de retiro se realizó exitosamente"));
                }else{
                    $payload = json_encode(array("mensaje" => "No se encontro el numero de cuenta"));
                }
            }else{
                $payload = json_encode(array("mensaje" => "El retiro no existe"));
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>