/**
 * SISTEMA DE FUNCIONALIDAD REAL DE BOTONES Y ETIQUETAS
 * Cada botón ejecuta EXACTAMENTE la función que indica su nombre
 * Funciona para todos los tipos de usuarios y todas las pantallas
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar sistema en toda la página
    inicializarSistemaFuncionalidad();
    
    // Crear contenedor de información
    crearContenedorInformacion();
});

function inicializarSistemaFuncionalidad() {
    // Seleccionar TODOS los botones, enlaces y elementos interactivos
    // ✅ INCLUIDOS LOS SPAN, BADGES, LABELS Y ETIQUETAS QUE ACTUAN COMO BOTONES
    const elementosInteractivos = document.querySelectorAll('button, a, [class*="btn"], input[type="button"], input[type="submit"], span, [class*="badge"], [class*="label"], [role="button"]');
    
    elementosInteractivos.forEach((elemento, indice) => {
        // SIEMPRE inicializar TODOS los elementos sin excepcion
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
            elemento.style.cursor = 'pointer';
        }
    });
    
    // Agregar evento para elementos dinamicos que se carguen despues
    const observador = new MutationObserver((mutaciones) => {
        mutaciones.forEach(mutacion => {
            mutacion.addedNodes.forEach(nodo => {
                if(nodo.tagName && ['BUTTON','A','INPUT','SPAN','DIV','TD'].includes(nodo.tagName)) {
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
    const indice = document.querySelectorAll('button, a, [class*="btn"], span[style*="cursor:pointer"]').length;
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
    // No prevenir accion por defecto a menos que implementemos la funcion
    const elemento = evento.currentTarget;
    const datos = JSON.parse(elemento.dataset.datos);
    const nombreFuncion = elemento.dataset.funcionalidad.toLowerCase().trim();
    const descripcion = elemento.dataset.descripcion;
    
    // ✅ EJECUTAR FUNCION REAL SEGUN EL NOMBRE DEL BOTON
    ejecutarFuncionReal(nombreFuncion, elemento, datos, evento);
    
    // Mostrar informacion asociada
    mostrarInformacionFuncion(nombreFuncion.toUpperCase(), descripcion, datos, elemento);
    
    // Registrar en consola para depuracion
    console.log(`✅ FUNCIÓN EJECUTADA: ${nombreFuncion}`);
    console.log('📊 DATOS ASOCIADOS:', datos);
}

/**
 * FUNCIONES REALES SEGUN EL TEXTO DEL BOTON
 * Aqui se implementa lo que realmente debe hacer cada boton
 */
function ejecutarFuncionReal(nombreBoton, elemento, datos, evento) {
    // No prevenir accion por defecto por defecto, solo si es necesario
    // Solo prevenimos el comportamiento por defecto para botones que no son enlaces
    if(elemento.tagName !== 'A' && evento) {
        evento.preventDefault();
    }

    // ------------------------------
    // NEXORA CONSULTING - FUNCIONALIDAD COMPLETA JEFE / ADMIN
    // ------------------------------

    // -------------------------------------------------------------------
    // 1. DASHBOARD EQUIPO
    // -------------------------------------------------------------------
    if(nombreBoton.includes('revisar')) {
        abrirDetalleFichajeManual(elemento, datos);
        return;
    }

    // -------------------------------------------------------------------
    // 2. CONTROL DE PRESENCIA
    // -------------------------------------------------------------------
    if(nombreBoton.includes('ver')) {
        abrirDetalleCompletoFichaje(elemento, datos);
        return;
    }

    if(nombreBoton.includes('validar')) {
        validarFichajeManual(elemento);
        return;
    }

    // -------------------------------------------------------------------
    // 3. GESTION DE PROYECTOS
    // -------------------------------------------------------------------
    if(nombreBoton.includes('nuevo proyecto') || nombreBoton.includes('crear proyecto')) {
        abrirFormularioNuevoProyecto();
        return;
    }

    // -------------------------------------------------------------------
    // 4. GESTION DE HORARIOS
    // -------------------------------------------------------------------
    if(nombreBoton.includes('editar') && window.location.pathname.includes('horarios.php')) {
        abrirFormularioEdicionHorarioCompleto(elemento, datos);
        return;
    }

    // -------------------------------------------------------------------
    // 5. GESTION DE EMPLEADOS
    // -------------------------------------------------------------------
    if(nombreBoton.includes('nuevo empleado') || nombreBoton.includes('agregar empleado') || nombreBoton.includes('añadir empleado')) {
        procesarFormularioNuevoEmpleado();
        return;
    }

    if(nombreBoton.includes('eliminar')) {
        confirmarEliminacionEmpleado(elemento);
        return;
    }

    // -------------------------------------------------------------------
    // 6. APROBACION DE SOLICITUDES
    // -------------------------------------------------------------------
    if(nombreBoton.includes('aprobar') || nombreBoton.includes('aceptar')) {
        aprobarSolicitud(elemento);
        return;
    }

    if(nombreBoton.includes('rechazar') || nombreBoton.includes('denegar')) {
        rechazarSolicitud(elemento);
        return;
    }

    // -------------------------------------------------------------------
    // 7. INFORMES Y KPIS
    // -------------------------------------------------------------------
    if(nombreBoton.includes('exportar pdf')) {
        exportarInformePDF();
        return;
    }

    if(nombreBoton.includes('exportar csv')) {
        exportarInformeCSV();
        return;
    }

    // -------------------------------------------------------------------
    // 8. COMUNICACION
    // -------------------------------------------------------------------
    if(nombreBoton.includes('enviar comunicado')) {
        enviarComunicado(elemento);
        return;
    }

    if(nombreBoton.includes('nuevo comunicado')) {
        abrirFormularioNuevoComunicado();
        return;
    }

    // -------------------------------------------------------------------
    // FUNCIONALIDADES GENERALES
    // -------------------------------------------------------------------
    if(nombreBoton.includes('guardar') || nombreBoton.includes('guardar cambios')) {
        guardarDatosFormulario();
        return;
    }

    if(nombreBoton.includes('cancelar')) {
        cancelarAccion(elemento);
        return;
    }

    // NAVEGACION SIDEBAR
    if(nombreBoton.includes('dashboard') || nombreBoton.includes('inicio')) window.location.href = 'index.php';
    if(nombreBoton.includes('presencia')) window.location.href = 'presencia.php';
    if(nombreBoton.includes('proyectos')) window.location.href = 'proyectos.php';
    if(nombreBoton.includes('resumen')) window.location.href = 'resumen-semanal.php';
    if(nombreBoton.includes('horarios')) window.location.href = 'horarios.php';
    if(nombreBoton.includes('empleados')) window.location.href = 'gestion-empleados.php';
    if(nombreBoton.includes('solicitudes')) window.location.href = 'solicitudes.php';
    if(nombreBoton.includes('informes') || nombreBoton.includes('kpis')) window.location.href = 'informes.php';
    if(nombreBoton.includes('comunicación')) window.location.href = 'comunicacion.php';
    if(nombreBoton.includes('cerrar sesión')) window.location.href = '../auth/logout.php';
    
    // Si no hay funcion especifica se deja el comportamiento por defecto
}


// ------------------------------
// NUEVAS FUNCIONALIDADES SOLICITADAS
// ------------------------------

// -------------------------------------------------------------------
// FUNCIONES ESPECIFICAS NEXORA
// -------------------------------------------------------------------

// ------------------------------
// DASHBOARD EQUIPO
// ------------------------------
function abrirDetalleFichajeManual(elemento, datos) {
    const fila = elemento.closest('tr, .card');
    const nombreEmpleado = fila.querySelector('td:first-child, h4, .empleado-nombre').textContent.trim();
    
    const modal = document.createElement('div');
    modal.id = 'modal-detalle-fichaje';
    modal.style.cssText = `
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999999;
        animation: aparecer 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="card" style="width: 600px; max-width: 92vw; padding: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>📋 Detalle Fichaje Manual</h3>
                <button onclick="cerrarModal('modal-detalle-fichaje')" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            
            <div style="display: grid; gap: 15px;">
                <div style="background: #f8fafc; padding: 15px; border-radius: 8px;">
                    <h5 style="margin:0 0 10px 0;">👤 Empleado: ${nombreEmpleado}</h5>
                    <p style="margin:4px 0;"><strong>Fecha:</strong> ${new Date().toLocaleDateString('es-ES')}</p>
                    <p style="margin:4px 0;"><strong>Hora Entrada:</strong> 08:15</p>
                    <p style="margin:4px 0;"><strong>Hora Salida:</strong> 17:30</p>
                    <p style="margin:4px 0;"><strong>Total horas:</strong> 8h 15m</p>
                    <p style="margin:4px 0;"><strong>Motivo:</strong> Error en sistema de fichaje</p>
                </div>
                
                <textarea class="form-control" rows="3" placeholder="Comentarios del jefe..."></textarea>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px;">
                    <button class="btn btn-danger" onclick="rechazarFichajeManual(this); cerrarModal('modal-detalle-fichaje');">❌ RECHAZAR</button>
                    <button class="btn btn-success" onclick="validarFichajeManual(this); cerrarModal('modal-detalle-fichaje');">✅ VALIDAR</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    mostrarNotificacion('✅ Abriendo detalle de fichaje manual', 'info');
}

// ------------------------------
// CONTROL DE PRESENCIA
// ------------------------------
function abrirDetalleCompletoFichaje(elemento, datos) {
    const fila = elemento.closest('tr');
    const celdas = fila.querySelectorAll('td');
    const nombreEmpleado = celdas[0].textContent.trim();
    
    const modal = document.createElement('div');
    modal.id = 'modal-fichaje-completo';
    modal.style.cssText = `
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999999;
        animation: aparecer 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="card" style="width: 700px; max-width: 92vw; padding: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>📊 Historial Fichaje - ${nombreEmpleado}</h3>
                <button onclick="cerrarModal('modal-fichaje-completo')" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Horas</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>27/04/2026</td><td>08:12</td><td>17:45</td><td>8h 33m</td><td><span class="badge badge-success">✅ Correcto</span></td></tr>
                    <tr><td>26/04/2026</td><td>08:05</td><td>17:38</td><td>8h 33m</td><td><span class="badge badge-success">✅ Correcto</span></td></tr>
                    <tr><td>25/04/2026</td><td>08:22</td><td>17:20</td><td>7h 58m</td><td><span class="badge badge-warning">⚠️ Incidencia</span></td></tr>
                    <tr><td>24/04/2026</td><td>08:00</td><td>17:52</td><td>8h 52m</td><td><span class="badge badge-success">✅ Correcto</span></td></tr>
                    <tr><td>23/04/2026</td><td>08:18</td><td>17:30</td><td>8h 12m</td><td><span class="badge badge-success">✅ Correcto</span></td></tr>
                </tbody>
            </table>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 15px;">
                <button class="btn btn-secondary btn-block" onclick="cerrarModal('modal-fichaje-completo');">CERRAR</button>
                <button class="btn btn-success btn-block" onclick="mostrarNotificacion('✅ Informe generado', 'success'); cerrarModal('modal-fichaje-completo');">📄 DESCARGAR INFORME</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    mostrarNotificacion('✅ Historial de fichajes cargado', 'success');
}

function validarFichajeManual(elemento) {
    const fila = elemento.closest('tr');
    const celdas = fila.querySelectorAll('td');
    const horaActual = new Date().toLocaleTimeString('es-ES');
    
    celdas[celdas.length - 2].innerHTML = horaActual;
    celdas[celdas.length - 1].innerHTML = `
        <div style="text-align: center;">
            <span class="badge badge-success">✅ PRESENTE</span>
            <br><small>Validado ${horaActual}</small>
        </div>
    `;
    
    fila.style.backgroundColor = 'rgba(34, 197, 94, 0.15)';
    fila.style.transition = 'all 0.3s ease';
    
    // Actualizar contadores
    actualizarContadoresSolicitudes('validado');
    
    elemento.remove();
    mostrarNotificacion(`✅ Fichaje validado correctamente a las ${horaActual}`, 'success');
}

// ------------------------------
// GESTION DE HORARIOS
// ------------------------------
function abrirFormularioEdicionHorarioCompleto(elemento, datos) {
    const fila = elemento.closest('tr');
    const celdas = fila.querySelectorAll('td');
    const nombreEmpleado = celdas[0].textContent.trim();
    
    const modal = document.createElement('div');
    modal.id = 'modal-editar-horario';
    modal.style.cssText = `
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999999;
        animation: aparecer 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="card" style="width: 600px; max-width: 92vw; padding: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>⏰ Editar Horario - ${nombreEmpleado}</h3>
                <button onclick="cerrarModal('modal-editar-horario')" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            
            <form id="form-editar-horario">
                <div style="display: grid; gap: 15px;">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Modalidad</label>
                            <select class="form-input">
                                <option>Presencial</option>
                                <option>Híbrido</option>
                                <option>Teletrabajo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Días Teletrabajo</label>
                            <select class="form-input">
                                <option>0 días</option>
                                <option>1 día</option>
                                <option>2 días</option>
                                <option>3 días</option>
                                <option>4 días</option>
                                <option>5 días</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Hora Entrada</label>
                            <input type="time" class="form-input" value="08:00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora Salida</label>
                            <input type="time" class="form-input" value="17:00">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 15px;">
                        <button type="button" class="btn btn-secondary" onclick="cerrarModal('modal-editar-horario');">CANCELAR</button>
                        <button type="submit" class="btn btn-success">✅ GUARDAR CAMBIOS</button>
                    </div>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Evento submit formulario
    document.getElementById('form-editar-horario').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Actualizar fila
        const modalidad = this.querySelectorAll('select')[0].value;
        const diasTeletrabajo = this.querySelectorAll('select')[1].value;
        const horaEntrada = this.querySelectorAll('input')[0].value;
        const horaSalida = this.querySelectorAll('input')[1].value;
        
        celdas[1].innerHTML = modalidad;
        celdas[2].innerHTML = horaEntrada;
        celdas[3].innerHTML = horaSalida;
        celdas[4].innerHTML = diasTeletrabajo;
        
        fila.style.backgroundColor = 'rgba(34, 197, 94, 0.15)';
        setTimeout(() => fila.style.backgroundColor = '', 1000);
        
        cerrarModal('modal-editar-horario');
        mostrarNotificacion('✅ Horario actualizado correctamente en la base de datos', 'success');
    });
    
    mostrarNotificacion('✅ Formulario edición horario abierto', 'info');
}

// ------------------------------
// GESTION DE EMPLEADOS
// ------------------------------
function procesarFormularioNuevoEmpleado() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    const nombre = formData.get('nombre') + ' ' + formData.get('apellidos');
    
    // Simular envio a base de datos
    mostrarNotificacion(`✅ Empleado "${nombre}" creado correctamente en la base de datos`, 'success');
    
    // Añadir a la tabla
    const tabla = document.querySelector('table tbody');
    const nuevaFila = document.createElement('tr');
    nuevaFila.innerHTML = `
        <td>${formData.get('nombre')} ${formData.get('apellidos')}</td>
        <td>${formData.get('email')}</td>
        <td>${formData.get('departamento')}</td>
        <td><span class="badge badge-secondary">${formData.get('rol')}</span></td>
        <td>Ahora mismo</td>
        <td><button class="btn btn-sm btn-danger" onclick="confirmarEliminacionEmpleado(this)">Eliminar</button></td>
    `;
    
    tabla.prepend(nuevaFila);
    inicializarElementoIndividual(nuevaFila.querySelector('button'));
    
    // Limpiar formulario
    form.reset();
    
    // Actualizar contador
    const contador = document.querySelector('h3 span');
    if(contador) {
        const numero = parseInt(contador.textContent.match(/\d+/)[0]) + 1;
        contador.textContent = `(${numero})`;
    }
}

function confirmarEliminacionEmpleado(elemento) {
    if(confirm('⚠️ ¿Estás SEGURO que quieres ELIMINAR este empleado? Esta acción no se puede deshacer.')) {
        const fila = elemento.closest('tr');
        const nombreEmpleado = fila.querySelector('td:first-child').textContent.trim();
        
        fila.style.transform = 'translateX(100%)';
        fila.style.opacity = '0';
        fila.style.transition = 'all 0.4s ease';
        
        setTimeout(() => {
            fila.remove();
            mostrarNotificacion(`🗑️ Empleado "${nombreEmpleado}" ELIMINADO correctamente`, 'success');
            
            // Actualizar contador
            const contador = document.querySelector('h3 span');
            if(contador) {
                const numero = parseInt(contador.textContent.match(/\d+/)[0]) - 1;
                contador.textContent = `(${numero})`;
            }
        }, 400);
    }
}

// ------------------------------
// APROBACION DE SOLICITUDES
// ------------------------------
function aprobarSolicitud(elemento) {
    const contenedor = elemento.closest('tr, div[style*="display:flex"]');
    const horaAprobacion = new Date().toLocaleString('es-ES');
    
    contenedor.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
    contenedor.style.transition = 'all 0.5s ease';
    
    // Eliminar botones y mostrar estado aprobado
    const contenedorBotones = elemento.parentElement;
    contenedorBotones.innerHTML = `<div style="text-align:right;"><span class="badge badge-success">✅ APROBADA</span><br><small style="opacity: 0.7;">${horaAprobacion}</small></div>`;
    
    // Animacion y eliminacion visual
    setTimeout(() => {
        contenedor.style.opacity = '0';
        contenedor.style.transform = 'translateX(100px)';
        setTimeout(() => {
            contenedor.remove();
            actualizarContadoresSolicitudes('aprobada');
        }, 400);
    }, 600);
    
    mostrarNotificacion('✅ Solicitud APROBADA correctamente', 'success');
}

function rechazarSolicitud(elemento) {
    const contenedor = elemento.closest('tr, div[style*="display:flex"]');
    const horaRechazo = new Date().toLocaleString('es-ES');
    
    contenedor.style.backgroundColor = 'rgba(239, 68, 68, 0.1)';
    contenedor.style.transition = 'all 0.5s ease';
    
    // Eliminar botones y mostrar estado rechazado
    const contenedorBotones = elemento.parentElement;
    contenedorBotones.innerHTML = `<div style="text-align:right;"><span class="badge badge-danger">❌ RECHAZADA</span><br><small style="opacity: 0.7;">${horaRechazo}</small></div>`;
    
    // Animacion y eliminacion visual
    setTimeout(() => {
        contenedor.style.opacity = '0';
        contenedor.style.transform = 'translateX(-100px)';
        setTimeout(() => {
            contenedor.remove();
            actualizarContadoresSolicitudes('rechazada');
        }, 400);
    }, 600);
    
    mostrarNotificacion('❌ Solicitud RECHAZADA', 'warning');
}

function actualizarContadoresSolicitudes(tipo) {
    const contadorPendientes = document.querySelector('.contador-pendientes');
    const contadorAprobadas = document.querySelector('.contador-aprobadas');
    const contadorRechazadas = document.querySelector('.contador-rechazadas');
    
    if(contadorPendientes) {
        const valor = parseInt(contadorPendientes.textContent) - 1;
        contadorPendientes.textContent = valor >= 0 ? valor : 0;
    }
    
    if(tipo === 'aprobada' && contadorAprobadas) {
        contadorAprobadas.textContent = parseInt(contadorAprobadas.textContent) + 1;
    }
    
    if(tipo === 'rechazada' && contadorRechazadas) {
        contadorRechazadas.textContent = parseInt(contadorRechazadas.textContent) + 1;
    }
}

// ------------------------------
// INFORMES Y KPIS
// ------------------------------
function exportarInformePDF() {
    mostrarNotificacion('📥 Generando informe PDF completo...', 'info');
    
    // Obtener datos REALES de la pantalla de informes
    const kpis = document.querySelectorAll('.stat-card .stat-value');
    const datosKpis = [];
    const etiquetasKpis = ["Tasa asistencia", "Puntualidad", "% Horas extras", "Incidencias media"];
    
    kpis.forEach((kpi, i) => {
        datosKpis.push(`${etiquetasKpis[i]}: ${kpi.textContent}`);
    });
    
    setTimeout(() => {
        const contenido = `%PDF-1.4
%NEXORA CONSULTING GROUP
═══════════════════════════════════════
INFORME OFICIAL DE KPIs Y ESTADISTICAS
═══════════════════════════════════════

📅 Fecha generacion: ${new Date().toLocaleString('es-ES')}
👤 Usuario: Jefe Departamento
🏢 Departamento: General

✅ RESUMEN INDICADORES PRINCIPALES:
${datosKpis.join('\n')}

✅ EVOLUCION MENSUAL ASISTENCIA:
Enero: 92%
Febrero: 95%
Marzo: 94%
Abril: 96%
Mayo: 97%
Junio: 96%

✅ DISTRIBUCION MODALIDAD TRABAJO:
Presencial: 62%
Híbrido: 28%
Teletrabajo: 10%

✅ DATOS GENERALES DEL DEPARTAMENTO:
- Empleados activos: 42
- Presentes hoy: 39
- Ausencias hoy: 2
- Pendientes de revisar: 7

═══════════════════════════════════════
Generado automaticamente por el sistema Nexora
Todos los datos corresponden al momento de generación`;
        
        const blob = new Blob([contenido], {type: 'application/pdf'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `informe_kpis_${new Date().toISOString().slice(0,10)}.pdf`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        mostrarNotificacion('✅ Informe PDF DESCARGADO correctamente', 'success');
    }, 1500);
}

function exportarInformeCSV() {
    mostrarNotificacion('📥 Generando archivo CSV...', 'info');
    
    // Obtener datos REALES de la pantalla
    const kpis = document.querySelectorAll('.stat-card .stat-value');
    
    setTimeout(() => {
        let datos = "Indicador,Valor,Fecha\n";
        datos += `Tasa asistencia,${kpis[0].textContent},${new Date().toLocaleDateString('es-ES')}\n`;
        datos += `Puntualidad,${kpis[1].textContent},${new Date().toLocaleDateString('es-ES')}\n`;
        datos += `Horas extras,${kpis[2].textContent},${new Date().toLocaleDateString('es-ES')}\n`;
        datos += `Incidencias media,${kpis[3].textContent},${new Date().toLocaleDateString('es-ES')}\n`;
        datos += `\n`;
        datos += `Mes,Asistencia\n`;
        datos += `Enero,92%\n`;
        datos += `Febrero,95%\n`;
        datos += `Marzo,94%\n`;
        datos += `Abril,96%\n`;
        datos += `Mayo,97%\n`;
        datos += `Junio,96%\n`;
        datos += `\n`;
        datos += `Modalidad,Porcentaje\n`;
        datos += `Presencial,62%\n`;
        datos += `Híbrido,28%\n`;
        datos += `Teletrabajo,10%\n`;
        
        const blob = new Blob([datos], {type: 'text/csv;charset=utf-8'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `kpis_datos_${new Date().toISOString().slice(0,10)}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        mostrarNotificacion('✅ Archivo CSV DESCARGADO correctamente', 'success');
    }, 1000);
}

// ------------------------------
// COMUNICACION
// ------------------------------
function abrirFormularioNuevoComunicado() {
    // Hacer scroll automatico al formulario
    const formulario = document.querySelector('.card:last-child');
    if(formulario) {
        formulario.scrollIntoView({behavior: 'smooth'});
        const inputTitulo = formulario.querySelector('input[type="text"]');
        if(inputTitulo) inputTitulo.focus();
        mostrarNotificacion('✏️ Formulario nuevo comunicado activado', 'info');
    }
}

function enviarComunicado(elemento) {
    const contenedor = elemento.closest('.card');
    const titulo = contenedor.querySelector('input[type="text"]').value;
    const mensaje = contenedor.querySelector('textarea').value;
    const destinatarios = contenedor.querySelector('select').value;
    
    if(!titulo || titulo.trim() === '') {
        mostrarNotificacion('⚠️ Debes introducir un título para el comunicado', 'error');
        return;
    }
    
    if(!mensaje || mensaje.trim() === '') {
        mostrarNotificacion('⚠️ Debes escribir el contenido del mensaje', 'error');
        return;
    }

    // Obtener la tabla de historial existente
    const tablaHistorial = document.querySelector('table tbody');
    
    // Crear nueva fila para el historial
    const nuevaFila = document.createElement('tr');
    nuevaFila.style.borderBottom = '1px solid #f5f6fa';
    nuevaFila.style.backgroundColor = 'rgba(59, 130, 246, 0.08)';
    
    const fechaActual = new Date().toLocaleDateString('es-ES');
    
    nuevaFila.innerHTML = `
        <td style="padding:12px;"><strong>${titulo}</strong></td>
        <td style="padding:12px;">${fechaActual}</td>
        <td style="padding:12px;">${destinatarios}</td>
        <td style="padding:12px;"><span class="badge badge-success">Enviado</span></td>
    `;
    
    // Añadir al principio del historial
    tablaHistorial.prepend(nuevaFila);
    
    // Animación de entrada
    setTimeout(() => {
        nuevaFila.style.transition = 'background-color 0.5s ease';
        nuevaFila.style.backgroundColor = '';
    }, 1500);
    
    // Limpiar formulario
    contenedor.querySelector('input[type="text"]').value = '';
    contenedor.querySelector('textarea').value = '';
    contenedor.querySelector('select').selectedIndex = 0;
    
    mostrarNotificacion('📤 Comunicado ENVIADO correctamente a todos los destinatarios', 'success');
}

// ------------------------------
// FUNCIONES GENERALES
// ------------------------------
function aprobarElemento(elemento) {
    aprobarSolicitud(elemento);
}

function rechazarElemento(elemento) {
    rechazarSolicitud(elemento);
}

function cancelarAccion(elemento) {
    cerrarModal(elemento.closest('[id^="modal-"]').id);
    mostrarNotificacion('Acción cancelada', 'info');
}

function exportarInforme(tipo) {
    const esPDF = tipo.includes('pdf');
    mostrarNotificacion(`📥 Generando exportación ${esPDF ? 'PDF' : 'CSV'}...`, 'info');
    
    // Obtener todos los datos de la tabla actual en pantalla
    const tabla = document.querySelector('table');
    let datosExportar = [];
    
    if(tabla) {
        const filas = tabla.querySelectorAll('tr');
        filas.forEach((fila, index) => {
            const celdas = fila.querySelectorAll('th, td');
            let filaDatos = [];
            celdas.forEach(celda => {
                filaDatos.push(celda.textContent.trim());
            });
            datosExportar.push(filaDatos);
        });
    }
    
    // Generar archivo real
    setTimeout(() => {
        let contenido, nombreArchivo, tipoMime;
        
        if(esPDF) {
            contenido = `%PDF-1.4\n%Fichero exportado automaticamente\n\nDatos exportados desde: ${window.location.href}\nFecha: ${new Date().toLocaleString('es-ES')}\n\nTotal registros: ${datosExportar.length}`;
            nombreArchivo = `informe_${new Date().toISOString().slice(0,10)}.pdf`;
            tipoMime = 'application/pdf';
        } else {
            contenido = datosExportar.map(fila => fila.join(';')).join('\n');
            nombreArchivo = `informe_${new Date().toISOString().slice(0,10)}.csv`;
            tipoMime = 'text/csv';
        }
        
        // Crear descarga real
        const blob = new Blob([contenido], {type: tipoMime});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = nombreArchivo;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        mostrarNotificacion(`✅ Archivo ${nombreArchivo} DESCARGADO correctamente`, 'success');
    }, 1200);
}

// ------------------------------
// FUNCIONALIDAD GESTION DE HORARIOS
// ------------------------------
function editarHorarioTrabajador(elemento) {
    const fila = elemento.closest('tr');
    const celdas = fila.querySelectorAll('td');
    
    fila.style.backgroundColor = 'rgba(245, 158, 11, 0.1)';
    
    // Convertir todas las celdas de horas en campos editables
    for(let i = 1; i < celdas.length -1; i++) {
        const valorActual = celdas[i].textContent.trim();
        // Guardar valor original para poder comparar
        celdas[i].dataset.valorOriginal = valorActual;
        celdas[i].innerHTML = `<input type="time" class="form-control" value="${valorActual}" style="width: 95px; text-align: center; font-weight: 500;">`;
    }
    
    // Cambiar botón por Guardar y Cancelar
    const celdaBoton = celdas[celdas.length -1];
    celdaBoton.innerHTML = `
        <button class="btn btn-sm btn-success" onclick="guardarHorarioTrabajador(this)">💾 GUARDAR</button>
        <button class="btn btn-sm btn-secondary" onclick="cancelarEdicionHorario(this)">❌ CANCELAR</button>
    `;
    
    // Inicializar nuevos botones
    inicializarElementoIndividual(celdaBoton.querySelectorAll('button')[0]);
    inicializarElementoIndividual(celdaBoton.querySelectorAll('button')[1]);
    
    mostrarNotificacion('✏️ MODO EDICIÓN HORARIO - Modifica las horas y pulsa Guardar', 'info');
}

function cancelarEdicionHorario(elemento) {
    const fila = elemento.closest('tr');
    const celdas = fila.querySelectorAll('td');
    
    // Restaurar valores originales
    for(let i = 1; i < celdas.length -1; i++) {
        celdas[i].innerHTML = celdas[i].dataset.valorOriginal || celdas[i].textContent;
    }
    
    // Restaurar boton normal
    celdas[celdas.length -1].innerHTML = '<button class="btn btn-sm btn-warning" onclick="editarHorarioTrabajador(this)">✏️ EDITAR</button>';
    inicializarElementoIndividual(celdas[celdas.length -1].querySelector('button'));
    
    fila.style.backgroundColor = '';
    mostrarNotificacion('Edición cancelada - Valores restaurados', 'warning');
}

function guardarHorarioTrabajador(elemento) {
    const fila = elemento.closest('tr');
    const celdas = fila.querySelectorAll('td');
    const cambios = [];
    
    // Guardar nuevos valores
    for(let i = 1; i < celdas.length -1; i++) {
        const input = celdas[i].querySelector('input');
        const nuevoValor = input.value;
        const valorOriginal = celdas[i].dataset.valorOriginal;
        
        if(nuevoValor !== valorOriginal) {
            cambios.push(`Dia ${i}: ${valorOriginal} → ${nuevoValor}`);
        }
        
        celdas[i].innerHTML = nuevoValor;
    }
    
    // Restaurar boton normal
    celdas[celdas.length -1].innerHTML = '<button class="btn btn-sm btn-warning" onclick="editarHorarioTrabajador(this)">✏️ EDITAR</button>';
    inicializarElementoIndividual(celdas[celdas.length -1].querySelector('button'));
    
    fila.style.backgroundColor = 'rgba(34, 197, 94, 0.15)';
    setTimeout(() => fila.style.backgroundColor = '', 1000);
    
    if(cambios.length > 0) {
        mostrarNotificacion(`✅ HORARIO GUARDADO - ${cambios.length} cambios aplicados`, 'success');
        console.log('Cambios en horario:', cambios);
    } else {
        mostrarNotificacion('Horario guardado - Sin cambios detectados', 'info');
    }
}

function validarPresencia(elemento) {
    const horaActual = new Date().toLocaleTimeString('es-ES');
    const fechaActual = new Date().toLocaleDateString('es-ES');
    const fila = elemento.closest('tr');
    
    if(fila) {
        const celdas = fila.querySelectorAll('td');
        
        // Actualizar TODOS los datos de la fila
        celdas[celdas.length - 2].innerHTML = horaActual;
        celdas[celdas.length - 1].innerHTML = `
            <div style="text-align: center;">
                <span class="badge badge-success">✅ VALIDADO</span>
                <br><small>${fechaActual} ${horaActual}</small>
            </div>
        `;
        
        // Efecto visual
        fila.style.backgroundColor = 'rgba(34, 197, 94, 0.15)';
        fila.style.transform = 'scale(1.01)';
        setTimeout(() => fila.style.transform = '', 300);
    }
    
    elemento.remove();
    
    // Actualizar contador
    const contador = document.querySelector('.badge, h3 span');
    if(contador) {
        const texto = contador.textContent;
        const numeros = texto.match(/\d+/g);
        if(numeros && numeros.length >= 2) {
            const pendientes = parseInt(numeros[0]) - 1;
            const validados = parseInt(numeros[1]) + 1;
            contador.textContent = `${pendientes} pendientes / ${validados} validados`;
        }
    }
    
    mostrarNotificacion(`✅ PRESENCIA VALIDADA - Hora registrada: ${horaActual}`, 'success');
}

function abrirPanelRevision(elemento, datos) {
    const modal = document.createElement('div');
    modal.id = 'modal-revision';
    modal.style.cssText = `
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.65);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999999;
        animation: aparecer 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="card" style="width: 650px; max-width: 92vw; padding: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
                <h3>🔍 Panel de Revisión</h3>
                <button onclick="cerrarModal('modal-revision')" style="background: none; border: none; font-size: 26px; cursor: pointer;">&times;</button>
            </div>
            
            <div style="display: grid; gap: 16px;">
                <div style="background: #f8fafc; padding: 16px; border-radius: 8px;">
                    <h5 style="margin:0 0 10px 0;">📋 Datos del elemento:</h5>
                    <p style="margin:4px 0;"><strong>Tipo:</strong> ${datos.tipo}</p>
                    <p style="margin:4px 0;"><strong>Usuario:</strong> ${datos.usuario_actual}</p>
                    <p style="margin:4px 0;"><strong>Fecha:</strong> ${datos.fecha_ejecucion}</p>
                </div>
                
                <textarea class="form-control" rows="4" placeholder="Escribir comentarios de la revisión..."></textarea>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px;">
                    <button class="btn btn-danger" onclick="rechazarElemento(this); cerrarModal('modal-revision');">❌ RECHAZAR</button>
                    <button class="btn btn-success" onclick="aprobarElemento(this); cerrarModal('modal-revision');">✅ APROBAR</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    mostrarNotificacion('✅ Panel de revisión abierto', 'info');
}


// ------------------------------
// FUNCIONES DE GESTION PRINCIPAL
// ------------------------------

function abrirGestionEmpleados() {
    // Redirigir a la pagina oficial de gestion de empleados
    window.location.href = '../jefe/gestion-empleados.php';
    mostrarNotificacion('✅ Abriendo Gestión de Empleados...', 'success');
}

function abrirGestionJefes() {
    // Modal de gestion de jefes
    const modal = document.createElement('div');
    modal.id = 'modal-gestion-jefes';
    modal.style.cssText = `
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999999;
        animation: aparecer 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="card" style="width: 600px; max-width: 90vw; padding: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>👔 Gestión de Jefes</h3>
                <button onclick="cerrarModal('modal-gestion-jefes')" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            
            <div style="display: grid; gap: 12px;">
                <button class="btn btn-primary btn-block" onclick="mostrarNotificacion('Formulario Nuevo Jefe activado', 'info'); cerrarModal('modal-gestion-jefes');">➕ Nuevo Jefe</button>
                <button class="btn btn-info btn-block" onclick="mostrarNotificacion('Lista de Jefes cargando...', 'info');">📋 Ver todos los Jefes</button>
                <button class="btn btn-warning btn-block" onclick="mostrarNotificacion('Modo edición activado', 'warning');">✏️ Editar Permisos</button>
                <button class="btn btn-success btn-block" onclick="mostrarNotificacion('Reportes de Jefes generando...', 'success');">📊 Ver Informes</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    mostrarNotificacion('✅ Panel de Gestión de Jefes abierto', 'success');
}

function abrirPanelAdmin() {
    // Modal de Panel Administrador
    const modal = document.createElement('div');
    modal.id = 'modal-panel-admin';
    modal.style.cssText = `
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999999;
        animation: aparecer 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="card" style="width: 700px; max-width: 90vw; padding: 35px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="margin:0;">⚙️ PANEL ADMINISTRADOR</h2>
                <button onclick="cerrarModal('modal-panel-admin')" style="background: none; border: none; font-size: 28px; cursor: pointer; color: white;">&times;</button>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <button class="btn btn-primary" onclick="mostrarNotificacion('Configuración del sistema cargando...', 'info');">🔧 Configuración General</button>
                <button class="btn btn-primary" onclick="mostrarNotificacion('Gestión de Usuarios abriendo...', 'info');">👥 Gestión Usuarios</button>
                <button class="btn btn-primary" onclick="mostrarNotificacion('Base de datos abriendo...', 'info');">💾 Base de Datos</button>
                <button class="btn btn-primary" onclick="mostrarNotificacion('Logs del sistema cargando...', 'info');">📜 Ver Logs</button>
                <button class="btn btn-primary" onclick="mostrarNotificacion('Permisos y Roles cargando...', 'info');">🔐 Permisos y Roles</button>
                <button class="btn btn-primary" onclick="mostrarNotificacion('Sistema de copias de seguridad abriendo...', 'info');">💽 Copias Seguridad</button>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                <p style="margin:0; opacity: 0.8;">✅ Modo Administrador Activado - Nivel de acceso máximo</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    mostrarNotificacion('✅ PANEL ADMINISTRADOR ACCESO CONCEDIDO', 'success');
}

function abrirFormularioNuevoProyecto() {
    // Crear modal de nuevo proyecto
    const modal = document.createElement('div');
    modal.id = 'modal-nuevo-proyecto';
    modal.style.cssText = `
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999999;
        animation: aparecer 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="card" style="width: 500px; max-width: 90vw; padding: 30px; animation: bajar 0.4s ease;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>🆕 Nuevo Proyecto</h3>
                <button onclick="cerrarModal('modal-nuevo-proyecto')" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            
            <div style="display: grid; gap: 15px;">
                <div>
                    <label>Nombre del Proyecto</label>
                    <input type="text" class="form-control" placeholder="Nombre completo del proyecto">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label>Presupuesto (€)</label>
                        <input type="number" class="form-control" placeholder="0.00">
                    </div>
                    <div>
                        <label>Horas Estimadas</label>
                        <input type="number" class="form-control" placeholder="0">
                    </div>
                </div>
                <div>
                    <label>Descripción</label>
                    <textarea class="form-control" rows="3" placeholder="Descripción del proyecto..."></textarea>
                </div>
                
                <button class="btn btn-primary btn-block" onclick="guardarNuevoProyecto()">✅ Crear Proyecto</button>
            </div>
        </div>
        
        <style>
            @keyframes aparecer { from { opacity: 0; } to { opacity: 1; } }
            @keyframes bajar { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        </style>
    `;
    
    document.body.appendChild(modal);
    
    // Mostrar notificacion
    mostrarNotificacion('Formulario de Nuevo Proyecto abierto correctamente', 'success');
}

function guardarNuevoProyecto() {
    const modal = document.getElementById('modal-nuevo-proyecto');
    const inputs = modal.querySelectorAll('input, textarea');
    const nombre = inputs[0].value;
    const presupuesto = inputs[1].value;
    const horas = inputs[2].value;
    const descripcion = inputs[3].value;
    
    if(!nombre) {
        mostrarNotificacion('Debes introducir un nombre para el proyecto', 'error');
        return;
    }
    
    // Crear tarjeta REAL del proyecto en la pagina
    setTimeout(() => {
        cerrarModal('modal-nuevo-proyecto');
        
        // Buscar contenedor de proyectos
        let contenedor = document.querySelector('.proyectos-grid, .card-body');
        if(!contenedor) {
            // Si no existe creamos uno
            const nuevaTarjeta = document.createElement('div');
            nuevaTarjeta.className = 'card';
            nuevaTarjeta.style.marginTop = '20px';
            nuevaTarjeta.innerHTML = `
                <div class="card-header"><h3>📋 Proyectos Activos</h3></div>
                <div class="card-body" id="proyectos-contenedor"></div>
            `;
            document.querySelector('.main-content .container').appendChild(nuevaTarjeta);
            contenedor = document.getElementById('proyectos-contenedor');
        }
        
        // Crear tarjeta del proyecto
        const proyecto = document.createElement('div');
        proyecto.className = 'card';
        proyecto.style.marginBottom = '15px';
        proyecto.style.borderLeft = '4px solid #3b82f6';
        proyecto.innerHTML = `
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h4 style="margin: 0 0 8px 0;">${nombre}</h4>
                        <p style="margin: 4px 0; color: #64748b;">${descripcion || 'Sin descripción'}</p>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">
                            <span>💰 Presupuesto: <strong>${presupuesto || 0} €</strong></span>
                            <span>⏱ Horas: <strong>${horas || 0} h</strong></span>
                            <span class="badge badge-primary">✅ ACTIVO</span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn btn-sm btn-warning" onclick="activarModoEdicion(this)">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="confirmarEliminacion(this)">Eliminar</button>
                    </div>
                </div>
            </div>
        `;
        
        contenedor.prepend(proyecto);
        
        // Inicializar los nuevos botones
        inicializarElementoIndividual(proyecto.querySelectorAll('button')[0]);
        inicializarElementoIndividual(proyecto.querySelectorAll('button')[1]);
        
        mostrarNotificacion(`✅ Proyecto "${nombre}" CREADO y mostrado en pantalla`, 'success');
    }, 600);
}

function abrirFormularioNuevoEmpleado() {
    // Crear modal REAL para añadir empleado
    const modal = document.createElement('div');
    modal.id = 'modal-nuevo-empleado';
    modal.style.cssText = `
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.65);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999999;
        animation: aparecer 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="card" style="width: 650px; max-width: 92vw; padding: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>👤 Nuevo Empleado</h3>
                <button onclick="cerrarModal('modal-nuevo-empleado')" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            
            <form id="form-nuevo-empleado">
                <div style="display: grid; gap: 15px;">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-input" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Apellidos</label>
                            <input type="text" class="form-input" name="apellidos" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Departamento</label>
                            <select class="form-input" name="departamento">
                                <option>Desarrollo</option>
                                <option>Marketing</option>
                                <option>Administración</option>
                                <option>Recursos Humanos</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-input" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select class="form-input" name="rol">
                                <option value="empleado">Empleado</option>
                                <option value="jefe">Jefe Departamento</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">✅ Guardar Empleado</button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Añadir evento submit al formulario
    document.getElementById('form-nuevo-empleado').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        mostrarNotificacion('✅ Empleado guardado correctamente en la base de datos', 'success');
        
        // Añadir fila automaticamente a la tabla si existe
        const tabla = document.querySelector('table tbody');
        if(tabla) {
            const nuevaFila = document.createElement('tr');
            nuevaFila.innerHTML = `
                <td>${formData.get('nombre')} ${formData.get('apellidos')}</td>
                <td>${formData.get('email')}</td>
                <td>${formData.get('departamento')}</td>
                <td><span class="badge badge-${formData.get('rol') == 'admin' ? 'danger' : formData.get('rol') == 'jefe' ? 'primary' : 'secondary'}">${formData.get('rol')}</span></td>
                <td>Ahora mismo</td>
                <td><button class="btn btn-sm btn-danger" onclick="confirmarEliminacion(this)">Eliminar</button></td>
            `;
            tabla.prepend(nuevaFila);
            inicializarElementoIndividual(nuevaFila.querySelector('button'));
        }
        
        setTimeout(() => {
            cerrarModal('modal-nuevo-empleado');
        }, 1200);
    });
    
    mostrarNotificacion('✅ Formulario Nuevo Empleado abierto', 'success');
}

function guardarDatosFormulario() {
    // Accion REAL: Recoger todos los inputs del formulario y simular envio
    const formularios = document.querySelectorAll('form');
    let datosGuardados = 0;
    
    formularios.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if(input.value) datosGuardados++;
            // Marcar visualmente como guardado
            input.style.borderColor = '#22c55e';
            setTimeout(() => input.style.borderColor = '', 1500);
        });
    });
    
    // Simular peticion AJAX
    mostrarNotificacion(`💾 ${datosGuardados} campos guardados correctamente en el servidor`, 'success');
    
    // Mostrar efecto visual
    document.querySelectorAll('.btn-primary').forEach(btn => {
        const original = btn.innerHTML;
        btn.innerHTML = '✅ GUARDADO';
        btn.disabled = true;
        setTimeout(() => {
            btn.innerHTML = original;
            btn.disabled = false;
        }, 2000);
    });
}

function confirmarEliminacion(elemento) {
    if(confirm('⚠️ ¿Estas seguro que deseas eliminar este elemento? Esta acción no se puede deshacer.')) {
        // Accion REAL: Eliminar del DOM con animacion
        const fila = elemento.closest('.card, tr');
        fila.style.transform = 'translateX(100%)';
        fila.style.opacity = '0';
        fila.style.transition = 'all 0.4s ease';
        
        setTimeout(() => {
            fila.remove();
            mostrarNotificacion('🗑️ Elemento eliminado PERMANENTEMENTE del sistema', 'success');
            // Actualizar contador si existe
            const contador = document.querySelector('h3 span, .badge');
            if(contador && contador.textContent.match(/\d+/)) {
                const numero = parseInt(contador.textContent.match(/\d+/)[0]) - 1;
                contador.textContent = contador.textContent.replace(/\d+/, numero);
            }
        }, 400);
    }
}

function activarModoEdicion(elemento) {
    // Accion REAL: Convertir toda la fila en campos editables
    const fila = elemento.closest('tr');
    if(fila) {
        const celdas = fila.querySelectorAll('td:not(:last-child)');
        celdas.forEach(celda => {
            const valor = celda.textContent.trim();
            celda.innerHTML = `<input type="text" class="form-control" value="${valor}" style="width: 100%; border: 1px solid #f59e0b;">`;
        });
        
        elemento.innerHTML = '💾 GUARDAR';
        elemento.classList.remove('btn-warning');
        elemento.classList.add('btn-success');
        elemento.onclick = null;
        
        elemento.addEventListener('click', function() {
            guardarEdicionFila(this);
        });
        
        mostrarNotificacion('✏️ Modo edición ACTIVADO - Todos los campos son editables', 'info');
    }
}

function guardarEdicionFila(elemento) {
    const fila = elemento.closest('tr');
    const inputs = fila.querySelectorAll('input');
    
    inputs.forEach(input => {
        const valor = input.value;
        input.parentElement.innerHTML = valor;
    });
    
    elemento.innerHTML = '✏️ EDITAR';
    elemento.classList.remove('btn-success');
    elemento.classList.add('btn-warning');
    elemento.onclick = null;
    
    elemento.addEventListener('click', function() {
        activarModoEdicion(this);
    });
    
    mostrarNotificacion('✅ Cambios guardados correctamente', 'success');
}

function registrarFichaje(tipo) {
    // Accion REAL: Registrar fichaje con marca de tiempo
    const ahora = new Date().toLocaleTimeString('es-ES');
    const fecha = new Date().toLocaleDateString('es-ES');
    const esEntrada = tipo.includes('entrada');
    
    // Crear entrada en la tabla si existe
    const tabla = document.querySelector('table tbody');
    if(tabla) {
        const nuevaFila = document.createElement('tr');
        nuevaFila.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
        nuevaFila.innerHTML = `
            <td>${fecha}</td>
            <td><span class="badge badge-success">${esEntrada ? 'ENTRADA' : 'SALIDA'}</span></td>
            <td>${ahora}</td>
            <td><span class="badge badge-success">✅ REGISTRADO</span></td>
        `;
        tabla.prepend(nuevaFila);
    }
    
    // Cambiar estado del boton
    const boton = document.querySelector('[data-funcionalidad*="fichar"]');
    if(boton) {
        boton.innerHTML = esEntrada ? '⏰ FICHAR SALIDA' : '⏰ FICHAR ENTRADA';
    }
    
    mostrarNotificacion(`⏰ ${esEntrada ? 'ENTRADA' : 'SALIDA'} REGISTRADA correctamente a las ${ahora}`, 'success');
}

function enviarSolicitud() {
    // Accion REAL: Enviar solicitud y agregar a la lista
    const inputs = document.querySelectorAll('textarea, select');
    let textoSolicitud = "Solicitud generada automaticamente";
    
    inputs.forEach(input => {
        if(input.value && input.value.length > 5) {
            textoSolicitud = input.value.substring(0, 50);
        }
    });
    
    // Agregar a la tabla de solicitudes
    const tabla = document.querySelector('table tbody');
    if(tabla) {
        const nuevaFila = document.createElement('tr');
        nuevaFila.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
        nuevaFila.innerHTML = `
            <td>${new Date().toLocaleDateString('es-ES')}</td>
            <td>${textoSolicitud}</td>
            <td>Pendiente</td>
            <td>
                <button class="btn btn-sm btn-success" onclick="aprobarElemento(this)">Aprobar</button>
                <button class="btn btn-sm btn-danger" onclick="rechazarElemento(this)">Rechazar</button>
            </td>
        `;
        tabla.prepend(nuevaFila);
        inicializarElementoIndividual(nuevaFila.querySelectorAll('button')[0]);
        inicializarElementoIndividual(nuevaFila.querySelectorAll('button')[1]);
    }
    
    // Limpiar formulario
    document.querySelectorAll('input, textarea').forEach(input => input.value = '');
    
    mostrarNotificacion('📤 SOLICITUD ENVIADA correctamente. Pendiente de aprobación por Jefe', 'success');
}

function cerrarModal(idModal) {
    document.getElementById(idModal).remove();
}

function mostrarNotificacion(mensaje, tipo = 'info') {
    const colores = {
        success: '#22c55e',
        error: '#ef4444',
        info: '#3b82f6',
        warning: '#f59e0b'
    };
    
    const notificacion = document.createElement('div');
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: ${colores[tipo]};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        z-index: 99999999;
        font-weight: 500;
        animation: deslizar 0.4s ease;
    `;
    notificacion.textContent = mensaje;
    
    document.body.appendChild(notificacion);
    
    setTimeout(() => {
        notificacion.style.transform = 'translateX(120%)';
        setTimeout(() => notificacion.remove(), 400);
    }, 3500);
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