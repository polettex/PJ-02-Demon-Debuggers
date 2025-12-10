// Sistema de drag and drop para mesas
let draggedElement = null;
let offsetX = 0;
let offsetY = 0;

// Validar y corregir posiciones al cargar la página
window.addEventListener('DOMContentLoaded', function() {
    const mesaWidth = 120;
    const mesaHeight = 120;
    const margin = 10;
    
    const allMesas = Array.from(document.querySelectorAll('.draggable'));
    
    allMesas.forEach((mesa, index) => {
        const parentRect = mesa.parentElement.getBoundingClientRect();
        
        // Obtener posición actual en porcentaje
        const currentLeft = parseFloat(mesa.style.left);
        const currentTop = parseFloat(mesa.style.top);
        
        // Convertir a píxeles
        let posXPx = (currentLeft / 100) * parentRect.width;
        let posYPx = (currentTop / 100) * parentRect.height;
        
        // Calcular límites seguros
        const minX = mesaWidth / 2 + margin;
        const maxX = parentRect.width - mesaWidth / 2 - margin;
        const minY = mesaHeight / 2 + margin;
        const maxY = parentRect.height - mesaHeight / 2 - margin;
        
        let corrected = false;
        
        // Solo corregir si está fuera de límites
        if (posXPx < minX || posXPx > maxX || posYPx < minY || posYPx > maxY) {
            posXPx = Math.max(minX, Math.min(posXPx, maxX));
            posYPx = Math.max(minY, Math.min(posYPx, maxY));
            corrected = true;
        }
        
        // Verificar colisiones REALES con mesas anteriores usando posición actual
        const currentRect = mesa.getBoundingClientRect();
        const currentLeft_px = currentRect.left - parentRect.left;
        const currentRight_px = currentRect.right - parentRect.left;
        const currentTop_px = currentRect.top - parentRect.top;
        const currentBottom_px = currentRect.bottom - parentRect.top;
        
        for (let i = 0; i < index; i++) {
            const otherMesa = allMesas[i];
            const otherRect = otherMesa.getBoundingClientRect();
            
            const otherLeft = otherRect.left - parentRect.left;
            const otherRight = otherRect.right - parentRect.left;
            const otherTop = otherRect.top - parentRect.top;
            const otherBottom = otherRect.bottom - parentRect.top;
            
            const collisionMargin = 5;
            
            // Verificar si HAY colisión REAL en la posición actual
            const hasCollision = !(currentRight_px + collisionMargin < otherLeft || 
                                   currentLeft_px - collisionMargin > otherRight || 
                                   currentBottom_px + collisionMargin < otherTop || 
                                   currentTop_px - collisionMargin > otherBottom);
            
            // Solo mover si hay colisión real
            if (hasCollision) {
                // Intentar mover a la derecha
                posXPx = otherRight + mesaWidth / 2 + collisionMargin + margin;
                
                // Si se sale del límite derecho, mover abajo
                if (posXPx > maxX) {
                    posXPx = minX;
                    posYPx = otherBottom + mesaHeight / 2 + collisionMargin + margin;
                }
                
                // Si se sale del límite inferior, mover al centro
                if (posYPx > maxY) {
                    posXPx = parentRect.width / 2;
                    posYPx = parentRect.height / 2;
                }
                
                corrected = true;
                break; // Salir del bucle una vez corregida
            }
        }
        
        // Solo aplicar y guardar si realmente se corrigió algo
        if (corrected) {
            // Convertir de vuelta a porcentaje
            const newPercentX = (posXPx / parentRect.width) * 100;
            const newPercentY = (posYPx / parentRect.height) * 100;
            
            // Aplicar nueva posición
            mesa.style.left = newPercentX + '%';
            mesa.style.top = newPercentY + '%';
            
            // Guardar en BD usando formulario oculto
            const mesaId = mesa.dataset.id;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../proc/proc_guardar_posicion_mesa.php';
            form.style.display = 'none';
            
            const inputMesa = document.createElement('input');
            inputMesa.type = 'hidden';
            inputMesa.name = 'id_mesa';
            inputMesa.value = mesaId;
            
            const inputX = document.createElement('input');
            inputX.type = 'hidden';
            inputX.name = 'x';
            inputX.value = newPercentX;
            
            const inputY = document.createElement('input');
            inputY.type = 'hidden';
            inputY.name = 'y';
            inputY.value = newPercentY;
            
            form.appendChild(inputMesa);
            form.appendChild(inputX);
            form.appendChild(inputY);
            document.body.appendChild(form);
            
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.name = 'positionFrame_' + mesaId;
            document.body.appendChild(iframe);
            
            form.target = 'positionFrame_' + mesaId;
            form.submit();
            
            setTimeout(() => {
                document.body.removeChild(form);
                document.body.removeChild(iframe);
            }, 1000);
        }
    });
});

document.querySelectorAll('.draggable').forEach(mesa => {
    mesa.addEventListener('mousedown', startDrag);
});

function startDrag(e) {
    // No arrastrar si se hace clic en el botón de toggle o su formulario
    if (e.target.closest('.mesa-toggle-form') || e.target.closest('.mesa-toggle-btn')) {
        return;
    }
    
    draggedElement = this;
    const rect = draggedElement.getBoundingClientRect();
    const parentRect = draggedElement.parentElement.getBoundingClientRect();
    
    offsetX = e.clientX - rect.left;
    offsetY = e.clientY - rect.top;
    
    draggedElement.classList.add('dragging');
    
    document.addEventListener('mousemove', drag);
    document.addEventListener('mouseup', stopDrag);
    
    e.preventDefault();
}

function drag(e) {
    if (!draggedElement) return;
    
    const parentRect = draggedElement.parentElement.getBoundingClientRect();
    const mesaWidth = 120; // Ancho fijo de la mesa
    const mesaHeight = 120; // Alto fijo de la mesa
    
    // Margen de seguridad (en píxeles) para evitar que se corten
    const margin = 10;
    
    // Calcular nueva posición en píxeles (centro de la mesa)
    let newX = e.clientX - parentRect.left - offsetX + (mesaWidth / 2);
    let newY = e.clientY - parentRect.top - offsetY + (mesaHeight / 2);
    
    // Limitar dentro del contenedor considerando que transform translate(-50%, -50%) centra la mesa
    // Mínimo: mitad del ancho/alto de la mesa + margen
    // Máximo: ancho/alto del contenedor - mitad del ancho/alto de la mesa - margen
    newX = Math.max(mesaWidth / 2 + margin, Math.min(newX, parentRect.width - mesaWidth / 2 - margin));
    newY = Math.max(mesaHeight / 2 + margin, Math.min(newY, parentRect.height - mesaHeight / 2 - margin));
    
    // Verificar colisiones con otras mesas
    const collision = checkCollision(newX, newY, mesaWidth, mesaHeight, parentRect);
    
    if (!collision) {
        // Convertir a porcentaje
        const percentX = (newX / parentRect.width) * 100;
        const percentY = (newY / parentRect.height) * 100;
        
        draggedElement.style.left = percentX + '%';
        draggedElement.style.top = percentY + '%';
        
        // Quitar indicador de colisión si existe
        draggedElement.classList.remove('collision');
    } else {
        // Agregar indicador visual de colisión
        draggedElement.classList.add('collision');
    }
}

function checkCollision(newX, newY, width, height, parentRect) {
    // Calcular los bordes de la mesa que se está arrastrando
    const draggedLeft = newX - width / 2;
    const draggedRight = newX + width / 2;
    const draggedTop = newY - height / 2;
    const draggedBottom = newY + height / 2;
    
    // Verificar colisión con todas las demás mesas
    const allMesas = document.querySelectorAll('.draggable');
    
    for (let mesa of allMesas) {
        // Saltar la mesa que se está arrastrando
        if (mesa === draggedElement) continue;
        
        // Obtener posición de la otra mesa
        const otherRect = mesa.getBoundingClientRect();
        const otherLeft = otherRect.left - parentRect.left;
        const otherRight = otherRect.right - parentRect.left;
        const otherTop = otherRect.top - parentRect.top;
        const otherBottom = otherRect.bottom - parentRect.top;
        
        // Detectar colisión (con un pequeño margen de separación)
        const collisionMargin = 5;
        if (!(draggedRight + collisionMargin < otherLeft || 
              draggedLeft - collisionMargin > otherRight || 
              draggedBottom + collisionMargin < otherTop || 
              draggedTop - collisionMargin > otherBottom)) {
            return true; // Hay colisión
        }
    }
    
    return false; // No hay colisión
}

function stopDrag(e) {
    if (!draggedElement) return;
    
    draggedElement.classList.remove('dragging');
    draggedElement.classList.remove('collision');
    
    // Guardar posición usando formulario oculto
    const parentRect = draggedElement.parentElement.getBoundingClientRect();
    const rect = draggedElement.getBoundingClientRect();
    
    const percentX = ((rect.left - parentRect.left) / parentRect.width) * 100;
    const percentY = ((rect.top - parentRect.top) / parentRect.height) * 100;
    
    const mesaId = draggedElement.dataset.id;
    
    // Crear formulario oculto para enviar datos
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../proc/proc_guardar_posicion_mesa.php';
    form.style.display = 'none';
    
    const inputMesa = document.createElement('input');
    inputMesa.type = 'hidden';
    inputMesa.name = 'id_mesa';
    inputMesa.value = mesaId;
    
    const inputX = document.createElement('input');
    inputX.type = 'hidden';
    inputX.name = 'x';
    inputX.value = percentX;
    
    const inputY = document.createElement('input');
    inputY.type = 'hidden';
    inputY.name = 'y';
    inputY.value = percentY;
    
    form.appendChild(inputMesa);
    form.appendChild(inputX);
    form.appendChild(inputY);
    document.body.appendChild(form);
    
    // Enviar formulario de forma asíncrona usando iframe oculto
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.name = 'positionFrame';
    document.body.appendChild(iframe);
    
    form.target = 'positionFrame';
    form.submit();
    
    // Limpiar después de un momento
    setTimeout(() => {
        document.body.removeChild(form);
        document.body.removeChild(iframe);
    }, 1000);
    
    document.removeEventListener('mousemove', drag);
    document.removeEventListener('mouseup', stopDrag);
    draggedElement = null;
}
