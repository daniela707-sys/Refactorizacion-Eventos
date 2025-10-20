const url = "http://localhost/Red-Emprendedora/files/api/back/apps/township/";

const townshipService = {
    getAllTownships: async () => {
        try {
            const response = await fetch(`${url}get_all_townships.php`);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            if (!Array.isArray(data)) {
                throw new Error("La respuesta no es un array válido de municipios.");
            }
            return data;
        } catch (error) {
            console.error("API ERROR: OBTENER MUNICIPIOS: " + error);
            throw error;
        }
    },

    getTownshipsByDepartment: async (departmentId) => {
      try {
          const response = await fetch(`${url}get_townships_by_department.php?department_id=${departmentId}`);
          if (!response.ok) {
              throw new Error(`Error HTTP: ${response.status}`);
          }
          const data = await response.json();
          if (!Array.isArray(data)) {
              throw new Error("La respuesta no es un array válido de municipios.");
          }
          return data;
      } catch (error) {
          console.error("API ERROR: OBTENER MUNICIPIOS POR DEPARTAMENTO: " + error);
          throw error;
      }
  },

    createTownship: async (township) => {
        try {
            const response = await fetch(`${url}create_township.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(township)
            });
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("API ERROR: CREAR MUNICIPIO: " + error);
            throw error;
        }
    },

    updateTownship: async (id, township) => {
        try {
            const response = await fetch(`${url}update_township.php?id=${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(township)
            });
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("API ERROR: ACTUALIZAR MUNICIPIO: " + error);
            throw error;
        }
    },

    deleteTownship: async (id) => {
        try {
            const response = await fetch(`${url}delete_township.php?id=${id}`, {
                method: 'DELETE'
            });
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("API ERROR: ELIMINAR MUNICIPIO: " + error);
            throw error;
        }
    },
};

export default townshipService;
