<script>
  import { onMount } from "svelte";
  import { isLoggedIn } from "./api/auth.js";
  import Login from "./pages/Login.svelte";
  import Register from "./pages/Register.svelte";
  import Dashboard from "./pages/Dashboard.svelte";
  import KitDetail from "./pages/KitDetail.svelte";
  import Categories from "./pages/Categories.svelte";
  import Navbar from "./lib/Navbar.svelte";

  let page = "login";
  let kitId = null;

  function getPage() {
    const hash = window.location.hash.replace("#/", "") || "";
    if (hash.startsWith("kits/")) {
      kitId = parseInt(hash.split("/")[1]);
      return "kit-detail";
    }
    if (hash === "register") return "register";
    if (hash === "dashboard") return "dashboard";
    if (hash === "categories") return "categories";
    return isLoggedIn() ? "dashboard" : "login";
  }

  onMount(() => {
    page = getPage();
    window.addEventListener("hashchange", () => {
      page = getPage();
    });
  });

  function navigate(to, id = null) {
    kitId = id;
    if (to === "kit-detail" && id) {
      window.location.hash = `#/kits/${id}`;
    } else {
      window.location.hash = `#/${to}`;
    }
    page = to;
  }
</script>

<div class="min-vh-100 bg-light">
  {#if isLoggedIn() && page !== "login" && page !== "register"}
    <Navbar on:navigate={(e) => navigate(e.detail.page)} />
  {/if}

  <main class="container py-4">
    {#if page === "login"}
      <Login on:navigate={(e) => navigate(e.detail.page)} />
    {:else if page === "register"}
      <Register on:navigate={(e) => navigate(e.detail.page)} />
    {:else if page === "dashboard"}
      <Dashboard on:navigate={(e) => navigate(e.detail.page, e.detail.id)} />
    {:else if page === "kit-detail"}
      <KitDetail {kitId} on:navigate={(e) => navigate(e.detail.page)} />
    {:else if page === "categories"}
      <Categories />
    {/if}
  </main>
</div>
