<?php
/**6- RetiroCuenta.php: (por POST) se recibe el Tipo de Cuenta, Nro de Cuenta y Moneda
y el importe a depositar, si la cuenta existe en banco.json, se decrementa el saldo
existente según el importe extraído y se registra en el archivo retiro.json la operación
con los datos de la cuenta y el depósito (fecha, monto) e id autoincremental.
Si la cuenta no existe o el saldo es inferior al monto a retirar, informar el tipo de error. */
require_once './models/Cuenta.php';
require_once './models/Retiro.php';
class RetiroController{
    function retirarCuenta($request, $response, $args){
    $parametros = $request->getParsedBody();
    $tipoCuenta = $parametros["tipoCuenta"];
    $numeroCuenta = $parametros["numeroCuenta"];
    $monto = $parametros["monto"];
    $cuenta = Cuenta::TraerCuentaPorTipoYNro($numeroCuenta, $tipoCuenta);
        if($cuenta){
            if($cuenta->saldo > $monto){
                $nuevoSaldo = $cuenta->saldo - $monto;
                Cuenta::ActualizarSaldo($numeroCuenta, $tipoCuenta, $nuevoSaldo);
                $retiro = new Retiro($tipoCuenta, $numeroCuenta, $monto);
                $retiro->CrearRetiro();
                $payload = json_encode(array("mensaje" => "El retiro se llevo a cabo con exito"));
            }else{
                $payload = json_encode(array("mensaje" => "El monto de retiro es mayor que el saldo de la cuenta"));
            }
        }else{
            $payload = json_encode(array("mensaje" => "La cuenta no existe"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>