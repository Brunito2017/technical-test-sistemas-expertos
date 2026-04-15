import warehouseApi from './warehouseApi.js';

/**
 * Obtiene el ID de la bodega desde la URL.
 * 
 * @returns {string|null} ID de la bodega o null
 */
function getWarehouseIdFromUrl() {
  const params = new URLSearchParams(window.location.search);
  return params.get('id');
}

/**
 * Carga los encargados disponibles como checkboxes en el formulario con los seleccionados.
 * 
 * @param {HTMLElement} container - Contenedor donde se renderizan los checkboxes
 * @param {Array<string>} selectedIds - IDs de encargados seleccionados
 */
async function loadManagersCheckboxes(container, selectedIds = []) {
  container.innerHTML = '<span style="color:#888">Cargando...</span>';
  try {
    const res = await warehouseApi.getManagers();
    container.innerHTML = '';
    (res.data || res).forEach(manager => {
      const label = document.createElement('label');
      label.style.display = 'block';
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.name = 'managers';
      checkbox.value = manager.id;
      if (selectedIds.includes(manager.id)) checkbox.checked = true;
      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(` ${manager.first_name} ${manager.last_name} ${manager.second_last_name} (${manager.run})`));
      container.appendChild(label);
    });
  } catch (e) {
    container.innerHTML = '<span style="color:#d32f2f">Error al cargar encargados</span>';
  }
}

/**
 * Carga los datos de la bodega en el formulario de edición.
 */
async function fillEditForm() {
  const form = document.getElementById('warehouse-edit-form');
  const managersContainer = form.querySelector('#managers-checkboxes');
  const warehouseId = getWarehouseIdFromUrl();
  if (!warehouseId) {
    document.getElementById('form-message').textContent = 'ID de bodega no especificado.';
    form.querySelector('button[type="submit"]').disabled = true;
    return;
  }
  const res = await warehouseApi.getById(warehouseId);
  const w = res.data || res;
  console.log('Datos recibidos para edición:', w);
  form.id.value = w.id;
  form.name.value = w.name;
  form.address.value = w.address;
  form.endowment.value = w.endowment;
  form.is_active.value = w.is_active ? 'true' : 'false';
  await loadManagersCheckboxes(managersContainer, w.manager_ids || []);
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('warehouse-edit-form');
  fillEditForm();

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const warehouseId = getWarehouseIdFromUrl();
    const isActiveSelect = form.querySelector('select[name="is_active"]');
    const checkedManagers = Array.from(form.querySelectorAll('input[name="managers"]:checked')).map(cb => cb.value);
    const data = {
      id: warehouseId,
      name: form.name.value.trim(),
      address: form.address.value.trim(),
      endowment: parseInt(form.endowment.value, 10),
      manager_ids: checkedManagers,
      is_active: isActiveSelect.value === 'true'
    };
    console.log('Datos a enviar:', data);
    if (!data.name || !data.address || !data.endowment) {
      alert('Todos los campos son obligatorios');
      return;
    }
    try {
      const res = await warehouseApi.update(data);
      alert('Bodega actualizada con éxito');
      window.location.href = 'index.html';
    } catch (error) {
      console.error('Error completo:', error);
      alert('Error al actualizar: ' + error.message);
    }
  });
});
