<script>
  import { createEventDispatcher } from "svelte";
  import { login } from "../api/auth.js";
  import { t } from "../lib/i18n/index.js";
  import shieldFillCheck from "../lib/icons/shield-fill-check.svg?raw";

  const dispatch = createEventDispatcher();

  let email = "";
  let password = "";
  let error = "";
  let loading = false;

  async function handleLogin() {
    error = "";
    loading = true;
    try {
      const data = await login(email, password);
      localStorage.setItem("token", data.token);
      localStorage.setItem("user", JSON.stringify(data.user));
      dispatch("navigate", { page: "dashboard" });
    } catch (e) {
      error = e.message || $t("auth.loginError");
    } finally {
      loading = false;
    }
  }
</script>

<div class="row justify-content-center mt-5">
  <div class="col-md-4">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="text-center mb-4">
          <span
            class="icon text-danger"
            style="width:3rem;height:3rem"
            aria-hidden="true"
          >
            {@html shieldFillCheck}
          </span>
          <h3 class="mt-2 fw-bold">{$t("app.name")}</h3>
          <p class="text-muted small">{$t("auth.loginTagline")}</p>
        </div>

        {#if error}
          <div class="alert alert-danger py-2">{error}</div>
        {/if}

        <form on:submit|preventDefault={handleLogin}>
          <div class="mb-3">
            <label for="email" class="form-label">{$t("auth.email")}</label>
            <input
              id="email"
              name="email"
              type="email"
              class="form-control"
              bind:value={email}
              placeholder={$t("auth.emailPlaceholder")}
              autocomplete="email"
              required
            />
          </div>
          <div class="mb-3">
            <label for="password" class="form-label"
              >{$t("auth.password")}</label
            >
            <input
              id="password"
              name="password"
              type="password"
              class="form-control"
              bind:value={password}
              placeholder="••••••••"
              autocomplete="current-password"
              required
            />
          </div>
          <button type="submit" class="btn btn-danger w-100" disabled={loading}>
            {#if loading}<span class="spinner-border spinner-border-sm me-2"
              ></span>{/if}
            {$t("auth.login")}
          </button>
        </form>

        <hr />
        <p class="text-center text-muted small mb-0">
          {$t("auth.noAccount")}
          <a
            href="#/register"
            on:click={() => dispatch("navigate", { page: "register" })}
          >
            {$t("auth.registerLink")}
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<style>
  .icon {
    display: inline-flex;
    align-items: center;
    width: 1em;
    height: 1em;
    flex-shrink: 0;
  }
</style>
