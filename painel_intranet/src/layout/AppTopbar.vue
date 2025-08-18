<script setup>
import { useLayout } from '@/layout/composables/layout';
import AppConfigurator from './AppConfigurator.vue';
import { ref } from 'vue';

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();

// Simulação do usuário logado
const user = ref({
    name: 'Eduardo Castro',
    avatar: 'http://localhost:8000/storage/imagens/colaboradores/849.446.544-65/w2oq3azguQQUvUt2A6Dn57dOml0MsvViAac8RQK9.jpg'
});

const logout = ref(() => {
    localStorage.clear();
    window.location.href = '/';
});

const imgUser = localStorage.getItem('imagem');
const nameUser = localStorage.getItem('usuario');
</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <!-- Logo -->
            <router-link to="/" class="layout-topbar-logo">
                <img src="https://link.gruporialma.com.br/assets/logo_sem_fundo-4776d6e6.png" alt="Logo" />
            </router-link>

            <!-- Botão para abrir o menu lateral -->
            <button class="layout-menu-button" @click="toggleMenu">
                <i class="pi pi-bars"></i>
            </button>
        </div>

        <div class="layout-topbar-actions">
            <!-- Modo claro/escuro -->
            <!-- <button style="color: white" type="button" class="layout-topbar-action" @click="toggleDarkMode">
                <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
            </button> -->


            <!-- Logout -->
            <router-link to="/" class="layout-topbar-action">
                <i @click="logout()" class="pi pi-sign-out text-white"></i>
            </router-link>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.layout-topbar {
    background: #1e40af;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 1rem;
    height: 70px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.layout-topbar-logo-container {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.layout-menu-button {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.4rem;
    cursor: pointer;
    transition:
        transform 0.2s,
        opacity 0.2s;
}
.layout-menu-button:hover {
    transform: scale(1.1);
    opacity: 0.8;
}

.layout-topbar-logo img {
    height: 35px;
}

.layout-topbar-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.layout-topbar-action {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    transition:
        transform 0.2s,
        opacity 0.2s;
}
.layout-topbar-action:hover {
    transform: scale(1.1);
    opacity: 0.85;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid white;
}

.user-name {
    font-size: 0.95rem;
    font-weight: 500;
}
</style>
