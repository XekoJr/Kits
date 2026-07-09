<script>
  import { createEventDispatcher } from "svelte";
  import { register } from "../api/auth.js";
  import { t } from "../lib/i18n/index.js";
  import shieldFillCheck from "../lib/icons/shield-fill-check.svg?raw";

  const dispatch = createEventDispatcher();

  let name = "";
  let email = "";
  let password = "";
  let password_confirmation = "";
  let errors = {};
  let loading = false;

  async function handleRegister() {
    errors = {};
    loading = true;
    try {
      const data = await register(name, email, password, password_confirmation);
      localStorage.setItem("token", data.token);
      localStorage.setItem("user", JSON.stringify(data.user));
      dispatch("navigate", { page: "dashboard" });
    } catch (e) {
      errors = e.errors || { general: [e.message] };
    } finally {
      loading = false;
    }
  }
</script>

<div class="row justify-content-center mt-5">
  <div class="col-md-5">
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
          <h3 class="mt-2 fw-bold">{$t("auth.register")}</h3>
          <p class="text-muted small">{$t("auth.registerTagline")}</p>
        </div>

        {#if errors.general}
          <div class="alert alert-danger py-2">{errors.general[0]}</div>
        {/if}

        <form on:submit|preventDefault={handleRegister}>
          <div class="mb-3">
            <label for="name" class="form-label">{$t("auth.name")}</label>
            <input
              id="name"
              name="name"
              type="text"
              class="form-control"
              class:is-invalid={errors.name}
              bind:value={name}
              placeholder={$t("auth.namePlaceholder")}
              autocomplete="name"
              required
            />
            {#if errors.name}<div class="invalid-feedback">
                {errors.name[0]}
              </div>{/if}
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">{$t("auth.email")}</label>
            <input
              id="email"
              name="email"
              type="email"
              class="form-control"
              class:is-invalid={errors.email}
              bind:value={email}
              placeholder={$t("auth.emailPlaceholder")}
              autocomplete="email"
              required
            />
            {#if errors.email}<div class="invalid-feedback">
                {errors.email[0]}
              </div>{/if}
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
              class:is-invalid={errors.password}
              bind:value={password}
              placeholder={$t("auth.passwordPlaceholder")}
              autocomplete="new-password"
              required
            />
            {#if errors.password}<div class="invalid-feedback">
                {errors.password[0]}
              </div>{/if}
          </div>

          <div class="mb-4">
            <label for="password_confirmation" class="form-label"
              >{$t("auth.confirmPassword")}</label
            >
            <input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              class="form-control"
              bind:value={password_confirmation}
              placeholder={$t("auth.confirmPasswordPlaceholder")}
              autocomplete="new-password"
              required
            />
          </div>

          <button type="submit" class="btn btn-danger w-100" disabled={loading}>
            {#if loading}<span class="spinner-border spinner-border-sm me-2"
              ></span>{/if}
            {$t("auth.register")}
          </button>
        </form>

        <hr />
        <p class="text-center text-muted small mb-0">
          {$t("auth.hasAccount")}
          <a
            href="#/login"
            on:click={() => dispatch("navigate", { page: "login" })}
          >
            {$t("auth.loginLink")}
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
