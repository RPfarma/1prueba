<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f6f9ff;
        }
        .centered-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>
    <script>
        function togglePassword(id) { // Esta funcion sirve para poder ver la contraseña que se puso en el login.
            const field = document.getElementById(id);
            const icon = document.getElementById(id + '-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</head>
<body>
    <div class="container centered-container">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h1>Bienvenido a RpFARMA</h1>
                <p>Por favor, inicia sesión para continuar.</p>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h4><i class="fas fa-sign-in-alt"></i> Inicio de Sesión</h4>
                </div>
                <div class="card-body">
                    <?php
                    session_start();
                    if (isset($_POST['login'])) {
                        $username = $_POST['username'];
                        $password = $_POST['password'];

                        
                        $conn = new mysqli("localhost", "root", "", "rpfarmapractica");// En esta linea se hace la conexion con la base de datos, se tiende a hacerlo en un archivo a parte pero a mi me gusta mas de esta forma en el mismo archivo.
                        if ($conn->connect_error) {
                            die("Conexión fallida: " . $conn->connect_error);
                        }

                        
                        $sql = "SELECT * FROM usuarios WHERE username = '$username'";//Esta linea sirve para rescatar el dato del usuario en la base de datos 
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $user = $result->fetch_assoc();
                            if ($password === $user['password']) {// En esta linea se auntentifica si la contraseña que se coloco coinicide con la del usuario en la base de datos
                    
                                $_SESSION['username'] = $username;
                                $_SESSION['role'] = $user['role'];

                                if ($user['role'] === 'administrador') {// Esta linea es la que separa el rol de administrador con el de cliente y los redirige a su respectiva vista.
                                    header("Location: MenuAdministrador.php");
                                } else {
                                    header("Location: MenuCliente.php");
                                }
                                exit();
                            } else {
                                echo "<div class='alert alert-danger mt-3'>Contraseña incorrecta. Intenta de nuevo.</div>";
                            }
                        } else {
                            echo "<div class='alert alert-danger mt-3'>Usuario no encontrado.</div>";
                        }

                        $conn->close();
                    }
                    ?>
                    <form action="Login.php" method="post">
                        <div class="mb-3">
                            <label class="form-label">Usuario:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña:</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                    <i id="password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="login" class="btn btn-success">Iniciar Sesión</button>
                        </div>
                    </form>
                    <div class="mt-3 text-center">
                    </div>
                </div>
            </div>
        </div>
    </div>