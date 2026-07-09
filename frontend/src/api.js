const BASE = "/api";

export async function api(method, path, body = null) {
  const token = localStorage.getItem("token");

  const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
  };
  if (token) headers["Authorization"] = `Bearer ${token}`;

  const res = await fetch(`${BASE}${path}`, {
    method,
    headers,
    body: body ? JSON.stringify(body) : null,
  });

  const data = await res.json();

  if (!res.ok) {
    const err = new Error(data.message || "Erro na API");
    err.errors = data.errors || {};
    throw err;
  }

  return data;
}

export function isLoggedIn() {
  return !!localStorage.getItem("token");
}

export function logout() {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
}
