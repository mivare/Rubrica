<?php
require 'config/db.php';
include_once 'config/definitions.php';
session_start();  
//require_once 'config/captcha.php';
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
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM cuentas WHERE cuenta = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO cuentas (cuenta, clave) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            if(isset($_POST["captcha"]))  
            if(@$_SESSION["captcha"]==$_POST["captcha"])  
            {    
                // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: index.php");
            } else{
                echo "Something went wrong. Please try again later.";
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
        <p>¿Ya tienes cuenta en <?php echo $WEB_TITLE; ?>? <a href="index.php">Conectate.</a></p>
      </div>

<p style="border:1px solid teal;border-radius:5px;padding:5px;">Datos de la Cuenta</p>
      <div class="form-label-group">
        <input name="username" type="text" id="Username" class="form-control" placeholder="Account" minlength="2" maxlength="16" required autofocus>
        <label for="Username">Cuenta <?php echo $WEB_TITLE; ?></label>
      </div>

      <div class="form-label-group">
        <input name="password" type="password" id="password" class="form-control" placeholder="Password" minlength="6" required>
        <label for="password">Contraseña</label>
      </div>

      <div class="form-label-group">
        <input name="confirm_password" type="password" id="confirm_password" class="form-control" placeholder="RepeatPassword" minlength="6" required>
        <label for="confirm_password">Repetir Contraseña</label>
      </div>

      <p style="border:1px solid purple;border-radius:5px;padding:5px;">Captcha</p>
      <div class="form-label-group">
        <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text" id="validationTooltipUsernamePrepend"><img src="config/captcha.php" /></span>
        </div>
        <input name="captcha" type="text" id="captcha" class="form-control" minlength="6" required>
      </div>


      </div>

      <!--<div class="checkbox mb-3">
        <label>
          <input type="checkbox" value="therms" required> <a href="">Acepto los términos de uso y condiciones</a>
        </label>
      </div>-->
      <button class="btn btn-lg btn-primary btn-block" type="submit">Registrarse</button>
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
