<?php
/*
require_once './Cuenta.php';
require_once './Deposito.php';
require_once './Retiro.php';*/

class Ajuste{
    public $id;
    public $idOperacion;
    public $motivo;
    public $monto;

    public function __construct(){
		$params = func_get_args();
		$num_params = func_num_args();
		$funcion_constructor ='__construct'.$num_params;
		if (method_exists($this,$funcion_constructor)) {
			call_user_func_array(array($this,$funcion_constructor),$params);
		}
    }

    public function __construct4($id, $idOperacion, $motivo, $monto){
        $this->id = $id;
        $this->idOperacion = $idOperacion;
        $this->motivo = $motivo;
        $this->monto = $monto;
    }

    public function __construct3($idOperacion, $motivo, $monto){
        $this->id = rand(100, 999);
        $this->idOperacion = $idOperacion;
        $this->motivo = $motivo;
        $this->monto = $monto;
    }

    public function CrearAjuste(){
        $accesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $accesoDato->prepararConsulta("INSERT INTO ajuste (idOperacion, motivo, monto) VALUES (:idOperacion, :motivo, :monto)");

        // Asigna los valores a los marcadores de posición en la consulta
        $consulta->bindParam(':idOperacion', $this->idOperacion);
        $consulta->bindParam(':motivo', $this->motivo);
        $consulta->bindParam(':monto', $this->monto);
    
        // Ejecuta la consulta
        $consulta->execute();
    
        return $accesoDato->obtenerUltimoId();
    }



}
?>