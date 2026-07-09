<script>
  import { onMount, createEventDispatcher } from "svelte";
  import { getKits, createKit, updateKit, deleteKit } from "../api/kits.js";
  import { t } from "../lib/i18n/index.js";
  import Pagination from "../lib/Pagination.svelte";
  import plusLg from "../lib/icons/plus-lg.svg?raw";
  import archive from "../lib/icons/archive.svg?raw";
  import boxSeam from "../lib/icons/box-seam.svg?raw";
  import eye from "../lib/icons/eye.svg?raw";
  import pencil from "../lib/icons/pencil.svg?raw";
  import trash from "../lib/icons/trash.svg?raw";
  import checkLg from "../lib/icons/check-lg.svg?raw";

  const dispatch = createEventDispatcher();

  let kits = [];
  let loading = true;
  let showForm = false;
  let editingKit = null;
  let form = { name: "", description: "" };
  let errors = {};
  let msg = "";
  let currentPage = 1;
  const perPage = 10;

  $: paged = kits.slice((currentPage - 1) * perPage, currentPage * perPage);

  onMount(load);

  async function load() {
    loading = true;
    kits = await getKits();
    loading = false;
  }

  function openCreate() {
    editingKit = null;
    form = { name: "", description: "" };
    showForm = true;
    errors = {};
  }

  function openEdit(kit) {
    editingKit = kit;
    form = { name: kit.name, description: kit.description || "" };
    showForm = true;
    errors = {};
  }

  async function save() {
    errors = {};
    try {
      if (editingKit) {
        const updated = await updateKit(editingKit.id, form);
        kits = kits.map((k) => (k.id === updated.id ? updated : k));
        msg = $t("dashboard.updated");
      } else {
        const created = await createKit(form);
        kits = [...kits, created];
        msg = $t("dashboard.created");
      }
      showForm = false;
      setTimeout(() => (msg = ""), 3000);
    } catch (e) {
      errors = e.errors || {};
    }
  }

  async function remove(kit) {
    if (!confirm($t("dashboard.confirmDelete", { name: kit.name }))) return;
    await deleteKit(kit.id);
    kits = kits.filter((k) => k.id !== kit.id);
    msg = $t("dashboard.removed");
    setTimeout(() => (msg = ""), 3000);
  }
</script>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">{$t("dashboard.title")}</h2>
  <button
    class="btn btn-danger d-flex align-items-center gap-1"
    on:click={openCreate}
  >
    <span class="icon" aria-hidden="true">{@html plusLg}</span>{$t(
      "dashboard.newKit",
    )}
  </button>
</div>

{#if msg}
  <div class="alert alert-success py-2">{msg}</div>
{/if}

{#if showForm}
  <div class="card border-danger mb-4 shadow-sm">
    <div class="card-body">
      <h5 class="card-title">
        {editingKit ? $t("dashboard.editKit") : $t("dashboard.newKitForm")}
      </h5>
      <form on:submit|preventDefault={save}>
        <div class="mb-3">
          <label for="kit-name" class="form-label"
            >{$t("dashboard.kitName")}</label
          >
          <input
            id="kit-name"
            name="name"
            type="text"
            class="form-control"
            class:is-invalid={errors.name}
            bind:value={form.name}
            placeholder="Ex: Kit Casa"
            required
          />
          {#if errors.name}<div class="invalid-feedback">
              {errors.name[0]}
            </div>{/if}
        </div>
        <div class="mb-3">
          <label for="kit-description" class="form-label"
            >{$t("dashboard.kitDescription")}</label
          >
          <textarea
            id="kit-description"
            name="description"
            class="form-control"
            bind:value={form.description}
            rows="2"
            placeholder={$t("dashboard.kitDescriptionPlaceholder")}
          ></textarea>
        </div>
        <div class="d-flex gap-2">
          <button
            type="submit"
            class="btn btn-danger d-flex align-items-center gap-1"
          >
            <span class="icon" aria-hidden="true">{@html checkLg}</span>{$t(
              "dashboard.save",
            )}
          </button>
          <button
            type="button"
            class="btn btn-outline-secondary"
            on:click={() => (showForm = false)}
          >
            {$t("dashboard.cancel")}
          </button>
        </div>
      </form>
    </div>
  </div>
{/if}

{#if loading}
  <div class="text-center py-5">
    <div class="spinner-border text-danger"></div>
    <p class="mt-2 text-muted">{$t("dashboard.loading")}</p>
  </div>
{:else if kits.length === 0}
  <div class="text-center py-5 text-muted">
    <span class="icon" style="width:4rem;height:4rem" aria-hidden="true">
      {@html archive}
    </span>
    <p class="mt-3">{$t("dashboard.empty")}</p>
  </div>
{:else}
  <div class="row g-3">
    {#each paged as kit}
      <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body">
            <h5 class="card-title fw-bold">{kit.name}</h5>
            {#if kit.description}
              <p class="card-text text-muted small">{kit.description}</p>
            {/if}
            <span
              class="badge bg-danger-subtle text-danger d-inline-flex align-items-center gap-1"
            >
              <span class="icon" aria-hidden="true">{@html boxSeam}</span>
              {kit.items_count ?? 0}
              {$t("dashboard.items")}
            </span>
          </div>
          <div class="card-footer bg-transparent d-flex gap-2">
            <button
              class="btn btn-danger btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1"
              on:click={() =>
                dispatch("navigate", { page: "kit-detail", id: kit.id })}
            >
              <span class="icon" aria-hidden="true">{@html eye}</span>{$t(
                "dashboard.viewItems",
              )}
            </button>
            <button
              class="btn btn-outline-secondary btn-sm"
              on:click={() => openEdit(kit)}
              title="Edit"
            >
              <span class="icon" aria-hidden="true">{@html pencil}</span>
            </button>
            <form
              on:submit|preventDefault={() => remove(kit)}
              style="display:contents"
            >
              <button
                type="submit"
                class="btn btn-outline-danger btn-sm"
                title="Delete"
              >
                <span class="icon" aria-hidden="true">{@html trash}</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    {/each}
  </div>
  <Pagination
    total={kits.length}
    {perPage}
    {currentPage}
    on:pageChange={(e) => (currentPage = e.detail)}
  />
{/if}

<style>
  .icon {
    display: inline-flex;
    align-items: center;
    width: 1em;
    height: 1em;
    flex-shrink: 0;
  }
</style>
