<?php
class Retiro{
    public $id;
    public $tipoCuenta;
    public $numeroCuenta;
    public $monto;
    public $fecha;

    public function __construct(){
		$params = func_get_args();
		$num_params = func_num_args();
		$funcion_constructor ='__construct'.$num_params;
		if (method_exists($this,$funcion_constructor)) {
			call_user_func_array(array($this,$funcion_constructor),$params);
		}
    }

    public function __construct3($tipoCuenta, $numeroCuenta, $monto){
        $this->tipoCuenta = $tipoCuenta;
        $this->numeroCuenta = $numeroCuenta;
        $this->monto = $monto;
        $this->fecha = date("d-m-Y");
    }

    public function __construct5($id, $tipoCuenta, $numeroCuenta, $monto, $fecha){
        $this->id = $id;
        $this->tipoCuenta = $tipoCuenta;
        $this->numeroCuenta = $numeroCuenta;
        $this->monto = $monto;
        $this->fecha = $fecha;
    }

    public function getMoneda(){
        $arrayTipo = explode(" ", $this->tipoCuenta);
        $moneda = $arrayTipo[1];
        return $moneda;
    }

    public function CrearRetiro(){
        $accesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $accesoDato->prepararConsulta("INSERT INTO retiro (tipoCuenta, numeroCuenta, monto, fecha) VALUES (:tipoCuenta, :numeroCuenta, :monto, :fecha)");
        $fecha = date("Y-m-d");
        // Asigna los valores a los marcadores de posición en la consulta
        $consulta->bindParam(':tipoCuenta', $this->tipoCuenta);
        $consulta->bindParam(':numeroCuenta', $this->numeroCuenta);
        $consulta->bindParam(':monto', $this->monto);
        $consulta->bindParam(':fecha', $fecha);
    
        // Ejecuta la consulta
        $consulta->execute();
    
        return $accesoDato->obtenerUltimoId();
    }

    public static function TraerRetiros(){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from retiro");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Retiro');
    } 

    public static function TraerRetiroPorID($id){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from retiro where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $retiro = $consulta->fetchObject('Retiro');
        return $retiro;
    }

    public static function RetirosPorTipo($tipo){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from retiro where tipo = ?");
        $consulta->bindValue(1, $tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Retiro');
    }

    public static function RetirosPorUsuario($numeroCuenta){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from retiro where numeroCuenta = ? ");
        $consulta->bindValue(1, $numeroCuenta, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Retiro');
    } 

    public static function TraerRetirosPorFechaYTipo($fecha, $tipo){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from retiro where fecha = ? AND tipoCuenta = ?");
        $consulta->bindValue(1, $fecha, PDO::PARAM_STR);
        $consulta->bindValue(2, $tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Retiro');
    }

    public function FechaDentroRango($fechaInicio, $fechaLimite)
    {
        $fechaDeposito = strtotime($this->fecha);
        $inicio = strtotime($fechaInicio);
        $fin = strtotime($fechaLimite);
        if($fechaDeposito >= $inicio && $fechaDeposito <=$fin)
        {
            return true;
        }
        return false;
    }

    public static function CompararNumeroCuenta($a, $b){
        return $a->numeroCuenta > $b->numeroCuenta;
    }
    public static function OrdenarRetirosPorNumeroCuenta($retiros){
        usort($retiros, 'Deposito::CompararNumeroCuenta');
        return $retiros;
    }

    public static function MostrarRetiros($retiros){
        if(count($retiros)>0){
            foreach($retiros as $retiro){
                echo "ID: ", $retiro->id, "\n";
                echo "Tipo Cuenta: ", $retiro->tipoCuenta, "\n";
                echo "Numero Cuenta: ", $retiro->numeroCuenta, "\n";
                echo "Monto: ", $retiro->monto, "\n";
                echo "Fecha: ", $retiro->fecha, "\n\n";
            }
        }
    }

}
?>