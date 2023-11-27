<?php
class Deposito{
    public $id;
    public $tipoCuenta;
    public $numeroCuenta;
    public $deposito;
    public $fecha;

    public function __construct(){
		$params = func_get_args();
		$num_params = func_num_args();
		$funcion_constructor ='__construct'.$num_params;
		if (method_exists($this,$funcion_constructor)) {
			call_user_func_array(array($this,$funcion_constructor),$params);
		}
    }

    public function __construct3($tipoCuenta, $numeroCuenta, $deposito){
        $this->tipoCuenta = $tipoCuenta;
        $this->numeroCuenta = $numeroCuenta;
        $this->deposito = $deposito;
        $this->fecha = date("Y-m-d");
    }

    public function __construct5($id, $tipoCuenta, $numeroCuenta, $deposito, $fecha){
        $this->id = $id;
        $this->tipoCuenta = $tipoCuenta;
        $this->numeroCuenta = $numeroCuenta;
        $this->deposito = $deposito;
        $this->fecha = $fecha;
    }

    public function getMoneda(){
        $arrayTipo = explode(" ", $this->tipoCuenta);
        $moneda = $arrayTipo[1];
        return $moneda;
    }

    public function CrearDeposito(){
        $accesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $accesoDato->prepararConsulta("INSERT INTO deposito (tipoCuenta, numeroCuenta, deposito, fecha) VALUES (:tipoCuenta, :numeroCuenta, :deposito, :fecha)");
    
        // Asigna los valores a los marcadores de posición en la consulta
        $consulta->bindParam(':tipoCuenta', $this->tipoCuenta);
        $consulta->bindParam(':numeroCuenta', $this->numeroCuenta);
        $consulta->bindParam(':deposito', $this->deposito);
        $consulta->bindParam(':fecha', $this->fecha);
    
        // Ejecuta la consulta
        $consulta->execute();
    
        return $accesoDato->obtenerUltimoId();
    }

    public static function TraerDepositos(){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from deposito");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Deposito');
;
    }

    public static function TraerDepositosPorFechaYTipo($fecha, $tipo){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from deposito where fecha = ? AND tipoCuenta = ?");
        $consulta->bindValue(1, $fecha, PDO::PARAM_STR);
        $consulta->bindValue(2, $tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Deposito');
    }

    public static function DepositosPorTipo($tipo){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from deposito where tipoCuenta = ?");
        $consulta->bindValue(1, $tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Deposito');
    } 

    public function DestinoImagenDeposito($ruta, $id){
        $destino = $ruta."\\".$this->tipoCuenta."-".$this->numeroCuenta."-".$id.".png";
        return $destino;
    }
    

    public static function DepositosPorUsuario($numeroCuenta){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from deposito where numeroCuenta = ? ");
        $consulta->bindValue(1, $numeroCuenta, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Deposito');
    } 

    public static function MostrarDepositos($depositos){
        if(count($depositos)>0){
            foreach($depositos as $deposito){
                echo "ID: ", $deposito->id, "\n";
                echo "Tipo Cuenta: ", $deposito->tipoCuenta, "\n";
                echo "Numero Cuenta: ", $deposito->numeroCuenta, "\n";
                echo "Deposito: ", $deposito->deposito, "\n";
                echo "Fecha: ", $deposito->fecha, "\n\n";
            }
        }
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
        if ($a->numeroCuenta == $b->numeroCuenta) {
            return 0; // Si son iguales
        } elseif ($a->numeroCuenta < $b->numeroCuenta) {
            return -1; // Si $a es menor que $b
        } else {
            return 1; // Si $a es mayor que $b
        }
    }
    public static function OrdenarDepositosPorNumeroCuenta($depositos){
        usort($depositos, 'Deposito::CompararNumeroCuenta');
        return $depositos;
    }


    public static function TraerDepositoPorID($id){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from deposito where id = ?");
        $consulta->bindValue(1, $id, PDO::PARAM_INT);
        $consulta->execute();
        $deposito = $consulta->fetchObject('Deposito');
        return $deposito;
    }
    
}


?>