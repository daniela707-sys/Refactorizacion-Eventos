document.addEventListener("DOMContentLoaded", function () {
    const tooltip = document.createElement("div");
    tooltip.classList.add("tooltip");
    document.body.appendChild(tooltip);

    const regions = document.querySelectorAll(".region");
    const regionName = document.getElementById("region-name");
    const regionInfo = document.getElementById("region-info");


    

    // Mover el path y el texto al frente
    document.querySelectorAll('.map-container path').forEach(function (path) {
        path.addEventListener('mouseover', function () {
            this.parentNode.appendChild(this);
            const id = this.id;
            const text = document.querySelector(`.map-container text[data-path-id="${id}"]`);
            if (text) {
                this.parentNode.appendChild(text);
            }
        });

        path.addEventListener('mouseout', function () {
            this.parentNode.appendChild(this);
            const id = this.id;
            const text = document.querySelector(`.map-container text[data-path-id="${id}"]`);
            if (text) {
                this.parentNode.appendChild(text);
            }
        });
    });

        document.addEventListener("DOMContentLoaded", function () {
        const tooltip = document.createElement("div");
        tooltip.classList.add("tooltip");
        document.body.appendChild(tooltip);
    
        const regions = document.querySelectorAll(".region");
        const regionName = document.getElementById("region-name");
        const regionInfo = document.getElementById("region-info");
    
        // Mover el path y el texto al frente
        document.querySelectorAll('.map-container path').forEach(function (path) {
            path.addEventListener('mouseover', function () {
                this.parentNode.appendChild(this);
                const id = this.id;
                const text = document.querySelector(`.map-container text[data-path-id="${id}"]`);
                if (text) {
                    this.parentNode.appendChild(text);
                }
            });
    
            path.addEventListener('mouseout', function () {
                this.parentNode.appendChild(this);
                const id = this.id;
                const text = document.querySelector(`.map-container text[data-path-id="${id}"]`);
                if (text) {
                    this.parentNode.appendChild(text);
                }
            });
    
            // Agregar evento click para mostrar alerta con el nombre de la región
            path.addEventListener('click', function () {
                const regionTitle = this.getAttribute('title');
                console.log(`Clicked on region with title: ${regionTitle}`);
                if (regionTitle) {
                    alert(`Has seleccionado la región: ${regionTitle}`);
                } else {
                    console.log('No title attribute found for this path element.');
                }
            });
        });
    
        // Inicialmente oculta el mapa en dispositivos móviles
        if (window.innerWidth < 768) {
            document.getElementById("map-container").style.display = "none";
        } else {
            document.getElementById("map-container").style.display = "block"; // Mostrar en pantallas grandes
        }
    });

    // Inicialmente oculta el mapa en dispositivos móviles
    if (window.innerWidth < 768) {
        document.getElementById("map-container").style.display = "none";
    } else {
        document.getElementById("map-container").style.display = "block"; // Mostrar en pantallas grandes
    }

});

