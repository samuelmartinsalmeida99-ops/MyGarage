<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

// Recebe os veículos vindos do Laravel Controller
defineProps({
    vehicles: Array
});

// Configura o formulário com os campos da nossa base de dados
const form = useForm({
    make: '',
    model: '',
    year: new Date().getFullYear(),
    kilometers: '',
    plate_number: '',
    iuc_paid: false,
    next_inspection_date: '',
    inspection_done: false
});

// Função para enviar os dados para o servidor
// Substitui a função submit por esta para testar:
const submit = () => {
    console.log("Dados que estão a ser enviados:", form.data());

    form.post(route('vehicles.store'), {
        onSuccess: () => {
            console.log("Sucesso! O carro foi adicionado.");
            form.reset();
        },
        onError: (errors) => {
            console.error("Erros de validação do Laravel:", errors);
        }
    });
};

const handlePlateMask = (event) => {
    let value = event.target.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();

    if (value.length > 6) {
        value = value.slice(0, 6);
    }

    let formatted = '';
    if (value.length > 0) {
        formatted += value.slice(0, 2);
    }
    if (value.length > 2) {
        formatted += '-' + value.slice(2, 4);
    }
    if (value.length > 4) {
        formatted += '-' + value.slice(4, 6);
    }

    form.plate_number = formatted;
};

// Função para verificar se a inspeção está em atraso
const isInspectionOverdue = (date, done) => {
    if (!date || done) return false;
    const today = new Date().setHours(0, 0, 0, 0);
    const inspectionDate = new Date(date).setHours(0, 0, 0, 0);
    return today > inspectionDate;
};

// Função auxiliar para formatar a data sem quebrar strings no template
const formatDate = (dateString) => {
    if (!dateString) return 'Não definida';
    return new Date(dateString).toLocaleDateString('pt-PT');
};
</script>

<template>

    <Head title="A minha Garagem" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Garagem Virtual</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Adicionar Novo Veículo</h3>

                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Marca</label>
                            <input v-model="form.make" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required />
                            <div v-if="form.errors.make" class="text-red-500 text-xs mt-1">{{ form.errors.make }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Modelo</label>
                            <input v-model="form.model" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required />
                            <div v-if="form.errors.model" class="text-red-500 text-xs mt-1">{{ form.errors.model }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ano</label>
                            <input v-model="form.year" type="number"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required />
                            <div v-if="form.errors.year" class="text-red-500 text-xs mt-1">{{ form.errors.year }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quilometragem (km)</label>
                            <input v-model="form.kilometers" type="number"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required />
                            <div v-if="form.errors.kilometers" class="text-red-500 text-xs mt-1">{{
                                form.errors.kilometers }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Matrícula</label>
                            <input v-model="form.plate_number" @input="handlePlateMask" type="text"
                                placeholder="XX-XX-XX" maxlength="8"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm uppercase placeholder-gray-400 tracking-wider font-mono text-lg"
                                required />
                            <span v-if="form.errors.plate_number" class="text-sm text-red-600 font-semibold mt-1 block">
                                {{ form.errors.plate_number }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data Próxima Inspeção</label>
                            <input v-model="form.next_inspection_date" type="date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <div v-if="form.errors.next_inspection_date" class="text-red-500 text-xs mt-1">{{
                                form.errors.next_inspection_date }}</div>
                        </div>

                        <div class="md:col-span-2 flex flex-wrap gap-6 py-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input v-model="form.iuc_paid" type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <span class="ml-2 text-sm text-gray-600">IUC Pago</span>
                            </label>

                            <label class="inline-flex items-center cursor-pointer">
                                <input v-model="form.inspection_done" type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <span class="ml-2 text-sm text-gray-600">Inspeção Realizada</span>
                            </label>
                        </div>

                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ form.processing ? 'A guardar...' : 'Guardar na Garagem' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-6 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Carros Estacionados</h3>

                    <div v-if="vehicles.length === 0" class="text-gray-500 text-sm">
                        Ainda não tens nenhum carro na tua garagem.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Marca / Modelo</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ano</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kms</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Matrícula</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        IUC</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Próxima Inspeção</th>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>