// main.js
import departmentService from '../services/departmentService.js';

const regionName = document.getElementById('region-name');
const regionInfo = document.getElementById('region-info');
const regions = document.querySelectorAll('.region');

async function loadDepartments() {
    try {
        const departments = await departmentService.getAllDepartments();
        setupRegionClickHandler(regions, departments);
    } catch (error) {
        console.error("Error al cargar los departamentos:", error);
    }
}

function setupRegionClickHandler(regions, departments) {
    regions.forEach(region => {
        region.addEventListener("click", async function () {
            const selectedRegionName = this.getAttribute("title");
            regionName.textContent = `Región: ${selectedRegionName}`;

            const department = departments.find(dep =>
                dep.department_name.toLowerCase().trim() === selectedRegionName.toLowerCase().trim()
            );

            if (department) {
                regionInfo.innerHTML = `
                    <div class="data-field"><strong>ID:</strong> ${department.department_id}</div>
                    <div class="data-field"><strong>Nombre:</strong> ${department.department_name}</div>
                    <div class="data-field"><strong>Descripción:</strong> ${department.description}</div>
                    <div class="extra-info">
                        <div class="data-field"><strong>Emprendimientos:</strong> ${department.department_entrepreneur}</div>                        
                    </div>
                `;
            } else {
                regionInfo.innerHTML = `<p>Información no disponible para esta región.</p>`;
            }
        });
    });
}

loadDepartments();