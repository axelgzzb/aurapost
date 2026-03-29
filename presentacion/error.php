<?php
session_start();

/*
    SISTEMA DE HISTORIAL DE ERRORES (NO TOQUES LAS DEMÁS PÁGINAS)
    --------------------------------------------------------------

    Todas tus páginas envían errores así:
        $_SESSION["error"] = "mensaje";
        o
        $_SESSION["errores"] = [lista de errores];

    Con este archivo, SE ACUMULAN AUTOMÁTICAMENTE en:
        $_SESSION["errores_totales"]

    Así podrás mostrar toda la lista de errores que ha cometido el usuario
    durante la sesión.
*/

// Crear historial si no existe
if (!isset($_SESSION["errores_totales"])) {
    $_SESSION["errores_totales"] = [];
}

// Si viene un error único
if (isset($_SESSION["error"])) {
    $_SESSION["errores_totales"][] = [
        "mensaje" => $_SESSION["error"],
        "pagina"  => $_SESSION["volver_a"] ?? "Página desconocida"
    ];
}

// Si vienen múltiples errores
if (isset($_SESSION["errores"]) && is_array($_SESSION["errores"])) {
    foreach ($_SESSION["errores"] as $err) {
        $_SESSION["errores_totales"][] = [
            "mensaje" => $err,
            "pagina"  => $_SESSION["volver_a"] ?? "Página desconocida"
        ];
    }
}

// Guardamos el historial para mostrarlo
$errores_totales = $_SESSION["errores_totales"];

// Página a la que volver - CORREGIDO
$volver_a = $_SESSION["volver_a"] ?? 'inici.php';

// Si no hay sesión de usuario y no se especificó volver_a, volver a inicio
if (!isset($_SESSION["usuario"]) && !isset($_SESSION["volver_a"])) {
    $volver_a = 'inici.php';
}

// Quitamos errores recientes, pero NO el historial
unset($_SESSION["errores"], $_SESSION["error"], $_SESSION["volver_a"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - AuraPost</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../css/accessibility.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <link href="../css/mobile.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; background: #f8f8f8; }
        .error-box { background: #ffe5e5; border-left: 4px solid #cc0000; padding: 15px 20px; border-radius: 6px; max-width: 600px; margin: 20px auto; }
        .error-box ul { margin: 0; padding-left: 20px; }
        a.btn-back { display: inline-block; margin-top: 15px; text-decoration: none; background: #6c5ce7; color: white; padding: 10px 16px; border-radius: 6px; transition: all 0.2s ease-in-out; }
        a.btn-back:hover { background: #5941c6; }
        h2 { text-align: center; color: #cc0000; }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #e3eafc, #f4e8ff, #e4fff4);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .error-container {
            max-width: 600px;
            width: 100%;
        }

        .error-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(123, 78, 255, 0.15);
            text-align: center;
        }

        .error-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: wiggle 3s ease infinite;
        }

        .error-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        @keyframes wiggle {
            0%, 100% { 
                transform: rotate(-5deg); 
            }
            25% { 
                transform: rotate(5deg); 
            }
            50% { 
                transform: rotate(-3deg); 
            }
            75% { 
                transform: rotate(3deg); 
            }
        }

        h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3436;
            margin-bottom: 1rem;
        }

        .error-message {
            background: rgba(255, 107, 107, 0.1);
            border-left: 4px solid #ff6b6b;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }

        .error-message ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .error-message li {
            padding: 0.75rem 0;
            color: #2d3436;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .error-message li:not(:last-child) {
            border-bottom: 1px solid rgba(255, 107, 107, 0.2);
        }

        .error-message li::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #ff6b6b;
            font-size: 1.1rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, #7b4eff, #9d7eff);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
            cursor: pointer;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(123, 78, 255, 0.3);
        }

        .btn-back i {
            font-size: 1.2rem;
        }

        .bubble {
            position: fixed;
            bottom: -50px;
            width: 15px;
            height: 15px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            animation: rise 15s linear infinite;
            box-shadow: inset 0 0 8px rgba(255, 255, 255, 0.8);
            pointer-events: none;
        }

        .bubble:nth-child(1) { left: 10%; animation-delay: 0s; }
        .bubble:nth-child(2) { left: 30%; animation-delay: 3s; width: 20px; height: 20px; }
        .bubble:nth-child(3) { left: 50%; animation-delay: 6s; }
        .bubble:nth-child(4) { left: 70%; animation-delay: 9s; width: 18px; height: 18px; }
        .bubble:nth-child(5) { left: 85%; animation-delay: 12s; }

        @keyframes rise {
            0% { bottom: -50px; opacity: 0; transform: translateX(0); }
            10% { opacity: 0.8; }
            90% { opacity: 0.6; }
            100% { bottom: 120%; opacity: 0; transform: translateX(20px); }
        }

        @media (max-width: 576px) {
            .error-card { padding: 1.5rem; }
            h2 { font-size: 1.4rem; }
            .error-icon { width: 80px; height: 80px; }
        }
    </style>
</head>
<body>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <img src="../src/fish-error.png" alt="Error icon">
            </div>
            
            <h2>Historial de errores</h2>
            
            <div class="error-message">
                <ul>
                    <?php
                    if (!empty($errores_totales)) {
                        foreach ($errores_totales as $e) {
                            echo "<li><strong>" .
                                 htmlspecialchars($e["mensaje"]) . "</strong></li>";
                        }
                    } else {
                        echo "<li>No hay errores registrados.</li>";
                    }
                    ?>
                </ul>
            </div>
            
            <a class="btn-back" href="<?php echo htmlspecialchars($volver_a); ?>">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>
</body>
</html>