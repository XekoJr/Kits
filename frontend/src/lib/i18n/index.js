import { writable, derived } from "svelte/store";
import en from "./en.js";
import pt from "./pt.js";

const translations = { en, pt };

export const locale = writable(localStorage.getItem("locale") || "en");

locale.subscribe((val) => localStorage.setItem("locale", val));

export const t = derived(locale, ($locale) => (key, vars = {}) => {
  let str = translations[$locale]?.[key] ?? key;
  Object.entries(vars).forEach(([k, v]) => {
    str = str.replace(`{${k}}`, v);
  });
  return str;
});
