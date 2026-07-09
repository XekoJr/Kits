import { api } from "../api.js";

export const getKits = () => api("GET", "/kits");
export const getKit = (id) => api("GET", `/kits/${id}`);
export const createKit = (data) => api("POST", "/kits", data);
export const updateKit = (id, data) => api("PUT", `/kits/${id}`, data);
export const deleteKit = (id) => api("DELETE", `/kits/${id}`);
