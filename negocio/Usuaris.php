<?php
require_once __DIR__ . '/../dades/Database.php';

class Usuaris {

    private $conn; // <- conexiÃ³n real a la base de datos
     private $db;

    public function __construct() {
      $this->db = new Database();          // <-- guardem Database
        $this->conn = $this->db->getConnection();// tenemos la conexiÃ³n mysqli
    }

    // =======================
    //     REGISTRO
    // =======================
    public function addUser($data) {

        // Hash de contraseÃ±a
        $data["contrasena"] = password_hash($data["contrasena"], PASSWORD_DEFAULT);

        // Campos opcionales
        if (!isset($data["pronombres"])) $data["pronombres"] = null;
        if (!isset($data["foto_perfil"])) $data["foto_perfil"] = null;
        if (!isset($data["biografia"])) $data["biografia"] = null;

        // Delegamos a Database, si lo tienes allÃ­
        return (new Database())->addUser($data);
    }

    // =======================
    //        LOGIN
    // =======================
    public function login($email, $password) {

        $usuario = $this->getUserByEmail($email);

        if (!$usuario) {
            return false; // email no existe
        }

        if (!password_verify($password, $usuario["contrasena"])) {
            return false; // contraseÃ±a incorrecta
        }

        return $usuario;
    }

    // =======================
    //   OBTENER POR EMAIL
    // =======================
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // =======================
    //   OBTENER POR USERNAME
    // =======================
    public function getUserByUsername($username) {
        $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE nombre_usuario = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // =======================
    //     OBTENER POR ID
    // =======================
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE id_usuario = ?");
        if (!$stmt) { die("Error en prepare(): " . $this->conn->error); }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // =======================
    //      ELIMINAR USER
    // =======================
    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM USUARIO WHERE id_usuario = ?");
        if (!$stmt) { die("Error en prepare(): " . $this->conn->error); }

        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Crear post
    public function crearPost($id_usuario, $contenido) {
        return $this->db->addPost(['id_usuario' => $id_usuario, 'contenido' => $contenido]);
    }
    public function crearPostConEmocion($id_usuario, $contenido, $id_emocion) {
        return $this->db->addPostWithEmotion([
            'id_usuario' => $id_usuario,
            'contenido' => $contenido,
            'id_emocion' => $id_emocion
        ]);
    }

    /**
     * Obtener nombre de emoción por ID
     */
    public function obtenerNombreEmocion($id_emocion) {
        return $this->db->getNombreEmocion($id_emocion);
    }

        /**
     * Obtener todos los datos de un usuario
     */
    public function obtenerDatosUsuario($id_usuario) {
        return $this->db->getUserById($id_usuario);
    }
    
    /**
     * Contar seguidores de un usuario
     */
    public function contarSeguidores($id_usuario) {
        return $this->db->contarSeguidores($id_usuario);
    }
    
    /**
     * Contar usuarios que sigue
     */
    public function contarSeguidos($id_usuario) {
        return $this->db->contarSeguidos($id_usuario);
    }
    
    /**
     * Contar posts de un usuario
     */
    public function contarPostsUsuario($id_usuario) {
        return $this->db->contarPostsUsuario($id_usuario);
    }
    
    // ==================== MÃ‰TODOS DE POSTS ====================
    
    /**
     * Obtener posts de usuarios seguidos
     */
    public function getPostsSeguidos($id_usuario) {
        return $this->db->obtenerPostsSeguidos($id_usuario);
    }
    // ========================================
    // MÃ‰TODOS PARA ACTUALIZACIÃ“N DE PERFIL
    // ========================================
    
    /**
     * Actualizar datos del usuario
     */
    public function updateUser($id, $data) {
        return $this->db->updateUserProfile($id, $data);
    }
     /**
     * Verificar si un email ya existe (excluyendo al usuario actual)
     */
    public function emailExists($email, $excludeUserId = null) {
        return $this->db->emailExists($email, $excludeUserId);
    }
    
    /**
     * Verificar si un nombre de usuario ya existe (excluyendo al usuario actual)
     */
    public function usernameExists($username, $excludeUserId = null) {
        return $this->db->usernameExists($username, $excludeUserId);
    }

        
    public function getUsuarioPorAlias($alias) {
    // He añadido "nombre" a la consulta para que no salga vacío en el visor
    $stmt = $this->conn->prepare("SELECT id_usuario, nombre_usuario, nombre, foto_perfil as avatar, pronombres FROM USUARIO WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $alias);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
    }


   public function getPostsUsuario($id_usuario) {
   $stmt = $this->conn->prepare("SELECT * FROM POST WHERE id_usuario = ? ORDER BY fecha DESC");
   $stmt->bind_param("i", $id_usuario);
   $stmt->execute();
   return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
// Guardar token y fecha de expiraciÃ³n
public function saveResetToken($email, $token, $expiration) {
    $stmt = $this->conn->prepare("UPDATE USUARIO SET reset_token=?, reset_expiration=? WHERE email=?");
    $stmt->bind_param("sss", $token, $expiration, $email);
    return $stmt->execute();
}


// Obtener usuario por token vÃ¡lido
public function getUserByToken($token) {
    $stmt = $this->conn->prepare("SELECT * FROM USUARIO WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


// Actualizar contraseña y limpiar token
public function updatePassword($id, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $this->conn->prepare("UPDATE USUARIO SET contrasena=?, reset_token=NULL, reset_expiration=NULL WHERE id_usuario=?");
    $stmt->bind_param("si", $hash, $id);
    return $stmt->execute();
    }

    public function seguirUsuario($id_seguidor, $id_seguido) {
        return $this->db->seguirUsuario($id_seguidor, $id_seguido);
    }

    public function dejarDeSeguir($id_seguidor, $id_seguido) {
        return $this->db->dejarDeSeguir($id_seguidor, $id_seguido);
    }

    public function esSeguidor($id_seguidor, $id_seguido) {
        return $this->db->esSeguidor($id_seguidor, $id_seguido);
    }

    public function darLike($id_usuario, $id_post) {
    return $this->db->toggleLike($id_usuario, $id_post);
}

public function guardarPost($id_usuario, $id_post) {
    return $this->db->toggleGuardar($id_usuario, $id_post);
}

public function obtenerLikes($id_usuario) {
    return $this->db->getLikesUsuario($id_usuario);
}

public function obtenerGuardados($id_usuario) {
    return $this->db->getPostsGuardados($id_usuario);
}

public function contarLikes($id_post) {
    return $this->db->getLikesCount($id_post);
}


// Dentro de la clase Usuaris en Usuaris.php
public function toggleLike($id_usuario, $id_post) {
    // 1. Comprobar si ya existe en GUSTA
    $stmt = $this->conn->prepare("SELECT id_usuario FROM GUSTA WHERE id_usuario = ? AND id_post = ?");
    $stmt->bind_param("ii", $id_usuario, $id_post);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        // 2. Si existe, lo borramos (quitar like)
        $stmt_del = $this->conn->prepare("DELETE FROM GUSTA WHERE id_usuario = ? AND id_post = ?");
        $stmt_del->bind_param("ii", $id_usuario, $id_post);
        return $stmt_del->execute();
    } else {
        // 3. Si no existe, lo insertamos (dar like)
        $stmt_ins = $this->conn->prepare("INSERT INTO GUSTA (id_usuario, id_post) VALUES (?, ?)");
        $stmt_ins->bind_param("ii", $id_usuario, $id_post);
        return $stmt_ins->execute();
    }
}

public function toggleGuardar($id_usuario, $id_post) {
    // 1. Comprobar si ya existe en GUARDA
    $stmt = $this->conn->prepare("SELECT id_usuario FROM GUARDA WHERE id_usuario = ? AND id_post = ?");
    $stmt->bind_param("ii", $id_usuario, $id_post);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        // 2. Si existe, lo borramos (quitar de guardados)
        $stmt_del = $this->conn->prepare("DELETE FROM GUARDA WHERE id_usuario = ? AND id_post = ?");
        $stmt_del->bind_param("ii", $id_usuario, $id_post);
        return $stmt_del->execute();
    } else {
        // 3. Si no existe, lo insertamos (guardar)
        $stmt_ins = $this->conn->prepare("INSERT INTO GUARDA (id_usuario, id_post) VALUES (?, ?)");
        $stmt_ins->bind_param("ii", $id_usuario, $id_post);
        return $stmt_ins->execute();
    }
}
// ==========================================
   //   ACTUALIZAR PASS POR EMAIL (Recuperación)
   // ==========================================
   public function updatePasswordByEmail($email, $newPassword) {
       // 1. Hashear la nueva contraseña
       $hash = password_hash($newPassword, PASSWORD_DEFAULT);
      
       // 2. Actualizar en base de datos
       $stmt = $this->conn->prepare("UPDATE USUARIO SET contrasena = ? WHERE email = ?");
       if (!$stmt) {
            // Debug si falla la consulta SQL
            die("Error prepare: " . $this->conn->error);
       }
       $stmt->bind_param("ss", $hash, $email);
      
       return $stmt->execute();
   }


   // Busca usuarios por nombre o nombre_usuario (alias)
public function buscarUsuarios($termino) {
   $termino = "%" . $termino . "%"; // Para buscar coincidencias parciales
   $stmt = $this->conn->prepare("SELECT id_usuario, nombre_usuario, nombre, apellidos, foto_perfil, pronombres
                                 FROM USUARIO
                                 WHERE nombre_usuario LIKE ? OR nombre LIKE ?
                                 LIMIT 20");
   $stmt->bind_param("ss", $termino, $termino);
   $stmt->execute();
   return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
// ==========================================
//   MÉTODOS PARA RETO DIARIO
// ==========================================

/**
 * Verifica si el usuario ya completó el reto diario hoy
 */
public function verificarRetoCompletadoHoy($id_usuario) {
    $hoy = date('Y-m-d');
    $stmt = $this->conn->prepare("SELECT COUNT(*) as total 
                                   FROM POST 
                                   WHERE id_usuario = ? 
                                   AND tipo_post = 'reto' 
                                   AND DATE(fecha) = ?");
    $stmt->bind_param("is", $id_usuario, $hoy);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    return $resultado['total'] > 0;
}

/**
 * Crea un post del reto diario y otorga recompensas
 */
public function crearPostRetoDiario($id_usuario, $contenido, $id_emocion = 1) {
    try {
        // Iniciar transacción
        $this->conn->begin_transaction();
        
        // 1. Crear el post con tipo_post = 'reto'
        $stmt = $this->conn->prepare("INSERT INTO POST (contenido, tipo_post, fecha, id_usuario, id_emocion) 
                                      VALUES (?, 'reto', NOW(), ?, ?)");
        $stmt->bind_param("sii", $contenido, $id_usuario, $id_emocion);
        $stmt->execute();
        
        $id_post = $this->conn->insert_id;
        
        // 2. Registrar en RETO_DIARIO
        $stmt = $this->conn->prepare("INSERT INTO RETO_DIARIO (id_post, id_reto) VALUES (?, 1)");
        $stmt->bind_param("i", $id_post);
        $stmt->execute();
        
        // 3. Incrementar racha
        $stmt = $this->conn->prepare("UPDATE USUARIO SET racha = racha + 1 WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        
        // Actualizar sesión si existe
        if (isset($_SESSION['usuario'])) {
            $_SESSION['usuario']['racha'] = ($_SESSION['usuario']['racha'] ?? 0) + 1;
        }
        
        // Confirmar transacción
        $this->conn->commit();
        return true;
        
    } catch (Exception $e) {
        // Revertir en caso de error
        $this->conn->rollback();
        error_log("Error en crearPostRetoDiario: " . $e->getMessage());
        return false;
    }
}


// ==========================================
//   MÉTODO PARA ESPECTRO EMOCIONAL
// ==========================================

/**
 * Calcula el porcentaje de cada emoción en los posts del usuario
 */
    public function calcularEspectroEmocional($id_usuario) {
        $sql = "SELECT id_emocion, COUNT(*) as total 
                FROM POST 
                WHERE id_usuario = ? 
                GROUP BY id_emocion";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $emociones = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        $totalPosts = 0;
        
        while ($fila = $resultado->fetch_assoc()) {
            $emociones[$fila['id_emocion']] = (int)$fila['total'];
            $totalPosts += (int)$fila['total'];
        }
        
        if ($totalPosts === 0) {
            return [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0];
        }
        
        $porcentajes = [];
        foreach ($emociones as $id => $count) {
            $porcentajes[$id] = round(($count / $totalPosts) * 100);
        }
        
        return $porcentajes;
    }

    public function insertarComentario($id_usuario, $id_post, $texto) {
        return $this->db->addComentario($id_usuario, $id_post, $texto);
    }

    public function obtenerComentariosPost($id_post) {
        return $this->db->getComentariosPost($id_post);
    }
    // Método para saber si el usuario le ha dado Like
public function yaDioLike($pid, $uid) {
    // Suponiendo que en el constructor de Usuaris haces: $this->db = new Database();
    return $this->db->existeLike($pid, $uid);
}

// Método para saber si el usuario lo tiene guardado
public function yaEstaGuardado($pid, $uid) {
    return $this->db->existeGuardado($pid, $uid);
}
public function tenerPostDiscover($limite = 5) {
    // Llamamos al método que acabamos de crear en la clase Database
    // Asumiendo que $this->db es tu instancia de conexión
    return $this->db->ejecutarConsultaDiscover($limite);
}

public function obtenerNotificacionesDinamicas($id_usuario) {
    // Usamos la conexión que ya tiene la clase
    $sql = "SELECT
                U.nombre_usuario,
                U.foto_perfil,
                'like' AS tipo,
                P.fecha, -- Usamos la fecha del post ya que GUSTA no tiene columna fecha
                P.contenido AS post_snippet
            FROM GUSTA G
            JOIN USUARIO U ON G.id_usuario = U.id_usuario
            JOIN POST P ON G.id_post = P.id_post
            WHERE P.id_usuario = ? AND G.id_usuario != ?
           
            UNION ALL
           
            SELECT
                U.nombre_usuario,
                U.foto_perfil,
                'comentario' AS tipo,
                C.fecha,
                C.contenido AS post_snippet
            FROM COMENTARIO C
            JOIN USUARIO U ON C.id_usuario = U.id_usuario
            JOIN POST P ON C.id_post = P.id_post
            WHERE P.id_usuario = ? AND C.id_usuario != ?
           
            ORDER BY fecha DESC LIMIT 30";


    $stmt = $this->conn->prepare($sql);
   
    if (!$stmt) {
        die("Error en la preparación SQL: " . $this->conn->error);
    }


    $stmt->bind_param("iiii", $id_usuario, $id_usuario, $id_usuario, $id_usuario);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
/**
 * Obtiene la lista de usuarios a los que sigue el usuario indicado
 */
public function getSeguidos($id_usuario) {
    $sql = "SELECT U.id_usuario, U.nombre_usuario, U.nombre, U.foto_perfil
            FROM USUARIO U
            JOIN SIGUE S ON U.id_usuario = S.id_seguido
            WHERE S.id_seguidor = ?
            LIMIT 50";
           
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) return [];
   
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


}
