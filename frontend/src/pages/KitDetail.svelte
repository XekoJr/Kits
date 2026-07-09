<script>
  import { onMount, createEventDispatcher } from "svelte";
  import { getKit } from "../api/kits.js";
  import {
    createItem,
    updateItem,
    deleteItem,
    toggleItem,
  } from "../api/items.js";
  import { getCategories } from "../api/categories.js";
  import { t } from "../lib/i18n/index.js";
  import Pagination from "../lib/Pagination.svelte";
  import plusLg from "../lib/icons/plus-lg.svg?raw";
  import arrowLeft from "../lib/icons/arrow-left.svg?raw";
  import inbox from "../lib/icons/inbox.svg?raw";
  import pencil from "../lib/icons/pencil.svg?raw";
  import trash from "../lib/icons/trash.svg?raw";
  import checkLg from "../lib/icons/check-lg.svg?raw";

  export let kitId;
  const dispatch = createEventDispatcher();

  let kit = null;
  let items = [];
  let categories = [];
  let loading = true;
  let showForm = false;
  let editingItem = null;
  let form = { name: "", quantity: 1, category_id: "", purchased: false };
  let errors = {};
  let filterCategory = "";
  let msg = "";
  let currentPage = 1;
  const perPage = 10;

  onMount(async () => {
    [kit, categories] = await Promise.all([getKit(kitId), getCategories()]);
    items = kit.items;
    loading = false;
  });

  $: filtered = filterCategory
    ? items.filter((i) => String(i.category_id) === filterCategory)
    : items;

  $: {
    filterCategory;
    currentPage = 1;
  }

  $: paged = filtered.slice((currentPage - 1) * perPage, currentPage * perPage);

  $: purchased = items.filter((i) => i.purchased).length;
  $: total = items.length;

  function openCreate() {
    editingItem = null;
    form = { name: "", quantity: 1, category_id: "", purchased: false };
    showForm = true;
    errors = {};
  }

  function openEdit(item) {
    editingItem = item;
    form = {
      name: item.name,
      quantity: item.quantity,
      category_id: item.category_id || "",
      purchased: item.purchased,
    };
    showForm = true;
    errors = {};
  }

  async function save() {
    errors = {};
    const payload = { ...form, category_id: form.category_id || null };
    try {
      if (editingItem) {
        const updated = await updateItem(kitId, editingItem.id, payload);
        items = items.map((i) => (i.id === updated.id ? updated : i));
        msg = $t("kit.itemUpdated");
      } else {
        const created = await createItem(kitId, payload);
        items = [...items, created];
        msg = $t("kit.itemAdded");
      }
      showForm = false;
      setTimeout(() => (msg = ""), 3000);
    } catch (e) {
      errors = e.errors || {};
    }
  }

  async function remove(item) {
    if (!confirm($t("kit.confirmDelete", { name: item.name }))) return;
    await deleteItem(kitId, item.id);
    items = items.filter((i) => i.id !== item.id);
  }

  async function toggle(item) {
    const updated = await toggleItem(kitId, item.id);
    items = items.map((i) => (i.id === updated.id ? updated : i));
  }
</script>

{#if loading}
  <div class="text-center py-5">
    <div class="spinner-border text-danger"></div>
  </div>
{:else}
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
      <button
        class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"
        on:click={() => dispatch("navigate", { page: "dashboard" })}
      >
        <span class="icon" aria-hidden="true">{@html arrowLeft}</span>{$t(
          "kit.back",
        )}
      </button>
      <span class="fw-bold fs-4">{kit.name}</span>
      {#if kit.description}
        <small class="text-muted">{kit.description}</small>
      {/if}
    </div>
    <button
      class="btn btn-danger d-flex align-items-center gap-1"
      on:click={openCreate}
    >
      <span class="icon" aria-hidden="true">{@html plusLg}</span>{$t(
        "kit.addItem",
      )}
    </button>
  </div>

  <div class="card border-0 bg-danger-subtle mb-4 p-3">
    <div class="d-flex justify-content-between align-items-center mb-1">
      <small class="fw-bold text-danger">{$t("kit.progress")}</small>
      <small class="text-muted">{purchased}/{total} {$t("kit.purchased")}</small
      >
    </div>
    <div class="progress" style="height:8px">
      <div
        class="progress-bar bg-danger"
        style="width:{total > 0 ? (purchased / total) * 100 : 0}%"
      ></div>
    </div>
  </div>

  {#if msg}
    <div class="alert alert-success py-2">{msg}</div>
  {/if}

  <div class="mb-3">
    <label for="filter-category" class="form-label visually-hidden"
      >{$t("kit.allCategories")}</label
    >
    <select
      id="filter-category"
      name="filter_category"
      class="form-select w-auto"
      bind:value={filterCategory}
    >
      <option value="">{$t("kit.allCategories")}</option>
      {#each categories as cat}
        <option value={String(cat.id)}>{cat.name}</option>
      {/each}
    </select>
  </div>

  {#if showForm}
    <div class="card border-danger mb-4 shadow-sm">
      <div class="card-body">
        <h6>{editingItem ? $t("kit.editItem") : $t("kit.newItem")}</h6>
        <form on:submit|preventDefault={save}>
          <div class="row g-2">
            <div class="col-md-4">
              <label for="item-name" class="form-label"
                >{$t("kit.itemName")}</label
              >
              <input
                id="item-name"
                name="name"
                type="text"
                class="form-control"
                class:is-invalid={errors.name}
                bind:value={form.name}
                placeholder={$t("kit.itemName")}
                required
              />
              {#if errors.name}<div class="invalid-feedback">
                  {errors.name[0]}
                </div>{/if}
            </div>
            <div class="col-md-2">
              <label for="item-quantity" class="form-label"
                >{$t("kit.quantity")}</label
              >
              <input
                id="item-quantity"
                name="quantity"
                type="number"
                min="1"
                class="form-control"
                bind:value={form.quantity}
                required
              />
            </div>
            <div class="col-md-3">
              <label for="item-category" class="form-label"
                >{$t("kit.noCategory")}</label
              >
              <select
                id="item-category"
                name="category_id"
                class="form-select"
                bind:value={form.category_id}
              >
                <option value="">{$t("kit.noCategory")}</option>
                {#each categories as cat}
                  <option value={String(cat.id)}>{cat.name}</option>
                {/each}
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2 pb-1">
              <input
                id="item-purchased"
                name="purchased"
                type="checkbox"
                class="form-check-input"
                bind:checked={form.purchased}
              />
              <label for="item-purchased" class="form-check-label"
                >{$t("kit.alreadyPurchased")}</label
              >
            </div>
          </div>
          <div class="d-flex gap-2 mt-3">
            <button
              type="submit"
              class="btn btn-danger btn-sm d-flex align-items-center gap-1"
            >
              <span class="icon" aria-hidden="true">{@html checkLg}</span>{$t(
                "kit.save",
              )}
            </button>
            <button
              type="button"
              class="btn btn-outline-secondary btn-sm"
              on:click={() => (showForm = false)}
            >
              {$t("kit.cancel")}
            </button>
          </div>
        </form>
      </div>
    </div>
  {/if}

  {#if filtered.length === 0}
    <div class="text-center py-4 text-muted">
      <span class="icon" style="width:2rem;height:2rem" aria-hidden="true"
        >{@html inbox}</span
      >
      <p class="mt-2">{$t("kit.empty")}</p>
    </div>
  {:else}
    <ul class="list-group shadow-sm">
      {#each paged as item}
        <li
          class="list-group-item d-flex align-items-center gap-3"
          class:list-group-item-success={item.purchased}
        >
          <input
            type="checkbox"
            id="toggle-{item.id}"
            name="purchased"
            class="form-check-input flex-shrink-0"
            checked={item.purchased}
            on:change={() => toggle(item)}
          />
          <label
            for="toggle-{item.id}"
            class="flex-grow-1 mb-0"
            class:text-decoration-line-through={item.purchased}
            style="cursor:pointer"
          >
            <span class="fw-semibold">{item.name}</span>
            <small class="text-muted ms-1">×{item.quantity}</small>
            {#if item.category}
              <span class="badge bg-danger-subtle text-danger ms-2"
                >{item.category.name}</span
              >
            {/if}
          </label>
          <button
            class="btn btn-outline-secondary btn-sm"
            on:click={() => openEdit(item)}
            title="Edit"
          >
            <span class="icon" aria-hidden="true">{@html pencil}</span>
          </button>
          <form
            on:submit|preventDefault={() => remove(item)}
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
        </li>
      {/each}
    </ul>
    <Pagination
      total={filtered.length}
      {perPage}
      {currentPage}
      on:pageChange={(e) => (currentPage = e.detail)}
    />
  {/if}
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
