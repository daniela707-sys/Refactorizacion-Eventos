const funcionInit = () => {
    if (!"geolocation" in navigator) {
        return alert("Tu navegador no soporta el acceso a la ubicación. Intenta con otro");
    }

    // Solicitar permiso para notificaciones
    Notification.requestPermission().then((resultado) => {
        if (resultado === 'granted') {
            console.log("Permiso para notificaciones concedido.");
        } else {
            console.log("Permiso para notificaciones denegado.");
        }
    });

    const RUTA_API = "./loguear.php";
    let idWatcher = null;
    let primeraUbicacion = true; // Bandera para controlar la primera ubicación

    const $latitud = document.querySelector("#latitud"),
        $longitud = document.querySelector("#longitud"),
        $btnIniciar = document.querySelector("#btnIniciar"),
        $btnDetener = document.querySelector("#btnDetener"),
        $log = document.querySelector("#log"),
        $gmapCanvas = document.querySelector("#gmap_canvas");

    const obtenerUbicacionDesdeCoordenadas = async (latitud, longitud) => {
        const url = `https://nominatim.openstreetmap.org/reverse?lat=${latitud}&lon=${longitud}&format=json`;
        
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error en la respuesta de la API');
            
            const data = await response.json();
            const { country, state, city } = data.address;

            return { country, state, city };
        } catch (error) {
            console.error('Error al obtener la ubicación:', error);
            return null;
        }
    };

    const onUbicacionConcedida = async (ubicacion) => {
        const coordenadas = ubicacion.coords;
        $latitud.innerText = coordenadas.latitude.toFixed(6);
        $longitud.innerText = coordenadas.longitude.toFixed(6);
        loguear(`${ubicacion.timestamp}: ${coordenadas.latitude},${coordenadas.longitude}`);
        await enviarAServidor(ubicacion);

        if (primeraUbicacion) { // Solo si es la primera ubicación
            const ubicacionGeografica = await obtenerUbicacionDesdeCoordenadas(coordenadas.latitude, coordenadas.longitude);
            if (ubicacionGeografica) {
                loguear(`País: ${ubicacionGeografica.country}, Departamento: ${ubicacionGeografica.state}, Municipio: ${ubicacionGeografica.city}`);
                mostrarNotificacion("Su ubicación es", `País: ${ubicacionGeografica.country}, Departamento: ${ubicacionGeografica.state}, Municipio: ${ubicacionGeografica.city}`);
                primeraUbicacion = false; // Desactivar la bandera después de mostrar la ubicación
            }
        }

        // Actualizar el iframe del mapa con las coordenadas
        actualizarMapa(coordenadas.latitude, coordenadas.longitude);
    };

    const enviarAServidor = async (ubicacion) => {
        const otraUbicacion = {
            coordenadas: {
                latitud: ubicacion.coords.latitude,
                longitud: ubicacion.coords.longitude,
            },
            timestamp: ubicacion.timestamp,
        };
        console.log("Enviando: ", otraUbicacion);
        await fetch(RUTA_API, {
            method: "POST",
            body: JSON.stringify(otraUbicacion),
        });
    };

    const loguear = (texto) => {
        $log.innerText += "\n" + texto;
    };

    const onErrorDeUbicacion = (err) => {
        $latitud.innerText = "Error obteniendo ubicación: " + err.message;
        $longitud.innerText = "Error obteniendo ubicación: " + err.message;
        console.log("Error obteniendo ubicación: ", err);
    }

    const detenerWatcher = () => {
        if (idWatcher) {
            navigator.geolocation.clearWatch(idWatcher);
            mostrarNotificacion("Su ubicación detenida");
            primeraUbicacion = true; // Reiniciar la bandera al detener el seguimiento
        }
    }

    const opcionesDeSolicitud = {
        enableHighAccuracy: true,
        maximumAge: 0,
        timeout: 10000
    };

    $btnIniciar.addEventListener("click", () => {
        detenerWatcher();
        idWatcher = navigator.geolocation.watchPosition(onUbicacionConcedida, onErrorDeUbicacion, opcionesDeSolicitud);
		mostrarNotificacion("Su ubicación es", `País: ${ubicacionGeografica.country},
												Departamento: ${ubicacionGeografica.state},
												Municipio: ${ubicacionGeografica.city}`);
    });

    $btnDetener.addEventListener("click", detenerWatcher);

    $latitud.innerText = "Cargando...";
    $longitud.innerText = "Cargando...";

    // Función para actualizar el mapa con las coordenadas
    const actualizarMapa = (latitud, longitud) => {
        const src = `https://maps.google.com/maps?q=${latitud},${longitud}&t=&z=13&ie=UTF8&iwloc=&output=embed`;
        $gmapCanvas.src = src;
    };

    // Función para mostrar una notificación
    const mostrarNotificacion = (titulo, mensaje) => {
        if (Notification.permission === 'granted') {
            new Notification(titulo, {
                body: mensaje,
                icon: 'path/to/icon.png' // Opcional: Ruta a un icono
            });
        }
    };
};

document.addEventListener("DOMContentLoaded", funcionInit);
