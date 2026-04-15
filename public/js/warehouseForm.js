import warehouseApi from './warehouseApi.js';

/**
 * Carga los encargados disponibles como checkboxes en el formulario.
 * 
 * @param {HTMLElement} container - Contenedor donde se renderizan los checkboxes
 */
async function loadManagersCheckboxes(container) {
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
      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(` ${manager.first_name} ${manager.last_name} ${manager.second_last_name} (${manager.run})`));
      container.appendChild(label);
    });
  } catch (e) {
    container.innerHTML = '<span style="color:#d32f2f">Error al cargar encargados</span>';
  }
}

/**
 * Inicializa el formulario de creación de bodegas.
 */
export function initForm() {
  const form = document.getElementById('warehouse-form');
  if (!form) return;

  const managersContainer = form.querySelector('#managers-checkboxes');
  if (managersContainer) loadManagersCheckboxes(managersContainer);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const checkedManagers = Array.from(form.querySelectorAll('input[name="managers"]:checked')).map(cb => cb.value);

    const data = {
      id:          form.id.value.trim(),
      name:        form.name.value.trim(),
      address:     form.address.value.trim(),
      endowment:   parseInt(form.endowment.value, 10),
      manager_ids: checkedManagers,
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