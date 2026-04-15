import warehouseApi from './warehouseApi.js';

function getWarehouseIdFromUrl() {
  const params = new URLSearchParams(window.location.search);
  return params.get('id');
}

async function loadManagers(managersSelect, selectedIds = []) {
  managersSelect.innerHTML = '<option disabled selected>Cargando...</option>';
  try {
    const res = await warehouseApi.getManagers();
    managersSelect.innerHTML = '';
    (res.data || res).forEach(manager => {
      const option = document.createElement('option');
      option.value = manager.id;
      option.textContent = `${manager.first_name} ${manager.last_name} ${manager.second_last_name} (${manager.run})`;
      if (selectedIds.includes(manager.id)) option.selected = true;
      managersSelect.appendChild(option);
    });
  } catch (e) {
    managersSelect.innerHTML = '<option disabled>Error al cargar encargados</option>';
  }
}

async function fillEditForm() {
  const form = document.getElementById('warehouse-edit-form');
  const managersSelect = form.querySelector('select[name="managers"]');
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
  await loadManagers(managersSelect, w.manager_ids || []);
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('warehouse-edit-form');
  fillEditForm();

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const warehouseId = getWarehouseIdFromUrl();
    const managersSelect = form.querySelector('select[name="managers"]');
    const isActiveSelect = form.querySelector('select[name="is_active"]');
    const data = {
      id: warehouseId,
      name: form.name.value.trim(),
      address: form.address.value.trim(),
      endowment: parseInt(form.endowment.value, 10),
      manager_ids: Array.from(managersSelect.selectedOptions).map(opt => opt.value),
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
