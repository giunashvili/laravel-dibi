<template>
    <div class="flex-1 flex flex-col overflow-y-auto bg-gray-800">
        <div class="px-2">
            <select
                class="py-2 w-full border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                @change="selectDatabase($event.target.value)"
            >
                <option 
                    v-for="db in databases"
                    :key="db"
                    :selected="db == database"
                    :value="db"
                >
                    {{ db }}
                </option>
            </select>
        </div>
        <div class="p-2 sticky top-0">
            <x-input
                v-model="keyword"
                type="text"
                placeholder="Search for item..."
                class="w-full text-sm"
            />
        </div>

        <div class="flex flex-col overflow-y-auto">
            <!-- Tables tabs -->
            <h3
                class="px-2 mt-4 text-xs leading-4 font-semibold text-gray-500 uppercase tracking-wider"
            >
                Tables
            </h3>

            <nav class="text-sm text-white">
                <router-link
                    v-for="table in filteredTables"
                    :key="`table-${table.tableName}`"
                    :to="`/tables/${table.tableName}`"
                    active-class="bg-gray-700 rounded-l-full border-r-4 border-blue-500 group mt-1"
                    class="flex w-full pr-6 pl-4 py-2 items-center gap-x-4 hover:bg-gray-700 hover:rounded-l-full"
                    :title="table.tableName"
                >
                    <icon-table
                        v-if="table.tableType == 'BASE TABLE'"
                        size="6"
                        class="shrink-0"
                    />
                    <icon-eye
                        v-else-if="table.tableType == 'VIEW'"
                        size="6"
                        class="shrink-0"
                    />
                    {{ table.tableName }}
                </router-link>
            </nav>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            keyword: '',
            databases: Dibi.allDatabases,
            database: Dibi.database,
        };
    },
    computed: {
        filteredTables() {
            if (!this.keyword) {
                return Dibi.informationSchema.tables;
            }
            return Dibi.informationSchema.tables.filter((table) => table.tableName.includes(this.keyword));
        },
    },
    methods: {
        async selectDatabase(database) {
            await axios.post(`${Dibi.path}/api/select-database`, {
                database,
            });

            window.location.href = Dibi.path;
        },
    },
};
</script>
