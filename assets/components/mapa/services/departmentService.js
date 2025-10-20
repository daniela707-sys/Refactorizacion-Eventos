const url = "http://localhost/Red-Emprendedora/files/api/back/apps/department/";

const departmentService = {
    getAllDepartments: async () => {
        try {
            const response = await fetch(`${url}get_all_departments.php`);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            if (!Array.isArray(data)) {
                throw new Error("La respuesta no es un array válido de departamentos.");
            }
            return data;
        } catch (error) {
            console.error("API ERROR: OBTENER DEPARTAMENTOS: " + error);
            throw error;
        }
    },

    getDepartmentById: async (id) => {
        try {
            const response = await fetch(`${url}get_department.php?id=${id}`);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("API ERROR: OBTENER DEPARTAMENTO POR ID: " + error);
            throw error;
        }
    },

    createDepartment: async (department) => {
        try {
            const response = await fetch(`${url}create_department.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(department)
            });
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("API ERROR: CREAR DEPARTAMENTO: " + error);
            throw error;
        }
    },

    updateDepartment: async (id, department) => {
        try {
            const response = await fetch(`${url}update_department.php?id=${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(department)
            });
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("API ERROR: ACTUALIZAR DEPARTAMENTO: " + error);
            throw error;
        }
    },

    deleteDepartment: async (id) => {
        try {
            const response = await fetch(`${url}delete_department.php?id=${id}`, {
                method: 'DELETE'
            });
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("API ERROR: ELIMINAR DEPARTAMENTO: " + error);
            throw error;
        }
    }
};

export default departmentService;