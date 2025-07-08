<?php
$conexion = new mysqli("localhost", "root", "", "rpfarmapractica");//En esta linea se hace la conexion con la base de datos.


if (isset($_POST['crear'])) {// En esta linea se verifica si se envio el formulario para crear un nuevo producto, despues captura los datos enviados desde el formulario.
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $nombreImagen = "default.jpg";//Despues de esta linea, lo que se esta haciendo es poder subir una imagen, esta actualmente no se sube como tal a la base de datos, se guarda en una carpeta en el directorio de wamp en www
    //esto es mas que nada, para no disminuir el rendimiento de la base de datos.Despues la imagen se rescata medinate el nombre del archivo.
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['imagen']['tmp_name'];
        $originalName = basename($_FILES['imagen']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extension, $permitidas)) {
            $nombreImagen = uniqid() . '.' . $extension;
            move_uploaded_file($tmpName, "imagenes/" . $nombreImagen);
        }
    }
    $conexion->query("INSERT INTO productos (nombre, precio, stock, imagen) VALUES ('$nombre', '$precio', '$stock', '$nombreImagen')");//Aca se insertan los datos del nuevo producto en la base de datos.
    header("Location: MenuAdministrador.php");//Esta linea sirve para redirigir a la pagina principal tras el ingreso del nuevo producto.
}


if (isset($_GET['eliminar'])) {//Esta linea verifica si se recibio la solicitud para eliminar un producto
    $id = $_GET['eliminar'];//Esta linea obtiene el ID del producto que se quiere eliminar
    $conexion->query("DELETE FROM productos WHERE id=$id");//Esta ejecuta la consulta para eliminar el producto
    header("Location: MenuAdministrador.php");
}


if (isset($_POST['actualizar'])) {//Esta linea verifica si se recibio la solicitud para actualizar un producto, despues captura los datos enviados desde el formulario.
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $conexion->query("UPDATE productos SET nombre='$nombre', precio='$precio', stock='$stock' WHERE id=$id");//Esta linea envia la consulta para actualizar los datos del producto seleccionado.
    header("Location: MenuAdministrador.php");
}


$productos = $conexion->query("SELECT * FROM productos");// Esta linea ejecuta la consulta para obtener todos los datos de la base de datos.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link rel="icon" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/box-seam.svg" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container py-5">
    <div class="mb-4">
        <a href="login.php" class="btn btn-outline-primary">
            <i class="bi bi-box-arrow-left"></i> Volver al Login
        </a>
    </div>
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex align-items-center">
                    <i class="bi bi-plus-circle me-2"></i>
                    <h4 class="mb-0">Agregar Nuevo Producto</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label"><i class="bi bi-box"></i> Nombre</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre del producto" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><i class="bi bi-currency-dollar"></i> Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label"><i class="bi bi-stack"></i> Stock</label>
                            <input type="number" name="stock" class="form-control" placeholder="Stock" required>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label"><i class="bi bi-image"></i> Imagen</label>
                            <input type="file" name="imagen" accept="image/*" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-4 d-grid">
                            <button type="submit" name="crear" class="btn btn-success btn-lg">
                                <i class="bi bi-plus-lg"></i> Agregar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mt-5 mb-3">Lista de Productos</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        // Lo siguiente recorre la lista de productos en la base de datos y despues genera una fila en la tabla con los datos obtenidos de los productos, ademas agrega un boton para editar o eliminar en cada uno de estos.
         while($row = $productos->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nombre'] ?></td>
                <td>$<?= number_format($row['precio'], 2) ?></td>
                <td><?= $row['stock'] ?></td>
                <td>
                    <a href="?editar=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="?eliminar=<?= $row['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar este producto?');" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <?php if (isset($_GET['editar'])): //El siguiente codigo hace que al presionar el boton editar, se obtienes los datos del producto elegido y se genera un formulario para la edicion.
        $idEditar = $_GET['editar'];
        $productoEditar = $conexion->query("SELECT * FROM productos WHERE id=$idEditar")->fetch_assoc();
    ?>
    <h2 class="mt-5 mb-3">Editar Producto</h2>
    <form method="POST" class="row g-3">
        <input type="hidden" name="id" value="<?= $productoEditar['id'] ?>">
        <div class="col-md-4">
            <input type="text" name="nombre" class="form-control" value="<?= $productoEditar['nombre'] ?>" required>
        </div>
        <div class="col-md-3">
            <input type="number" step="0.01" name="precio" class="form-control" value="<?= $productoEditar['precio'] ?>" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="stock" class="form-control" value="<?= $productoEditar['stock'] ?>" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="actualizar" class="btn btn-success w-100">Actualizar</button>
        </div>
    </form>
    <?php endif; ?>

</body>
</html>