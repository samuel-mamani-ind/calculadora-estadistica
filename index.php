<?php
/* ========== CONEXION A LA BASE DE DATOS ========== */
$servidor = "localhost";
$usuario = "root";
$password = "";
$basedatos = "estadistica";

$conexion = new mysqli($servidor, $usuario, $password, $basedatos);

/* ========== EJEMPLO DE ESTADISTICA ========== */
if (!empty($_POST)) {

  $datos = $_POST['datos'];

  $conjunto_datos = explode(",", $datos);

  $datos_ingresados=implode(", ", $conjunto_datos);

  $n=count($conjunto_datos);

  switch ($_POST["opc"]) {
    case '1':
      $suma=0;
      for ($i = 0; $i < $n; $i++) {
        $suma = $suma + $conjunto_datos[$i];
      }
      $media = $suma / $n;
      $tipo='Media';
      $resultado=$media;
      break;

    case '2':
      $aux=0;
      for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
    
            if ($conjunto_datos[$j] > $conjunto_datos[$j + 1]) {
                $aux = $conjunto_datos[$j];
                $conjunto_datos[$j] = $conjunto_datos[$j + 1];
                $conjunto_datos[$j + 1] = $aux;
            }
        }
      }

      if ($n % 2 == 0) {
        $mediana = ($conjunto_datos[$n / 2 - 1] + $conjunto_datos[$n / 2]) / 2;
      } else {
          $mediana = $conjunto_datos[($n - 1) / 2];
      }

      $tipo='Mediana';
      $resultado=$mediana;
      break;

    case '3':
      $aux = 0;
      for ($i=0;$i<$n;$i++) {

        $valor_evaluado = $conjunto_datos[$i];
        $c = 0;

        for ($j=0;$j<$n;$j++) {
          if ($valor_evaluado==$conjunto_datos[$j]) {
            $c = $c+1;
          }
        }

        if ($c>$aux) {
          $aux = $c;
          $moda = $valor_evaluado;
        }
      }
      $tipo='Moda';
      $resultado=$moda;
      break;

    case '4':
      $media = array_sum($conjunto_datos) / $n;

      $suma_dif = 0;
      for ($i = 0; $i < $n; $i++) {
          $dif = $conjunto_datos[$i] - $media;
          $suma_dif += $dif ** 2;
      }

      $d_estandar = sqrt($suma_dif / $n);

      $tipo='Desviación estándar';
      $resultado=$d_estandar;
      break;

    case '5':
      $media = array_sum($conjunto_datos) / $n;

      $suma_dif = 0;
      for ($i = 0; $i < $n; $i++) {
          $dif = $conjunto_datos[$i] - $media;
          $suma_dif += $dif ** 2;
      }

      $varianza = $suma_dif / $n;

      $tipo='Varianza';
      $resultado=$varianza;
      break;

    case '6':
      $media = array_sum($conjunto_datos) / $n;

      $suma_dif = 0;
      for ($i = 0; $i < $n; $i++) {
          $suma_dif += abs($conjunto_datos[$i] - $media);
      }

      $d_media = $suma_dif / $n;

      $tipo='Desviación media';
      $resultado=$d_media;
      break;
    
    case '7':
      $vMax = max($conjunto_datos);
      $vMin = min($conjunto_datos);

      $rango = $vMax - $vMin;

      $tipo='Rango';
      $resultado=$rango;
      break;

    default:
      "Opcion no valida";
      break;
  }

  /* ========== CONSULTA Y EJECUCION SQL ========== */
  $sql = "INSERT INTO operacion(tipo, resultado) VALUES('$tipo','$resultado')";
  $conexion->query($sql);
  $conexion->close();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: #ecf0f1;
      font-family: 'Montserrat', sans-serif;
      transition: background-color 0.5s;
    }

    body.dark-mode-local-storage {
      background-color: #0A1823;
    }

    .card {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: background-color 0.5s;
    }

    .dark-mode-local-storage .card {
      background-color: #34495e;
    }

    .toggle-switch {
      position: absolute;
      top: 10px;
      right: 10px;
      display: flex;
      align-items: center;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 40px;
      height: 20px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
      border-radius: 20px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 16px;
      width: 16px;
      left: 4px;
      bottom: 2px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background-color: #2e9267;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2e9267;
    }

    input:checked + .slider:before {
      -webkit-transform: translateX(16px);
      -ms-transform: translateX(16px);
      transform: translateX(16px);
    }

    .grid-container {
      display: grid;
      grid-template-columns: repeat(3, 1fr) 1fr;
      grid-template-rows: repeat(2, 1fr);
      gap: 10px;
    }

    .grid-item,
    .grid-item-4 {
      border: none;
      background-color: #2e9267;
      color: white;
      font-size: 1rem;
      font-weight: 500;
      padding: 0.5rem 1.5rem;
      border-radius: 0.5rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      cursor: pointer;
      transform: translate(0) translate(0, 0);
      transition: transform 225ms, box-shadow 225ms, background-color 0.5s;
      margin-bottom: 10px;
      text-align: center;
    }

    .dark-mode-local-storage .grid-item,
    .dark-mode-local-storage .grid-item-4 {
      background-color: #37ab98;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.35);
    }

    .grid-item:hover {
      transform: scale(1.05) translate(0, -0.15rem);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.35);
    }

    .grid-item:active {
      transform: scale(1) translate(0, 0.15rem);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .grid-item-wide {
      grid-row: span 2;
    }

    .grid-item-4 {
      border: none;
      background-color: #2e9267;
      color: white;
      font-size: 1rem;
      font-weight: 500;
      padding: 0.5rem 1.5rem;
      border-radius: 0.5rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      transform: translate(0) translate(0, 0);
      transition: transform 225ms, box-shadow 225ms, background-color 0.5s;
      margin-bottom: 10px;
      text-align: center;
    }

    .dark-mode-local-storage .grid-item-4 {
      background-color: #37ab98;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.35);
    }

    .grid-item-4:hover {
      transform: scale(1.05) translate(0, -0.15rem);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.35);
    }

    .grid-item-4:active {
      transform: scale(1) translate(0, 0.15rem);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .card-title {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 20px;
      color: #2e9267;
    }

    .input-text {
      width: 100%;
      padding: 0.5rem;
      margin-bottom: 10px;
      box-sizing: border-box;
      border: 2px solid #ccc;
      border-radius: 4px;
      outline: none;
      transition: border-color 0.5s, background-color 0.5s;
    }

    body.dark-mode-local-storage .input-text {
      border-color: #91a398;
      background-color: #2C3842;
      color: white;
    }

    body.dark-mode-local-storage .card-title {
      color: #00988d;
    }

    .input-text:focus {
      border-color: #2e9267;
    }

  </style>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const darkModeToggle = document.querySelector('.toggle-switch input');

      if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode-local-storage');
        darkModeToggle.checked = true;
      }

      darkModeToggle.addEventListener('change', function () {
        localStorage.setItem('darkMode', darkModeToggle.checked);

        document.body.classList.toggle('dark-mode-local-storage', darkModeToggle.checked);
      });

    });
  </script>
</head>
<body>

<div class="card">
  <div class="toggle-switch">
    <label class="switch">
      <input type="checkbox">
      <span class="slider"></span>
    </label>
  </div>
  <div class="card-title">Calculadora Estadística</div>
  <form action="" method="post">
    <input type="text" class="input-text" name="datos" placeholder="Escriba los datos separados por coma ej: 1,2,3,4,5" value="<?php if (!empty($datos)) {echo $datos; } ?>">
    <div class="grid-container">
      <button class="grid-item" type="submit" name="opc" value="1" >Media</button>
      <button class="grid-item" type="submit" name="opc" value="2" >Mediana</button>
      <button class="grid-item" type="submit" name="opc" value="3" >Moda</button>
      <button class="grid-item-wide grid-item-4" type="submit" name="opc" value="4" >Desviación estándar</button>
      <button class="grid-item" type="submit" name="opc" value="5" >Varianza</button>
      <button class="grid-item" type="submit" name="opc" value="6" >Desviación media</button>
      <button class="grid-item" type="submit" name="opc" value="7" >Rango</button>
    </div>
    <input type="text" class="input-text" placeholder="Aqui se mostrará el resultado de la operación" value="<?php if (!empty($resultado)) {echo $resultado; } ?>" disabled>
  </form>
</div>

</body>
</html>
