<?php

declare(strict_types=1);


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

require __DIR__ . '/../src/db/conexion.php';

return function (App $app) {
    // ---------------------------------------------
    // ENDPOINT DE PRUEBA
    // ---------------------------------------------
    // GET /
    // Este endpoint solo sirve para comprobar que la API está funcionando.
    $app->get('/', function (Request $request, Response $response): Response {
        $data = ['mensaje' => 'API CasaTop funcionando'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // ---------------------------------------------
    // ENDPOINTS PARA VEHICULOS
    // ---------------------------------------------

    // POST /vehiculos
    // Este endpoint recibe los datos de un nuevo vehículo por JSON
    // y los inserta en la tabla Vehiculo.
    $app->post('/vehiculos/agregar', function (Request $request, Response $response): Response {
        $pdo = obtenerConexion();

        // Se obtienen los datos enviados en el cuerpo de la petición
        $parametros = (array)$request->getParsedBody();

        $placa = $parametros['placa'] ?? null;
        $modelo = $parametros['modeloVehiculo'] ?? null;
        $color = $parametros['color'] ?? null;
        $anio = $parametros['anioFabricacion'] ?? null;
        $kilometraje = $parametros['kilometraje'] ?? null;
        $precio = $parametros['precioOriginal'] ?? null;
        $idMarca = $parametros['idMarca'] ?? null;

        // Se arma la consulta de inserción
        $sentencia = "INSERT INTO Vehiculo 
                (placa, modeloVehiculo, color, anioFabricacion, kilometraje, precioOriginal, idMarca)
                VALUES (:placa, :modelo, :color, :anio, :kilometraje, :precio, :idMarca)";

        $guardar = $pdo->prepare($sentencia);
        $guardar->bindParam(':placa', $placa);
        $guardar->bindParam(':modelo', $modelo);
        $guardar->bindParam(':color', $color);
        $guardar->bindParam(':anio', $anio);
        $guardar->bindParam(':kilometraje', $kilometraje);
        $guardar->bindParam(':precio', $precio);
        $guardar->bindParam(':idMarca', $idMarca);

        try {
            $guardar->execute();
            $data = ['mensaje' => 'Vehiculo insertado correctamente'];
            $status = 201;
        } catch (\PDOException $e) {
            $data = ['error' => 'No se pudo insertar el vehiculo','detalle' => $e->getMessage() ];
            $status = 500;
        }

        $response->getBody()->write(json_encode($data));
        return $response->withStatus($status)
                        ->withHeader('Content-Type', 'application/json');
    });

    // GET /vehiculos
    // Este endpoint devuelve todos los registros de la tabla Vehiculo.
    $app->get('/vehiculos/obtener', function (Request $request, Response $response): Response {
        $pdo = obtenerConexion();

        $sentencia = "SELECT * FROM Vehiculo";
        $guardar = $pdo->query($sentencia);
        $vehiculos = $guardar->fetchAll(\PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($vehiculos));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // ---------------------------------------------
    // ENDPOINTS PARA MARCAS DE VEHICULOS
    // ---------------------------------------------

    // POST /marcas
    // Este endpoint recibe los datos de una nueva marca por JSON
    // y los inserta en la tabla Marcas_Vehiculo.
    $app->post('/marcas/agregar', function (Request $request, Response $response): Response {
        $pdo = obtenerConexion();

        $parametros = (array)$request->getParsedBody();

        $descripcion = $parametros['descripMarca'] ?? null;
        $pais = $parametros['paisMarca'] ?? null;
        $sitioWeb = $parametros['sitioWebOficial'] ?? null;

        $sentencia = "INSERT INTO Marcas_Vehiculo (descripMarca, paisMarca, sitioWebOficial)
                VALUES (:descripcion, :pais, :sitioWeb)";

        $guardar = $pdo->prepare($sentencia);
        $guardar->bindParam(':descripcion', $descripcion);
        $guardar->bindParam(':pais', $pais);
        $guardar->bindParam(':sitioWeb', $sitioWeb);

        try {
            $guardar->execute();
            $data = ['mensaje' => 'Marca insertada correctamente'];
            $status = 201;
        } catch (\PDOException $e) {
            $data = ['error' => 'No se pudo insertar la marca'];
            $status = 500;
        }

        $response->getBody()->write(json_encode($data));
        return $response->withStatus($status)
                        ->withHeader('Content-Type', 'application/json');
    });

    // GET /marcas/{idMarca}
    // Este endpoint devuelve una marca específica, buscando por idMarca.
    $app->get('/marcas/obtener/{idMarca}', function (Request $request, Response $response, array $args): Response {
        $pdo = obtenerConexion();

        $idMarca = $args['idMarca'];

        $sentencia = "SELECT * FROM Marcas_Vehiculo WHERE idMarca = :idMarca";
        $guardar = $pdo->prepare($sentencia);
        $guardar->bindParam(':idMarca', $idMarca, \PDO::PARAM_INT);
        $guardar->execute();

        $marca = $guardar->fetch(\PDO::FETCH_ASSOC);

        if ($marca) {
            $response->getBody()->write(json_encode($marca));
            $status = 200;
        } else {
            $response->getBody()->write(json_encode(['mensaje' => 'Marca no encontrada']));
            $status = 404;
        }

        return $response->withStatus($status)
                        ->withHeader('Content-Type', 'application/json');
    });
};
