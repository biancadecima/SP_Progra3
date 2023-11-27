<?php
class Cuenta{
    public $numeroCuenta;
    public $nombre;
    public $tipoDoc;
    public $numeroDoc;
    public $mail;
    public $tipoCuenta;
    public $saldo;
    public $activo;

    public function __construct(){
		$params = func_get_args();
		$num_params = func_num_args();
		$funcion_constructor ='__construct'.$num_params;
		if (method_exists($this,$funcion_constructor)) {
			call_user_func_array(array($this,$funcion_constructor),$params);
		}
    }

    public function __construct6($nombre, $tipoDoc, $numeroDoc, $mail, $tipoCuenta, $saldo) {
        $this->nombre = $nombre;
        $this->tipoDoc = $tipoDoc;
        $this->numeroDoc = $numeroDoc;
        $this->mail = $mail;
        $this->tipoCuenta = $tipoCuenta;
        $this->saldo = $saldo;
    }
    public function __construct8($numeroCuenta, $nombre, $tipoDoc, $numeroDoc, $mail, $tipoCuenta, $saldo, $activo) {
        $this->numeroCuenta = $numeroCuenta;
        $this->nombre = $nombre;
        $this->tipoDoc = $tipoDoc;
        $this->numeroDoc = $numeroDoc;
        $this->mail = $mail;
        $this->tipoCuenta = $tipoCuenta;
        $this->saldo = $saldo;
        $this->activo = $activo;
    }

    public function getMoneda(){
        $arrayTipo = explode(" ", $this->tipoCuenta);
        $moneda = $arrayTipo[1];
        return $moneda;
    }

    public function CrearCuenta(){
        $accesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $accesoDato->prepararConsulta("INSERT INTO cuenta (nombre, tipoDoc, numeroDoc, mail, tipoCuenta, saldo, activo) VALUES (:nombre, :tipoDoc, :numeroDoc, :mail, :tipoCuenta, :saldo, :activo)");
    
        $activo = 1;
        // Asigna los valores a los marcadores de posición en la consulta
        $consulta->bindParam(':nombre', $this->nombre);
        $consulta->bindParam(':tipoDoc', $this->tipoDoc);
        $consulta->bindParam(':numeroDoc', $this->numeroDoc);
        $consulta->bindParam(':mail', $this->mail);
        $consulta->bindParam(':tipoCuenta', $this->tipoCuenta);
        $consulta->bindParam(':saldo', $this->saldo);
        $consulta->bindParam(':activo', $activo);
    
        // Ejecuta la consulta
        $consulta->execute();
    
        return $accesoDato->obtenerUltimoId();
    }

    public static function TraerCuentaPorTipoYDni($numeroDoc, $tipoCuenta)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * FROM cuenta WHERE numeroDoc = ? AND tipoCuenta = ? AND activo = 1");
        $consulta->bindValue(1, $numeroDoc, PDO::PARAM_INT);
        $consulta->bindValue(2, $tipoCuenta, PDO::PARAM_STR);
        $consulta->execute();
        
        $cuenta = $consulta->fetchObject('Cuenta');
        return $cuenta;   
    }

    public static function TraerCuentaPorDni($numeroDoc)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from cuenta where numeroDoc = ? AND activo = 1");
        $consulta->bindValue(1, $numeroDoc, PDO::PARAM_INT);
        $consulta->execute();
        
        $cuenta = $consulta->fetchObject('Cuenta');
        return $cuenta;   
    }

    public static function ActualizarSaldo($numeroCuenta, $tipoCuenta, $nuevoSaldo){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta =$objetoAccesoDato->prepararConsulta("UPDATE cuenta SET saldo = ? WHERE numeroCuenta = ? AND tipoCuenta = ? AND activo = 1");
        $consulta->bindValue(1, $nuevoSaldo, PDO::PARAM_INT);
        $consulta->bindValue(2, $numeroCuenta, PDO::PARAM_INT);
        $consulta->bindValue(3, $tipoCuenta, PDO::PARAM_STR);
        return $consulta->execute();
    } 

    public static function TraerCuentaPorTipoYNro($numeroCuenta, $tipoCuenta)
    {
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objetoAccesoDato->prepararConsulta("SELECT * from cuenta where numeroCuenta = ? AND tipoCuenta = ? AND activo = 1");
        $consulta->bindValue(1, $numeroCuenta, PDO::PARAM_INT);
        $consulta->bindValue(2, $tipoCuenta, PDO::PARAM_STR);
        $consulta->execute();
        
        $cuenta = $consulta->fetchObject('Cuenta');
        return $cuenta;   
    }

    public function ModificarUsuario(){
        $objetoAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta =$objetoAccesoDato->prepararConsulta("UPDATE cuenta SET nombre = ?, tipoDoc = ?, numeroDoc = ?, mail = ? WHERE numeroCuenta = ? AND tipoCuenta = ? AND activo = 1");
        $consulta->bindValue(1, $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(2, $this->tipoDoc, PDO::PARAM_STR);
        $consulta->bindValue(3, $this->numeroDoc, PDO::PARAM_STR);
        $consulta->bindValue(4, $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(5, $this->numeroCuenta, PDO::PARAM_STR);
        $consulta->bindValue(6, $this->tipoCuenta, PDO::PARAM_STR);;
        return $consulta->execute();
    }

    public function EliminarCuenta(){ // debe ser una baja logica
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE cuenta SET activo = 0 WHERE numeroCuenta = ? AND tipoCuenta = ?");
        $consulta->bindValue(1, $this->numeroCuenta, PDO::PARAM_INT);
        $consulta->bindValue(2, $this->tipoCuenta, PDO::PARAM_STR);
        return $consulta->execute();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function DestinoImagenCuenta($ruta, $numeroCuenta){
        $destino = $ruta."\\".$numeroCuenta."-".$this->tipoCuenta.".png";
        return $destino;
    }
   
}
?>