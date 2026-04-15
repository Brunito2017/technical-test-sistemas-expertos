import warehouseApi from './warehouseApi.js';

/**
 * Carga los encargados disponibles en el select del formulario.
 * 
 * @param {HTMLSelectElement} managersSelect - Elemento select para los encargados
 */
async function loadManagers(managersSelect) {
  managersSelect.innerHTML = '<option disabled selected>Cargando...</option>';
  try {
    const res = await warehouseApi.getManagers();
    managersSelect.innerHTML = '';
    (res.data || res).forEach(manager => {
      const option = document.createElement('option');
      option.value = manager.id;
      option.textContent = `${manager.first_name} ${manager.last_name} ${manager.second_last_name} (${manager.run})`;
      managersSelect.appendChild(option);
    });
  } catch (e) {
    managersSelect.innerHTML = '<option disabled>Error al cargar encargados</option>';
  }
}

/**
 * Inicializa el formulario de creación de bodegas.
 */
export function initForm() {
  const form = document.getElementById('warehouse-form');
  if (!form) return; 

  const managersSelect = form.querySelector('select[name="managers"]');
  if (managersSelect) loadManagers(managersSelect);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = {
      id:          form.id.value.trim(),
      name:        form.name.value.trim(),
      address:     form.address.value.trim(),
      endowment:   parseInt(form.endowment.value, 10),
      manager_ids: Array.from(form.managers.selectedOptions).map(opt => opt.value),
      is_active:   form.is_active.value === 'true'
    };

    if (!data.id || !data.name || !data.address || !data.endowment) {
      alert('Todos los campos son obligatorios');
      return;
    }

    try {
      const res = await warehouseApi.create(data); 
      alert('Bodega registrada con éxito');
      form.reset();
      document.dispatchEvent(new CustomEvent('warehouse:created'));
    } catch (error) {
      console.error('Error completo:', error);
      alert('Error al registrar: ' + error.message);
    }
  });
}