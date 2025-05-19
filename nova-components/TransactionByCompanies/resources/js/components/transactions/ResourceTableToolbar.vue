<template>
  <div class="flex flex-col md:flex-row md:items-center py-3 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center flex-1">
      <!-- Toolbar Items -->
      <div class="h-9 ml-auto flex items-center pr-2 md:pr-3">
        <!-- Filters -->
        <FilterMenu
          :filter="filter"
          @filter-changed="value => $emit('filter-changed', value)"
          @clear-selected-filters="$emit('clear-selected-filters')"
        />
      </div>
    </div>
  </div>
</template>

<script>
import FilterMenu from "./FilterMenu.vue";

export default {
  components: {FilterMenu},
  emits: [
    'filter-changed',
    'clear-selected-filters'
  ],
  props: {
    filter: {type: Object, required: true},
  },
}
</script>
