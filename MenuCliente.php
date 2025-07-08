<?php
session_start();

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {//La siguiente linea inicializa el carrito siempre como un array si no existe, esto es para evitar errores si el carrito no se inicializa correctamente.
    $_SESSION['carrito'] = [];
}

$conexion = new mysqli("localhost", "root", "", "rpfarmapractica");


if (isset($_POST['agregar'])) {//Esto es para agregar el producto seleccionado al carrito, ademas de controlar el stock,esto queriendo decir que si solo hay 2 en stock no se puedan agregar mas de estos 2.
    $id = $_POST['id'];
    $cantidad = $_POST['cantidad'];


    $producto = $conexion->query("SELECT * FROM productos WHERE id = $id")->fetch_assoc();

    if ($producto && $cantidad <= $producto['stock']) {
        $_SESSION['carrito'][$id] = [
            'id' => $producto['id'],
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => $cantidad,
            'stock' => $producto['stock']
        ];
    }
}


if (isset($_GET['quitar'])) {//Esto sirve para quitar algun producto del carrito
    $id = $_GET['quitar'];
    unset($_SESSION['carrito'][$id]);
}


if (isset($_GET['vaciar'])) {// Esto sirve para vaciar el carrito
    unset($_SESSION['carrito']);
    header("location: MenuCliente.php");//Redirecciona a la misma pagina para que se vea el carrito vacio.
    exit;
}

$productos = $conexion->query("SELECT * FROM productos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Rpfarma</title>
    <link rel="icon" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/cart.svg" type="image/svg+xml">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script>
    function actualizarTotal() {//Esta funcion sirve para actualizar de manera automatica el total del carrito, ademas de actualizar el subtotal del producto al cambiar la cantidad a comprar.
        let filas = document.querySelectorAll("#carrito tbody tr");
        let total = 0;
        filas.forEach(fila => {
            let precio = parseFloat(fila.dataset.precio);
            let cantidad = parseInt(fila.querySelector("input").value);
            let subtotal = precio * cantidad;
            fila.querySelector(".subtotal").innerText = "$" + subtotal.toFixed(2);
            total += subtotal;
    });
    document.getElementById("total").innerText = total.toFixed(2);
}
</script>
<script>
function filtrarProductos() {//Esta funcion es mas que nada una barra para buscar los productos de manera mas facil, en este caso filtra por el campo nombre del producto.
    let filtro = document.getElementById('buscador').value.toLowerCase();
    let contenedor = document.getElementById('contenedorProductos');
    let columnas = contenedor.querySelectorAll('.col-md-4');

    let found = false;
    columnas.forEach(function(columna) {
        let card = columna.querySelector('.card');
        let nombre = card.querySelector('.card-title').innerText.toLowerCase();
        if (!found && nombre.includes(filtro)) {
            columna.style.display = '';
            contenedor.prepend(columna);
            found = true;
        } else {
            columna.style.display = 'none';
        }
    });

    if (filtro.trim() === '') {
        columnas.forEach(columna => columna.style.display = '');
    }
}
</script>
</head>
<body class="container py-5">
    <div class="mb-4">
        <a href="login.php" class="btn btn-outline-primary">
            <i class="bi bi-box-arrow-left"></i> Volver al Login
        </a>
    </div>
    <h2 class="mb-4 bg-dark text-white">Productos Disponibles</h2>
    <div class="mb-4">
    <input type="text" id="buscador" class="form-control" placeholder="Buscar producto..." onkeyup="filtrarProductos()">
    </div>
    <div class="row" id="contenedorProductos">
        <?php //Las lineas siguientes sirven para generar las cartas que contengan los productos para mostrarlos con los datos obtenidos con la base de datos.
        while ($row = $productos->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="imagenes/<?= $row['imagen'] ?>" class="card-img-top" alt="<?= $row['nombre'] ?>" style="height: 200px; object-fit: contain;">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['nombre'] ?></h5>
                        <p class="card-text">Precio: $<?= number_format($row['precio'], 2) ?></p>
                        <p class="card-text">Stock: <?= $row['stock'] ?></p>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="number" name="cantidad" class="form-control mb-2" placeholder="Cantidad" min="1" max="<?= $row['stock'] ?>" required>
                            <button type="submit" name="agregar" class="btn btn-success w-100">
                                <i class="bi bi-cart-plus"></i> Agregar al Carrito
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <h2 class="mt-5 mb-3 bg-dark text-white">Carrito de Compras</h2>
    <?php //Desde aqui se va generando la tabla que sera el carrito de compras
     if (!empty($_SESSION['carrito'])): ?>
        <table class="table table-bordered" id="carrito">
            <thead class="table-dark">
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php //Desde esta linea se genera el carrito de compras, ademas se agrega el boton de eliminar el producto y el cambio del stock.
                foreach ($_SESSION['carrito'] as $item): ?>
                    <tr data-precio="<?= $item['precio'] ?>">
                        <td><?= $item['nombre'] ?></td>
                        <td>$<?= number_format($item['precio'], 2) ?></td>
                        <td>
                        <input type="number" value="<?= $item['cantidad'] ?>" class="form-control cantidad" min="1" max="<?= $item['stock'] ?>" onchange="actualizarTotal()">
                        </td>
                        <td class="subtotal">$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                        <td>
                            <a href="?quitar=<?= $item['id'] ?>" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4>Total: $<span id="total">0.00</span></h4>
        <a href="?vaciar=1" class="btn btn-warning mt-3"><i class="bi bi-x-circle"></i> Vaciar Carrito</a>
        <script>window.onload = actualizarTotal;</script>
    <?php else: ?>
        <p>No hay productos en el carrito.</p>
    <?php endif; ?>

</body>
</html>