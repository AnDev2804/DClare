<?php

/**
 * Carga configuración privada si existe.
 *
 * En HostGator:
 *   /home4/hgrqrrte/clara_private/config.php
 *
 * En local puedes seguir usando variables de entorno o los valores
 * de desarrollo por defecto del constructor.
 */
function clara_private_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = [];

    $localConfig = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
    $privateConfigPath = dirname(__DIR__, 2)
        . DIRECTORY_SEPARATOR . 'clara_private'
        . DIRECTORY_SEPARATOR . 'config.php';

    if (is_file($localConfig)) {
        $loaded = require $localConfig;
        if (is_array($loaded)) {
            $config = $loaded;
        }
    } elseif (is_file($privateConfigPath)) {
        $loaded = require $privateConfigPath;
        if (is_array($loaded)) {
            $config = $loaded;
        }
    }

    return $config;
}

class conexion
{
	//------------------------------------------------------------------------
	//ATRIBUTOS
	private $servidor;
	private $usuario;
	private $clave;
	private $bdd;
	private $conex=null;
	//------------------------------------------------------------------------
	//GETTER'S & SETTER'S
	//GETTER'S
	public function getServidor(){return $this->servidor;}
	public function getUsuario(){return $this->usuario;}
	public function getClave(){return $this->clave;}
	public function getBdd(){return $this->bdd;}
	public function getConex(){return $this->conex;}
	//SETTER'S
	public function setServidor($valor){$this->servidor=$valor;}
	public function setUsuario($valor){$this->usuario=$valor;}
	public function setClave($valor){$this->clave=$valor;}
	public function setBdd($valor){$this->bdd=$valor;}
	public function setConex($valor){$this->conex=$valor;}
	//------------------------------------------------------------------------
	//CONSTRUCTOR
	public function __construct($Servidor=null,$Usuario=null,$Clave=null,$Bdd=null)
	{
	 $privateConfig = clara_private_config();

	 $envHost = getenv("DB_HOST");
	 $envUser = getenv("DB_USER");
	 $envPassword = getenv("DB_PASSWORD");
	 $envDatabase = getenv("DB_NAME");
	 $envPort = getenv("DB_PORT");

	 $defaultServer = $envHost !== false && $envHost !== ""
	     ? $envHost
	     : ($privateConfig["DB_HOST"] ?? "localhost");

	 $defaultUser = $envUser !== false && $envUser !== ""
	     ? $envUser
	     : ($privateConfig["DB_USER"] ?? "root");

	 $defaultPassword = $envPassword !== false
	     ? $envPassword
	     : ($privateConfig["DB_PASSWORD"] ?? "");

	 $defaultDatabase = $envDatabase !== false && $envDatabase !== ""
	     ? $envDatabase
	     : ($privateConfig["DB_NAME"] ?? "declaraconclara");

	 $defaultPort = intval(
	     ($envPort !== false && $envPort !== "")
	         ? $envPort
	         : ($privateConfig["DB_PORT"] ?? 3306)
	 );

	 if($Servidor!=null)$this->setServidor($Servidor); else $this->setServidor($defaultServer);
	 if($Usuario!=null)$this->setUsuario($Usuario); else $this->setUsuario($defaultUser);
	 if($Clave!=null)$this->setClave($Clave); else $this->setClave($defaultPassword === false ? "" : $defaultPassword);
	 if($Bdd!=null)$this->setBdd($Bdd); else $this->setBdd($defaultDatabase);
	 
	 $this->setConex(mysqli_connect(
	     $this->getServidor(),
	     $this->getUsuario(),
	     $this->getClave(),
	     $this->getBdd(),
	     $defaultPort
	 ));
	 
	 if (mysqli_connect_errno()) 
	    {$this->setConex(null);}
	 else
	 {
	    mysqli_set_charset($this->getConex(), "utf8mb4");
	    mysqli_query($this->getConex(), "SET time_zone = '+00:00'");
	 }
	}
	//------------------------------------------------------------------------
	//toString
	public function toString()
	{
		if($this->getConex()==null)
		  return "No Conexion <br>";
		else
		  return "Conexion <br>";
	}
	//------------------------------------------------------------------------
	//METODOS OPERATIVOS
	//------------------------------------------------------------------------
    public function register($name, $email, $pass)
    {
        $passwordHash = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO logdata (correo, clave, reg_state, num_rol_data) VALUES (?, ?, 0, 1)";
        $stmt = mysqli_prepare($this->getConex(), $sql);

        try {
            mysqli_stmt_bind_param($stmt, "ss", $email, $passwordHash);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                $sql2 = "INSERT INTO infoper (ID_Foranea, nya) VALUES (LAST_INSERT_ID(), ?)";
                $stmt2 = mysqli_prepare($this->getConex(), $sql2);
                mysqli_stmt_bind_param($stmt2, "s", $name);

                if (mysqli_stmt_execute($stmt2)) {
                    mysqli_stmt_close($stmt2);
                    return true;
                }

                mysqli_stmt_close($stmt2);
            }

            return false;
        } catch (Exception $e) {
            return false;
        } finally {
            mysqli_close($this->getConex());
        }
    }
    public function regRIF($email,$number,$tipo,$nombre)
    {
        $connection = $this->getConex();
        try 
        {
            mysqli_begin_transaction($connection);

            $sqlUser = "SELECT ID FROM logdata WHERE Correo = ? LIMIT 1 FOR UPDATE";
            $stmtUser = mysqli_prepare($connection, $sqlUser);
            mysqli_stmt_bind_param($stmtUser, "s", $email);
            mysqli_stmt_execute($stmtUser);
            $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtUser));
            mysqli_stmt_close($stmtUser);

            if (!$user) {
                mysqli_rollback($connection);
                return false;
            }

            $userId = intval($user["ID"]);
            $tipoInt = intval($tipo);
            $sqlInsert = "INSERT INTO rif (ID_Foranea, RIF, tipo_de_cliente, nombre_empresa) VALUES (?, ?, ?, ?)";
            $stmtInsert = mysqli_prepare($connection, $sqlInsert);
            mysqli_stmt_bind_param($stmtInsert, "isis", $userId, $number, $tipoInt, $nombre);
            if (!mysqli_stmt_execute($stmtInsert)) {
                throw new RuntimeException("No se pudo registrar el RIF.");
            }
            mysqli_stmt_close($stmtInsert);

            if (!$this->createDefaultTaxPreferencesForRif($number)) {
                throw new RuntimeException("No se pudieron crear las preferencias tributarias.");
            }

            $sqlUpdate = "UPDATE logdata SET reg_state = 1 WHERE ID = ?";
            $stmtUpdate = mysqli_prepare($connection, $sqlUpdate);
            mysqli_stmt_bind_param($stmtUpdate, "i", $userId);
            if (!mysqli_stmt_execute($stmtUpdate)) {
                throw new RuntimeException("No se pudo actualizar el estado de registro.");
            }
            mysqli_stmt_close($stmtUpdate);

            mysqli_commit($connection);
            return true;
        }
        catch(Throwable $e) 
        {
            mysqli_rollback($connection);
            return false;
        }
        finally
        {
            mysqli_close($connection);	
        }
    }
    public function validlogin($email, $pass)
    {
        $sql = "
            SELECT l.correo, l.clave, i.nya, l.reg_state, l.num_rol_data
            FROM logdata l
            INNER JOIN infoper i ON l.ID = i.ID_Foranea
            WHERE l.correo = ?
            LIMIT 1
        ";

        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$row) {
            mysqli_close($this->getConex());
            return null;
        }

        if (!password_verify($pass, $row["clave"])) {
            mysqli_close($this->getConex());
            return null;
        }

        mysqli_close($this->getConex());
        return $row;
    }
    
    public function verif($email)
    {
        $sql = "SELECT l.correo FROM logdata l WHERE l.correo = ?";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if (mysqli_num_rows($result) > 0) {
            return $result;
        } else {
            return NULL;
        }
    }
    
    public function cambiar($email, $pass)
    {
        $passwordHash = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "UPDATE logdata SET Clave = ? WHERE Correo = ?";

        $stmt = mysqli_prepare($this->getConex(), $sql);

        try {
            mysqli_stmt_bind_param($stmt, "ss", $passwordHash, $email);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $ok;
        } catch (Exception $e) {
            return false;
        } finally {
            mysqli_close($this->getConex());
        }
    }
    public function googleSignIn($email)
    {
        $sql="SELECT l.correo, i.nya, l.reg_state, l.num_rol_data
        FROM logdata l
        INNER JOIN infoper i ON l.ID = i.ID_Foranea
        WHERE l.correo = ?
        LIMIT 1";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($this->getConex());
        return $result;
    }
    
    public function getRif($email)
    {
        $sql="SELECT r.rif, r.tipo_de_cliente, r.nombre_empresa
        FROM rif r
        INNER JOIN logdata l ON l.ID = r.ID_Foranea
        WHERE l.correo = ?";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($this->getConex());
        return $result;
    }
    
    public function getTable($digit)
    {
        $digit = intval($digit);
        if ($digit < 0 || $digit > 9) {
            return array();
        }

        $sql = "
        SELECT * FROM `ANTI_ISLR` a WHERE a.ID = $digit;
        SELECT * FROM `DD_ISLR` d WHERE d.ID = $digit;
        SELECT * FROM `DEC_AUTO_ENTDES` e WHERE e.ID = $digit;
        SELECT * FROM `DEC_DEF_ISLREI` z WHERE z.ID = $digit;
        SELECT * FROM `DEC_GRA_PATRI` f WHERE f.ID = $digit;
        SELECT * FROM `DEC_IGTF` g WHERE g.ID = $digit;
        SELECT * FROM `DEC_IVA` h WHERE h.ID = $digit;
        SELECT * FROM `DEC_PROT_PEN` i WHERE i.ID = $digit;
        SELECT * FROM `ENVITE_AZAR` j WHERE j.ID = $digit;
        SELECT * FROM `EST_ISLR_RENT` k WHERE k.ID = $digit;
        SELECT * FROM `RET_ISLR_LOT` l WHERE l.ID = $digit;
        SELECT * FROM `RET_ISLR_RENT` m WHERE m.ID = $digit;
        SELECT * FROM `RET_IVA` n WHERE n.ID = $digit;
        ";
    
        $connection = $this->getConex();
        $results = array();
    
        if (mysqli_multi_query($connection, $sql)) {
            do {
                if ($result = mysqli_store_result($connection)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $results[] = $row;
                    }
                    mysqli_free_result($result);
                }
            } while (mysqli_next_result($connection));
        } else {
            echo "Error al ejecutar las consultas: " . mysqli_error($connection);
            return false;
        }
    
        mysqli_close($connection);
        
        return $results;
    }

    public function getNextDeclarationByEmail($email)
    {
        $connection = $this->getConex();
        if ($connection == null) {
            return array("success" => false, "message" => "Sin conexión a BD.");
        }

        // Recomendado: zona horaria consistente para fechas (ajusta si quieres)
        $tz = new DateTimeZone("America/Caracas");
        $today = new DateTimeImmutable("today", $tz);

        // 1) Trae empresas del usuario (rif + nombre + tipo)
        $empresas = array();
        $sqlEmp = "
            SELECT r.RIF, r.tipo_de_cliente, r.nombre_empresa
            FROM rif r
            WHERE r.ID_FORANEA = (SELECT l.ID FROM logdata l WHERE l.correo = ?)
        ";
        $stmt = mysqli_prepare($connection, $sqlEmp);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($res)) {
            $empresas[] = $row;
        }
        mysqli_stmt_close($stmt);

        if (count($empresas) === 0) {
            mysqli_close($connection);
            return array("success" => true, "next" => null, "message" => "Sin empresas registradas.");
        }

        // 2) Map digit => empresas (porque el calendario está por terminal)
        $byDigit = array(); // "6" => [empresa1, empresa2]
                $prefsByRif = array();

        $sqlPrefs = "
            SELECT
                p.rif,
                p.anti_islr,
                p.dd_islr,
                p.dec_auto_entdes,
                p.dec_def_islrei,
                p.dec_gra_patri,
                p.dec_igtf,
                p.dec_iva,
                p.dec_prot_pen,
                p.envite_azar,
                p.est_islr_rent,
                p.ret_islr_lot,
                p.ret_islr_rent,
                p.ret_iva
            FROM rif_tax_preferences p
            INNER JOIN rif r ON r.RIF = p.rif
            INNER JOIN logdata l ON l.ID = r.ID_Foranea
            WHERE l.correo = ?
        ";

        $stmtPrefs = mysqli_prepare($connection, $sqlPrefs);
        mysqli_stmt_bind_param($stmtPrefs, "s", $email);
        mysqli_stmt_execute($stmtPrefs);
        $resPrefs = mysqli_stmt_get_result($stmtPrefs);

        while ($row = mysqli_fetch_assoc($resPrefs)) {
            $prefsByRif[$row["rif"]] = $row;
        }

        mysqli_stmt_close($stmtPrefs);

        foreach ($empresas as $e) {
            $rifDigits = preg_replace('/\D/', '', $e["RIF"]);
            if ($rifDigits === "" ) continue;
            $digit = substr($rifDigits, -1); // terminal
            if (!isset($byDigit[$digit])) $byDigit[$digit] = array();
            $byDigit[$digit][] = array(
                "rif" => $e["RIF"],
                "tipo" => (int)$e["tipo_de_cliente"],
                "nombre" => $e["nombre_empresa"]
            );
        }

        if (count($byDigit) === 0) {
            mysqli_close($connection);
            return array("success" => true, "next" => null, "message" => "RIF inválidos.");
        }

        // 3) Catálogo de impuestos (tabla => metadata)
        $taxTables = array(
            "ANTI_ISLR"      => array("code" => "ANTI_ISLR",      "name" => "ANTICIPO DE I.S.L.R."),
            "DD_ISLR"        => array("code" => "DD_ISLR",        "name" => "DECLARACIONES DEFINITIVAS DEL I.S.L.R."),
            "DEC_AUTO_ENTDES"=> array("code" => "DEC_AUTO_ENTDES","name" => "DECLARACIONES DE LOS SERVICIOS Y ENTES DESCENTRALIZADOS"),
            "DEC_DEF_ISLREI" => array("code" => "DEC_DEF_ISLREI", "name" => "DECLARACIONES DEFINITIVAS DEL I.S.R.L."),
            "DEC_GRA_PATRI"  => array("code" => "DEC_GRA_PATRI",  "name" => "DECLARACIONES A LOS GRANDES PATRIMONIOS"),
            "DEC_IGTF"       => array("code" => "DEC_IGTF",       "name" => "DECLARACIONES DE I.G.T.F."),
            "DEC_IVA"        => array("code" => "DEC_IVA",        "name" => "DECLARACIONES DE I.V.A."),
            "DEC_PROT_PEN"   => array("code" => "DEC_PROT_PEN",   "name" => "DECLARACIONES DE PROTECCIÓN DE LAS PENSIONES"),
            "ENVITE_AZAR"    => array("code" => "ENVITE_AZAR",    "name" => "DECLARACIONES DE JUEGOS DE ENVITE Y AZAR"),
            "EST_ISLR_RENT"  => array("code" => "EST_ISLR_RENT",  "name" => "ESTIMADAS DE I.S.L.R."),
            "RET_ISLR_LOT"   => array("code" => "RET_ISLR_LOT",   "name" => "RETENCIONES ISLR (PREMIOS LOTERÍA)"),
            "RET_ISLR_RENT"  => array("code" => "RET_ISLR_RENT",  "name" => "RETENCIONES DE I.S.L.R."),
            "RET_IVA"        => array("code" => "RET_IVA",        "name" => "RETENCIONES DE I.V.A.")
        );

        $months = array(
            "enero" => 1, "febrero" => 2, "marzo" => 3, "abril" => 4,
            "mayo" => 5, "junio" => 6, "julio" => 7, "agosto" => 8,
            "septiembre" => 9, "octubre" => 10, "noviembre" => 11, "diciembre" => 12
        );

        $best = null;   // array con la mejor opción
        $ties = array();// otras opciones si empatan la misma fecha exacta

        // helper: calcula fecha próxima para un day/month
        $computeNext = function($month, $day) use ($today, $tz) {
            $year = (int)$today->format("Y");
            if (!checkdate($month, $day, $year)) return null;

            $d = DateTimeImmutable::createFromFormat('!Y-n-j', $year . "-" . $month . "-" . $day, $tz);
            if ($d === false) return null;

            if ($d < $today) {
                $d2 = $d->modify("+1 year");
                return $d2;
            }
            return $d;
        };

        // 4) Recorre digit × impuesto y encuentra la mínima fecha >= hoy
        foreach ($byDigit as $digitStr => $empList) {
            $digit = (int)$digitStr;

            foreach ($taxTables as $table => $meta) {
                // SELECT * FROM `TABLE` WHERE ID=? LIMIT 1
                $prefColumn = $this->getTaxPreferenceColumnByCode($meta["code"]);
                if ($prefColumn === null) {
                    continue;
                }

                $filteredEmpList = array_values(array_filter($empList, function($empresa) use ($prefsByRif, $prefColumn) {
                    $rif = $empresa["rif"];

                    if (!isset($prefsByRif[$rif])) {
                        return true; // default true si no existe fila
                    }

                    return intval($prefsByRif[$rif][$prefColumn] ?? 1) === 1;
                }));

                if (count($filteredEmpList) === 0) {
                    continue;
                }
                $sql = "SELECT * FROM `$table` WHERE ID = ? LIMIT 1";
                $stmt = mysqli_prepare($connection, $sql);
                mysqli_stmt_bind_param($stmt, "i", $digit);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($res);
                mysqli_stmt_close($stmt);

                if (!$row) continue;

                foreach ($months as $mes => $monthNum) {
                    if (!array_key_exists($mes, $row)) continue;
                    $raw = $row[$mes];
                    if ($raw === null || trim((string)$raw) === "" || strtolower((string)$raw) === "null") continue;

                    $parts = explode(";", (string)$raw);
                    foreach ($parts as $p) {
                        $day = intval(trim($p));
                        if ($day < 1 || $day > 31) continue;

                        $nextDate = $computeNext($monthNum, $day);
                        if ($nextDate === null) continue;

                        $candidate = array(
                            "date" => $nextDate->format("Y-m-d"),
                            "digit" => (string)$digitStr,
                            "impuesto" => $meta,
                            "empresas" => $filteredEmpList,
                            "source" => array("table" => $table, "month" => $mes, "day" => $day)
                        );

                        if ($best === null) {
                            $best = $candidate;
                        } else {
                            $bestDate = new DateTimeImmutable($best["date"], $tz);
                            if ($nextDate < $bestDate) {
                                $best = $candidate;
                                $ties = array();
                            } else if ($nextDate == $bestDate) {
                                // empate exacto
                                $ties[] = $candidate;
                            }
                        }
                    }
                }
            }
        }

        mysqli_close($connection);

        return array(
            "success" => true,
            "today" => $today->format("Y-m-d"),
            "next" => $best,
            "ties" => $ties
        );
    }

    public function emailExists($email)
    {
        $sql = "SELECT 1 FROM logdata WHERE correo = ? LIMIT 1";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }

    public function createOrUpdatePendingRegister($name, $email, $passwordHash, $codeHash, $expiresAt)
    {
        $sql = "
            INSERT INTO pending_email_verifications
                (email, name, password_hash, verification_code_hash, code_expires_at, attempts, max_attempts, verified, purpose)
            VALUES (?, ?, ?, ?, ?, 0, 5, 0, 'register')
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                password_hash = VALUES(password_hash),
                verification_code_hash = VALUES(verification_code_hash),
                code_expires_at = VALUES(code_expires_at),
                attempts = 0,
                max_attempts = 5,
                verified = 0,
                purpose = 'register'
        ";

        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $email, $name, $passwordHash, $codeHash, $expiresAt);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function getPendingRegisterByEmail($email)
    {
        $sql = "
            SELECT id, email, name, password_hash, verification_code_hash, code_expires_at, attempts, max_attempts, verified, created_at, updated_at
            FROM pending_email_verifications
            WHERE email = ? AND purpose = 'register'
            LIMIT 1
        ";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    public function incrementPendingAttempts($email)
    {
        $sql = "
            UPDATE pending_email_verifications
            SET attempts = attempts + 1
            WHERE email = ? AND purpose = 'register'
        ";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function deletePendingRegister($email)
    {
        $sql = "DELETE FROM pending_email_verifications WHERE email = ? AND purpose = 'register'";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function registerVerifiedUser($name, $email, $passwordHash)
    {
        mysqli_begin_transaction($this->getConex());

        try {
            $sql1 = "INSERT INTO logdata (correo, clave, reg_state, num_rol_data) VALUES (?, ?, 0, 1)";
            $stmt1 = mysqli_prepare($this->getConex(), $sql1);
            mysqli_stmt_bind_param($stmt1, "ss", $email, $passwordHash);
            mysqli_stmt_execute($stmt1);
            $insertId = mysqli_insert_id($this->getConex());
            mysqli_stmt_close($stmt1);

            $sql2 = "INSERT INTO infoper (ID_Foranea, nya) VALUES (?, ?)";
            $stmt2 = mysqli_prepare($this->getConex(), $sql2);
            mysqli_stmt_bind_param($stmt2, "is", $insertId, $name);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);

            mysqli_commit($this->getConex());
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->getConex());
            return false;
        }
    }

    public function createOrUpdatePendingPasswordReset($email, $codeHash, $expiresAt)
    {
        $sql = "
            INSERT INTO pending_email_verifications
                (email, name, password_hash, verification_code_hash, code_expires_at, attempts, max_attempts, verified, purpose)
            VALUES (?, '', '', ?, ?, 0, 5, 0, 'password_reset')
            ON DUPLICATE KEY UPDATE
                verification_code_hash = VALUES(verification_code_hash),
                code_expires_at = VALUES(code_expires_at),
                attempts = 0,
                max_attempts = 5,
                verified = 0,
                purpose = 'password_reset'
        ";

        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "sss", $email, $codeHash, $expiresAt);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
    
    public function getPendingPasswordResetByEmail($email)
    {
        $sql = "
            SELECT id, email, verification_code_hash, code_expires_at, attempts, max_attempts, verified
            FROM pending_email_verifications
            WHERE email = ? AND purpose = 'password_reset'
            LIMIT 1
        ";

        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return $row ?: null;
    }

    public function isPasswordResetVerified($email)
    {
        $sql = "
            SELECT 1
            FROM pending_email_verifications
            WHERE email = ? AND purpose = 'password_reset' AND verified = 1
            LIMIT 1
        ";

        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        return $exists;
    }

    public function deletePendingPasswordReset($email)
    {
        $sql = "DELETE FROM pending_email_verifications WHERE email = ? AND purpose = 'password_reset'";

        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function markPendingPasswordResetVerified($email)
    {
        $sql = "
            UPDATE pending_email_verifications
            SET verified = 1
            WHERE email = ? AND purpose = 'password_reset'
        ";

        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function incrementPendingPasswordResetAttempts($email)
    {
        $sql = "
            UPDATE pending_email_verifications
            SET attempts = attempts + 1
            WHERE email = ? AND purpose = 'password_reset'
        ";

        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function updateProfileName($email, $name)
    {
        $connection = $this->getConex();

        if ($connection == null) {
            return array(
                "success" => false,
                "message" => "Sin conexión a la base de datos."
            );
        }

        try {
            $normalizedName = preg_replace('/\s+/', ' ', trim($name));

            $sqlUser = "
                SELECT l.ID
                FROM logdata l
                INNER JOIN infoper i ON i.ID_Foranea = l.ID
                WHERE l.Correo = ?
                LIMIT 1
            ";

            $stmtUser = mysqli_prepare($connection, $sqlUser);
            mysqli_stmt_bind_param($stmtUser, "s", $email);
            mysqli_stmt_execute($stmtUser);
            $resultUser = mysqli_stmt_get_result($stmtUser);
            $user = mysqli_fetch_assoc($resultUser);
            mysqli_stmt_close($stmtUser);

            if (!$user) {
                return array(
                    "success" => false,
                    "message" => "Usuario no encontrado."
                );
            }

            $userId = (int)$user["ID"];

            $sqlUpdate = "UPDATE infoper SET nya = ? WHERE ID_Foranea = ?";
            $stmtUpdate = mysqli_prepare($connection, $sqlUpdate);
            mysqli_stmt_bind_param($stmtUpdate, "si", $normalizedName, $userId);
            $ok = mysqli_stmt_execute($stmtUpdate);
            mysqli_stmt_close($stmtUpdate);

            if (!$ok) {
                return array(
                    "success" => false,
                    "message" => "No se pudo actualizar el nombre."
                );
            }

            return array(
                "success" => true,
                "message" => "Nombre actualizado correctamente.",
                "name" => $normalizedName
            );
        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error interno al actualizar el nombre."
            );
        } finally {
            mysqli_close($connection);
        }
    }

    public function updateRifByEmail($email, $originalRif, $newRif, $tipo, $nombre)
    {
        $connection = $this->getConex();

        if ($connection == null) {
            return array(
                "success" => false,
                "message" => "Sin conexión a la base de datos."
            );
        }

        try {
            $normalizedEmail = trim($email);
            $normalizedOriginalRif = trim($originalRif);
            $normalizedNewRif = trim($newRif);
            $normalizedNombre = preg_replace('/\s+/', ' ', trim($nombre));
            $tipoInt = intval($tipo);

            if ($tipoInt !== 1 && $tipoInt !== 2) {
                return array(
                    "success" => false,
                    "message" => "Tipo de cliente inválido."
                );
            }

            $sql = "
                UPDATE rif r
                INNER JOIN logdata l ON l.ID = r.ID_Foranea
                SET r.RIF = ?, r.tipo_de_cliente = ?, r.nombre_empresa = ?
                WHERE l.Correo = ? AND r.RIF = ?
                LIMIT 1
            ";

            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "sisss",
                $normalizedNewRif,
                $tipoInt,
                $normalizedNombre,
                $normalizedEmail,
                $normalizedOriginalRif
            );

            $ok = mysqli_stmt_execute($stmt);
            $affected = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);

            if (!$ok) {
                return array(
                    "success" => false,
                    "message" => "No se pudo actualizar el cliente."
                );
            }

            if ($affected < 0) {
                return array(
                    "success" => false,
                    "message" => "No se pudo actualizar el cliente."
                );
            }

            return array(
                "success" => true,
                "message" => "Cliente actualizado correctamente."
            );
        } catch (mysqli_sql_exception $e) {
            $message = $e->getMessage();

            if (stripos($message, "Duplicate entry") !== false) {
                return array(
                    "success" => false,
                    "message" => "Ya existe un cliente con ese RIF/Cédula."
                );
            }

            return array(
                "success" => false,
                "message" => "Error interno al actualizar el cliente."
            );
        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error interno al actualizar el cliente."
            );
        } finally {
            mysqli_close($connection);
        }
    }

    public function acquireNamedLock($key, $timeoutSeconds = 5)
    {
        $sql = "SELECT GET_LOCK(?, ?) AS lck";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "si", $key, $timeoutSeconds);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return isset($row["lck"]) && (int)$row["lck"] === 1;
    }

    public function releaseNamedLock($key)
    {
        $sql = "SELECT RELEASE_LOCK(?) AS rel";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $key);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    private function getTaxPreferenceColumnByCode($taxCode)
    {
        $map = array(
            "ANTI_ISLR"       => "anti_islr",
            "DD_ISLR"         => "dd_islr",
            "DEC_AUTO_ENTDES" => "dec_auto_entdes",
            "DEC_DEF_ISLREI"  => "dec_def_islrei",
            "DEC_GRA_PATRI"   => "dec_gra_patri",
            "DEC_IGTF"        => "dec_igtf",
            "DEC_IVA"         => "dec_iva",
            "DEC_PROT_PEN"    => "dec_prot_pen",
            "ENVITE_AZAR"     => "envite_azar",
            "EST_ISLR_RENT"   => "est_islr_rent",
            "RET_ISLR_LOT"    => "ret_islr_lot",
            "RET_ISLR_RENT"   => "ret_islr_rent",
            "RET_IVA"         => "ret_iva"
        );

        return $map[$taxCode] ?? null;
    }

    public function createDefaultTaxPreferencesForRif($rif)
    {
        $sql = "INSERT IGNORE INTO rif_tax_preferences (rif) VALUES (?)";
        $stmt = mysqli_prepare($this->getConex(), $sql);
        mysqli_stmt_bind_param($stmt, "s", $rif);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function getTaxPreferencesByRif($email, $rif)
    {
        $connection = $this->getConex();
        if ($connection == null) {
            return array("success" => false, "message" => "Sin conexión a BD.");
        }

        try {
            $sqlOwner = "
                SELECT r.RIF
                FROM rif r
                INNER JOIN logdata l ON l.ID = r.ID_Foranea
                WHERE l.Correo = ? AND r.RIF = ?
                LIMIT 1
            ";

            $stmtOwner = mysqli_prepare($connection, $sqlOwner);
            mysqli_stmt_bind_param($stmtOwner, "ss", $email, $rif);
            mysqli_stmt_execute($stmtOwner);
            $resOwner = mysqli_stmt_get_result($stmtOwner);
            $ownerRow = mysqli_fetch_assoc($resOwner);
            mysqli_stmt_close($stmtOwner);

            if (!$ownerRow) {
                return array(
                    "success" => false,
                    "message" => "El cliente no pertenece al usuario."
                );
            }

            $this->createDefaultTaxPreferencesForRif($rif);

            $sql = "
                SELECT
                    rif,
                    anti_islr,
                    dd_islr,
                    dec_auto_entdes,
                    dec_def_islrei,
                    dec_gra_patri,
                    dec_igtf,
                    dec_iva,
                    dec_prot_pen,
                    envite_azar,
                    est_islr_rent,
                    ret_islr_lot,
                    ret_islr_rent,
                    ret_iva
                FROM rif_tax_preferences
                WHERE rif = ?
                LIMIT 1
            ";

            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "s", $rif);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($res);
            mysqli_stmt_close($stmt);

            if (!$row) {
                return array(
                    "success" => false,
                    "message" => "No se encontraron preferencias."
                );
            }

            return array(
                "success" => true,
                "preferences" => array(
                    "ANTI_ISLR"       => (bool)$row["anti_islr"],
                    "DD_ISLR"         => (bool)$row["dd_islr"],
                    "DEC_AUTO_ENTDES" => (bool)$row["dec_auto_entdes"],
                    "DEC_DEF_ISLREI"  => (bool)$row["dec_def_islrei"],
                    "DEC_GRA_PATRI"   => (bool)$row["dec_gra_patri"],
                    "DEC_IGTF"        => (bool)$row["dec_igtf"],
                    "DEC_IVA"         => (bool)$row["dec_iva"],
                    "DEC_PROT_PEN"    => (bool)$row["dec_prot_pen"],
                    "ENVITE_AZAR"     => (bool)$row["envite_azar"],
                    "EST_ISLR_RENT"   => (bool)$row["est_islr_rent"],
                    "RET_ISLR_LOT"    => (bool)$row["ret_islr_lot"],
                    "RET_ISLR_RENT"   => (bool)$row["ret_islr_rent"],
                    "RET_IVA"         => (bool)$row["ret_iva"]
                )
            );
        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error interno obteniendo preferencias."
            );
        } finally {
            mysqli_close($connection);
        }
    }

    public function updateTaxPreference($email, $rif, $taxCode, $applies)
    {
        $connection = $this->getConex();
        if ($connection == null) {
            return array("success" => false, "message" => "Sin conexión a BD.");
        }

        try {
            $column = $this->getTaxPreferenceColumnByCode($taxCode);

            if ($column === null) {
                return array(
                    "success" => false,
                    "message" => "Código de impuesto inválido."
                );
            }

            $sqlOwner = "
                SELECT r.RIF
                FROM rif r
                INNER JOIN logdata l ON l.ID = r.ID_Foranea
                WHERE l.Correo = ? AND r.RIF = ?
                LIMIT 1
            ";

            $stmtOwner = mysqli_prepare($connection, $sqlOwner);
            mysqli_stmt_bind_param($stmtOwner, "ss", $email, $rif);
            mysqli_stmt_execute($stmtOwner);
            $resOwner = mysqli_stmt_get_result($stmtOwner);
            $ownerRow = mysqli_fetch_assoc($resOwner);
            mysqli_stmt_close($stmtOwner);

            if (!$ownerRow) {
                return array(
                    "success" => false,
                    "message" => "El cliente no pertenece al usuario."
                );
            }

            $this->createDefaultTaxPreferencesForRif($rif);

            $appliesInt = $applies ? 1 : 0;

            $sql = "
                UPDATE rif_tax_preferences
                SET `$column` = ?
                WHERE rif = ?
                LIMIT 1
            ";

            $stmt = mysqli_prepare($connection, $sql);
            mysqli_stmt_bind_param($stmt, "is", $appliesInt, $rif);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if (!$ok) {
                return array(
                    "success" => false,
                    "message" => "No se pudo actualizar la preferencia."
                );
            }

            return array(
                "success" => true,
                "message" => "Preferencia actualizada correctamente."
            );
        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error interno actualizando preferencia."
            );
        } finally {
            mysqli_close($connection);
        }
    }
}

?>
