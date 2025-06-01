<template>
    <div>
        <heading class="mb-6">{{ __('admin.console_command') }}</heading>
        <div v-if="commands.length > 0">
            <div class="flex">
                <div class="relative h-9 w-full md:w-1/3 md:flex-shrink-0 mb-6 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         width="20"
                         height="24" class="inline-block absolute ml-2 text-gray-400" style="top: 4px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input v-model="searchQuery"
                           class="appearance-none bg-white dark:bg-gray-800 shadow rounded-full h-8 w-full dark:focus:bg-gray-800
                  appearance-none rounded-full h-8 pl-10 w-full focus:bg-white focus:outline-none focus:ring
                  ring-primary-200 dark:ring-gray-600 appearance-none bg-white dark:bg-gray-800 shadow rounded-full
                  h-8 w-full dark:focus:bg-gray-800"
                           placeholder="Search" type="search" spellcheck="false" aria-label="Search">
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div
                    class="flex flex-col md:flex-row md:items-center py-3 border-b border-gray-200 dark:border-gray-700"></div>
                <div class="relative">
                    <div class="overflow-hidden overflow-x-auto relative">
                        <table class="w-full table-default">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
                                    <span>{{ __('Id') }}</span>
                                </th>
                                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
                                    <span>{{ __("Name") }}</span>
                                </th>
                                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2">
                                    <span>{{ __("Description") }}</span>
                                </th>
                                <th class="uppercase text-xxs tracking-wide px-2 py-2"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="group" v-for="(command, index) in commandsFiltered">
                                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap dark:bg-gray-800">
                                    <div class="text-center">
                                        <Link class="link-default" :href="'/nova-vendor/console-commands/' + command.name">
                                            {{ index + 1 }}
                                        </Link>
                                    </div>
                                </td>
                                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer
                  dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                    <div class="text-left text-left">
                                        {{ command.name }}
                                    </div>
                                </td>
                                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer
                  dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                    <div class="text-left text-left">
                                        {{ command.description }}
                                    </div>
                                </td>
                                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer
                  dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                    <div class="inline-flex items-center">
                    <span class="mr-3">
                      <Link class="link-default" :href="'/nova-vendor/console-commands/' + command.name">Open</Link>
                    </span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="relative">
                    <div class="flex flex-col justify-center items-center px-6 py-8">
                        <svg class="inline-block text-gray-300 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                             width="65"
                             height="51" viewBox="0 0 65 51">
                            <path class="fill-current" d="M56 40h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1
               1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1
               1v2zm-5.364125-8H38v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H6c-3.313708
               0-6-2.686292-6-6V6c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375C61.053323 31.5511
               65 35.814652 65 41c0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM20 30h16v-8H20v8zm0
                2v8h16v-8H20zm34-2v-8H38v8h16zM2 30h16v-8H2v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8H2zm18-12h16v-8H20v8zm34
                 0v-8H38v8h16zM2 20h16v-8H2v8zm52-10V6c0-2.209139-1.790861-4-4-4H6C3.790861 2 2 3.790861 2 6v4h52zm1
                 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z">
                            </path>
                        </svg>
                        <h3 class="text-base font-normal mt-3">There are no available commands. Please set the class
                            property '
                            isVisibleOnUI' to true to required commands.</h3></div>
                    <div class="overflow-hidden overflow-x-auto relative"><!----></div>
                    <div class="border-t border-gray-200 dark:border-gray-700"><!----></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useLocalization } from 'laravel-nova'

const { __ } = useLocalization()

export default {
    metaInfo() {
        return {
            title: 'Console Commands',
        }
    },
    data() {
        return {
            searchQuery: null,
            commands: null,
        }
    },
    mounted() {
        Nova.request().get('/nova-vendor/console-commands', {
            params: {}
        }).then((result) => {
            this.commands = result.data
        }).catch((reason) => {
            Nova.error(reason);
        });
    },
    computed: {
        commandsFiltered() {
            if (this.searchQuery) {
                return this.commands.filter(command => {
                    return command.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                })
            } else {
                return this.commands;
            }
        }
    }
}
</script>
