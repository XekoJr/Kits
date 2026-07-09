<script>
  import { createEventDispatcher } from 'svelte';
  import { t } from './i18n/index.js';

  export let total = 0;
  export let perPage = 10;
  export let currentPage = 1;

  const dispatch = createEventDispatcher();

  $: totalPages = Math.ceil(total / perPage);
  $: from = (currentPage - 1) * perPage + 1;
  $: to = Math.min(currentPage * perPage, total);
</script>

{#if totalPages > 1}
  <nav aria-label="pagination" class="d-flex justify-content-between align-items-center mt-3">
    <small class="text-muted">{$t('pagination.showing', { from, to, total })}</small>
    <ul class="pagination pagination-sm mb-0">
      <li class="page-item" class:disabled={currentPage === 1}>
        <button class="page-link"
                on:click={() => dispatch('pageChange', currentPage - 1)}
                disabled={currentPage === 1}>
          &lsaquo;
        </button>
      </li>

      {#each Array.from({ length: totalPages }, (_, i) => i + 1) as page}
        <li class="page-item" class:active={page === currentPage}>
          <button class="page-link" on:click={() => dispatch('pageChange', page)}>{page}</button>
        </li>
      {/each}

      <li class="page-item" class:disabled={currentPage === totalPages}>
        <button class="page-link"
                on:click={() => dispatch('pageChange', currentPage + 1)}
                disabled={currentPage === totalPages}>
          &rsaquo;
        </button>
      </li>
    </ul>
  </nav>
{/if}
