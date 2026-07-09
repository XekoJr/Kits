import { api } from "../api.js";

export const register = (name, email, password, password_confirmation) =>
  api("POST", "/register", { name, email, password, password_confirmation });

export const login = (email, password) =>
  api("POST", "/login", { email, password });

export async function logout() {
  try {
    await api("POST", "/logout");
  } catch (_) {}
  localStorage.removeItem("token");
  localStorage.removeItem("user");
}

export const isLoggedIn = () => !!localStorage.getItem("token");
