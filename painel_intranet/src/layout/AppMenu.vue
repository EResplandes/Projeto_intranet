<script setup>
import { ref } from 'vue';
import AppMenuItem from './AppMenuItem.vue';

// Importa os componentes do PrimeVue que vamos usar
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import Menu from 'primevue/menu';

// Dados do menu principal
const mainMenuItems = ref([
    {
        label: 'Gestão Intranet',
        items: [
            { label: 'Avisos', icon: 'pi pi-fw pi-volume-up', to: '/avisos' },
            { label: 'Colaboradores', icon: 'pi pi-fw pi-user', to: '/colaboradores' },
            { label: 'Perguntas Frequentes', icon: 'pi pi-fw pi-question', to: '/faq' }
        ]
    },
    {
        label: 'RH Connect',
        items: [
            { label: 'Início', icon: 'pi pi-fw pi-home', to: '/inicio' },
            { label: 'Banco de Horas', icon: 'pi pi-fw pi-clock', to: '/avisos' },
            { label: 'Contracheques', icon: 'pi pi-fw pi-credit-card', to: '/faq' },
            { label: 'Atualização Cadastral', icon: 'pi pi-fw pi-user', to: '/colaboradores' }
        ]
    },
    {
        label: 'Gestão Contracheques',
        items: [
            { label: 'Importação', icon: 'pi pi-fw pi-home', to: '/importacao' },
            { label: 'Validação', icon: 'pi pi-fw pi-home', to: '/inicio' }
        ]
    }
]);

const permissoes = localStorage.getItem('permissoes');
const usuarioNome = localStorage.getItem('usuario');
const usuarioImagem = localStorage.getItem('imagem');
const usuarioCargo = localStorage.getItem('cargo');

// Dados e lógica para o menu de perfil do usuário
const profileMenu = ref();
const profileItems = ref([
    {
        label: 'Meu Perfil',
        icon: 'pi pi-user-edit',
        to: '/perfil'
    },
    {
        label: 'Configurações',
        icon: 'pi pi-cog',
        to: '/configuracoes'
    },
    {
        separator: true
    },
    {
        label: 'Sair',
        icon: 'pi pi-sign-out',
        command: () => {
            // Coloque aqui a sua lógica de logout
            console.log('Usuário deslogado.');
        }
    }
]);

const toggleProfileMenu = (event) => {
    profileMenu.value.toggle(event);
};
</script>

<template>
    <div class="layout-sidebar">
        <!-- Perfil do usuário -->
        <div class="user-profile">
            <img :src="'http://localhost:8000/storage/' + usuarioImagem" alt="Avatar" class="avatar" />

            <div class="user-info">
                <span class="user-name">{{ usuarioNome }}</span
                ><br />
                <span class="user-email">{{ usuarioCargo }}</span>
            </div>
        </div>

        <!-- Menu de perfil -->

        <hr class="menu-divider" />

        <!-- Menu principal -->
        <ul class="layout-menu">
            <template v-for="(item, i) in mainMenuItems" :key="item">
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
}

.avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    border: 2px solid rgb(0, 0, 0);
    object-fit: cover;
}

.user-info {
    text-align: center;

    .user-name {
        font-weight: 600;
        font-size: 1rem;
    }

    .user-email {
        font-size: 0.85rem;
        color: var(--text-color-secondary);
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
