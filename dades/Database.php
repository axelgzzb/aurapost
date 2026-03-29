<?php
require_once __DIR__ . '/config.php';

class Database {

    private $conn;

    public function __construct() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Error de conexión: " . $this->conn->connect_error);
        }

        // Importante para caracteres especiales
        $this->conn->set_charset("utf8mb4");
    }

    public function getConnection() {
        return $this->conn;
    }

    // Insertar usuario y devolver ID creado
    public function addUser($data) {

        $stmt = $this->conn->prepare(
            "INSERT INTO USUARIO 
            (nombre_usuario, nombre, apellidos, pronombres, email, contrasena, foto_perfil, biografia, racha)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)"
        );

        $stmt->bind_param(
            "ssssssss",
            $data["nombre_usuario"],
            $data["nombre"],
            $data["apellidos"],
            $data["pronombres"],
            $data["email"],
            $data["contrasena"],
            $data["foto_perfil"],
            $data["biografia"]
        );

        if ($stmt->execute()) {
            return $this->conn->insert_id; // DEVUELVE EL ID REAL
        }

        return false; // Error
    }

    // Obtener por email
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getUserById($id) {
      $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    // Obtener por nombre de usuario
public function getUserByUsername($username) {
    $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


        public function deleteUser($id) {

        $sql = "DELETE FROM USUARIO WHERE id_usuario = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) { die("Error en prepare(): " . $this->conn->error); }

            $stmt->bind_param("i", $id);

            return $stmt->execute();
    }

    // --- SISTEMA DE SEGUIMIENTO (SIGUE) ---
    public function seguirUsuario($id_seguidor, $id_seguido) {
        // Evitar seguirse a uno mismo
        if ($id_seguidor == $id_seguido) return false;
        
        $stmt = $this->conn->prepare("INSERT IGNORE INTO SIGUE (id_seguidor, id_seguido) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_seguidor, $id_seguido);
        return $stmt->execute();
    }

    public function dejarDeSeguir($id_seguidor, $id_seguido) {
        $stmt = $this->conn->prepare("DELETE FROM SIGUE WHERE id_seguidor = ? AND id_seguido = ?");
        $stmt->bind_param("ii", $id_seguidor, $id_seguido);
        return $stmt->execute();
    }


     // =======================
    //        SIGUE
    // =======================
    public function esSeguidor($id_seguidor, $id_seguido) {
        $stmt = $this->conn->prepare("SELECT 1 FROM SIGUE WHERE id_seguidor = ? AND id_seguido = ?");
        $stmt->bind_param("ii", $id_seguidor, $id_seguido);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function contarSeguidores($id_usuario) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM SIGUE
            WHERE id_seguido = ?
        ");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    public function contarSeguidos($id_usuario) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total
            FROM SIGUE
            WHERE id_seguidor = ?
        ");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    // =======================
    //        POSTS
    // =======================
    public function addPost($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO POST (id_usuario, contenido, fecha)
            VALUES (?, ?, NOW())
        ");
        $stmt->bind_param("is", $data["id_usuario"], $data["contenido"]);
        return $stmt->execute();
    }

public function contarPostsUsuario($id_usuario) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM POST WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
#-----
public function addPostWithEmotion($data) {
        $stmt = $this->conn->prepare("INSERT INTO POST (id_usuario, contenido, fecha, id_emocion) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("isi", $data["id_usuario"], $data["contenido"], $data["id_emocion"]);
        return $stmt->execute();
    }

/**
 * Obtener posts de usuarios seguidos CON información de emoción
 */
public function obtenerPostsSeguidos($id_usuario) {
    // Añadimos E.color para que PHP pueda pintar el fondo
    $stmt = $this->conn->prepare("
        SELECT DISTINCT
            P.*, 
            U.nombre_usuario, 
            U.foto_perfil, 
            E.nombre as emocion_nombre, 
            E.color as emocion_color 
        FROM POST P
        JOIN USUARIO U ON P.id_usuario = U.id_usuario
        LEFT JOIN EMOCION E ON P.id_emocion = E.id_emocion
        LEFT JOIN SIGUE S ON P.id_usuario = S.id_seguido
        WHERE S.id_seguidor = ? OR P.id_usuario = ?
        ORDER BY P.fecha DESC
    ");
    
    $stmt->bind_param("ii", $id_usuario, $id_usuario);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Obtener nombre de emoción por ID
 */
public function getNombreEmocion($id_emocion) {
    $stmt = $this->conn->prepare("SELECT nombre FROM EMOCION WHERE id_emocion = ?");
    $stmt->bind_param("i", $id_emocion);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['nombre'] : null;
}
        public function updateUserProfile($id, $data) {
    // Construir la consulta dinámicamente según los campos que se quieran actualizar
    $updates = [];
    $types = "";
    $values = [];
    
    if (isset($data['nombre'])) {
        $updates[] = "nombre = ?";
        $types .= "s";
        $values[] = $data['nombre'];
    }
    
    if (isset($data['apellidos'])) {
        $updates[] = "apellidos = ?";
        $types .= "s";
        $values[] = $data['apellidos'];
    }
    
    if (isset($data['email'])) {
        $updates[] = "email = ?";
        $types .= "s";
        $values[] = $data['email'];
    }
    
    if (isset($data['nombre_usuario'])) {
        $updates[] = "nombre_usuario = ?";
        $types .= "s";
        $values[] = $data['nombre_usuario'];
    }
    
    if (empty($updates)) {
        return false; // No hay nada que actualizar
    }
    
    // Añadir el ID al final
    $types .= "i";
    $values[] = $id;
    
    $sql = "UPDATE USUARIO SET " . implode(", ", $updates) . " WHERE id_usuario = ?";
    $stmt = $this->conn->prepare($sql);
    
    if (!$stmt) {
        die("Error en prepare(): " . $this->conn->error);
    }
    
    // Bind dinámico
    $stmt->bind_param($types, ...$values);
    
    return $stmt->execute();
}

    // Verificar si un nombre de usuario ya existe (excluyendo al usuario actual)
    public function usernameExists($username, $excludeUserId = null) {
        if ($excludeUserId) {
            $stmt = $this->conn->prepare("SELECT id_usuario FROM USUARIO WHERE nombre_usuario = ? AND id_usuario != ?");
            $stmt->bind_param("si", $username, $excludeUserId);
        } else {
            $stmt = $this->conn->prepare("SELECT id_usuario FROM USUARIO WHERE nombre_usuario = ?");
            $stmt->bind_param("s", $username);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

// --- LIKES (Tabla GUSTA) ---
public function toggleLike($id_usuario, $id_post) {
    $stmt = $this->conn->prepare("SELECT 1 FROM GUSTA WHERE id_usuario = ? AND id_post = ?");
    $stmt->bind_param("ii", $id_usuario, $id_post);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt = $this->conn->prepare("DELETE FROM GUSTA WHERE id_usuario = ? AND id_post = ?");
    } else {
        $stmt = $this->conn->prepare("INSERT INTO GUSTA (id_usuario, id_post) VALUES (?, ?)");
    }
    $stmt->bind_param("ii", $id_usuario, $id_post);
    return $stmt->execute();
}

public function getLikesUsuario($id_usuario) {
    $stmt = $this->conn->prepare("
        SELECT P.*, U.nombre_usuario, U.foto_perfil 
        FROM POST P 
        JOIN GUSTA G ON P.id_post = G.id_post 
        JOIN USUARIO U ON P.id_usuario = U.id_usuario
        WHERE G.id_usuario = ? 
        ORDER BY P.fecha DESC
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

public function getLikesCount($id_post) {
    $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM GUSTA WHERE id_post = ?");
    $stmt->bind_param("i", $id_post);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}
// --- GUARDADOS (Tabla GUARDA) ---
public function toggleGuardar($id_usuario, $id_post) {
    $stmt = $this->conn->prepare("SELECT 1 FROM GUARDA WHERE id_usuario = ? AND id_post = ?");
    $stmt->bind_param("ii", $id_usuario, $id_post);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt = $this->conn->prepare("DELETE FROM GUARDA WHERE id_usuario = ? AND id_post = ?");
    } else {
        $stmt = $this->conn->prepare("INSERT INTO GUARDA (id_usuario, id_post) VALUES (?, ?)");
    }
    $stmt->bind_param("ii", $id_usuario, $id_post);
    return $stmt->execute();
}

public function getPostsGuardados($id_usuario) {
    $stmt = $this->conn->prepare("
        SELECT P.*, U.nombre_usuario, U.foto_perfil 
        FROM POST P 
        JOIN GUARDA G ON P.id_post = G.id_post 
        JOIN USUARIO U ON P.id_usuario = U.id_usuario
        WHERE G.id_usuario = ? 
        ORDER BY P.fecha DESC
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
// --- COMENTARIOS (Tabla COMENTARIO) ---
public function addComentario($id_usuario, $id_post, $contenido) {
    $stmt = $this->conn->prepare("INSERT INTO COMENTARIO (id_usuario, id_post, contenido, fecha) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $id_usuario, $id_post, $contenido);
    return $stmt->execute();
}

public function getComentariosPost($id_post) {
    $stmt = $this->conn->prepare("
        SELECT C.*, U.nombre_usuario, U.foto_perfil 
        FROM COMENTARIO C 
        JOIN USUARIO U ON C.id_usuario = U.id_usuario 
        WHERE C.id_post = ? 
        ORDER BY C.fecha ASC
    ");
    $stmt->bind_param("i", $id_post);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    // Comprueba si existe el registro en la tabla GUSTA
public function existeLike($pid, $uid) {
    $sql = "SELECT 1 FROM GUSTA WHERE id_usuario = ? AND id_post = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ii", $uid, $pid);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

// Comprueba si existe el registro en la tabla GUARDA
public function existeGuardado($pid, $uid) {
    $sql = "SELECT 1 FROM GUARDA WHERE id_usuario = ? AND id_post = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ii", $uid, $pid);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

public function ejecutarConsultaDiscover($limite) {
    $sql = "SELECT P.id_post, P.contenido, U.nombre_usuario, U.foto_perfil, E.nombre as emocion_nombre 
            FROM POST P 
            JOIN USUARIO U ON P.id_usuario = U.id_usuario 
            LEFT JOIN EMOCION E ON P.id_emocion = E.id_emocion
            ORDER BY RAND() 
            LIMIT ?";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $limite);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
}
?>
