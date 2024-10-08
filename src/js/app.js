let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

document.addEventListener('DOMContentLoaded', function() {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion();// muesta y culta la seecion
    tabs();// cambia la seccion cuando seleciona la seccion
    botonesPaginador();//agregar o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();
    consultarApi();// Consulta la Api en el Backen de php
    idCliente();
    nombreCliente();//añade el nombre dek ckuebte eb ek objeto de cita
    fechaCliente();// añade fecha de la cita
    horaCliente();// añade hora de la cita
    muestraResumen();

}

function mostrarSeccion() {
    //Ocultar la seccion que tena la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    } 
    
    //selecionar la seccion con el paso
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    //quita el tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    //resalta el tabs actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');

}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');
    botones.forEach(boton => {
        boton.addEventListener('click', function(e) {
            paso = parseInt( e.target.dataset.paso);

            mostrarSeccion();
            botonesPaginador();
        });
    })
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');

    } else if(paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        muestraResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function() {
        
        if(paso <= pasoInicial) return;
        paso--;

        botonesPaginador();

    })
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {
        
        if(paso >= pasoFinal) return;
        paso++;

        botonesPaginador();

    })
}

async function consultarApi() {
    try {
        const url = '/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const{id, nombre, precio} = servicio;
        
        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;
        

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function() {
            selecionarServicio(servicio);
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
        
    });
}

function selecionarServicio(servicio) {
    const { id } = servicio;
    
    const { servicios } = cita;
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);


    // comprobar si un servucio ya fye agregadi
    if(servicios.some( agregado => agregado.id === id)) {
        //elimarlo
        
        cita.servicios = servicios.filter(agregado => agregado.id !== id);
        divServicio.classList.remove('selecionado');

        
    } else {
        //agregarlo
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('selecionado');
    }
    
    
}

function idCliente() {
    cita.id = document.querySelector('#id').value
}

function nombreCliente() {
    cita.nombre = document.querySelector('#nombre').value
}

function fechaCliente() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) {
        const dia = new Date(e.target.value).getUTCDay();
        
        if([6, 0].includes(dia)) {
            e.target.value = '';
            mostrarAlerta('Fines de semana no abrimos', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }
        
    });

}

function horaCliente() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {

        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        if(hora < 10 || hora > 18) {
            e.target.value = '';
            mostrarAlerta('Hora no valida', 'error', '.formulario');
        } else {
            cita.hora = e.target.value;

            //console.log(cita);

        }
    })
}

function mostrarAlerta (mensaje, tipo, elemento, desaparece =  true) {
    //previene que se genere multiple alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    };
    
    //scripting para crear
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);
    
    if(desaparece) {
        //eliminar la alerta
        setTimeout(() => {
            alerta.remove();
        }, 3000);
}
    }
    
    

function muestraResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    //limpiar contnido de resumen
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    if(Object.values(cita).includes('') || cita.servicios.length === 0) {
        mostrarAlerta('Hacen falta datos de Servicio, Fecha u Hora', 'error', '.contenido-resumen', false);

        return;
    } 

    // Formatera el div de resumen
    const { nombre, fecha, hora, servicios} = cita;

    

    //heading para servicios
    const headingServicios = document.createElement('H3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);


    servicios.forEach(servicio => {
        const { id, precio, nombre} = servicio;
        const contenedorServicio = document.createElement('DIV');    
        contenedorServicio.classList.add('contenedor-servicio');    

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre  
        
        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;
    
    
        console.log();

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio)


    });

     //heading para Cita
    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Total de la Cita';
    resumen.appendChild(headingCita);
    

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre} `;

    //formatear fecha en español
     //formatear fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const año = fechaObj.getFullYear();
 
    const fechaUTC = new Date( Date.UTC(año, mes, dia));
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones); 

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada} `;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas `;

    // boton para crear una cita

    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;


    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar);
}

async function reservarCita() {

    const {nombre, fecha, hora, servicios, id} = cita;
    const idServicio = servicios.map( servicio => servicio.id);

    //console.log(idServicio);
    
    

    const datos = new FormData();
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);

    datos.append('servicios', idServicio);
    //console.log([...datos]);
    try {
        //preticion hacia la api
    const url = '/api/citas'

    const repuesta = await fetch(url, {
        method: 'POST',
        body: datos
    });

    const resultado = await repuesta.json();
    console.log(resultado.resultado);

    if(resultado.resultado) {
        Swal.fire({
            icon: "success",
            title: "Cita Creada",
            text: "Tu Cita fue Creada Correctamente!",
            Button: "OK"
          }).then(() => {
            setTimeout(() =>{
                window.location.reload();
            }, 1000);
            
          } )
    }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al guardar la cita!"
            
        });
    }

    //console.log([...datos]);
}