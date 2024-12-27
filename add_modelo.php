<?php
require 'backend/db/db.php';

// Obtener todos los modelos
$modelosQuery = "SELECT * FROM Modelos";
$modelosResult = mysqli_query($enlace, $modelosQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelos - SimaSolution</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="css/modelo.css">
</head>
    <script>
        function confirmDelete() {
            return confirm('¿Estás seguro de que deseas eliminar este modelo?');
        }

        function hideNotification() {
            const notification = document.querySelector('.notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 6000); // 6 segundos
            }
        }

        document.addEventListener('DOMContentLoaded', hideNotification);
    </script>
</head>
<body>
    <header>
        <!-- Enlaces a la página de inicio y al historial -->
        <a href="index.php" class="home-icon"><i class="fas fa-home"></i></a>
        <nav>
           
        </nav>
    </header>
    <main>
        <h1>Numeros de parte</h1>
        <?php if (isset($_GET['message'])): ?>
            <div class="notification <?php echo strpos($_GET['message'], 'exitosamente') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        <form action="modelo_backend.php" method="post" class="center-form">
            <input type="hidden" name="action" value="create">
            <label for="nifco_numero">NIFCO Número:</label>
            <input type="text" id="nifco_numero" name="nifco_numero" required>
            
            <label for="numero_parte">Número de Parte:</label>
            <input type="text" id="numero_parte" name="numero_parte" required>
            
            <button type="submit"><i class="fas fa-plus"></i> Agregar Numero</button>
        </form>
        <h2>Lista de Numeros de parte</h2>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table id="tabla-modelos" class="table" data-search="true" data-pagination="true" data-striped="true">
                    <thead>
                        <tr>
                            <th data-field="nifco_numero" class="custom-th">NIFCO Número</th>
                            <th data-field="numero_parte" class="custom-th">Número de Parte</th>
                            <th data-field="acciones" class="custom-th">Nifco & Numero de parte</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($modelosResult && mysqli_num_rows($modelosResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($modelosResult)): ?>
                                <tr>
                                    <td class="custom-td"><?php echo htmlspecialchars($row['nifco_numero']); ?></td>
                                    <td class="custom-td"><?php echo htmlspecialchars($row['numero_parte']); ?></td>
                                    <td class="custom-td">
                                        <form action="modelo_backend.php" method="post" style="display:inline;" onsubmit="return confirmDelete();">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Eliminar</button>
                                        </form>
                                        <form action="modelo_backend.php" method="post" style="display:inline;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <input type="text" name="nifco_numero" value="<?php echo htmlspecialchars($row['nifco_numero']); ?>" required>
                                            <input type="text" name="numero_parte" value="<?php echo htmlspecialchars($row['numero_parte']); ?>" required>
                                            <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Actualizar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No hay modelos registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-2"></div>
        </div>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/bootstrap-table@1.14.2/dist/bootstrap-table.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabla-modelos').bootstrapTable();
        });
    </script>
</body>
</html>