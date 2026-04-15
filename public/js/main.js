/**
 * Punto de entrada principal de la aplicación.
 */

import warehouseApi from './warehouseApi.js';
import { initTable } from './warehouseTable.js';
import { initForm } from './warehouseForm.js';

document.addEventListener('DOMContentLoaded', () => {
  initTable();
  initForm();
});