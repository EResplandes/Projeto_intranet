const token = localStorage.getItem('token');

import API_URL from './Config.js';

export default class FaqService {
    async importaFolha(mes, ano, folha) {
        const formData = new FormData();
        formData.append('mes', mes ?? null);
        formData.append('ano', ano ?? null);
        formData.append('folha', folha ?? null);

        return fetch(`${API_URL}/folha/importacao-folha`, {
            method: 'POST',
            body: formData
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async buscaFolhasColaborador(id) {
        return await fetch(`${API_URL}/folha/busca-folhas/${id}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async validacaoFolha(id) {
        return await fetch(`${API_URL}/folha/validacao-folha/${id}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async painelColaborador(id) {
        return await fetch(`${API_URL}/folha/painel-colaborador/${id}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }

    async secullum(cpf) {
        return await fetch(`${API_URL}/folha/secullum/${cpf}`, {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                Authorization: 'Bearer ' + token
            }
        })
            .then((res) => res.json())
            .then((d) => {
                return d;
            })
            .catch((error) => {
                console.error('Error:', error);
                throw error;
            });
    }
}
