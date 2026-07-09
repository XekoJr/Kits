import { api } from "../api.js";

export const getItems = (kitId) => api("GET", `/kits/${kitId}/items`);
export const createItem = (kitId, data) => api("POST", `/kits/${kitId}/items`, data);
export const updateItem = (kitId, id, data) => api("PUT", `/kits/${kitId}/items/${id}`, data);
export const deleteItem = (kitId, id) => api("DELETE", `/kits/${kitId}/items/${id}`);
export const toggleItem = (kitId, id) => api("PATCH", `/kits/${kitId}/items/${id}/toggle`);
