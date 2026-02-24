// Generador de contraseñas simplificado
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando generador de contraseñas...');
    
    function addPasswordControls(passwordField) {
        if (passwordField.hasAttribute('data-password-controls')) {
            return;
        }

        passwordField.setAttribute('data-password-controls', 'true');

        // Crear contenedor de controles
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'password-controls d-flex align-items-center mt-2';

        // Botón para generar contraseña
        const generateBtn = document.createElement('button');
        generateBtn.type = 'button';
        generateBtn.className = 'btn btn-sm btn-outline-primary mr-2';
        generateBtn.innerHTML = '<i class="fas fa-key"></i> Generar';
        generateBtn.onclick = () => generatePassword(passwordField);

        // Botón para mostrar/ocultar contraseña
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'btn btn-sm btn-outline-secondary mr-2';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
        toggleBtn.onclick = () => togglePasswordVisibility(passwordField, toggleBtn);

        // Botón para copiar contraseña
        const copyBtn = document.createElement('button');
        copyBtn.type = 'button';
        copyBtn.className = 'btn btn-sm btn-outline-info';
        copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copiar';
        copyBtn.onclick = () => copyPassword(passwordField, copyBtn);

        // Agregar botones al contenedor
        controlsContainer.appendChild(generateBtn);
        controlsContainer.appendChild(toggleBtn);
        controlsContainer.appendChild(copyBtn);

        // Insertar contenedor después del campo de contraseña
        passwordField.parentNode.insertBefore(controlsContainer, passwordField.nextSibling);

        // Agregar indicador de fortaleza
        addStrengthIndicator(passwordField);
    }

    function generatePassword(field) {
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?";
        let password = "";
        
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }

        field.value = password;
        
        // Actualizar campo de confirmación si existe
        const confirmField = document.querySelector('input[name="password_confirmation"]');
        if (confirmField) {
            confirmField.value = password;
        }

        // Actualizar indicador de fortaleza
        updateStrengthIndicator(field, password);

        // Mostrar notificación
        showNotification('Contraseña generada exitosamente', 'success');
    }

    function togglePasswordVisibility(field, button) {
        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
        
        // Actualizar icono del botón
        if (type === 'text') {
            button.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            button.innerHTML = '<i class="fas fa-eye"></i>';
        }

        // También actualizar el campo de confirmación si existe
        const confirmField = document.querySelector('input[name="password_confirmation"]');
        if (confirmField) {
            confirmField.setAttribute('type', type);
        }
    }

    async function copyPassword(field, button) {
        if (!field.value) {
            showNotification('No hay contraseña para copiar', 'warning');
            return;
        }

        try {
            await navigator.clipboard.writeText(field.value);
            
            // Cambiar temporalmente el botón para indicar que se copió
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copiado';
            button.className = 'btn btn-sm btn-outline-success';
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.className = 'btn btn-sm btn-outline-info';
            }, 2000);

            showNotification('Contraseña copiada al portapapeles', 'success');
        } catch (err) {
            showNotification('Error al copiar la contraseña', 'error');
        }
    }

    function addStrengthIndicator(field) {
        const strengthContainer = document.createElement('div');
        strengthContainer.className = 'password-strength mt-2';
        strengthContainer.innerHTML = `
            <div class="progress" style="height: 5px;">
                <div class="progress-bar strength-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <small class="strength-text text-muted">Fortaleza: <span class="strength-label">-</span></small>
        `;

        field.parentNode.insertBefore(strengthContainer, field.parentNode.lastChild.nextSibling);

        // Agregar evento para actualizar fortaleza
        field.addEventListener('input', () => {
            updateStrengthIndicator(field, field.value);
        });
    }

    function updateStrengthIndicator(field, password) {
        const strengthBar = field.parentNode.querySelector('.strength-bar');
        const strengthLabel = field.parentNode.querySelector('.strength-label');
        
        if (!strengthBar || !strengthLabel) return;

        const strength = calculatePasswordStrength(password);
        
        // Actualizar barra de progreso
        strengthBar.style.width = strength.percentage + '%';
        strengthBar.className = `progress-bar strength-bar ${strength.class}`;
        
        // Actualizar texto
        strengthLabel.textContent = strength.label;
    }

    function calculatePasswordStrength(password) {
        if (!password) return { percentage: 0, class: 'bg-secondary', label: '-' };

        let score = 0;
        
        // Longitud
        if (password.length >= 8) score++;
        if (password.length >= 12) score++;
        
        // Complejidad
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^a-zA-Z0-9]/.test(password)) score++;

        const percentage = (score / 6) * 100;

        if (percentage <= 33) {
            return { percentage, class: 'bg-danger', label: 'Débil' };
        } else if (percentage <= 66) {
            return { percentage, class: 'bg-warning', label: 'Media' };
        } else {
            return { percentage, class: 'bg-success', label: 'Fuerte' };
        }
    }

    function showNotification(message, type = 'info') {
        // Crear notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

        document.body.appendChild(notification);

        // Auto eliminar después de 3 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Inicializar todos los campos de contraseña existentes
    const passwordFields = document.querySelectorAll('input[type="password"]');
    console.log('Campos de contraseña encontrados:', passwordFields.length);
    
    passwordFields.forEach(field => {
        addPasswordControls(field);
    });

    // Hacer la función global para que pueda ser llamada desde otros scripts
    window.addPasswordControls = addPasswordControls;
});
