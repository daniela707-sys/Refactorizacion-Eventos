<style>
    /* Custom classes for the login and register forms */
    .personalized-container {
        background-color: var(--ogenix-white);
        border-radius: var(--ogenix-bdr-radius);
        box-shadow: 0 4px 6px rgba(var(--ogenix-black), 0.1);
        width: 100%;
        height: auto;
        overflow: hidden;
    }

    .personalized-forms-container {
        display: flex;
        width: 200%;
        transition: transform 0.5s ease-in-out;
    }

    .personalized-login-container,
    .personalized-register-container {
        width: 50%;
        padding: 0.5rem;
        height: auto;
        animation: fadeIn 1s ease;
    }

    .personalized-register-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .personalized-container h1 {
        color: var(--ogenix-black);
        text-align: center;
        margin-bottom: 1.5rem;
        letter-spacing: var(--ogenix-letter-spacing);
    }

    .personalized-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .personalized-buttom {
        display: flex;
        width: 100%;
        justify-content: center;
        align-items: center
    }

    .personalized-form-group {
        display: flex;
        flex-direction: column;
    }

    .personalized-form-group label {
        color: var(--ogenix-gray);
        margin-bottom: 0.5rem;
    }

    .personalized-form-group input {
        padding: 0.75rem;
        border: 1px solid var(--ogenix-bdr-color);
        border-radius: var(--ogenix-bdr-radius);
        font-family: var(--ogenix-font);
    }

    .personalized-submit-button {
        background-color: var(--ogenix-base);
        color: var(--ogenix-white);
        padding: 0.75rem;
        border: none;
        border-radius: var(--ogenix-bdr-radius);
        cursor: pointer;
        font-family: var(--ogenix-font);
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .personalized-submit-button:hover {
        background-color: var(--ogenix-primary);
    }

    .personalized-form-footer {
        margin-top: 1rem;
        text-align: center;
    }

    .personalized-form-link {
        color: var(--ogenix-base);
        text-decoration: none;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .personalized-form-link:hover {
        text-decoration: underline;
    }

    .personalized-form-footer p {
        color: var(--ogenix-gray);
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }

    .personalized-checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .personalized-checkbox-group label {
        font-size: 0.9rem;
        color: var(--ogenix-gray);
    }

    #form-register {
        width: 80%;
    }

    /* Basic styling for modal */
    .modal_login {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100vh;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content_login {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 10px;
        width: 80%;
        max-width: 500px;
        overflow-y: auto;
        animation: fadeIn 0.5s linear;
        max-height: 90%;
        overflow-y: auto;
    }

    .close_login {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close_login:hover,
    .close_login:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Spinner styles */
    .login-register-spinner-container {
        display: none;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 10000;
    }

    .login-register-spinner {
        width: 50px;
        height: 50px;
        border: 6px solid #f3f3f3;
        border-top: 6px solid #3498db;
        border-radius: 50%;
        animation: login-register-spin 1s linear infinite;
    }

    @keyframes login-register-spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>

<!-- Modal login -->
<div id="loginModal" class="modal_login">
    <div class="modal-content_login">
        <!-- Close Button -->
        <span class="close_login">&times;</span>
        <!-- Include login content -->
        <div class="personalized-container">
            <div class="personalized-forms-container">
                <div id="loginForm" class="personalized-login-container">
                    <h1>Iniciar Sesión</h1>
                    <form id="form-login" class="personalized-form login-form">
                        <div class="personalized-form-group">
                            <label for="login-email">Correo electrónico</label>
                            <input type="email" id="login-username" name="username" required>
                        </div>
                        <div class="personalized-form-group">
                            <label for="login-password">Contraseña</label>
                            <input type="password" id="login-password" name="password" required>
                        </div>
                        <button type="submit" class="personalized-submit-button">Iniciar Sesión</button>
                    </form>
                    <div class="personalized-form-footer">
                        <a href="#" class="personalized-form-link forgot-password">¿Olvidaste tu contraseña?</a>
                        <p>¿No tienes una cuenta? <a href="#"
                                class="personalized-form-link register-link">Regístrate</a></p>
                    </div>
                </div>
                <div id="registerForm" class="personalized-register-container">
                    <h1>Registro</h1>
                    <div class="personalized-form-footer">
                        <p>¿Ya tienes una cuenta? <a href="#" class="personalized-form-link login-link">Inicia sesión</a></p>
                    </div>
                    <form id="form-register" class="personalized-form register-form">
                        <div class="personalized-form-group">
                            <label for="register-email">Correo electrónico</label>
                            <input maxlength="50" type="email" id="register-email" name="correo" required>
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-document-type">Tipo de documento</label>
                            <select id="register-document-type" name="tipo_identificacion" required>
                                <option value="" disabled selected>Seleccione el tipo de documento</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PP">Pasaporte</option>
                            </select>
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-document-number">Número de documento</label>
                            <input maxlength="11" type="number" id="register-document-number" name="numero_identificacion" required>
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-firstname">Nombre</label>
                            <input maxlength="24" type="text" id="register-firstname" name="nombres" required>
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-middlename">Segundo nombre</label>
                            <input maxlength="24" type="text" id="register-middlename" name="segundo_nombre">
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-lastname">Apellido</label>
                            <input maxlength="24" type="text" id="register-lastname" name="apellidos" required>
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-secondlastname">Segundo apellido</label>
                            <input maxlength="24" type="text" id="register-secondlastname" name="segundo_apellido">
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-phone">Número de teléfono</label>
                            <input maxlength=19  type="tel" id="register-phone" name="numero_celular" required>
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-password">Contraseña</label>
                            <input maxlength="49" type="password" id="register-password" name="password" required>
                        </div>
                        <div class="personalized-form-group">
                            <label for="register-confirm-password">Confirmar contraseña</label>
                            <input maxlength="49" type="password" id="register-confirm-password" name="confirm_password" required>
                        </div>
                        <div class="personalized-checkbox-group">
                            <input type="checkbox" id="register-policy" name="policy" required>
                            <label for="register-policy">Acepto la política de tratamiento de datos</label>
                        </div>
                        <div class="personalized-buttom">
                            <button type="submit" class="personalized-submit-button">Registrarse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Spinner container -->
<div class="login-register-spinner-container" id="login-register-spinner-container">
    <div class="login-register-spinner"></div>
</div>

<script>
    const formregistro = document.getElementById('form-register');
    formregistro.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(formregistro);
        const data = {
            correo: formData.get('correo'),
            password: formData.get('password'),
            nombres: formData.get('nombres') + ' ' + formData.get('segundo_nombre'),
            apellidos: formData.get('apellidos') + ' ' + formData.get('segundo_apellido'),
            tipo_identificacion: formData.get('tipo_identificacion'),
            numero_identificacion: formData.get('numero_identificacion'),
            numero_celular: formData.get('numero_celular')
        };

        // Mostrar el spinner
        document.getElementById('login-register-spinner-container').style.display = 'flex';

        fetch('assets/components/acceso/registro.php', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Ocultar el spinner
            document.getElementById('login-register-spinner-container').style.display = 'none';

            if (data.success) {
                alert('Usuario registrado exitosamente.');
                moveralogin();
                // Redirigir o realizar alguna acción adicional
            } else {
                alert('registrar el usuario: ' + data.message);
            }
        })
        .catch(error => {
            // Ocultar el spinner
            document.getElementById('login-register-spinner-container').style.display = 'none';

            console.error('Error:', error);
            alert('Error al registrar el usuario.');
        });
    });

    const formlogin = document.getElementById('form-login');
    //si es local se coloca /red_emprendedores/output/login.php?page=login sino /redemprendedores/output/login.php?page=login
    const loginurl = document.location.href.includes('localhost') ? '/redemprendedores/output/login.php?page=login' : '/redemprendedores/login.php?page=login';

    formlogin.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(formlogin);
        formData.append('btnSubmit', 'Login');

        // Mostrar el spinner
        document.getElementById('login-register-spinner-container').style.display = 'flex';

        fetch(loginurl, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // Ocultar el spinner
            document.getElementById('login-register-spinner-container').style.display = 'none';

            if (response.status == 200) {
                alert('Inicio de sesión exitoso.');
                window.location.reload();
            } else {
                alert('Error al iniciar sesión.');
            }
        })
        .catch(error => {
            // Ocultar el spinner
            document.getElementById('login-register-spinner-container').style.display = 'none';

            console.error('Error:', error);
            alert('Error al iniciar sesión.');
        });
    });

    const container = document.querySelector('.personalized-container');
    const formsContainer = document.querySelector('.personalized-forms-container');
    const registerLink = document.querySelector('.register-link');
    const loginLink = document.querySelector('.login-link');
    const divloginform = document.querySelector('.login-form');
    const divregisterform = document.querySelector('.register-form');
    const modal_login = document.querySelector('.modal-content_login');
    divregisterform.style.display = 'none';
    divregisterform.style.visibility = 'hidden';

    registerLink.addEventListener('click', function(e) {
        e.preventDefault();
        formsContainer.style.transform = 'translateX(-50%)';
        container.style.overflowY = 'auto'
        container.style.overflow = 'hidden'
        divregisterform.style.display = 'block';
        divregisterform.style.visibility = 'visible';
        modal_login.style.margin = '5% auto';
    });

    loginLink.addEventListener('click', function(e) {
        e.preventDefault();
        formsContainer.style.transform = 'translateX(0)';
        divregisterform.style.display = 'none';
        divregisterform.style.visibility = 'hidden';
        modal_login.style.margin = '15% auto';
    });

    var loginModal = document.getElementById("loginModal");
    var closeLogin = document.getElementsByClassName("close_login")[0];
    var openLoginModalButton = document.getElementById("openLoginModal");

    if (openLoginModalButton && loginModal && closeLogin) {
        // Close the login modal
        closeLogin.onclick = function() {
            loginModal.style.display = "none";
            document.body.classList.remove("no-scroll");
            formsContainer.style.transform = 'translateX(0)';
            divregisterform.style.display = 'none';
            divregisterform.style.visibility = 'hidden';
        }

        // Close the login modal if clicked outside of it
        window.onclick = function(event) {
            if (event.target === loginModal) {
                loginModal.style.display = "none";
                document.body.classList.remove("no-scroll");
                formsContainer.style.transform = 'translateX(0)';
                divregisterform.style.display = 'none';
                divregisterform.style.visibility = 'hidden';
            }
        }
    }

    function openlogin_mobil() {
        loginModal.style.display = "block";
        document.body.classList.add("no-scroll");
    }

    function moveralogin() {
        formsContainer.style.transform = 'translateX(0)';
        divregisterform.style.display = 'none';
        divregisterform.style.visibility = 'hidden';
        modal_login.style.margin = '15% auto';
    }
</script>