<script setup>
import { ref } from 'vue';
import AppMenuItem from './AppMenuItem.vue';

// Importa os componentes do PrimeVue que vamos usar
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import Menu from 'primevue/menu';

const admin = localStorage.getItem('admin') === '1'; // Verifica se é admin

const mainMenuItems = ref([
    {
        label: 'RH Connect',
        items: [{ label: 'Meu RH', icon: 'pi pi-fw pi-home', to: '/inicio' }]
    },
    // Só adiciona se for admin
    ...(admin
        ? [
              {
                  label: 'Gestão',
                  items: [
                      { label: 'Importação', icon: 'pi pi-fw pi-file', to: '/importacao' },
                      { label: 'Avisos', icon: 'pi pi-fw pi-volume-up', to: '/avisos' },
                      { label: 'Colaboradores', icon: 'pi pi-fw pi-user', to: '/colaboradores' },
                      { label: 'Perguntas Frequentes', icon: 'pi pi-fw pi-question', to: '/faq' }
                  ]
              }
          ]
        : [])
]);

const usuarioNome = localStorage.getItem('usuario');
const usuarioImagem = localStorage.getItem('imagem');
const usuarioCargo = localStorage.getItem('cargo');

// Dados e lógica para o menu de perfil do usuário
const profileMenu = ref();

const toggleProfileMenu = (event) => {
    profileMenu.value.toggle(event);
};
</script>

<template>
    <div class="layout-sidebar">
        <!-- Perfil do usuário -->
        <div class="user-profile" @click="toggleProfileMenu($event)">
            <div class="avatar-wrapper">
                <img :src="'http://localhost:8000/storage/' + usuarioImagem" alt="Avatar" class="avatar" />
            </div>

            <div class="user-info">
                <span class="user-name">{{ usuarioNome }}</span
                ><br />
                <span class="user-email">{{ usuarioCargo }}</span>
            </div>
        </div>

        <hr class="menu-divider" />

        <!-- Menu principal -->
        <ul class="layout-menu">
            <template v-for="(item, i) in mainMenuItems" :key="i">
                <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
                <li v-if="item.separator" class="menu-separator"></li>
            </template>
        </ul>
    </div>
</template>

<style lang="scss" scoped>
.layout-sidebar {
    padding: 1rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.sidebar-logo {
    margin-bottom: 1.5rem;
    img {
        max-width: 120px;
        height: auto;
    }
}

.user-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.6rem;
    padding: 1rem;
    background-color: var(--surface-card);
    border-radius: var(--border-radius);
    box-shadow: var(--p-shadow-1);
    margin-bottom: 1.5rem;
    width: 100%;
    cursor: pointer;
    transition: transform 0.2s;

    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }
}

.avatar-wrapper {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #4e73df; // cor azul da plataforma RH Connect
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-info {
    text-align: center;

    .user-name {
        font-weight: 700;
        font-size: 1.1rem;
        color: #2c3e50;
    }

    .user-email {
        font-size: 0.9rem;
        color: #7f8c8d;
    }
}

.menu-divider {
    border: none;
    height: 1px;
    background-color: var(--surface-border);
    margin: 1rem 0;
    width: 100%;
}

.layout-menu {
    flex-grow: 1;
    overflow-y: auto;
    width: 100%;
}
</style>
