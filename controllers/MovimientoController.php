<?php
require_once './models/Cuenta.php';
require_once './models/Deposito.php';
require_once './models/Retiro.php';
/**4- ConsultaMovimientos.php: (por GET)
Datos a consultar:
a- El total depositado (monto) por tipo de cuenta y moneda en un día en
particular (se envía por parámetro), si no se pasa fecha, se muestran las del día
anterior.
b- El listado de depósitos para un usuario en particular.
c- El listado de depósitos entre dos fechas ordenado por nombre.
d- El listado de depósitos por tipo de cuenta.
e- El listado de depósitos por moneda.

a- El total retirado (monto) por tipo de cuenta y moneda en un día en particular
(se envía por parámetro), si no se pasa fecha, se muestran las del día anterior.
b- El listado de retiros para un usuario en particular.
c- El listado de retiros entre dos fechas ordenado por nombre.
d- El listado de retiros por tipo de cuenta.
e- El listado de retiros por moneda.
f- El listado de todas las operaciones (depósitos y retiros) por usuario*/

class MovimientoController{
    function consultaOperaciones($request, $response, $args){
        $parametros = $request->getQueryParams();
        $numeroDoc = $parametros["numeroDoc"];
        $cuenta = Cuenta::TraerCuentaPorDni($numeroDoc);
        $depositos = Deposito::DepositosPorUsuario($cuenta->numeroCuenta);
        if($depositos){
            $payload = json_encode(array("mensaje" => "Los depositos por Usuario son:". $depositos));
            //Deposito::MostrarDepositos($depositos);
        }
        $retiros = Retiro::RetirosPorUsuario($cuenta->numeroCuenta);
        if($retiros){
            $payload = json_encode(array("mensaje" => "Los retiros por Usuario son:". $retiros));
            //Retiro::MostrarRetiros($retiros);
        }
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function consultaTotalDeposito($request, $response, $args){
        $parametros = $request->getQueryParams();
        $tipoCuenta = $parametros["tipoCuenta"];
        if(isset($parametros["fecha"])){
            $fecha = $parametros["fecha"];
            $total = 0;
            $depositos = Deposito::TraerDepositosPorFechaYTipo($fecha, $tipoCuenta);
            foreach($depositos as $deposito){
                $total += $deposito->deposito; 
            }
            if($total > 0){
                $payload = json_encode(array("mensaje" =>"El total de depositos por tipo de cuenta y moneda en la fecha ingresada es de: ".$total));
            }else{
                $payload = json_encode(array("mensaje" =>"No se encontraron coincidencias con el tipo de cuenta, moneda y fecha de deposito"));
            }
        }else{
            $fechaAnterior = date("d-m-Y", strtotime(date("d-m-Y") . "-1 day"));
            $total = 0;
            $depositos = Deposito::TraerDepositosPorFechaYTipo($fechaAnterior, $tipoCuenta);
            foreach($depositos as $deposito){
                $total += $deposito->deposito; 
            }
            if($total > 0){
                $payload = json_encode(array("mensaje" =>"El total de depositos por tipo de cuenta y moneda en la fecha ingresada es de: ".$total));
            }else{
                $payload = json_encode(array("mensaje" =>"No se encontraron coincidencias con el tipo de cuenta, moneda y fecha de deposito"));
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function consultaTotalRetiro($request, $response, $args){
        $parametros = $request->getQueryParams();
        $tipoCuenta = $parametros["tipoCuenta"];
        if(isset($parametros["fecha"])){
            $fecha = $parametros["fecha"];
            $total = 0;
            $retiros = Retiro::TraerRetirosPorFechaYTipo($fecha, $tipoCuenta);
            foreach($retiros as $retiro){
                $total += $retiro->monto; 
            }
            if($total > 0){
                $payload = json_encode(array("mensaje" =>"El total de retiros por tipo de cuenta y moneda en la fecha ingresada es de: ".$total));
            }else{
                $payload = json_encode(array("mensaje" =>"No se encontraron coincidencias con el tipo de cuenta y fecha de deposito"));
            }
        }else{
            $fechaAnterior = date("d-m-Y", strtotime(date("d-m-Y") . "-1 day"));
            $total = 0;
            $retiros = Retiro::TraerRetirosPorFechaYTipo($fechaAnterior, $tipoCuenta);
            foreach($retiros as $retiro){
                $total += $retiro->monto; 
            }
            if($total > 0){
                $payload = json_encode(array("mensaje" =>"El total de retiros por tipo de cuenta en la fecha ingresada es de: ".$total));
            }else{
                $payload = json_encode(array("mensaje" =>"No se encontraron coincidencias con el tipo de cuenta y fecha de deposito"));
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

/**El listado de depósitos para un usuario en particular. */
    function depositosPorUsuario($request, $response, $args){
        $parametros = $request->getQueryParams();
        $numeroCuenta = $parametros["numeroCuenta"];
        $depositos = Deposito::DepositosPorUsuario($numeroCuenta);
        if($depositos){
            $payload = json_encode(array("mensaje" => "Los depositos por Usuario son:"));
            Deposito::MostrarDepositos($depositos);
        }else{
            $payload = json_encode(array("mensaje" => "No hay depositos hechos por Usuario"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function retirosPorUsuario($request, $response, $args){
        $parametros = $request->getQueryParams();
        $numeroCuenta = $parametros["numeroCuenta"];
        $retiros = Retiro::RetirosPorUsuario($numeroCuenta);
        if($retiros){
            $payload = json_encode(array("mensaje" => "Los retiros por Usuario son:"));
            Retiro::MostrarRetiros($retiros);
        }else{
            $payload = json_encode(array("mensaje" => "No hay retiros hechos por Usuario"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**El listado de depósitos entre dos fechas ordenado por nombre. */
    function depositosEntreDosFechas($request, $response, $args){
        $parametros = $request->getQueryParams();
        $fechaUno = $parametros["fechaUno"];
        $fechaDos = $parametros["fechaDos"];
        $depositos = Deposito::TraerDepositos();
        $depositosFiltrado = array();
        foreach($depositos as $deposito){
            if($deposito->FechaDentroRango($fechaUno, $fechaDos)){
                array_push($depositosFiltrado, $deposito);
            }
        }
        $depositosOrdenados = Deposito::OrdenarDepositosPorNumeroCuenta($depositosFiltrado);
        $payload = json_encode(array("mensaje" => "Los depositos entre dos fechas ordenado por nombre son:"));
        Deposito::MostrarDepositos($depositosOrdenados);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function retirosEntreDosFechas($request, $response, $args){
        $parametros = $request->getQueryParams();
        $fechaUno = $parametros["fechaUno"];
        $fechaDos = $parametros["fechaDos"];
        $retiros = Retiro::TraerRetiros();
        $retirosFiltrado = array();
        foreach($retiros as $retiro){
            if($retiro->FechaDentroRango($fechaUno, $fechaDos)){
                array_push($retirosFiltrado, $retiro);
            }
        }
        $retirosOrdenados = Retiro::OrdenarRetirosPorNumeroCuenta($retirosFiltrado);
        $payload = json_encode(array("mensaje" => "Los retiros entre dos fechas ordenado por nombre son:". $retirosOrdenados));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    //El listado de depósitos por tipo de cuenta.
    function depositosPorTipoCuenta($request, $response, $args){
        $parametros = $request->getQueryParams();
        $tipoCuenta = $parametros["tipoCuenta"];
        $depositosPorTipo = Deposito::DepositosPorTipo($tipoCuenta);
        $payload = json_encode(array("mensaje" => "Los depositos por tipo de cuenta son:". $depositosPorTipo));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    } 

    function retirosPorTipoCuenta($request, $response, $args){
        $parametros = $request->getQueryParams();
        $tipoCuenta = $parametros["tipoCuenta"];
        $retirosPorTipo = Retiro::RetirosPorTipo($tipoCuenta);
        $payload = json_encode(array("mensaje" => "Los retiros por tipo de cuenta son:". $retirosPorTipo));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    } 

    function depositosPorMoneda($request, $response, $args){
        $parametros = $request->getQueryParams();
        $moneda = $parametros["moneda"];
        $depositosPorMoneda = array();
        $depositos = Deposito::TraerDepositos();
        foreach($depositos as $deposito){
            if($deposito->getMoneda() == $moneda){
                array_push($depositosPorMoneda, $deposito);
            }    
        }

        $payload = json_encode(array("mensaje" => "Los depositos por moneda son:". $depositosPorMoneda));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    function retirosPorMoneda($request, $response, $args){
        $parametros = $request->getQueryParams();
        $moneda = $parametros["moneda"];
        $retirosPorMoneda = array();
        $retiros = Retiro::TraerRetiros();
        foreach($retiros as $retiro){
            if($retiro->getMoneda() == $moneda){
                array_push($retirosPorMoneda, $retiro);
            }    
        }

        $payload = json_encode(array("mensaje" => "Los retiros por moneda son:". $retirosPorMoneda));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}




?>