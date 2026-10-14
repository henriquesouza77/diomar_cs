/**
 * api.js — Cliente HTTP centralizado
 * Toda comunicação com o backend passa por aqui.
 * Detecta automaticamente a base URL e adiciona CSRF token.
 */
'use strict';

const API = (() => {
  // Detecta a base do projeto (ex: /diomar-cs)
  const detectBase = () => {
    const path = window.location.pathname;
    const idx = path.indexOf('/frontend/');
    return idx >= 0 ? path.substring(0, idx) : '';
  };

  const BASE_URL = detectBase();
  const ENDPOINT = `${BASE_URL}/backend/api/index.php`;

  /**
   * Requisição interna.
   */
  async function request(route, options = {}) {
    const { method = 'GET', body = null, params = {}, headers = {} } = options;

    const url = new URL(ENDPOINT, window.location.origin);
    url.searchParams.set('route', route);

    Object.entries(params).forEach(([k, v]) => {
      if (v !== null && v !== undefined && v !== '') {
        url.searchParams.set(k, v);
      }
    });

    const fetchOptions = {
      method,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...headers,
      },
      credentials: 'same-origin',
    };

    if (!['GET', 'HEAD', 'OPTIONS'].includes(method.toUpperCase())) {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
      if (csrfToken) fetchOptions.headers['X-CSRF-Token'] = csrfToken;
    }

    if (body && !(body instanceof FormData)) {
      fetchOptions.headers['Content-Type'] = 'application/json';
      fetchOptions.body = JSON.stringify(body);
    } else if (body instanceof FormData) {
      fetchOptions.body = body;
    }

    try {
      const response = await fetch(url.toString(), fetchOptions);

      const contentType = response.headers.get('content-type') || '';
      let data;

      if (contentType.includes('application/json')) {
        data = await response.json();
      } else {
        data = await response.text();
      }

      if (!response.ok || (data && typeof data === 'object' && data.success === false)) {
        const error = new Error(data?.error || data?.message || `Erro ${response.status}`);
        error.status = response.status;
        error.data = data;
        throw error;
      }

      return data && typeof data === 'object' && data.success === true && 'data' in data
        ? data.data
        : data;
    } catch (error) {
      if (error.name === 'TypeError' && error.message.includes('fetch')) {
        console.error('[API] Erro de rede:', error);
        throw new Error('Não foi possível conectar ao servidor. Verifique sua internet.');
      }
      throw error;
    }
  }

  // Atalhos
  const get    = (route, params) => request(route, { method: 'GET', params });
  const post   = (route, body)   => request(route, { method: 'POST', body });
  const put    = (route, body)   => request(route, { method: 'PUT', body });
  const del    = (route, body)   => request(route, { method: 'DELETE', body });
  const upload = (route, formData) => request(route, { method: 'POST', body: formData });

  return {
    BASE_URL,
    request,
    get,
    post,
    put,
    del,
    upload,

    // ============ ENDPOINTS ============
    properties: {
      list:      (params) => get('properties', params),
      adminList: (params) => get('properties/admin-list', params),
      get:       (id)     => get('property', { id }),
      create:    (data)   => upload('properties/store', data),
      update:    (data)   => upload('properties/update', data),
      remove:    (id)     => post('properties/delete', { id }),
      duplicate: (id)     => post('properties/duplicate', { id }),
      toggleFeatured: (id) => post('properties/toggle-featured', { id }),
      togglePublished: (id) => post('properties/toggle-published', { id }),
      similar:   (id)     => get('properties/similar', { id }),
      uploadImages: (formData) => upload('property-images/upload', formData),
      removeImage: (id) => post('property-images/remove', { id }),
      setPrimaryImage: (propertyId, imageId) => post('property-images/set-primary', {
        property_id: propertyId,
        image_id: imageId,
      }),
      reorderImages: (ids) => post('property-images/reorder', { ids }),
    },

    favorites: {
      list:   ()   => get('favorites'),
      toggle: (id) => post('favorites/toggle', { property_id: id }),
    },

    leads: {
      create: (data) => post('leads/store', data),
      list:   (params) => get('leads', params),
      updateStatus: (id, status_id) => post('leads/update-status', { id, status_id }),
    },

    users: {
      list:   (params) => get('users', params),
      createAgent: (data) => post('users/store', data),
      update: (id, data) => post('users/update', { ...data, id }),
      remove: (id) => post('users/delete', { id }),
    },

    agents: {
      list: () => get('agents'),
    },

    auth: {
      login:    (data) => post('auth/login', data),
      register: (data) => post('auth/register', data),
      logout:   ()     => post('auth/logout'),
      me:       ()     => get('auth/me'),
    },

    dashboard: {
      stats: () => get('dashboard/stats'),
      recentProperties: () => get('dashboard/recent-properties'),
      charts: () => get('dashboard/charts'),
    },

    categories: () => get('categories'),
    propertyTypes: (categoryId) => get('property-types', { category_id: categoryId }),
    features: () => get('features'),
    settings: {
      get: () => get('settings'),
      save: (data) => post('settings/save', data),
    },
  };
})();

window.API = API;