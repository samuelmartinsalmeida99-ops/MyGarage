<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Recebe os veículos que enviámos através do web.php
defineProps({
    vehicles: Array
});

// Controlo do Modal de Edição
class VehicleForm { }
const isEditModalOpen = ref(false);
const selectedVehicleId = ref(null);

// Formulário reativo para a Edição (Atualizado com os novos campos)
const editForm = useForm({
    make: '',
    model: '',
    year: '',
    kilometers: '',
    plate_number: '',
    iuc_paid: false,
    next_inspection_date: '',
    inspection_done: false
});

// Função que abre o modal e preenche os campos com os dados do carro selecionado
const openEditModal = (vehicle) => {
    selectedVehicleId.value = vehicle.id;
    editForm.make = vehicle.make;
    editForm.model = vehicle.model;
    editForm.year = vehicle.year;
    editForm.kilometers = vehicle.kilometers;
    editForm.plate_number = vehicle.plate_number;
    editForm.iuc_paid = vehicle.iuc_paid === 1 || vehicle.iuc_paid === true;
    editForm.next_inspection_date = vehicle.next_inspection_date || '';
    editForm.inspection_done = vehicle.inspection_done === 1 || vehicle.inspection_done === true;
    isEditModalOpen.value = true;
};

// Submeter a alteração (Envia um pedido PUT)
const submitUpdate = () => {
    editForm.put(route('vehicles.update', selectedVehicleId.value), {
        onSuccess: () => {
            isEditModalOpen.value = false;
        }
    });
};

// Função para eliminar o carro (Envia um pedido DELETE)
const deleteVehicle = (id) => {
    if (confirm('Tens a certeza de que queres eliminar este veículo da garagem?')) {
        router.delete(route('vehicles.destroy', id));
    }
};

// Função para verificar se a inspeção está fora do prazo
const isInspectionOverdue = (date, done) => {
    if (!date || done) return false;
    const today = new Date().setHours(0, 0, 0, 0);
    const inspectionDate = new Date(date).setHours(0, 0, 0, 0);
    return today > inspectionDate;
};

// Função auxiliar para formatar a data de inspeção
const formatDate = (dateString) => {
    if (!dateString) return 'Não definida';
    return new Date(dateString).toLocaleDateString('pt-PT');
};
</script>

<template>

    <Head title="Minha Garagem" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Minha Garagem</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Visão Geral da Minha Garagem</h3>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                            Total: {{ vehicles.length }} {{ vehicles.length === 1 ? 'carro' : 'carros' }}
                        </span>
                    </div>

                    <div v-if="vehicles.length === 0" class="text-gray-500 text-center py-8">
                        Não há carros registados. Vai à página de Veículos para estacionar o teu primeiro automóvel!
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Carro
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ano</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">KM</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Matrícula</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IUC</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Próxima
                                        Inspeção
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="vehicle in vehicles" :key="vehicle.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ vehicle.make }} {{ vehicle.model }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ vehicle.year }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ vehicle.kilometers ? vehicle.kilometers.toLocaleString() : 0 }} km
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span
                                            class="px-2 py-1 bg-gray-100 border border-gray-300 rounded font-mono text-xs">
                                            {{ vehicle.plate_number }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            :class="vehicle.iuc_paid ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'"
                                            class="px-2 py-0.5 rounded-full text-xs font-semibold">
                                            {{ vehicle.iuc_paid ? 'Pago' : 'Pendente' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex items-center gap-2">
                                            <span :class="vehicle.inspection_done ? 'text-green-600 font-medium' : ''">
                                                {{ formatDate(vehicle.next_inspection_date) }}
                                            </span>
                                            <div v-if="isInspectionOverdue(vehicle.next_inspection_date, vehicle.inspection_done)"
                                                title="Atenção: A data limite desta inspeção já foi ultrapassada!"
                                                class="cursor-help text-amber-500 animate-pulse">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                                        <button @click="openEditModal(vehicle)"
                                            class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                            Editar
                                        </button>
                                        <button @click="deleteVehicle(vehicle.id)"
                                            class="text-red-600 hover:text-red-900 font-semibold">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div v-if="isEditModalOpen"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Veículo</h3>

                <form @submit.prevent="submitUpdate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Marca</label>
                        <input v-model="editForm.make" type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Modelo</label>
                        <input v-model="editForm.model" type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ano</label>
                        <input v-model="editForm.year" type="number"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quilometragem</label>
                        <input v-model="editForm.kilometers" type="number"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Matrícula</label>
                        <input v-model="editForm.plate_number" type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data Próxima Inspeção</label>
                        <input v-model="editForm.next_inspection_date" type="date"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>

                    <div class="flex flex-wrap gap-4 py-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input v-model="editForm.iuc_paid" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                            <span class="ml-2 text-sm text-gray-600">IUC Pago</span>
                        </label>

                        <label class="inline-flex items-center cursor-pointer">
                            <input v-model="editForm.inspection_done" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                            <span class="ml-2 text-sm text-gray-600">Inspeção Realizada</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" @click="isEditModalOpen = false"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="editForm.processing"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50">
                            {{ editForm.processing ? 'A guardar...' : 'Guardar Alterações' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>