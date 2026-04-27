/**
 * SISTEMA DE FUNCIONALIDAD DE BOTONES Y ETIQUETAS
 * Funciona para todos los tipos de usuarios y todas las pantallas
 * Al hacer clic en cualquier botón o elemento etiquetado muestra los datos asociados
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar sistema en toda la página
    inicializarSistemaFuncionalidad();
    
    // Crear contenedor de información
    crearContenedorInformacion();
});

function inicializarSistemaFuncionalidad() {
    // Seleccionar TODOS los botones, enlaces y elementos interactivos
    const elementosInteractivos = document.querySelectorAll('button, a, [class*="btn"], input[type="button"], input[type="submit"]');
    
    elementosInteractivos.forEach((elemento, indice) => {
        // Agregar datos de funcionalidad si no los tiene
        if(!elemento.dataset.funcionalidad) {
            const nombreFuncion = obtenerNombreFuncion(elemento);
            const descripcionFuncion = obtenerDescripcionFuncion(elemento);
            const datosAsociados = obtenerDatosAsociados(elemento, indice);
            
            elemento.dataset.funcionalidad = nombreFuncion;
            elemento.dataset.descripcion = descripcionFuncion;
            elemento.dataset.datos = JSON.stringify(datosAsociados);
            elemento.dataset.indice = indice;
            
            // Agregar evento clic
            elemento.addEventListener('click', manejarClicElemento);
            
            // Agregar efecto visual indicando que es funcional
            elemento.style.position = 'relative';
            elemento.title = "Click para ver detalles de esta función";
        }
    });
    
    // Agregar evento para elementos dinamicos que se carguen despues
    const observador = new MutationObserver((mutaciones) => {
        mutaciones.forEach(mutacion => {
            mutacion.addedNodes.forEach(nodo => {
                if(nodo.tagName && ['BUTTON','A','INPUT'].includes(nodo.tagName)) {
                    if(!nodo.dataset.funcionalidad) {
                        inicializarElementoIndividual(nodo);
                    }
                }
            });
        });
    });
    
    observador.observe(document.body, { childList: true, subtree: true });
}

function inicializarElementoIndividual(elemento) {
    const indice = document.querySelectorAll('button, a, [class*="btn"]').length;
    const nombreFuncion = obtenerNombreFuncion(elemento);
    const descripcionFuncion = obtenerDescripcionFuncion(elemento);
    const datosAsociados = obtenerDatosAsociados(elemento, indice);
    
    elemento.dataset.funcionalidad = nombreFuncion;
    elemento.dataset.descripcion = descripcionFuncion;
    elemento.dataset.datos = JSON.stringify(datosAsociados);
    elemento.dataset.indice = indice;
    elemento.addEventListener('click', manejarClicElemento);
    elemento.title = "Click para ver detalles de esta función";
}

function obtenerNombreFuncion(elemento) {
    const texto = elemento.textContent.trim() || elemento.value || elemento.getAttribute('aria-label') || 'Función Genérica';
    return texto.substring(0, 30);
}

function obtenerDescripcionFuncion(elemento) {
    const clases = elemento.className;
    let descripcion = "Elemento interactivo del sistema";
    
    if(clases.includes('btn-primary')) descripcion = "Acción principal confirmatoria";
    else if(clases.includes('btn-danger')) descripcion = "Acción de eliminación o alerta";
    else if(clases.includes('btn-success')) descripcion = "Acción de aprobación o éxito";
    else if(clases.includes('btn-warning')) descripcion = "Acción de advertencia o edición";
    else if(clases.includes('btn-info')) descripcion = "Acción de información o consulta";
    else if(elemento.tagName === 'A' && elemento.getAttribute('href')) descripcion = "Enlace de navegación a otra pantalla";
    
    return descripcion;
}

function obtenerDatosAsociados(elemento, indice) {
    const datos = {
        id_elemento: elemento.id || `elemento_${indice}`,
        tipo: elemento.tagName.toLowerCase(),
        texto: obtenerNombreFuncion(elemento),
        fecha_ejecucion: new Date().toLocaleString('es-ES'),
        usuario_actual: obtenerUsuarioActual(),
        tipo_usuario: obtenerTipoUsuario(),
        pantalla_actual: window.location.pathname.split('/').pop() || 'Inicio',
        clases_aplicadas: elemento.className.split(' ').filter(c => c.length > 0),
        posicion_visual: elemento.getBoundingClientRect(),
        dimensiones: {
            ancho: elemento.offsetWidth,
            alto: elemento.offsetHeight
        },
        estado: {
            habilitado: !elemento.disabled,
            visible: elemento.offsetParent !== null,
            enfocado: document.activeElement === elemento
        }
    };
    
    // Agregar datos especificos segun pantalla
    const ruta = window.location.pathname;
    if(ruta.includes('/jefe/')) {
        datos.nivel_acceso = "Jefe / Administrador";
        datos.permisos = "Control total del sistema";
    } else if(ruta.includes('/empleado/')) {
        datos.nivel_acceso = "Empleado";
        datos.permisos = "Acceso limitado a funciones personales";
    }
    
    return datos;
}

function obtenerUsuarioActual() {
    const metaUsuario = document.querySelector('meta[name="usuario"]');
    return metaUsuario ? metaUsuario.content : "Usuario activo";
}

function obtenerTipoUsuario() {
    const ruta = window.location.pathname;
    if(ruta.includes('/jefe/')) return "JEFE";
    if(ruta.includes('/empleado/')) return "EMPLEADO";
    if(ruta.includes('/auth/')) return "INVITADO";
    return "GENERAL";
}

function manejarClicElemento(evento) {
    // No prevenir accion por defecto, solo agregar informacion
    const elemento = evento.currentTarget;
    const datos = JSON.parse(elemento.dataset.datos);
    const nombreFuncion = elemento.dataset.funcionalidad;
    const descripcion = elemento.dataset.descripcion;
    
    // Mostrar informacion asociada
    mostrarInformacionFuncion(nombreFuncion, descripcion, datos, elemento);
    
    // Registrar en consola para depuracion
    console.log(`✅ FUNCIÓN EJECUTADA: ${nombreFuncion}`);
    console.log('📊 DATOS ASOCIADOS:', datos);
}

function crearContenedorInformacion() {
    const contenedor = document.createElement('div');
    contenedor.id = 'informacion-funcion';
    contenedor.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 380px;
        max-width: 90vw;
        background: linear-gradient(135deg, #1a2744 0%, #2d3a57 100%);
        color: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        z-index: 999999;
        transform: translateY(150%);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        font-family: 'Segoe UI', system-ui, sans-serif;
        font-size: 14px;
        overflow: hidden;
    `;
    
    document.body.appendChild(contenedor);
}

function mostrarInformacionFuncion(nombre, descripcion, datos, elemento) {
    const contenedor = document.getElementById('informacion-funcion');
    
    contenedor.innerHTML = `
        <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <div style="width: 12px; height: 12px; background: #4ade80; border-radius: 50%; animation: pulso 1.5s infinite;"></div>
                <h3 style="margin:0; font-size: 16px; font-weight: 600;">${nombre}</h3>
            </div>
            <p style="margin:0; opacity: 0.8; font-size: 13px;">${descripcion}</p>
        </div>
        
        <div style="padding: 15px 20px; max-height: 280px; overflow-y: auto;">
            <div style="display: grid; gap: 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="opacity: 0.7;">Tipo elemento:</span>
                    <span style="font-weight: 500;">${datos.tipo}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="opacity: 0.7;">Tipo usuario:</span>
                    <span style="font-weight: 500;">${datos.tipo_usuario}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="opacity: 0.7;">Pantalla:</span>
                    <span style="font-weight: 500;">${datos.pantalla_actual}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="opacity: 0.7;">Nivel acceso:</span>
                    <span style="font-weight: 500;">${datos.nivel_acceso || 'Estándar'}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="opacity: 0.7;">Fecha ejecución:</span>
                    <span style="font-weight: 500;">${datos.fecha_ejecucion}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="opacity: 0.7;">Estado:</span>
                    <span style="font-weight: 500; color: ${datos.estado.habilitado ? '#4ade80' : '#f87171'};">${datos.estado.habilitado ? 'HABILITADO' : 'DESHABILITADO'}</span>
                </div>
            </div>
        </div>
        
        <div style="padding: 12px 20px; background: rgba(0,0,0,0.2); text-align: center; font-size: 12px; opacity: 0.7;">
            Esta información se asociará a la acción ejecutada
        </div>
        
        <style>
            @keyframes pulso {
                0% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
                70% { box-shadow: 0 0 0 10px rgba(74, 222, 128, 0); }
                100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
            }
        </style>
    `;
    
    // Mostrar contenedor con animacion
    contenedor.style.transform = 'translateY(0)';
    
    // Ocultar despues de 6 segundos
    setTimeout(() => {
        contenedor.style.transform = 'translateY(150%)';
    }, 6000);
    
    // Efecto visual en el elemento pulsado
    elemento.style.boxShadow = '0 0 0 3px rgba(212, 184, 92, 0.5)';
    setTimeout(() => {
        elemento.style.boxShadow = '';
    }, 1000);
}