import warehouseApi from './warehouseApi.js';

/**
 * Renderiza la tabla de bodegas con los datos proporcionados.
 * 
 * @param {Array<Object>} data - Array de bodegas a mostrar
 */
function renderTable(data) {
  const tbody = document.querySelector('#warehouse-table tbody');
  tbody.innerHTML = '';

  data.forEach(w => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${w.id}</td>
      <td>${w.name}</td>
      <td>${w.address}</td>
      <td>${w.endowment}</td>
      <td>${w.manager_name || '-'}</td>
      <td>${w.created_at || ''}</td>
      <td>${w.is_active ? 'Activada' : 'Desactivada'}</td>
      <td>
        <button class="btn-edit" data-id="${w.id}">Editar</button>
        <button class="btn-delete" data-id="${w.id}">Eliminar</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

/**
 * Carga las bodegas desde la API y las renderiza en la tabla.
 * 
 * @param {string|null} status - Estado de las bodegas a filtrar ('active', 'inactive' o null)
 */
async function loadWarehouses(status = null) {
  try {
    const json = await warehouseApi.getAll(status);
    renderTable(json.data || json);
  } catch (err) {
    console.error('Error cargando bodegas:', err);
  }
}

/**
 * Inicializa la tabla de bodegas y sus eventos.
 */
export function initTable() {
  loadWarehouses();

  document.querySelector('#filter-status')?.addEventListener('change', (e) => {
    loadWarehouses(e.target.value || null);
  });

  document.querySelector('#warehouse-table')?.addEventListener('click', async (e) => {
    if (e.target.classList.contains('btn-edit')) {
      const id = e.target.dataset.id;
      window.location.href = `form-edit.html?id=${encodeURIComponent(id)}`;
      return;
    }

    if (e.target.classList.contains('btn-delete')) {
      const id = e.target.dataset.id;
      const confirmar = confirm('¿Estás seguro? Esta acción eliminará la bodega y no se puede deshacer.');
      if (confirmar) {
        await warehouseApi.delete(id);
        loadWarehouses();
      }
      return;
    }
  });

  
}