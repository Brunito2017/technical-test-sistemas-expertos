
const BASE_URL = 'http://localhost/technical-test-sistemas-expertos/app/api';

const http = {
  async request(endpoint, method = 'GET', body = null) {
    const options = {
      method,
      headers: { 'Content-Type': 'application/json' },
    };

    if (body) options.body = JSON.stringify(body);

    const res = await fetch(`${BASE_URL}/${endpoint}`, options);

    if (!res.ok) throw new Error(`Error ${res.status}: ${res.statusText}`);

    return res.json();
  },

  get(endpoint, params = {}) {
    const query = new URLSearchParams(params).toString();
    const url = query ? `${endpoint}?${query}` : endpoint;
    return this.request(url, 'GET');
  },

  post(endpoint, body)   { return this.request(endpoint, 'POST',   body); },
  put(endpoint, body)    { return this.request(endpoint, 'PUT',    body); },
  delete(endpoint, body) { return this.request(endpoint, 'DELETE', body); },
};

const warehouseApi = {
  getAll(status = null)  { return http.get('Warehouse.php', status ? { status } : {}); },
  create(data)           { return http.post('Warehouse.php', data); },
  update(data)           { return http.put('Warehouse.php', data); },
  delete(id)             { return http.delete('Warehouse.php', { id }); },
  getManagers()          { return http.get('Managers.php'); },
};

export default warehouseApi;