import { api } from "../api.js";

export const getCategories = () => api("GET", "/categories");
export const createCategory = (data) => api("POST", "/categories", data);
export const updateCategory = (id, data) => api("PUT", `/categories/${id}`, data);
export const deleteCategory = (id) => api("DELETE", `/categories/${id}`);
