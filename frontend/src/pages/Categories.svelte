<script>
  import { onMount } from "svelte";
  import {
    getCategories,
    createCategory,
    updateCategory,
    deleteCategory,
  } from "../api/categories.js";
  import { t } from "../lib/i18n/index.js";
  import Pagination from "../lib/Pagination.svelte";
  import plusLg from "../lib/icons/plus-lg.svg?raw";
  import tags from "../lib/icons/tags.svg?raw";
  import tag from "../lib/icons/tag.svg?raw";
  import pencil from "../lib/icons/pencil.svg?raw";
  import trash from "../lib/icons/trash.svg?raw";
  import checkLg from "../lib/icons/check-lg.svg?raw";

  let categories = [];
  let loading = true;
  let showForm = false;
  let editingCat = null;
  let name = "";
  let errors = {};
  let msg = "";
  let currentPage = 1;
  const perPage = 10;

  $: paged = categories.slice(
    (currentPage - 1) * perPage,
    currentPage * perPage,
  );

  onMount(load);

  async function load() {
    loading = true;
    categories = await getCategories();
    loading = false;
  }

  function openCreate() {
    editingCat = null;
    name = "";
    showForm = true;
    errors = {};
  }

  function openEdit(cat) {
    editingCat = cat;
    name = cat.name;
    showForm = true;
    errors = {};
  }

  async function save() {
    errors = {};
    try {
      if (editingCat) {
        await updateCategory(editingCat.id, { name });
        msg = $t("categories.updated");
      } else {
        await createCategory({ name });
        msg = $t("categories.created");
      }
      showForm = false;
      await load();
      setTimeout(() => (msg = ""), 3000);
    } catch (e) {
      errors = e.errors || {};
    }
  }

  async function remove(cat) {
    if (!confirm($t("categories.confirmDelete", { name: cat.name }))) return;
    await deleteCategory(cat.id);
    categories = categories.filter((c) => c.id !== cat.id);
    msg = $t("categories.removed");
    setTimeout(() => (msg = ""), 3000);
  }
</script>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">{$t("categories.title")}</h2>
  <button
    class="btn btn-danger d-flex align-items-center gap-1"
    on:click={openCreate}
  >
    <span class="icon" aria-hidden="true">{@html plusLg}</span>{$t(
      "categories.new",
    )}
  </button>
</div>

{#if msg}
  <div class="alert alert-success py-2">{msg}</div>
{/if}

{#if showForm}
  <div class="card border-danger mb-4 shadow-sm">
    <div class="card-body">
      <h6>
        {editingCat ? $t("categories.editForm") : $t("categories.newForm")}
      </h6>
      <form on:submit|preventDefault={save}>
        <div class="mb-3">
          <label for="category-name" class="form-label"
            >{$t("categories.namePlaceholder")}</label
          >
          <input
            id="category-name"
            name="name"
            type="text"
            class="form-control"
            class:is-invalid={errors.name}
            bind:value={name}
            placeholder={$t("categories.namePlaceholder")}
            required
          />
          {#if errors.name}<div class="invalid-feedback">
              {errors.name[0]}
            </div>{/if}
        </div>
        <div class="d-flex gap-2">
          <button
            type="submit"
            class="btn btn-danger btn-sm d-flex align-items-center gap-1"
          >
            <span class="icon" aria-hidden="true">{@html checkLg}</span>{$t(
              "categories.save",
            )}
          </button>
          <button
            type="button"
            class="btn btn-outline-secondary btn-sm"
            on:click={() => (showForm = false)}
          >
            {$t("categories.cancel")}
          </button>
        </div>
      </form>
    </div>
  </div>
{/if}

{#if loading}
  <div class="text-center py-5">
    <div class="spinner-border text-danger"></div>
  </div>
{:else if categories.length === 0}
  <div class="text-center py-5 text-muted">
    <span class="icon" style="width:3rem;height:3rem" aria-hidden="true"
      >{@html tags}</span
    >
    <p class="mt-3">{$t("categories.empty")}</p>
  </div>
{:else}
  <ul class="list-group shadow-sm">
    {#each paged as cat}
      <li
        class="list-group-item d-flex justify-content-between align-items-center"
      >
        <span class="d-flex align-items-center gap-2">
          <span class="icon text-danger" aria-hidden="true">{@html tag}</span>
          <span class="fw-semibold">{cat.name}</span>
        </span>
        <div class="d-flex gap-2">
          <button
            class="btn btn-outline-secondary btn-sm"
            on:click={() => openEdit(cat)}
            title="Edit"
          >
            <span class="icon" aria-hidden="true">{@html pencil}</span>
          </button>
          <form
            on:submit|preventDefault={() => remove(cat)}
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
      </li>
    {/each}
  </ul>
  <Pagination
    total={categories.length}
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
