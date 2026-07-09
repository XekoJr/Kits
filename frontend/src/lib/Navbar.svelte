<script>
  import { createEventDispatcher } from 'svelte';
  import { logout } from '../api/auth.js';
  import { locale, t } from './i18n/index.js';
  import shieldFillCheck from './icons/shield-fill-check.svg?raw';
  import grid from './icons/grid.svg?raw';
  import tags from './icons/tags.svg?raw';
  import boxArrowRight from './icons/box-arrow-right.svg?raw';

  const dispatch = createEventDispatcher();
  const user = JSON.parse(localStorage.getItem('user') || '{}');

  async function handleLogout() {
    await logout();
    dispatch('navigate', { page: 'login' });
  }

  function toggleLocale() {
    locale.update(l => l === 'en' ? 'pt' : 'en');
  }
</script>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#/dashboard">
      <span class="icon" aria-hidden="true">{@html shieldFillCheck}</span>{$t('app.name')}
    </a>
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-1"
              on:click={() => dispatch('navigate', { page: 'dashboard' })}>
        <span class="icon" aria-hidden="true">{@html grid}</span>{$t('nav.kits')}
      </button>
      <button class="btn btn-outline-light btn-sm d-flex align-items-center gap-1"
              on:click={() => dispatch('navigate', { page: 'categories' })}>
        <span class="icon" aria-hidden="true">{@html tags}</span>{$t('nav.categories')}
      </button>
      <span class="text-white-50 small">{user.name || ''}</span>
      <button class="btn btn-outline-light btn-sm fw-bold" on:click={toggleLocale}
              title="Switch language">
        {$locale === 'en' ? '🇵🇹 PT' : '🇬🇧 EN'}
      </button>
      <button class="btn btn-light btn-sm d-flex align-items-center gap-1" on:click={handleLogout}>
        <span class="icon" aria-hidden="true">{@html boxArrowRight}</span>{$t('auth.logout')}
      </button>
    </div>
  </div>
</nav>

<style>
  .icon {
    display: inline-flex;
    align-items: center;
    width: 1em;
    height: 1em;
    flex-shrink: 0;
  }
</style>
