<?php 
// Librerías necesarias para PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Requiere los archivos de PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Conexión a MySQL
$host = "localhost"; // o el host que Webcindario te dé
$user = "root"; // tu usuario MySQL
$pass = ""; // tu contraseña MySQL
$dbname = "anakonda"; // el nombre de tu BD

// Crea conexión
$conn = new mysqli($host, $user, $pass, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verifica si se recibieron todos los campos del formulario
if (isset($_POST['dni'])    &&
	isset($_POST['nombres'])   &&
    isset($_POST['apellidos']) &&
    isset($_POST['email']) &&
    isset($_POST['correo_destino'])) {
	
     // Captura los datos enviados por POST
	$dni = $_POST['dni'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $email = $_POST['email'];
    $correoDestino = $_POST['correo_destino'];

    // Asunto dinámico con nombres y apellidos
    $subject = 'Nuevo registro: ' . $nombres . ' ' . $apellidos;
        
     // Validación del correo del remitente
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    	$em = "Invalid email format";
    	header("Location: index.php?error=$em");
        exit;
    }

    // Validación del correo de destino
    if (!filter_var($correoDestino, FILTER_VALIDATE_EMAIL)) {
    $em = "Correo de destino no válido";
    header("Location: index.php?error=$em");
    exit;
    }

    // Validación de campos obligatorios
    if (empty($dni) || empty($nombres) || empty($apellidos) || empty($email) || empty($correoDestino)) {
    $em = "Todos los campos son obligatorios";
    header("Location: index.php?error=$em");
    exit;
    }

    // Inserta los datos en la base de datos
    $sql = "INSERT INTO usuarios (dni, nombres, apellidos, correo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $em = "Error en la preparación de la consulta: " . $conn->error;
        header("Location: index.php?error=$em");
        exit;
    }
    $stmt->bind_param("ssss", $dni, $nombres, $apellidos, $email);

    // Ejecuta la consulta
    if (!$stmt->execute()) {
        $em = "Error al guardar en la base de datos: " . $stmt->error;
        header("Location: index.php?error=$em");
        exit;
    }

    // Cierra la consulta y conexión
    $stmt->close();
    $conn->close();

	// Crear una instancia de PHPMailer (con excepciones habilitadas)
	$mail = new PHPMailer(true);

	try {
        // Configuración del servidor SMTP de Gmail
	    $mail->isSMTP();                               
	    $mail->Host = 'smtp.gmail.com'; 
	    $mail->SMTPAuth   = true;
	    $mail->Username= 'lfernandez@isil.pe'; //correo Gmail
	    $mail->Password = 'emytqsmfmcfdqkfe'; //contraseña de aplicación de Gmail
	    $mail->SMTPSecure = "ssl";          
	    $mail->Port       = 465;                                  
	    
        // Establece el remitente (persona que llena el formulario)
	    $mail->setFrom('lfernandez@isil.pe', $nombres . ' ' . $apellidos);  
        
        // Establece el destinatario (correo dinámico)
        $mail->addAddress($correoDestino); 

         // Configura el contenido del correo
        $mail->isHTML(true);                             
        $mail->Subject = $subject;

        // Contenido HTML del correo con HTML ytabla
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #ffffff; color: #000;'>

        <h2 style='font-size: 18px; font-weight: bold; margin-bottom: 20px; text-align: center;'>
            Estimado equipo de RRHH
        </h2>
        <p style='font-size: 15px;'>
            Se ha registrado un nuevo colaborador. A continuación, los datos:
        </p>

        <div style='max-width: 700px; margin: auto; border: 1px solid #ccc; border-radius: 6px; overflow: hidden;'>
            <table style='width: 100%; border-collapse: collapse; table-layout: auto; word-wrap: break-word; font-family: Arial, sans-serif;'>
            <thead>
                <tr style='background-color: #00B0F0; color: white; text-align: left; font-size: 15px;'>
                <th style='padding: 12px 10px; border: 1px solid #ccc;'>DNI</th>
                <th style='padding: 12px 10px; border: 1px solid #ccc;'>Nombres</th>
                <th style='padding: 12px 10px; border: 1px solid #ccc;'>Apellidos</th>
                <th style='padding: 12px 10px; border: 1px solid #ccc;'>Correo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$dni}</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$nombres}</td>
                <td style='padding: 10px; border: 1px solid #ddd;'>{$apellidos}</td>
                <td style='padding: 10px; border: 1px solid #ddd;'><a href='mailto:{$email}'>{$email}</a></td>
                </tr>
            </tbody>
            </table>

            <p style='margin-top: 30px; text-align: center;'>
            Atentamente,<br>
            <strong>El equipo de T.I</strong>
            </p>

            <div style='background-color: #000000; color: white; text-align: center; padding: 15px; font-size: 20px; font-weight: bold;'>
            ANAKONDA
            </div>
        </div>
        </div>";

        // Envía el correo
	    $mail->send();

        // Redirecciona con mensaje de éxito
	    $sm= 'Mensaje enviado correctamente';
    	header("Location: index.php?success=$sm");
		exit;
	} catch (Exception $e) {
        // Si hay error en el envío de correo
	    $em = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    	header("Location: index.php?error=$em");
		exit;
	}

}