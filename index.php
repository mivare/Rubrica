<?php
require 'config/db.php';
include_once 'config/definitions.php';
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo $WEB_LOGO; ?>">

    <title><?php echo $WEB_TITLE; ?></title>
    <link href="https://getbootstrap.com/docs/4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/4.1/examples/floating-labels/floating-labels.css" rel="stylesheet">
  </head>

<body>

<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: community");
    exit;
}
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Entra un nombre de usuario.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Entra tu contraseña.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, cuenta, clave, fRegistro, baneado, textoEstado, avatar, rango FROM cuentas WHERE cuenta = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            if(isset($_POST["captcha"]))  
            if(@$_SESSION["captcha"]==$_POST["captcha"])  
            {    
               
                 // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $fRegistro, $baneado, $textoEstado, $avatar, $rango);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["fRegistro"] = $fRegistro;   
                            $_SESSION["baneado"] = $baneado;
                            $_SESSION["textoEstado"] = $textoEstado;  
                            $_SESSION["avatar"] = $avatar; 
                            $_SESSION["rango"] = $rango; 

                            // Redirect user to welcome page
                            header("location: community");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "La contraseña otorgada no es válida.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No se ha encontrado ninguna cuenta con este nombre.";
                }
            } else{
                echo "Oops! Algo fue mal, intentalo de nuevo más tarde.";
            }
            
            }  
            else
            {  
                echo '<div class="alert alert-danger">¡El captcha introducido no es correcto!</div>';  
            }

        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>

    <form class="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="text-center mb-4">
      <img class="mb-4" src="<?php echo $WEB_LOGO; ?>" alt="" width="250" height="72">
        <p>Bienvenido, conectate a <?php echo $WEB_TITLE; ?> para acceder a nuestros servicios. Si no dispones de Cuenta <?php echo $WEB_TITLE; ?> puedes crear una desde <a href="registration.php">este enlace.</a></p>
      </div>

      <div class="form-label-group">
        <input name="username" type="text" id="inputText" class="form-control" placeholder="Account" required autofocus>
        <label for="inputText">Cuenta <?php echo $WEB_TITLE; ?></label>
      </div>

      <div class="form-label-group">
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <label for="inputPassword">Contraseña</label>
      </div>

      <div class="form-label-group">
        <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text" id="validationTooltipUsernamePrepend"><img src="config/captcha.php" /></span>
        </div>
        <input name="captcha" type="text" id="captcha" class="form-control" minlength="6" required>
      </div>
<br>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Conectarse</button>
      <hr>
      <div id="google_translate_element"></div>
<script  type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'es'}, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<hr>
      <p class="mt-5 mb-3 text-muted text-center">&copy; 2018-2019</p>
    </form>
  </body>
</html>
